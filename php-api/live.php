<?php
require __DIR__ . '/_bootstrap.php';

try {
    $pdo = db();
    $action = $_GET['action'] ?? 'channels';

    if ($action === 'schedule') {
        $where = [];
        $params = [];

        if (!empty($_GET['date'])) {
            $where[] = 'ls.date = ?';
            $params[] = $_GET['date'];
        }
        if (!empty($_GET['channel_id'])) {
            $where[] = 'ls.channel_id = ?';
            $params[] = $_GET['channel_id'];
        }

        $sql = "SELECT ls.*, ch.name AS channel_name FROM live_schedules ls JOIN channels ch ON ch.id = ls.channel_id";
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY ls.date ASC, ls.start_time ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        json_response(['schedule' => $stmt->fetchAll()]);
    }

    $stmt = $pdo->query("SELECT id, name, description, logo_text, stream_url, active, status, category FROM channels WHERE active = 1 ORDER BY name ASC");
    json_response(['channels' => $stmt->fetchAll()]);
} catch (Throwable $e) {
    json_response(['error' => 'SERVER_ERROR', 'message' => $e->getMessage()], 500);
}
