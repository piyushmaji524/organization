<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../config/db.php';
$db = getDB();

// Categories list
if (!empty($_GET['categories'])) {
    echo json_encode(['success'=>true,'data'=>$db->query('SELECT * FROM blog_categories ORDER BY name')->fetchAll()]);
    exit;
}

// Tags list
if (!empty($_GET['tags'])) {
    echo json_encode(['success'=>true,'data'=>$db->query('SELECT * FROM blog_tags ORDER BY name')->fetchAll()]);
    exit;
}

// Single post
if (!empty($_GET['slug']) || !empty($_GET['id'])) {
    $col   = !empty($_GET['slug']) ? 'p.slug' : 'p.id';
    $val   = !empty($_GET['slug']) ? $_GET['slug'] : (int)$_GET['id'];
    $stmt  = $db->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug, c.color AS category_color
        FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id=c.id
        WHERE $col=? AND p.status='published'");
    $stmt->execute([$val]);
    $post = $stmt->fetch();
    if (!$post) { http_response_code(404); echo json_encode(['success'=>false,'error'=>'Not found']); exit; }

    $db->prepare('UPDATE blog_posts SET views=views+1 WHERE id=?')->execute([$post['id']]);

    $ts = $db->prepare('SELECT t.name,t.slug FROM blog_tags t JOIN blog_post_tags pt ON t.id=pt.tag_id WHERE pt.post_id=?');
    $ts->execute([$post['id']]); $post['tags'] = $ts->fetchAll();

    $prev = $db->prepare("SELECT id,slug,title_en,title_hi,title_bn,thumbnail FROM blog_posts WHERE status='published' AND published_at<? ORDER BY published_at DESC LIMIT 1");
    $prev->execute([$post['published_at']]); $post['prev'] = $prev->fetch() ?: null;

    $next = $db->prepare("SELECT id,slug,title_en,title_hi,title_bn,thumbnail FROM blog_posts WHERE status='published' AND published_at>? ORDER BY published_at ASC LIMIT 1");
    $next->execute([$post['published_at']]); $post['next'] = $next->fetch() ?: null;

    echo json_encode(['success'=>true,'data'=>$post]); exit;
}

// Related posts
if (!empty($_GET['related'])) {
    $catId   = (int)$_GET['related'];
    $exclude = (int)($_GET['exclude'] ?? 0);
    $stmt = $db->prepare("SELECT p.id,p.slug,p.title_en,p.title_hi,p.title_bn,p.thumbnail,p.published_at,p.author_name,
        c.name AS category_name,c.color AS category_color
        FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id=c.id
        WHERE p.status='published' AND p.id!=? AND p.category_id=? ORDER BY p.published_at DESC LIMIT 3");
    $stmt->execute([$exclude,$catId]); $related = $stmt->fetchAll();
    if (count($related) < 3) {
        $ids = array_merge(array_column($related,'id'), [$exclude]);
        $ph  = implode(',', array_fill(0,count($ids),'?'));
        $more = $db->prepare("SELECT p.id,p.slug,p.title_en,p.title_hi,p.title_bn,p.thumbnail,p.published_at,p.author_name,
            c.name AS category_name,c.color AS category_color
            FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id=c.id
            WHERE p.status='published' AND p.id NOT IN ($ph) ORDER BY p.published_at DESC LIMIT ".(3-count($related)));
        $more->execute($ids); $related = array_merge($related,$more->fetchAll());
    }
    echo json_encode(['success'=>true,'data'=>$related]); exit;
}

// List (paginated)
$page    = max(1,(int)($_GET['page']??1));
$perPage = 10;
$offset  = ($page-1)*$perPage;
$where   = ["p.status='published'"]; $params = [];

if (!empty($_GET['category'])) { $where[] = 'c.slug=?'; $params[] = $_GET['category']; }

$whereStr = 'WHERE '.implode(' AND ',$where);

if (!empty($_GET['tag'])) {
    $tRow = $db->prepare('SELECT id FROM blog_tags WHERE slug=?');
    $tRow->execute([$_GET['tag']]);
    $tid = $tRow->fetchColumn();
    if ($tid) { $whereStr .= ' AND p.id IN (SELECT post_id FROM blog_post_tags WHERE tag_id=?)'; $params[] = $tid; }
}

$total = $db->prepare("SELECT COUNT(*) FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id=c.id $whereStr");
$total->execute($params); $totalCount = (int)$total->fetchColumn();

$params[] = $perPage; $params[] = $offset;
$stmt = $db->prepare("SELECT p.id,p.slug,p.title_en,p.title_hi,p.title_bn,
    p.excerpt_en,p.excerpt_hi,p.excerpt_bn,p.thumbnail,p.author_name,p.views,p.published_at,
    c.id AS category_id,c.name AS category_name,c.slug AS category_slug,c.color AS category_color
    FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id=c.id
    $whereStr ORDER BY p.published_at DESC LIMIT ? OFFSET ?");
$stmt->execute($params); $posts = $stmt->fetchAll();

foreach ($posts as &$p) {
    $ts = $db->prepare('SELECT t.name,t.slug FROM blog_tags t JOIN blog_post_tags pt ON t.id=pt.tag_id WHERE pt.post_id=?');
    $ts->execute([$p['id']]); $p['tags'] = $ts->fetchAll();
}

echo json_encode(['success'=>true,'data'=>$posts,'meta'=>[
    'total'=>$totalCount,'page'=>$page,'per_page'=>$perPage,'pages'=>(int)ceil($totalCount/$perPage)
]]);
