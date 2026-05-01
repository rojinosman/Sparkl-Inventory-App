<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

api_require_method('GET');

global $USR;

$auth = api_require_bearer($USR);
$pdo = $USR->getDb();

$objColsStmt = $pdo->prepare(
    "SELECT `COLUMN_NAME`
     FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'inventory_objects'"
);
$objColsStmt->execute();
$objCols = $objColsStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

if (!in_array('rfid_uid', $objCols, true)) {
    api_json(500, ['ok' => false, 'error' => 'inventory_objects schema missing rfid_uid']);
}

$parentCol = null;
if (in_array('parent_id', $objCols, true)) {
    $parentCol = 'parent_id';
} elseif (in_array('inventory_object_id', $objCols, true)) {
    $parentCol = 'inventory_object_id';
} elseif (in_array('inventory_id', $objCols, true)) {
    $parentCol = 'inventory_id';
}
if ($parentCol === null) {
    api_json(500, ['ok' => false, 'error' => 'inventory_objects schema missing parent mapping column']);
}

$objCreatedCol = in_array('create_d', $objCols, true)
    ? 'create_d'
    : (in_array('created_at', $objCols, true) ? 'created_at' : (in_array('update_d', $objCols, true) ? 'update_d' : null));
$createdExpr = $objCreatedCol !== null ? ('obj.`' . $objCreatedCol . '`') : 'CURRENT_TIMESTAMP';

$stmt = $pdo->prepare(
    'SELECT
       obj.`' . $parentCol . '` AS inventory_id,
       obj.`rfid_uid` AS rfid_uid,
       ' . $createdExpr . ' AS created_at,
       inv.name,
       inv.label
     FROM inventory_objects obj
     LEFT JOIN inventory inv ON inv.id = obj.`' . $parentCol . '`
     ORDER BY created_at DESC'
);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

api_json(200, [
    'ok' => true,
    'user_id' => $auth['user_id'],
    'items' => $rows,
]);
