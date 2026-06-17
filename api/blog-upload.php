<?php
session_start();
header('Content-Type: application/json');
if (empty($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
if (empty($_FILES['image']['tmp_name'])) {
    echo json_encode(['success' => false, 'error' => 'No image uploaded']);
    exit;
}
$file    = $_FILES['image'];
$allowed = ['image/jpeg','image/png','image/webp','image/gif'];
if (!in_array($file['type'], $allowed) || $file['size'] > 5*1024*1024) {
    echo json_encode(['success' => false, 'error' => 'Image must be JPG/PNG/WEBP/GIF under 5MB']);
    exit;
}
$dir = __DIR__ . '/../assets/uploads/blog/';
if (!is_dir($dir)) mkdir($dir, 0755, true);
$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$fn   = 'blog_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
if (move_uploaded_file($file['tmp_name'], $dir . $fn)) {
    echo json_encode(['success' => true, 'url' => '/assets/uploads/blog/' . $fn]);
} else {
    echo json_encode(['success' => false, 'error' => 'Upload failed']);
}
