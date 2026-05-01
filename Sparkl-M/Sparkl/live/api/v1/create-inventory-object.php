<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

api_require_method('POST');

global $USR;
$auth = api_require_bearer($USR);

if (($auth['type'] ?? '') !== 'operator' && ($auth['type'] ?? '') !== 'admin') {
    api_json(403, ['ok' => false, 'error' => 'Only operator/admin can create inventory objects']);
}

$body = api_read_json();
$nameRaw = trim((string) ($body['name'] ?? ''));
$labelRaw = trim((string) ($body['label'] ?? ''));
$categoryGroupRaw = strtoupper(trim((string) ($body['category_group'] ?? '')));

if ($nameRaw === '') {
    api_json(400, ['ok' => false, 'error' => 'name is required']);
}

if (strlen($nameRaw) > 128 || strlen($labelRaw) > 255 || strlen($categoryGroupRaw) > 32) {
    api_json(400, ['ok' => false, 'error' => 'name/label too long']);
}

$normalized = strtolower($nameRaw);
$slug = preg_replace('/[^a-z0-9]+/', '_', $normalized);
$slug = trim((string) $slug, '_');
if ($slug === '') {
    api_json(400, ['ok' => false, 'error' => 'name must include letters or numbers']);
}

$label = $labelRaw !== '' ? $labelRaw : ucwords(str_replace('_', ' ', $slug));

$pdo = $USR->getDb();
$groupPrefixes = [
    'TRAYS' => 'tray',
    'CUPS' => 'cup',
    'PLATES' => 'plate',
    'CLAMSHELLS' => 'clamshell',
    'UTENSILS' => 'utensil',
    'MISC' => 'misc',
];
if ($categoryGroupRaw !== '' && !isset($groupPrefixes[$categoryGroupRaw])) {
    api_json(400, ['ok' => false, 'error' => 'Invalid category_group']);
}
if ($categoryGroupRaw !== '') {
    $prefix = $groupPrefixes[$categoryGroupRaw];
    if (strpos($slug, $prefix) === false) {
        $slug = $prefix . '_' . $slug;
    }
}

$baseSlug = $slug;
$slugSuffix = 1;
$chk = $pdo->prepare('SELECT `id` FROM `inventory` WHERE `name` = ? LIMIT 1');
while (true) {
    $chk->execute([$slug]);
    if (!$chk->fetch(PDO::FETCH_ASSOC)) {
        break;
    }
    $slugSuffix++;
    $slug = $baseSlug . '_' . $slugSuffix;
}

$ins = $pdo->prepare('INSERT INTO `inventory` (`name`, `label`) VALUES (?, ?)');
$ins->execute([$slug, $label]);
$newId = (int) $pdo->lastInsertId();

api_json(201, [
    'ok' => true,
    'id' => $newId,
    'created' => true,
    'item' => [
        'id' => $newId,
        'name' => $slug,
        'label' => $label,
        'category_group' => $categoryGroupRaw !== '' ? $categoryGroupRaw : null,
    ],
]);
