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

$data    = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$name    = trim($data['name']    ?? '');
$email   = trim($data['email']   ?? '');
$phone   = trim($data['phone']   ?? '');
$subject = trim($data['subject'] ?? '');
$message = trim($data['message'] ?? '');

if (!$name || !$email || !$message) {
    echo json_encode(['success' => false, 'error' => 'Name, email, and message are required']); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address']); exit;
}

$db = getDB();
$ins = $db->prepare('INSERT INTO messages (name, email, phone, subject, message) VALUES (?,?,?,?,?)');
$ins->execute([$name, $email, $phone, $subject, $message]);

notify_contact(['name' => $name, 'email' => $email, 'phone' => $phone, 'subject' => $subject, 'message' => $message]);

echo json_encode(['success' => true, 'message' => 'Your message has been sent. We will get back to you soon!']);
