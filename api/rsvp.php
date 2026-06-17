<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success' => false, 'error' => 'Method not allowed']); exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/mailer.php';

$data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$eventId = (int)($data['event_id'] ?? 0);
$name    = trim($data['name']    ?? '');
$phone   = trim($data['phone']   ?? '');
$email   = trim($data['email']   ?? '');
$count   = max(1, (int)($data['attendee_count'] ?? 1));

if (!$eventId || !$name || !$phone) {
    echo json_encode(['success' => false, 'error' => 'event_id, name, and phone are required']); exit;
}

$db = getDB();

// Check event exists and RSVP is enabled
$eStmt = $db->prepare('SELECT id, title_en, rsvp_enabled, max_attendees FROM events WHERE id = ?');
$eStmt->execute([$eventId]);
$event = $eStmt->fetch();
if (!$event || !$event['rsvp_enabled']) {
    echo json_encode(['success' => false, 'error' => 'RSVP is not available for this event']); exit;
}

// Check max attendees
if ($event['max_attendees']) {
    $rStmt = $db->prepare('SELECT COALESCE(SUM(attendee_count),0) FROM rsvp WHERE event_id = ?');
    $rStmt->execute([$eventId]);
    $current = (int)$rStmt->fetchColumn();
    if ($current + $count > $event['max_attendees']) {
        echo json_encode(['success' => false, 'error' => 'Sorry, seats are full for this event']); exit;
    }
}

// Prevent duplicate RSVP for same phone + event
$dStmt = $db->prepare('SELECT id FROM rsvp WHERE event_id = ? AND phone = ?');
$dStmt->execute([$eventId, $phone]);
if ($dStmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'You have already registered for this event']); exit;
}

$ins = $db->prepare('INSERT INTO rsvp (event_id, name, email, phone, attendee_count) VALUES (?,?,?,?,?)');
$ins->execute([$eventId, $name, $email, $phone, $count]);

notify_rsvp(['name' => $name, 'email' => $email, 'phone' => $phone, 'attendee_count' => $count], $event['title_en']);

echo json_encode(['success' => true, 'message' => 'RSVP registered successfully! We look forward to seeing you.']);
