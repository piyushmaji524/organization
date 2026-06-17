<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../config/db.php';

$db = getDB();

$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : null;
$year    = isset($_GET['year'])     ? (int)$_GET['year']     : null;
$isVideo = isset($_GET['video'])    ? (int)$_GET['video']    : null;

$where  = [];
$params = [];

if ($eventId !== null) { $where[] = 'event_id = ?';  $params[] = $eventId; }
if ($year    !== null) { $where[] = 'year = ?';       $params[] = $year; }
if ($isVideo !== null) { $where[] = 'is_video = ?';   $params[] = $isVideo; }

$sql = 'SELECT g.*, e.title_en AS event_title_en FROM gallery g LEFT JOIN events e ON g.event_id = e.id';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY g.uploaded_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

// Get distinct years for filter
$years = $db->query('SELECT DISTINCT year FROM gallery ORDER BY year DESC')->fetchAll(PDO::FETCH_COLUMN);

echo json_encode(['success' => true, 'data' => $items, 'years' => $years]);
