<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../config/db.php';

$db = getDB();

// Single article
if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare('SELECT * FROM news WHERE id = ?');
    $stmt->execute([$id]);
    $article = $stmt->fetch();
    if (!$article) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Article not found']);
        exit;
    }
    echo json_encode(['success' => true, 'data' => $article]);
    exit;
}

// Alert banner (latest active alert)
if (!empty($_GET['alert'])) {
    $stmt = $db->prepare('SELECT * FROM news WHERE is_alert = 1 ORDER BY published_at DESC LIMIT 1');
    $stmt->execute();
    $alert = $stmt->fetch();
    echo json_encode(['success' => true, 'data' => $alert ?: null]);
    exit;
}

$category = $_GET['category'] ?? '';
$limit    = min((int)($_GET['limit'] ?? 50), 50);
$offset   = max(0, (int)($_GET['offset'] ?? 0));

$where  = [];
$params = [];
if ($category) {
    $where[] = 'category = ?';
    $params[] = $category;
}

$sql = 'SELECT id, title_en, title_hi, title_bn, category, cover_image, is_alert, published_at FROM news';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY published_at DESC LIMIT ? OFFSET ?';
$params[] = $limit;
$params[] = $offset;

$stmt = $db->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Total count
$countSql = 'SELECT COUNT(*) FROM news' . ($where ? ' WHERE ' . implode(' AND ', array_slice($where, 0)) : '');
$countStmt = $db->prepare($countSql);
$countStmt->execute(array_slice($params, 0, -2));
$total = (int)$countStmt->fetchColumn();

echo json_encode(['success' => true, 'data' => $articles, 'total' => $total]);
