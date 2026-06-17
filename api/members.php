<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../config/db.php';

$db = getDB();

// Single member by ID
if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare('SELECT * FROM members WHERE id = ? AND is_active = 1');
    $stmt->execute([$id]);
    $member = $stmt->fetch();
    if (!$member) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Member not found']);
        exit;
    }
    echo json_encode(['success' => true, 'data' => $member]);
    exit;
}

// General members (approved applications shown on website)
if (!empty($_GET['type']) && $_GET['type'] === 'general') {
    $stmt = $db->query(
        "SELECT id, full_name, photo, member_id, badge_name, visible_fields, age,
                father_name, phone, email, education, occupation, address, referral
         FROM applications
         WHERE status='approved' AND show_on_website=1
         ORDER BY (badge_name IS NOT NULL AND badge_name != '') DESC, full_name ASC"
    );
    $rows = $stmt->fetchAll();
    $members = [];
    foreach ($rows as $r) {
        $visible = $r['visible_fields'] ? json_decode($r['visible_fields'], true) : [];
        $card = [
            'id'        => $r['id'],
            'full_name' => $r['full_name'],
            'photo'     => $r['photo'],
            'member_id' => $r['member_id'],
            'badge_name'=> $r['badge_name'],
        ];
        $allowed = ['father_name','age','phone','email','education','occupation','address','referral'];
        foreach ($allowed as $f) {
            if (in_array($f, $visible) && !empty($r[$f])) $card[$f] = $r[$f];
        }
        $members[] = $card;
    }
    echo json_encode(['success' => true, 'data' => $members]);
    exit;
}

// All members grouped by category
$stmt = $db->query('SELECT * FROM members WHERE is_active = 1 ORDER BY category, display_order ASC');
$all = $stmt->fetchAll();

$grouped = ['executive' => [], 'core' => [], 'advisory' => []];
foreach ($all as $m) {
    $grouped[$m['category']][] = $m;
}

echo json_encode(['success' => true, 'data' => $grouped]);
