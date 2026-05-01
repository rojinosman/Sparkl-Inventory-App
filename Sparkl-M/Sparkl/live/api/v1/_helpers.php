<?php

declare(strict_types=1);

function api_json(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function api_read_json(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function api_require_method(string $method): void
{
    if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== strtoupper($method)) {
        api_json(405, ['ok' => false, 'error' => 'Method not allowed']);
    }
}

/** @return array{user_id:int,email:string,type:string} */
function api_require_bearer(Users $USR): array
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if (!preg_match('/Bearer\s+([a-fA-F0-9]{64})/', $header, $m)) {
        api_json(401, ['ok' => false, 'error' => 'Missing or invalid Authorization: Bearer <token>']);
    }
    $token = strtolower($m[1]);
    $pdo = $USR->getDb();
    $sql = 'SELECT u.id AS user_id, u.email, u.type
            FROM api_tokens t
            INNER JOIN users u ON u.id = t.user_id
            WHERE t.token = ?
            AND (t.expires_at IS NULL OR t.expires_at > NOW())';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        api_json(401, ['ok' => false, 'error' => 'Invalid or expired token']);
    }
    return [
        'user_id' => (int) $row['user_id'],
        'email' => (string) $row['email'],
        'type' => (string) $row['type'],
    ];
}
