<?php
ob_start();
register_shutdown_function(function() {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'PHP Fatal: ' . $e['message'] . ' (line ' . $e['line'] . ')']);
    }
});
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

$fullName   = trim($_POST['full_name']   ?? '');
$fatherName = trim($_POST['father_name'] ?? '');
$age        = (int)($_POST['age']        ?? 0);
$address    = trim($_POST['address']     ?? '');
$phone      = trim($_POST['phone']       ?? '');
$email      = trim($_POST['email']       ?? '');
$education  = trim($_POST['education']   ?? '');
$occupation = trim($_POST['occupation']  ?? '');
$referral   = trim($_POST['referral']    ?? '');

if (!$fullName || !$fatherName || !$age || !$address || !$phone) {
    echo json_encode(['success' => false, 'error' => 'Full name, father name, age, address, and phone are required']); exit;
}
if ($age < 15 || $age > 40) {
    echo json_encode(['success' => false, 'error' => 'Age must be between 15 and 40']); exit;
}
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address']); exit;
}

// Photo upload
$photoPath = null;
if (!empty($_FILES['photo']['tmp_name'])) {
    $file = $_FILES['photo'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowed)) {
        echo json_encode(['success' => false, 'error' => 'Only JPG, PNG, WEBP photos allowed']); exit;
    }
    if ($file['size'] > $maxSize) {
        echo json_encode(['success' => false, 'error' => 'Photo must be under 5MB']); exit;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'app_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
    $dest = __DIR__ . '/../assets/uploads/members/' . $filename;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        $photoPath = 'assets/uploads/members/' . $filename;
    }
}

try {
    $db = getDB();
    $ins = $db->prepare('INSERT INTO applications (full_name, father_name, age, address, phone, email, education, occupation, referral, photo) VALUES (?,?,?,?,?,?,?,?,?,?)');
    $ins->execute([$fullName, $fatherName, $age, $address, $phone, $email, $education, $occupation, $referral, $photoPath]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]); exit;
}

try { notify_application(['full_name' => $fullName, 'age' => $age, 'phone' => $phone, 'email' => $email, 'education' => $education, 'occupation' => $occupation, 'referral' => $referral]); } catch (\Throwable $e) { error_log('Notify error: ' . $e->getMessage()); }

echo json_encode(['success' => true, 'message' => 'Your application has been submitted! We will review it and contact you soon.']);
