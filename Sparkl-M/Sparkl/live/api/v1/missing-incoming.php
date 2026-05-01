<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

api_require_method('POST');

global $USR;
$auth = api_require_bearer($USR);
$body = api_read_json();

$inventoryId = (int) ($body['inventory_id'] ?? 0);
$fromLocationId = (int) ($body['from_location_id'] ?? 0);
$kitchenLocationId = (int) ($body['kitchen_location_id'] ?? 0);

if ($inventoryId < 1 || $fromLocationId < 1 || $kitchenLocationId < 1) {
    api_json(400, ['ok' => false, 'error' => 'inventory_id, from_location_id and kitchen_location_id are required']);
}

$pdo = $USR->getDb();

// Resolve inventory_objects row id column and category parent column.
$objColsStmt = $pdo->prepare(
    "SELECT `COLUMN_NAME`
     FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'inventory_objects'"
);
$objColsStmt->execute();
$objCols = $objColsStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

$objIdCol = in_array('inventory_id', $objCols, true) ? 'inventory_id' : 'id';
$objParentCol = null;
if (in_array('parent_id', $objCols, true)) {
    $objParentCol = 'parent_id';
} elseif (in_array('inventory_object_id', $objCols, true)) {
    $objParentCol = 'inventory_object_id';
} elseif (in_array('inventory_id', $objCols, true)) {
    $objParentCol = 'inventory_id';
}

if ($objParentCol === null) {
    api_json(500, ['ok' => false, 'error' => 'inventory_objects parent mapping column not found']);
}

// Outgoing to vendor location (expected to come back)
$outStmt = $pdo->prepare(
    "SELECT DISTINCT LOWER(m.`rfid_uid`) AS rfid_uid
     FROM `inventory_movements` m
     INNER JOIN `inventory_objects` obj ON obj.`$objIdCol` = m.`inventory_id`
     WHERE `direction` = 'out'
       AND m.`to_location_id` = ?
       AND obj.`$objParentCol` = ?
       AND m.`rfid_uid` IS NOT NULL
       AND m.`rfid_uid` <> ''"
);
$outStmt->execute([$fromLocationId, $inventoryId]);
$outgoing = $outStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
$outSet = [];
foreach ($outgoing as $rfid) {
    $rfid = is_string($rfid) ? strtolower($rfid) : '';
    if ($rfid !== '') $outSet[$rfid] = true;
}

// Incoming from vendor back to kitchen
$inStmt = $pdo->prepare(
    "SELECT DISTINCT LOWER(m.`rfid_uid`) AS rfid_uid
     FROM `inventory_movements` m
     INNER JOIN `inventory_objects` obj ON obj.`$objIdCol` = m.`inventory_id`
     WHERE `direction` = 'in'
       AND m.`from_location_id` = ?
       AND m.`to_location_id` = ?
       AND obj.`$objParentCol` = ?
       AND m.`rfid_uid` IS NOT NULL
       AND m.`rfid_uid` <> ''"
);
$inStmt->execute([$fromLocationId, $kitchenLocationId, $inventoryId]);
$incoming = $inStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
$inSet = [];
foreach ($incoming as $rfid) {
    $rfid = is_string($rfid) ? strtolower($rfid) : '';
    if ($rfid !== '') $inSet[$rfid] = true;
}

$missing = [];
foreach ($outSet as $rfid => $_) {
    if (!isset($inSet[$rfid])) {
        $missing[] = $rfid;
    }
}

api_json(200, [
    'ok' => true,
    'outstanding_count' => count($missing),
    'outstanding_rfids' => $missing,
]);

