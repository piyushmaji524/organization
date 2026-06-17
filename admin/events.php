<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('events', 'view');

$pageTitle     = 'Events Manager';
$activeSection = 'events';
$db = getDB();
$msg = ''; $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && can('events','edit')) {
    $id = (int)($_POST['id'] ?? 0);
    $fields = ['title_en','title_hi','title_bn','description_en','description_hi','description_bn',
               'event_date','event_time','location_en','location_hi','location_bn','type','status'];
    $vals = [];
    foreach ($fields as $f) $vals[$f] = trim($_POST[$f] ?? '');
    $vals['rsvp_enabled']  = isset($_POST['rsvp_enabled'])  ? 1 : 0;
    $vals['max_attendees'] = $_POST['max_attendees'] ? (int)$_POST['max_attendees'] : null;

    $coverPath = $_POST['existing_cover'] ?? null;
    if (!empty($_FILES['cover_image']['tmp_name'])) {
        $file = $_FILES['cover_image'];
        if (in_array($file['type'], ['image/jpeg','image/png','image/webp']) && $file['size'] <= 5*1024*1024) {
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fn   = 'event_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dest = __DIR__ . '/../assets/uploads/events/' . $fn;
            if (move_uploaded_file($file['tmp_name'], $dest)) $coverPath = 'assets/uploads/events/' . $fn;
        } else { $err = 'Cover image must be JPG/PNG/WEBP under 5MB.'; }
    }

    if (!$err) {
        $allCols = array_merge($fields, ['rsvp_enabled','max_attendees']);
        $allVals = array_values($vals);
        if ($id) {
            $set = implode(', ', array_map(fn($f) => "$f = ?", $allCols)) . ', cover_image = ?';
            $db->prepare("UPDATE events SET $set WHERE id = ?")->execute(array_merge($allVals, [$coverPath, $id]));
            $msg = 'Event updated.';
        } else {
            $cols = implode(', ', $allCols) . ', cover_image';
            $phs  = implode(', ', array_fill(0, count($allCols)+1, '?'));
            $db->prepare("INSERT INTO events ($cols) VALUES ($phs)")->execute(array_merge($allVals, [$coverPath]));
            $msg = 'Event created.';
        }
    }
}

if (!empty($_GET['delete']) && can('events','delete')) {
    $db->prepare('DELETE FROM events WHERE id = ?')->execute([(int)$_GET['delete']]);
    header('Location: /admin/events.php?deleted=1'); exit;
}
if (!empty($_GET['deleted'])) $msg = 'Event deleted.';

$editing = null;
if (!empty($_GET['edit'])) {
    $s = $db->prepare('SELECT * FROM events WHERE id = ?'); $s->execute([(int)$_GET['edit']]); $editing = $s->fetch();
}

try {
    $events = $db->query('SELECT e.*, (SELECT COUNT(*) FROM rsvp r WHERE r.event_id=e.id) AS rsvp_count FROM events e ORDER BY event_date DESC')->fetchAll();
} catch (PDOException $e) {
    $events = [];
    $err = 'Database error: ' . htmlspecialchars($e->getMessage());
}

require_once __DIR__ . '/includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success" data-auto-dismiss="3000">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger">⚠ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="section-header">
  <h2>Events Manager</h2>
  <?php if (can('events','edit')): ?><a href="/admin/events.php" class="btn btn-primary">+ Add Event</a><?php endif; ?>
</div>

<?php if (can('events','edit')): ?>
<div class="card" style="margin-bottom:24px">
  <div class="card-header"><span class="card-title"><?= $editing ? 'Edit Event' : 'Add New Event' ?></span></div>
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>">
        <input type="hidden" name="existing_cover" value="<?= htmlspecialchars($editing['cover_image'] ?? '') ?>">
      <?php endif; ?>

      <div style="margin-bottom:16px">
        <div class="lang-tabs" data-group="evt">
          <button type="button" class="lang-tab active" data-lang="en" data-group="evt">EN</button>
          <button type="button" class="lang-tab" data-lang="hi" data-group="evt">HI</button>
          <button type="button" class="lang-tab" data-lang="bn" data-group="evt">BN</button>
        </div>
        <?php foreach(['en'=>'English','hi'=>'Hindi','bn'=>'Bengali'] as $lc=>$ln): ?>
        <div class="lang-panel <?=$lc==='en'?'active':''?>" data-lang="<?=$lc?>" data-group="evt">
          <div class="form-grid">
            <div class="form-group full">
              <label>Title (<?=$ln?>)</label>
              <input type="text" name="title_<?=$lc?>" value="<?= htmlspecialchars($editing["title_$lc"]??'') ?>" <?=$lc==='en'?'required':''?>>
            </div>
            <div class="form-group full">
              <label>Description (<?=$ln?>)</label>
              <textarea name="description_<?=$lc?>" rows="4"><?= htmlspecialchars($editing["description_$lc"]??'') ?></textarea>
            </div>
            <div class="form-group full">
              <label>Location (<?=$ln?>)</label>
              <input type="text" name="location_<?=$lc?>" value="<?= htmlspecialchars($editing["location_$lc"]??'') ?>">
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="form-grid">
        <div class="form-group"><label>Event Date</label><input type="date" name="event_date" value="<?= $editing['event_date']??'' ?>" required></div>
        <div class="form-group"><label>Event Time</label><input type="time" name="event_time" value="<?= $editing['event_time']??'' ?>"></div>
        <div class="form-group">
          <label>Type</label>
          <select name="type">
            <?php foreach(['religious','sports','education','business','general'] as $t): ?>
              <option value="<?=$t?>" <?= ($editing['type']??'general')===$t?'selected':'' ?>><?= ucfirst($t) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <?php foreach(['upcoming','ongoing','completed','cancelled'] as $s): ?>
              <option value="<?=$s?>" <?= ($editing['status']??'upcoming')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Max Attendees (blank = unlimited)</label>
          <input type="number" name="max_attendees" value="<?= $editing['max_attendees']??'' ?>" min="1">
        </div>
        <div class="form-group">
          <label>Cover Image</label>
          <?php if (!empty($editing['cover_image'])): ?>
            <img src="/<?= htmlspecialchars($editing['cover_image']) ?>" id="cover-preview" class="img-preview-lg" style="display:block;margin-bottom:8px">
          <?php else: ?>
            <img src="" id="cover-preview" class="img-preview-lg" style="display:none;margin-bottom:8px">
          <?php endif; ?>
          <input type="file" name="cover_image" accept="image/*" data-preview="cover-preview">
        </div>
        <div class="form-group" style="align-self:flex-end">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="rsvp_enabled" value="1" <?= ($editing['rsvp_enabled']??1)?'checked':'' ?>>
            Enable RSVP for this event
          </label>
        </div>
      </div>
      <div style="margin-top:16px;display:flex;gap:10px">
        <button type="submit" class="btn btn-primary"><?= $editing ? 'Update Event' : 'Create Event' ?></button>
        <?php if ($editing): ?><a href="/admin/events.php" class="btn btn-outline">Cancel</a><?php endif; ?>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header"><span class="card-title">All Events (<?= count($events) ?>)</span></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Cover</th><th>Title</th><th>Date</th><th>Type</th><th>Status</th><th>RSVP</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($events as $ev): ?>
        <tr>
          <td><?php if($ev['cover_image']): ?><img src="/<?= htmlspecialchars($ev['cover_image']) ?>" style="width:60px;height:40px;object-fit:cover;border-radius:6px"><?php else: ?><div style="width:60px;height:40px;background:var(--gray-200);border-radius:6px;display:flex;align-items:center;justify-content:center">📅</div><?php endif; ?></td>
          <td><strong><?= htmlspecialchars($ev['title_en']) ?></strong></td>
          <td><?= date('d M Y', strtotime($ev['event_date'])) ?></td>
          <td><?= ucfirst($ev['type']) ?></td>
          <td><span class="status-badge status-<?= $ev['status'] ?>"><?= ucfirst($ev['status']) ?></span></td>
          <td><?= $ev['rsvp_enabled'] ? '<span style="color:var(--success)">✅ ' . $ev['rsvp_count'] . ' RSVPs</span>' : '<span style="color:var(--gray-400)">Off</span>' ?></td>
          <td>
            <div class="td-actions">
              <?php if(can('events','edit')): ?><a href="/admin/events.php?edit=<?=$ev['id']?>" class="btn btn-sm btn-outline">✏️</a><?php endif; ?>
              <?php if(can('events','delete')): ?><a href="/admin/events.php?delete=<?=$ev['id']?>" class="btn btn-sm btn-danger" data-confirm="Delete this event?">🗑</a><?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
