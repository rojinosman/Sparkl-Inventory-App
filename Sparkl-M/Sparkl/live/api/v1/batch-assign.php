<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

api_require_method('POST');

global $USR;
$auth = api_require_bearer($USR);
$body = api_read_json();

$inventoryId = (int) ($body['inventory_id'] ?? $body['inventoryId'] ?? $body['id'] ?? 0);
$rfidsRaw = $body['rfids'] ?? $body['rfid_uids'] ?? $body['rfidUids'] ?? $body['tags'] ?? null;
if ($inventoryId < 1) {
    api_json(400, ['ok' => false, 'error' => 'inventory_id is required']);
}

if (is_string($rfidsRaw)) {
    $rfids = preg_split('/[\s,;]+/', $rfidsRaw) ?: [];
} elseif (is_array($rfidsRaw)) {
    $rfids = $rfidsRaw;
} else {
    $rfids = [];
}
if (count($rfids) < 1) {
    api_json(400, ['ok' => false, 'error' => 'rfids[] are required']);
}

$pdo = $USR->getDb();

$chkInv = $pdo->prepare('SELECT `id`, `name`, `label` FROM `inventory` WHERE `id` = ? LIMIT 1');
$chkInv->execute([$inventoryId]);
$invRow = $chkInv->fetch(PDO::FETCH_ASSOC) ?: null;
if (!$invRow) {
    api_json(404, ['ok' => false, 'error' => 'Unknown inventory_id']);
}
$inventoryName = (string) ($invRow['name'] ?? '');
$inventoryLabel = (string) ($invRow['label'] ?? '');

$objColsStmt = $pdo->prepare(
    "SELECT `COLUMN_NAME`, `COLUMN_KEY`, `EXTRA`
     FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'inventory_objects'"
);
$objColsStmt->execute();
$objRows = $objColsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
$objCols = [];
$objIsPrimary = [];
$objIsAutoInc = [];
foreach ($objRows as $r) {
    $c = (string) ($r['COLUMN_NAME'] ?? '');
    if ($c === '') {
        continue;
    }
    $objCols[] = $c;
    $objIsPrimary[$c] = (($r['COLUMN_KEY'] ?? '') === 'PRI');
    $objIsAutoInc[$c] = stripos((string) ($r['EXTRA'] ?? ''), 'auto_increment') !== false;
}
if (!in_array('inventory_id', $objCols, true) || !in_array('parent_id', $objCols, true) || !in_array('rfid_uid', $objCols, true)) {
    api_json(500, ['ok' => false, 'error' => 'inventory_objects must include inventory_id, parent_id, rfid_uid']);
}
$includeInventoryIdOnInsert = !(($objIsPrimary['inventory_id'] ?? false) && ($objIsAutoInc['inventory_id'] ?? false));
$objInsertCols = $includeInventoryIdOnInsert ? ['inventory_id', 'parent_id', 'rfid_uid'] : ['parent_id', 'rfid_uid'];
if (in_array('name', $objCols, true)) {
    $objInsertCols[] = 'name';
}
if (in_array('label', $objCols, true)) {
    $objInsertCols[] = 'label';
}
$objInsertValsTemplate = [];
$quotedObjCols = array_map(static fn(string $c): string => '`' . $c . '`', $objInsertCols);
$objInsert = $pdo->prepare(
    'INSERT INTO `inventory_objects` (' . implode(',', $quotedObjCols) . ')
     VALUES (' . implode(',', array_fill(0, count($objInsertCols), '?')) . ')'
);
$nextObjIdStmt = null;
if (($objIsPrimary['inventory_id'] ?? false) && !($objIsAutoInc['inventory_id'] ?? false)) {
    $nextObjIdStmt = $pdo->prepare('SELECT COALESCE(MAX(`inventory_id`), 0) + 1 FROM `inventory_objects`');
}
$objExists = $pdo->prepare(
    'SELECT `inventory_id`
     FROM `inventory_objects`
     WHERE LOWER(`rfid_uid`) = LOWER(?)
     LIMIT 1'
);

$normalize = static function ($v): string {
    $s = strtolower(trim((string) $v));
    if ($s === '' || strlen($s) < 8 || strlen($s) > 128) {
        return '';
    }
    return $s;
};

$looksLikeRfid = static function (string $s): bool {
    if (strlen($s) < 8 || strlen($s) > 128) {
        return false;
    }
    // Accept typical EPC/UID hex-like values (length >= 16 preferred), or mixed alnum tag IDs.
    if (preg_match('/^[a-f0-9]{16,}$/i', $s)) {
        return true;
    }
    return (bool) preg_match('/^(?=.*[a-z])(?=.*[0-9])[a-z0-9_-]{8,}$/i', $s);
};

$collect = static function ($node) use (&$collect, $normalize, $looksLikeRfid): array {
    $out = [];
    if (is_scalar($node)) {
        $raw = trim((string) $node);
        if ($raw === '') {
            return $out;
        }
        $parts = preg_split('/[\s,;]+/', $raw) ?: [$raw];
        foreach ($parts as $p) {
            $n = $normalize($p);
            if ($n !== '' && $looksLikeRfid($n)) {
                $out[] = $n;
            }
        }
        return $out;
    }
    if (!is_array($node)) {
        return $out;
    }
    foreach ($node as $v) {
        foreach ($collect($v) as $x) {
            $out[] = $x;
        }
    }
    return $out;
};

$pool = [];
foreach ($rfids as $entry) {
    $entryAllowLoose = is_scalar($entry);
    foreach ($collect($entry, null, $entryAllowLoose) as $tag) {
        $pool[] = $tag;
    }
}

$seen = [];
$assigned = 0;
$ignoredDuplicates = 0;
$ignoredAlreadyAssigned = 0;
$failed = 0;
$firstError = null;

foreach ($pool as $rfid) {
    if (isset($seen[$rfid])) {
        $ignoredDuplicates++;
        continue;
    }
    $seen[$rfid] = true;

    try {
        $objExists->execute([$rfid]);
        if ($objExists->fetch(PDO::FETCH_ASSOC)) {
            $ignoredAlreadyAssigned++;
            continue;
        }

        $objInventoryId = null;
        if ($nextObjIdStmt !== null) {
            $nextObjIdStmt->execute();
            $objInventoryId = (int) ($nextObjIdStmt->fetchColumn() ?: 0);
            if ($objInventoryId < 1) {
                $objInventoryId = 1;
            }
        }

        $objValues = $includeInventoryIdOnInsert
            ? [($objInventoryId ?? 0), $inventoryId, $rfid]
            : [$inventoryId, $rfid];
        if (in_array('name', $objInsertCols, true)) {
            $nameBase = trim($inventoryName) !== '' ? $inventoryName : ('inventory_' . $inventoryId);
            $rfidSlug = preg_replace('/[^a-z0-9]+/i', '', $rfid) ?: 'tag';
            $objValues[] = $nameBase . '_' . substr($rfidSlug, 0, 24);
        }
        if (in_array('label', $objInsertCols, true)) {
            $objValues[] = (trim($inventoryLabel) !== '' ? $inventoryLabel : $inventoryName);
        }
        $objInsert->execute($objValues);
        $assigned++;
    } catch (Throwable $e) {
        $failed++;
        if ($firstError === null) {
            $firstError = $e->getMessage();
        }
        continue;
    }
}

if ($failed > 0 && $assigned === 0) {
    api_json(409, [
        'ok' => false,
        'error' => 'Failed to insert tags into inventory_objects',
        'detail' => $firstError,
        'assigned' => $assigned,
        'failed' => $failed,
    ]);
}

api_json(200, [
    'ok' => true,
    'inventory_id' => $inventoryId,
    'assigned' => $assigned,
    'ignored_duplicates' => $ignoredDuplicates,
    'ignored_already_assigned' => $ignoredAlreadyAssigned,
    'failed' => $failed,
    'first_error' => $firstError,
    'target_table' => 'inventory_objects',
    'writes_user_inventory' => false,
]);
