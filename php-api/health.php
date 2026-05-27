<?php
require __DIR__ . '/_bootstrap.php';

try {
    $stmt = db()->query('SELECT NOW() AS now_time');
    $row = $stmt->fetch();
    json_response([
        'ok' => true,
        'service' => 'F5 TV PHP API',
        'database' => 'online',
        'time' => $row['now_time'] ?? null,
    ]);
} catch (Throwable $e) {
    json_response([
        'ok' => false,
        'service' => 'F5 TV PHP API',
        'database' => 'offline',
        'message' => $e->getMessage(),
    ], 503);
}
