<?php
require __DIR__ . '/_bootstrap.php';

try {
    $pdo = db();
    $action = $_GET['action'] ?? 'contents';

    if ($action === 'categories') {
        $stmt = $pdo->query('SELECT id, name, slug FROM categories ORDER BY name ASC');
        json_response(['categories' => $stmt->fetchAll()]);
    }

    if ($action === 'content') {
        $id = $_GET['id'] ?? '';
        $stmt = $pdo->prepare("SELECT c.*, cat.name AS category_name FROM contents c LEFT JOIN categories cat ON cat.id = c.category_id WHERE c.id = ? LIMIT 1");
        $stmt->execute([$id]);
        $content = $stmt->fetch();
        if (!$content) json_response(['error' => 'CONTENT_NOT_FOUND'], 404);

        $reviews = $pdo->prepare("SELECT id, content_id, profile_name, avatar_color, rating, comment, status, created_at FROM reviews WHERE content_id = ? AND status = 'published' ORDER BY created_at DESC");
        $reviews->execute([$id]);
        json_response(['content' => $content, 'reviews' => $reviews->fetchAll()]);
    }

    if ($action === 'series') {
        $stmt = $pdo->query("SELECT id, title, description, cover_url, banner_url, genre, status, views_count FROM series ORDER BY views_count DESC");
        json_response(['series' => $stmt->fetchAll()]);
    }

    $where = ["c.status = 'published'"];
    $params = [];

    if (!empty($_GET['q'])) {
        $where[] = '(c.title LIKE ? OR c.genre LIKE ? OR c.short_description LIKE ?)';
        $q = '%' . $_GET['q'] . '%';
        $params[] = $q;
        $params[] = $q;
        $params[] = $q;
    }
    if (!empty($_GET['type'])) {
        $where[] = 'c.type = ?';
        $params[] = $_GET['type'];
    }
    if (!empty($_GET['category_id'])) {
        $where[] = 'c.category_id = ?';
        $params[] = $_GET['category_id'];
    }

    $sql = "SELECT c.*, cat.name AS category_name FROM contents c LEFT JOIN categories cat ON cat.id = c.category_id WHERE " . implode(' AND ', $where) . " ORDER BY c.is_featured DESC, c.views_count DESC, c.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    json_response(['contents' => $stmt->fetchAll()]);
} catch (Throwable $e) {
    json_response(['error' => 'SERVER_ERROR', 'message' => $e->getMessage()], 500);
}
