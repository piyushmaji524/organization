<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('news', 'view');

$pageTitle     = 'News Manager';
$activeSection = 'news';
$db = getDB();
$msg = ''; $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && can('news','edit')) {
    $id = (int)($_POST['id'] ?? 0);
    $vals = [
        'title_en'   => trim($_POST['title_en']   ?? ''),
        'title_hi'   => trim($_POST['title_hi']   ?? ''),
        'title_bn'   => trim($_POST['title_bn']   ?? ''),
        'content_en' => trim($_POST['content_en'] ?? ''),
        'content_hi' => trim($_POST['content_hi'] ?? ''),
        'content_bn' => trim($_POST['content_bn'] ?? ''),
        'category'   => trim($_POST['category']   ?? 'general'),
        'is_alert'   => isset($_POST['is_alert']) ? 1 : 0,
    ];

    $coverPath = $_POST['existing_cover'] ?? null;
    if (!empty($_FILES['cover_image']['tmp_name'])) {
        $file = $_FILES['cover_image'];
        if (in_array($file['type'], ['image/jpeg','image/png','image/webp']) && $file['size'] <= 5*1024*1024) {
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fn   = 'news_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dest = __DIR__ . '/../assets/uploads/news/' . $fn;
            if (move_uploaded_file($file['tmp_name'], $dest)) $coverPath = 'assets/uploads/news/' . $fn;
        } else $err = 'Cover image must be JPG/PNG/WEBP under 5MB.';
    }

    if (!$err) {
        // Only one alert at a time
        if ($vals['is_alert']) $db->exec('UPDATE news SET is_alert=0');
        $cols = implode(', ', array_keys($vals));
        $phs  = implode(', ', array_fill(0, count($vals), '?'));
        if ($id) {
            $set = implode(', ', array_map(fn($f) => "$f = ?", array_keys($vals)));
            $db->prepare("UPDATE news SET $set, cover_image=? WHERE id=?")->execute([...array_values($vals), $coverPath, $id]);
            $msg = 'Article updated.';
        } else {
            $db->prepare("INSERT INTO news ($cols, cover_image) VALUES ($phs, ?)")->execute([...array_values($vals), $coverPath]);
            $msg = 'Article published.';
        }
    }
}

if (!empty($_GET['delete']) && can('news','delete')) {
    $db->prepare('DELETE FROM news WHERE id=?')->execute([(int)$_GET['delete']]);
    header('Location: /admin/news.php?deleted=1'); exit;
}
if (!empty($_GET['deleted'])) $msg = 'Article deleted.';

$editing = null;
if (!empty($_GET['edit'])) {
    $s = $db->prepare('SELECT * FROM news WHERE id=?'); $s->execute([(int)$_GET['edit']]); $editing = $s->fetch();
}

$articles = $db->query('SELECT id, title_en, category, is_alert, cover_image, published_at FROM news ORDER BY published_at DESC')->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success" data-auto-dismiss="3000">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger">⚠ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="section-header">
  <h2>News & Announcements</h2>
  <?php if (can('news','edit')): ?><a href="/admin/news.php" class="btn btn-primary">+ Add Article</a><?php endif; ?>
</div>

<?php if (can('news','edit')): ?>
<div class="card" style="margin-bottom:24px">
  <div class="card-header"><span class="card-title"><?= $editing ? 'Edit Article' : 'New Article' ?></span></div>
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>">
        <input type="hidden" name="existing_cover" value="<?= htmlspecialchars($editing['cover_image']??'') ?>"><?php endif; ?>

      <div style="margin-bottom:16px">
        <div class="lang-tabs" data-group="news">
          <button type="button" class="lang-tab active" data-lang="en" data-group="news">EN</button>
          <button type="button" class="lang-tab" data-lang="hi" data-group="news">HI</button>
          <button type="button" class="lang-tab" data-lang="bn" data-group="news">BN</button>
        </div>
        <?php foreach(['en'=>'English','hi'=>'Hindi','bn'=>'Bengali'] as $lc=>$ln): ?>
        <div class="lang-panel <?=$lc==='en'?'active':''?>" data-lang="<?=$lc?>" data-group="news">
          <div class="form-group" style="margin-bottom:12px">
            <label>Title (<?=$ln?>)</label>
            <input type="text" name="title_<?=$lc?>" value="<?= htmlspecialchars($editing["title_$lc"]??'') ?>" <?=$lc==='en'?'required':''?>>
          </div>
          <div class="form-group">
            <label>Content (<?=$ln?>)</label>
            <textarea name="content_<?=$lc?>" rows="8"><?= htmlspecialchars($editing["content_$lc"]??'') ?></textarea>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="form-grid">
        <div class="form-group">
          <label>Category</label>
          <select name="category">
            <?php foreach(['general','announcement','achievement','event','news'] as $c): ?>
              <option value="<?=$c?>" <?= ($editing['category']??'general')===$c?'selected':'' ?>><?= ucfirst($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Cover Image</label>
          <?php if(!empty($editing['cover_image'])): ?><img src="/<?= htmlspecialchars($editing['cover_image']) ?>" id="news-prev" class="img-preview-lg" style="display:block;margin-bottom:8px">
          <?php else: ?><img src="" id="news-prev" class="img-preview-lg" style="display:none;margin-bottom:8px"><?php endif; ?>
          <input type="file" name="cover_image" accept="image/*" data-preview="news-prev">
        </div>
        <div class="form-group" style="align-self:flex-end">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;background:var(--cream);padding:12px;border-radius:8px;border:2px solid var(--gold)">
            <input type="checkbox" name="is_alert" value="1" <?= ($editing['is_alert']??0)?'checked':'' ?>>
            <span><strong>🔔 Show as Home Page Alert Banner</strong><br><small>Only one article can be alert at a time</small></span>
          </label>
        </div>
      </div>
      <div style="margin-top:16px;display:flex;gap:10px">
        <button type="submit" class="btn btn-primary"><?= $editing ? 'Update Article' : 'Publish Article' ?></button>
        <?php if ($editing): ?><a href="/admin/news.php" class="btn btn-outline">Cancel</a><?php endif; ?>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Cover</th><th>Title</th><th>Category</th><th>Alert</th><th>Published</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($articles as $a): ?>
        <tr>
          <td><?php if($a['cover_image']): ?><img src="/<?= htmlspecialchars($a['cover_image']) ?>" style="width:60px;height:40px;object-fit:cover;border-radius:6px"><?php else: ?><div style="width:60px;height:40px;background:var(--gray-200);border-radius:6px;display:flex;align-items:center;justify-content:center">📰</div><?php endif; ?></td>
          <td><?= htmlspecialchars($a['title_en']) ?></td>
          <td><?= ucfirst($a['category']) ?></td>
          <td><?= $a['is_alert'] ? '🔔 <strong>Active</strong>' : '—' ?></td>
          <td><?= date('d M Y', strtotime($a['published_at'])) ?></td>
          <td><div class="td-actions">
            <?php if(can('news','edit')): ?><a href="/admin/news.php?edit=<?=$a['id']?>" class="btn btn-sm btn-outline">✏️</a><?php endif; ?>
            <?php if(can('news','delete')): ?><a href="/admin/news.php?delete=<?=$a['id']?>" class="btn btn-sm btn-danger" data-confirm="Delete this article?">🗑</a><?php endif; ?>
          </div></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
