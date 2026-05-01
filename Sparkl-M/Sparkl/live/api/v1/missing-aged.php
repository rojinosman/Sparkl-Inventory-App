<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

api_require_method('POST');

global $USR;
api_require_bearer($USR);
$body = api_read_json();

$days = isset($body['days']) ? (int) $body['days'] : 9;
$locationId = isset($body['location_id'])
    ? (int) $body['location_id']
    : (isset($body['locationId']) ? (int) $body['locationId'] : 0);
$objectGroup = strtoupper(trim((string) ($body['object_group'] ?? ($body['objectGroup'] ?? ''))));

if ($days < 1) {
    $days = 9;
}

$allowedGroups = ['TRAYS', 'CUPS', 'BOWLS', 'LIDS', 'CLAMSHELLS', 'UTENSILS', 'OTHER'];
if ($objectGroup !== '' && !in_array($objectGroup, $allowedGroups, true)) {
    api_json(400, ['ok' => false, 'error' => 'Invalid object_group']);
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

$where = [
    "LOWER(mo.`direction`) IN ('out', 'outgoing')",
    "mo.`rfid_uid` IS NOT NULL",
    "mo.`rfid_uid` <> ''",
    "mo.`created_at` <= DATE_SUB(NOW(), INTERVAL ? DAY)",
];
$params = [$days];

if ($locationId > 0) {
    $where[] = "mo.`to_location_id` = ?";
    $params[] = $locationId;
}

if ($objectGroup !== '') {
    $groupExpr = "LOWER(CONCAT(COALESCE(io.`name`, ''), ' ', COALESCE(io.`label`, '')))";
    switch ($objectGroup) {
        case 'TRAYS':
            $where[] = "$groupExpr LIKE '%tray%'";
            break;
        case 'CUPS':
            $where[] = "$groupExpr LIKE '%cup%'";
            break;
        case 'BOWLS':
            $where[] = "$groupExpr LIKE '%bowl%'";
            break;
        case 'LIDS':
            $where[] = "$groupExpr LIKE '%lid%'";
            break;
        case 'CLAMSHELLS':
            $where[] = "($groupExpr LIKE '%clamshell%' OR $groupExpr LIKE '%clam shell%')";
            break;
        case 'UTENSILS':
            $where[] = "($groupExpr LIKE '%utensil%' OR $groupExpr LIKE '%fork%' OR $groupExpr LIKE '%spoon%' OR $groupExpr LIKE '%knife%' OR $groupExpr LIKE '%cutlery%')";
            break;
        case 'OTHER':
            $where[] = "($groupExpr NOT LIKE '%tray%' AND $groupExpr NOT LIKE '%cup%' AND $groupExpr NOT LIKE '%bowl%' AND $groupExpr NOT LIKE '%lid%' AND $groupExpr NOT LIKE '%clamshell%' AND $groupExpr NOT LIKE '%clam shell%' AND $groupExpr NOT LIKE '%utensil%' AND $groupExpr NOT LIKE '%fork%' AND $groupExpr NOT LIKE '%spoon%' AND $groupExpr NOT LIKE '%knife%' AND $groupExpr NOT LIKE '%cutlery%')";
            break;
    }
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
            mo.`rfid_uid`,
            mo.`inventory_id`,
            mo.`to_location_id` AS `vendor_location_id`,
            l.`name` AS `vendor_location_name`,
            mo.`created_at` AS `checked_out_date`,
            TIMESTAMPDIFF(DAY, mo.`created_at`, NOW()) AS `days_out`,
            io.`name` AS `object_name`,
            io.`label` AS `object_label`
        FROM `inventory_movements` mo
        LEFT JOIN `inventory_objects` io ON io.`" . $objIdCol . "` = mo.`inventory_id`
        LEFT JOIN `" . $locationTable . "` l ON l.`id` = mo.`to_location_id`
        WHERE " . implode(" AND ", $where) . "
        ORDER BY mo.`created_at` ASC, mo.`id` ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

api_json(200, [
    'ok' => true,
    'days' => $days,
    'total_missing' => count($rows),
    'items' => $rows,
]);

