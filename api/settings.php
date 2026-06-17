<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../config/db.php';

$db = getDB();
$stmt = $db->query('SELECT setting_key, setting_value FROM site_settings');
$rows = $stmt->fetchAll();

$settings = [];
foreach ($rows as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

echo json_encode(['success' => true, 'data' => $settings]);
