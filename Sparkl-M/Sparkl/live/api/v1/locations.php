<?php

declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

api_require_method('GET');

global $USR;
api_require_bearer($USR);

$pdo = $USR->getDb();
$stmt = $pdo->query('SELECT `id`, `name` FROM `locations` ORDER BY `sort_order` ASC, `name` ASC');
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

api_json(200, ['ok' => true, 'items' => $items]);
