<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

api_require_method('POST');

global $USR;
api_require_bearer($USR);
$body = api_read_json();

$rfids = $body['rfids'] ?? ($body['rfid_uids'] ?? ($body['rfidUids'] ?? null));
if (!is_array($rfids)) {
    api_json(400, ['ok' => false, 'error' => 'rfids[] are required (or rfid_uids[])']);
}

$clean = [];
foreach ($rfids as $r) {
    if (!is_string($r)) {
        continue;
    }
    $r = strtolower(trim($r));
    if ($r === '' || strlen($r) > 128) {
        continue;
    }
    $clean[$r] = true;
}
$rfidList = array_keys($clean);
if (count($rfidList) < 1) {
    api_json(400, ['ok' => false, 'error' => 'No valid rfids provided']);
}

$pdo = $USR->getDb();
$placeholders = implode(',', array_fill(0, count($rfidList), '?'));

try {
    $delObjects = $pdo->prepare("DELETE FROM `inventory_objects` WHERE LOWER(`rfid_uid`) IN ($placeholders)");
    $delObjects->execute($rfidList);
    $removedFromObjects = (int) $delObjects->rowCount();

    $del = $pdo->prepare("DELETE FROM `user_inventory` WHERE LOWER(`rfid_uid`) IN ($placeholders)");
    $del->execute($rfidList);
    $unassigned = (int) $del->rowCount();
} catch (Throwable $e) {
    api_json(500, ['ok' => false, 'error' => 'Failed to unassign tags']);
}

api_json(200, [
    'ok' => true,
    'assigned' => $unassigned,
    'unassigned' => $unassigned,
    'removed_from_inventory_objects' => $removedFromObjects,
]);
