<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

api_require_method('POST');

global $USR;
$auth = api_require_bearer($USR);
$body = api_read_json();

$direction = $body['direction'] ?? '';
$inventoryId = (int) ($body['inventory_id'] ?? 0);
$quantity = (int) ($body['quantity'] ?? 1);
$fromLocationId = isset($body['from_location_id']) ? (int) $body['from_location_id'] : 0;
$toLocationId = isset($body['to_location_id']) ? (int) $body['to_location_id'] : 0;
$rfidUid = isset($body['rfid_uid']) ? trim((string) $body['rfid_uid']) : '';
$note = isset($body['note']) ? trim((string) $body['note']) : '';
$createdAtRaw = isset($body['created_at']) ? trim((string) $body['created_at']) : '';

if (!in_array($direction, ['in', 'out'], true)) {
    api_json(400, ['ok' => false, 'error' => 'direction must be "in" or "out"']);
}
if ($inventoryId < 1) {
    api_json(400, ['ok' => false, 'error' => 'inventory_id required']);
}
if ($quantity < 1) {
    $quantity = 1;
}

if ($direction === 'in' && $fromLocationId < 1) {
    api_json(400, ['ok' => false, 'error' => 'Incoming requires from_location_id (where it came from)']);
}
if ($direction === 'out' && $toLocationId < 1) {
    api_json(400, ['ok' => false, 'error' => 'Outgoing requires to_location_id (where it is going)']);
}

$fromLocationId = $fromLocationId > 0 ? $fromLocationId : null;
$toLocationId = $toLocationId > 0 ? $toLocationId : null;

$pdo = $USR->getDb();

$fkReferencedTable = static function (PDO $pdo, string $column): ?string {
    try {
        $stmt = $pdo->prepare(
            "SELECT `REFERENCED_TABLE_NAME`
             FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'inventory_movements'
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL
             LIMIT 1"
        );
        $stmt->execute([$column]);
        $t = $stmt->fetchColumn();
        return is_string($t) && $t !== '' ? $t : null;
    } catch (Throwable $e) {
        return null;
    }
};

$invTables = [];
$invChk = $pdo->prepare(
    "SELECT `TABLE_NAME`
     FROM INFORMATION_SCHEMA.TABLES
     WHERE TABLE_SCHEMA = DATABASE()
       AND `TABLE_NAME` IN ('inventory', 'inventory_objects')"
);
$invChk->execute();
$invTables = $invChk->fetchAll(PDO::FETCH_COLUMN) ?: [];
$hasInventoryObjects = in_array('inventory_objects', $invTables, true);
if (!$hasInventoryObjects) {
    api_json(500, ['ok' => false, 'error' => 'inventory_objects table not found']);
}
$objIdCol = null;
try {
    $objColsStmt = $pdo->prepare(
        "SELECT `COLUMN_NAME`
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'inventory_objects'
           AND `COLUMN_NAME` IN ('id', 'inventory_id')"
    );
    $objColsStmt->execute();
    $objCols = $objColsStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    if (in_array('inventory_id', $objCols, true)) {
        $objIdCol = 'inventory_id';
    } elseif (in_array('id', $objCols, true)) {
        $objIdCol = 'id';
    }
} catch (Throwable $e) {
    $objIdCol = null;
}
if ($objIdCol === null) {
    api_json(500, ['ok' => false, 'error' => 'inventory_objects id column not found (expected id or inventory_id)']);
}
$objTagCol = null;
try {
    $tagColsStmt = $pdo->prepare(
        "SELECT `COLUMN_NAME`
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'inventory_objects'"
    );
    $tagColsStmt->execute();
    $tagCols = $tagColsStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    foreach (['rfid_uid', 'rfid', 'tag_uid', 'epc'] as $candCol) {
        if (in_array($candCol, $tagCols, true)) {
            $objTagCol = $candCol;
            break;
        }
    }
} catch (Throwable $e) {
    $objTagCol = null;
}
$normalizeToken = static function (string $s): string {
    $s = strtolower($s);
    $s = preg_replace('/[^a-z0-9]+/', '', $s) ?? '';
    return $s;
};

$resolvedInventoryObjectId = null;
$rfidUidNormalized = strtolower(trim($rfidUid));

// If a tag is scanned, resolve from user_inventory first (authoritative tag->type mapping).
if ($rfidUidNormalized !== '') {
    try {
        $tagMapStmt = $pdo->prepare(
            "SELECT `inventory_id`
             FROM `user_inventory`
             WHERE LOWER(`rfid_uid`) = LOWER(?)
             ORDER BY `id` DESC
             LIMIT 1"
        );
        $tagMapStmt->execute([$rfidUidNormalized]);
        $mappedObjId = $tagMapStmt->fetchColumn();
        if ($mappedObjId !== false) {
            $mappedObjId = (int) $mappedObjId;
            if ($mappedObjId > 0) {
                $chkMapped = $pdo->prepare(
                    'SELECT `' . $objIdCol . '` AS `obj_id` FROM `inventory_objects` WHERE `' . $objIdCol . '` = ? LIMIT 1'
                );
                $chkMapped->execute([$mappedObjId]);
                $mappedRow = $chkMapped->fetch(PDO::FETCH_ASSOC);
                if ($mappedRow) {
                    $resolvedInventoryObjectId = (int) $mappedRow['obj_id'];
                }
            }
        }
    } catch (Throwable $e) {
        // fall through
    }
}

// Secondary: if inventory_objects has a tag column, resolve by that column.
if ($resolvedInventoryObjectId === null && $rfidUidNormalized !== '' && $objTagCol !== null) {
    $tagObjStmt = $pdo->prepare(
        'SELECT `' . $objIdCol . '` AS `obj_id` FROM `inventory_objects` WHERE LOWER(`' . $objTagCol . '`) = LOWER(?) LIMIT 1'
    );
    $tagObjStmt->execute([$rfidUidNormalized]);
    $tagObjRow = $tagObjStmt->fetch(PDO::FETCH_ASSOC);
    if ($tagObjRow) {
        $resolvedInventoryObjectId = (int) $tagObjRow['obj_id'];
    }
}

// For scanned tags with no mapping yet, ignore this tag (do not create movement row).
if ($resolvedInventoryObjectId === null && $rfidUidNormalized !== '') {
    api_json(200, [
        'ok' => true,
        'ignored' => true,
        'reason' => 'unassigned_rfid',
        'rfid_uid' => $rfidUidNormalized,
    ]);
}

if ($resolvedInventoryObjectId === null) {
    $chk = $pdo->prepare('SELECT `' . $objIdCol . '` AS `obj_id` FROM `inventory_objects` WHERE `' . $objIdCol . '` = ? LIMIT 1');
    $chk->execute([$inventoryId]);
    $objRow = $chk->fetch(PDO::FETCH_ASSOC);
    if ($objRow) {
        $resolvedInventoryObjectId = (int) $objRow['obj_id'];
    }
}

if ($resolvedInventoryObjectId === null && in_array('inventory', $invTables, true)) {
    // Backward compatibility: incoming id may be legacy inventory.id.
    $legacy = $pdo->prepare('SELECT `name`, `label` FROM `inventory` WHERE `id` = ? LIMIT 1');
    $legacy->execute([$inventoryId]);
    $legacyRow = $legacy->fetch(PDO::FETCH_ASSOC);
    if ($legacyRow) {
        $legacyA = $normalizeToken((string) ($legacyRow['name'] ?? ''));
        $legacyB = $normalizeToken((string) ($legacyRow['label'] ?? ''));

        $allObj = $pdo->query('SELECT `' . $objIdCol . '` AS `obj_id`, `name`, `label` FROM `inventory_objects`');
        $bestId = null;
        foreach (($allObj ? $allObj->fetchAll(PDO::FETCH_ASSOC) : []) as $r) {
            $a = $normalizeToken((string) ($r['name'] ?? ''));
            $b = $normalizeToken((string) ($r['label'] ?? ''));
            if (($legacyA !== '' && ($legacyA === $a || $legacyA === $b)) || ($legacyB !== '' && ($legacyB === $a || $legacyB === $b))) {
                $bestId = (int) $r['obj_id'];
                break;
            }
        }
        if ($bestId !== null && $bestId > 0) {
            $resolvedInventoryObjectId = $bestId;
        }
    }
}

if ($resolvedInventoryObjectId === null) {
    api_json(400, ['ok' => false, 'error' => 'inventory_id must be a valid inventory_objects.id (or mappable legacy inventory id).']);
}

$tblChk = $pdo->prepare(
    "SELECT `TABLE_NAME`
     FROM INFORMATION_SCHEMA.TABLES
     WHERE TABLE_SCHEMA = DATABASE()
       AND `TABLE_NAME` IN ('sites', 'locations')"
);
$tblChk->execute();
$locTables = $tblChk->fetchAll(PDO::FETCH_COLUMN) ?: [];
$hasSites = in_array('sites', $locTables, true);
$hasLocations = in_array('locations', $locTables, true);
if (!$hasSites && !$hasLocations) {
    api_json(500, ['ok' => false, 'error' => 'No location table found (sites/locations)']);
}

$fromLocationFkTable = $fkReferencedTable($pdo, 'from_location_id');
$toLocationFkTable = $fkReferencedTable($pdo, 'to_location_id');

$locationExists = static function (PDO $pdo, int $id, bool $hasSites, bool $hasLocations): bool {
    if ($hasLocations) {
        $c = $pdo->prepare('SELECT `id` FROM `locations` WHERE `id` = ? LIMIT 1');
        $c->execute([$id]);
        if ($c->fetch()) {
            return true;
        }
    }
    if ($hasSites) {
        $c = $pdo->prepare('SELECT `id` FROM `sites` WHERE `id` = ? LIMIT 1');
        $c->execute([$id]);
        if ($c->fetch()) {
            return true;
        }
    }

    return false;
};

if ($fromLocationId !== null && !$locationExists($pdo, $fromLocationId, $hasSites, $hasLocations)) {
    api_json(400, ['ok' => false, 'error' => 'Invalid from_location_id']);
}
if ($toLocationId !== null && !$locationExists($pdo, $toLocationId, $hasSites, $hasLocations)) {
    api_json(400, ['ok' => false, 'error' => 'Invalid to_location_id']);
}

$resolveLocationForFk = static function (
    PDO $pdo,
    ?int $id,
    ?string $fkTable,
    bool $hasSites,
    bool $hasLocations
): ?int {
    if ($id === null) {
        return null;
    }
    if ($fkTable === null) {
        return $id;
    }
    if ($fkTable !== 'sites' && $fkTable !== 'locations') {
        return $id;
    }
    $check = $pdo->prepare('SELECT `id`, `name` FROM `' . $fkTable . '` WHERE `id` = ? LIMIT 1');
    $check->execute([$id]);
    $row = $check->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        return (int) $row['id'];
    }
    $sourceTable = $fkTable === 'sites' ? 'locations' : 'sites';
    if (($sourceTable === 'sites' && !$hasSites) || ($sourceTable === 'locations' && !$hasLocations)) {
        return $id;
    }
    $src = $pdo->prepare('SELECT `name` FROM `' . $sourceTable . '` WHERE `id` = ? LIMIT 1');
    $src->execute([$id]);
    $srcRow = $src->fetch(PDO::FETCH_ASSOC);
    if (!$srcRow || !isset($srcRow['name'])) {
        return $id;
    }
    $dst = $pdo->prepare('SELECT `id` FROM `' . $fkTable . '` WHERE LOWER(`name`) = LOWER(?) LIMIT 1');
    $dst->execute([(string) $srcRow['name']]);
    $mappedId = $dst->fetchColumn();
    if ($mappedId !== false) {
        return (int) $mappedId;
    }
    return $id;
};

$fromLocationId = $resolveLocationForFk($pdo, $fromLocationId, $fromLocationFkTable, $hasSites, $hasLocations);
$toLocationId = $resolveLocationForFk($pdo, $toLocationId, $toLocationFkTable, $hasSites, $hasLocations);

$uid = $auth['user_id'];

// inventory_movements.inventory_id is a FK to inventory_objects.id
// but the app sends inventory.id (category). Translate via inventory.inventory_object_id when available.
$movementInventoryObjectId = $resolvedInventoryObjectId;

$rfidVal = ($rfidUid !== '' && strlen($rfidUid) <= 128) ? $rfidUid : null;
$inventoryInsertId = $movementInventoryObjectId;
// Movements are always written to inventory_movements using inventory_objects.id
// (the same object/type ID selected in the app).

$noteVal = ($note !== '') ? substr($note, 0, 512) : null;
$createdAtVal = null;
if ($createdAtRaw !== '') {
    if (strlen($createdAtRaw) > 64) {
        api_json(400, ['ok' => false, 'error' => 'created_at is too long']);
    }
    // Support date-only from app: MM-DD-YYYY (optionally with / separators).
    if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $createdAtRaw, $m)) {
        $mm = str_pad($m[1], 2, '0', STR_PAD_LEFT);
        $dd = str_pad($m[2], 2, '0', STR_PAD_LEFT);
        $yyyy = $m[3];
        $createdAtVal = $yyyy . '-' . $mm . '-' . $dd . ' 00:00:00';
    } else {
        $ts = strtotime($createdAtRaw);
        if ($ts === false) {
            api_json(400, ['ok' => false, 'error' => 'created_at must be a valid date/time string (MM-DD-YYYY or ISO)']);
        }
        // MySQL DATETIME format
        $createdAtVal = date('Y-m-d H:i:s', $ts);
    }
}

$cols = '`user_id`, `direction`, `inventory_id`, `quantity`, `from_location_id`, `to_location_id`, `rfid_uid`, `note`';
$vals = '?, ?, ?, ?, ?, ?, ?, ?';
$params = [
    $uid,
    $direction,
    $inventoryInsertId,
    $quantity,
    $fromLocationId,
    $toLocationId,
    $rfidVal,
    $noteVal,
];
if ($createdAtVal !== null) {
    $cols .= ', `created_at`';
    $vals .= ', ?';
    $params[] = $createdAtVal;
}

// Ignore duplicate scans of the same tag/movement signature in a short window.
if ($rfidVal !== null) {
    $dupStart = date('Y-m-d H:i:s', strtotime(($createdAtVal ?? date('Y-m-d H:i:s')) . ' -2 minutes'));
    $dupEnd = date('Y-m-d H:i:s', strtotime(($createdAtVal ?? date('Y-m-d H:i:s')) . ' +2 minutes'));
    $dupStmt = $pdo->prepare(
        "SELECT `id`
         FROM `inventory_movements`
         WHERE `direction` = ?
           AND `inventory_id` = ?
           AND (
             (`from_location_id` <=> ?)
             AND (`to_location_id` <=> ?)
           )
           AND LOWER(`rfid_uid`) = LOWER(?)
           AND `created_at` BETWEEN ? AND ?
         ORDER BY `id` DESC
         LIMIT 1"
    );
    $dupStmt->execute([
        $direction,
        $inventoryInsertId,
        $fromLocationId,
        $toLocationId,
        $rfidVal,
        $dupStart,
        $dupEnd,
    ]);
    $existingId = $dupStmt->fetchColumn();
    if ($existingId !== false) {
        api_json(200, [
            'ok' => true,
            'ignored_duplicate' => true,
            'existing_id' => (int) $existingId,
            'inventory_id_used' => $inventoryInsertId,
            'rfid_uid' => $rfidVal,
        ]);
    }
}

$sql = 'INSERT INTO `inventory_movements` (' . $cols . ') VALUES (' . $vals . ')';
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute($params);
} catch (PDOException $e) {
    $sqlState = $e->errorInfo[0] ?? '';
    $driverCode = (int) ($e->errorInfo[1] ?? 0);
    if (!($sqlState === '23000' || $driverCode === 1452)) {
        api_json(500, ['ok' => false, 'error' => 'Movement save failed', 'detail' => $e->getMessage()]);
    }
    $detail = (string) ($e->errorInfo[2] ?? $e->getMessage());
    $detail = trim(str_replace(["\r", "\n"], ' ', $detail));
    api_json(400, [
        'ok' => false,
        'error' => 'Movement could not be saved: ' . $detail
            . ' | inventory_id must equal inventory_objects.id'
            . ' | inv=' . (string) $inventoryInsertId
            . ' from=' . (string) ($fromLocationId ?? 0)
            . ' to=' . (string) ($toLocationId ?? 0),
        'detail' => $detail,
        'inventory_id_used' => $inventoryInsertId,
        'from_location_id' => $fromLocationId,
        'to_location_id' => $toLocationId,
    ]);
}
$newId = (int) $pdo->lastInsertId();

api_json(200, [
    'ok' => true,
    'id' => $newId,
    'inventory_id_used' => $inventoryInsertId,
]);
