<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('gallery', 'view');

$pageTitle     = 'Gallery Manager';
$activeSection = 'gallery';
$db = getDB();
$msg = ''; $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && can('gallery','edit')) {
    $isVideo   = isset($_POST['is_video']) ? 1 : 0;
    $videoUrl  = trim($_POST['video_url']  ?? '');
    $eventId   = $_POST['event_id'] ? (int)$_POST['event_id'] : null;
    $year      = (int)($_POST['year'] ?? date('Y'));
    $captionEn = trim($_POST['caption_en'] ?? '');
    $captionHi = trim($_POST['caption_hi'] ?? '');
    $captionBn = trim($_POST['caption_bn'] ?? '');

    $imagePath = null;
    if (!$isVideo && !empty($_FILES['image']['tmp_name'])) {
        $file = $_FILES['image'];
        if (!in_array($file['type'], ['image/jpeg','image/png','image/webp']) || $file['size'] > 5*1024*1024) {
            $err = 'Image must be JPG/PNG/WEBP under 5MB.';
        } else {
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fn   = 'gallery_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dest = __DIR__ . '/../assets/uploads/gallery/' . $fn;
            if (move_uploaded_file($file['tmp_name'], $dest)) $imagePath = 'assets/uploads/gallery/' . $fn;
        }
    } elseif (!$isVideo) {
        $err = 'Please select an image to upload.';
    }

    if (!$err) {
        $db->prepare('INSERT INTO gallery (image_path, caption_en, caption_hi, caption_bn, event_id, year, is_video, video_url) VALUES (?,?,?,?,?,?,?,?)')
           ->execute([$imagePath, $captionEn, $captionHi, $captionBn, $eventId, $year, $isVideo, $videoUrl ?: null]);
        $msg = 'Item added to gallery.';
    }
}

if (!empty($_GET['delete']) && can('gallery','delete')) {
    $db->prepare('DELETE FROM gallery WHERE id=?')->execute([(int)$_GET['delete']]);
    header('Location: /admin/gallery.php?deleted=1'); exit;
}
if (!empty($_GET['deleted'])) $msg = 'Item deleted.';

$events  = $db->query('SELECT id, title_en FROM events ORDER BY event_date DESC')->fetchAll();
$gallery = $db->query('SELECT g.*, e.title_en AS event_name FROM gallery g LEFT JOIN events e ON g.event_id=e.id ORDER BY g.uploaded_at DESC')->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success" data-auto-dismiss="3000">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger">⚠ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="section-header"><h2>Gallery Manager</h2></div>

<?php if (can('gallery','edit')): ?>
<div class="card" style="margin-bottom:24px">
  <div class="card-header"><span class="card-title">Upload Photo / Add Video</span></div>
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <div class="form-grid">
        <div class="form-group" style="align-self:flex-end">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="is_video" id="isVideoChk" value="1" onchange="document.getElementById('imgUploadField').style.display=this.checked?'none':'block';document.getElementById('videoUrlField').style.display=this.checked?'block':'none'">
            This is a YouTube Video (not a photo)
          </label>
        </div>
        <div class="form-group" id="imgUploadField">
          <label>Photo</label>
          <img src="" id="gal-prev" class="img-preview-lg" style="display:none;margin-bottom:8px">
          <input type="file" name="image" accept="image/*" data-preview="gal-prev">
        </div>
        <div class="form-group" id="videoUrlField" style="display:none">
          <label>YouTube Video URL</label>
          <input type="url" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
        </div>
        <div class="form-group">
          <label>Caption (English)</label>
          <input type="text" name="caption_en" placeholder="Photo/video caption in English">
        </div>
        <div class="form-group">
          <label>Caption (Hindi)</label>
          <input type="text" name="caption_hi" placeholder="हिंदी में कैप्शन">
        </div>
        <div class="form-group">
          <label>Caption (Bengali)</label>
          <input type="text" name="caption_bn" placeholder="বাংলায় ক্যাপশন">
        </div>
        <div class="form-group">
          <label>Link to Event (optional)</label>
          <select name="event_id"><option value="">— Not linked to any event —</option>
            <?php foreach ($events as $ev): ?><option value="<?=$ev['id']?>"><?= htmlspecialchars($ev['title_en']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Year</label>
          <input type="number" name="year" value="<?= date('Y') ?>" min="2020" max="2099">
        </div>
      </div>
      <div style="margin-top:16px"><button type="submit" class="btn btn-primary">Upload / Add</button></div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Gallery grid -->
<div class="card">
  <div class="card-header"><span class="card-title">All Gallery Items (<?= count($gallery) ?>)</span></div>
  <div class="card-body">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px">
      <?php foreach ($gallery as $g): ?>
      <div style="border-radius:10px;overflow:hidden;border:1px solid var(--gray-200);background:var(--white)">
        <?php if ($g['is_video']): ?>
          <div style="height:140px;background:#000;display:flex;align-items:center;justify-content:center;font-size:40px">▶️</div>
        <?php elseif ($g['image_path']): ?>
          <img src="/<?= htmlspecialchars($g['image_path']) ?>" style="width:100%;height:140px;object-fit:cover">
        <?php else: ?>
          <div style="height:140px;background:var(--gray-200);display:flex;align-items:center;justify-content:center;font-size:40px">🖼️</div>
        <?php endif; ?>
        <div style="padding:10px">
          <div style="font-size:12.5px;font-weight:500;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($g['caption_en'] ?: '(no caption)') ?></div>
          <div style="font-size:11px;color:var(--gray-400);margin-bottom:8px"><?= $g['year'] ?> <?= $g['event_name'] ? '· '.htmlspecialchars($g['event_name']) : '' ?></div>
          <?php if(can('gallery','delete')): ?>
          <a href="/admin/gallery.php?delete=<?=$g['id']?>" class="btn btn-sm btn-danger" style="width:100%;justify-content:center" data-confirm="Delete this gallery item?">🗑 Delete</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if(empty($gallery)): ?><p style="color:var(--gray-400)">No gallery items yet.</p><?php endif; ?>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
