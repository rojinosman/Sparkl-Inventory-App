<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

api_require_method('POST');

global $USR;

$auth = api_require_bearer($USR);
$body = api_read_json();
$rfid = isset($body['rfid']) ? trim((string) $body['rfid']) : '';

if ($rfid === '') {
    api_json(400, ['ok' => false, 'error' => 'rfid required']);
}

$pdo = $USR->getDb();

$uid = $auth['user_id'];
$rfid = strtolower($rfid);

$stmt = $pdo->prepare(
    'SELECT inv.id, inv.rfid_uid, inv.inventory_object_id, obj.name, obj.label
     FROM inventory inv
     LEFT JOIN inventory_objects obj ON obj.id = inv.inventory_object_id
     WHERE LOWER(inv.rfid_uid) = LOWER(?)'
);
$stmt->execute([$rfid]);
$inv = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$inv) {
    $ins = $pdo->prepare(
        'INSERT INTO inventory (rfid_uid, assigned_user_id, assigned_at)
         VALUES (?, ?, NOW())'
    );
    $ins->execute([$rfid, $uid]);
    $inv = [
        'id' => (int) $pdo->lastInsertId(),
        'rfid_uid' => $rfid,
        'inventory_object_id' => null,
        'name' => null,
        'label' => null,
    ];
} else {
    $upd = $pdo->prepare(
        'UPDATE inventory SET assigned_user_id = ?, assigned_at = NOW() WHERE id = ?'
    );
    $upd->execute([$uid, (int) $inv['id']]);
}

if (!empty($inv['inventory_object_id'])) {
    $sync = $pdo->prepare(
        'INSERT INTO user_inventory (user_id, inventory_id, rfid_uid)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE inventory_id = VALUES(inventory_id), created_at = CURRENT_TIMESTAMP'
    );
    $sync->execute([$uid, (int) $inv['inventory_object_id'], $rfid]);
}

api_json(200, [
    'ok' => true,
    'inventory' => [
        'id' => (int) $inv['id'],
        'inventory_id' => isset($inv['inventory_object_id']) ? (int) $inv['inventory_object_id'] : null,
        'name' => $inv['name'],
        'label' => $inv['label'],
        'rfid_uid' => $inv['rfid_uid'],
    ],
    'user_id' => $uid,
]);
