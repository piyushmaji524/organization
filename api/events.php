<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../config/db.php';

$db = getDB();

// Single event + its gallery
if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    if (!$event) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Event not found']);
        exit;
    }
    // Gallery for this event
    $gStmt = $db->prepare('SELECT * FROM gallery WHERE event_id = ? ORDER BY uploaded_at DESC');
    $gStmt->execute([$id]);
    $event['gallery'] = $gStmt->fetchAll();

    // RSVP count
    $rStmt = $db->prepare('SELECT SUM(attendee_count) as total FROM rsvp WHERE event_id = ?');
    $rStmt->execute([$id]);
    $event['rsvp_count'] = (int)($rStmt->fetchColumn() ?? 0);

    echo json_encode(['success' => true, 'data' => $event]);
    exit;
}

$filter = $_GET['filter'] ?? 'all'; // upcoming | past | all
$type   = $_GET['type']   ?? '';
$year   = $_GET['year']   ?? '';
$limit  = min((int)($_GET['limit'] ?? 100), 100);

$where  = [];
$params = [];

if ($filter === 'upcoming') {
    $where[] = "status IN ('upcoming','ongoing') AND event_date >= CURDATE()";
} elseif ($filter === 'past') {
    $where[] = "status = 'completed' OR event_date < CURDATE()";
}
if ($type) {
    $where[] = 'type = ?';
    $params[] = $type;
}
if ($year) {
    $where[] = 'YEAR(event_date) = ?';
    $params[] = (int)$year;
}

$sql = 'SELECT * FROM events';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY event_date ' . ($filter === 'past' ? 'DESC' : 'ASC');
$sql .= ' LIMIT ' . $limit;

$stmt = $db->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

echo json_encode(['success' => true, 'data' => $events]);
