<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

api_require_method('POST');

global $USR;
api_require_bearer($USR);
$body = api_read_json();

$days = isset($body['days']) ? (int) $body['days'] : 30;
if ($days < 1) {
    $days = 30;
}

$pdo = $USR->getDb();

$locationTable = null;
$locTblChk = $pdo->prepare(
    "SELECT `TABLE_NAME`
     FROM INFORMATION_SCHEMA.TABLES
     WHERE TABLE_SCHEMA = DATABASE()
       AND `TABLE_NAME` IN ('sites', 'locations')
     ORDER BY CASE WHEN `TABLE_NAME` = 'locations' THEN 0 ELSE 1 END"
);
$locTblChk->execute();
$locationTable = $locTblChk->fetchColumn() ?: null;
if ($locationTable === null) {
    api_json(500, ['ok' => false, 'error' => 'No location table found (sites/locations)']);
}

$objIdCol = 'id';
try {
    $objColStmt = $pdo->prepare(
        "SELECT `COLUMN_NAME`
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'inventory_objects'
           AND `COLUMN_NAME` IN ('id', 'inventory_id')"
    );
    $objColStmt->execute();
    $objCols = $objColStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    if (in_array('inventory_id', $objCols, true)) {
        $objIdCol = 'inventory_id';
    }
} catch (Throwable $e) {
    $objIdCol = 'id';
}

$sql = "SELECT
            mo.`id`,
            mo.`direction`,
            mo.`rfid_uid`,
            mo.`inventory_id`,
            mo.`from_location_id`,
            mo.`to_location_id`,
            COALESCE(NULLIF(io.`label`, ''), NULLIF(io.`name`, ''), CONCAT('Inventory #', mo.`inventory_id`)) AS `object_label`,
            mo.`quantity`,
            mo.`created_at`,
            fl.`name` AS `from_location_name`,
            tl.`name` AS `to_location_name`
        FROM `inventory_movements` mo
        LEFT JOIN `inventory_objects` io ON io.`" . $objIdCol . "` = mo.`inventory_id`
        LEFT JOIN `" . $locationTable . "` fl ON fl.`id` = mo.`from_location_id`
        LEFT JOIN `" . $locationTable . "` tl ON tl.`id` = mo.`to_location_id`
        WHERE mo.`created_at` >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ORDER BY mo.`created_at` DESC, mo.`id` DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$days]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

api_json(200, [
    'ok' => true,
    'days' => $days,
    'total' => count($rows),
    'items' => $rows,
]);

