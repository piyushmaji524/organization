<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('members', 'view');

$pageTitle     = 'Members Manager';
$activeSection = 'members';
$db = getDB();
$msg = '';
$err = '';

// ── Save (add / edit) ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && can('members','edit')) {
    $id = (int)($_POST['id'] ?? 0);

    $fields = [
        'name_en','name_hi','name_bn',
        'designation_en','designation_hi','designation_bn',
        'bio_en','bio_hi','bio_bn',
        'achievements_en','achievements_hi','achievements_bn',
        'category','display_order','email','phone','whatsapp',
        'facebook_url','instagram_url','linkedin_url','is_active'
    ];
    $vals = [];
    foreach ($fields as $f) {
        $vals[$f] = trim($_POST[$f] ?? '');
    }
    $vals['is_active']     = isset($_POST['is_active']) ? 1 : 0;
    $vals['display_order'] = (int)($vals['display_order'] ?: 99);

    // Photo upload
    $photoPath = $_POST['existing_photo'] ?? null;
    if (!empty($_FILES['photo']['tmp_name'])) {
        $file = $_FILES['photo'];
        $allowed = ['image/jpeg','image/png','image/webp'];
        if (!in_array($file['type'], $allowed) || $file['size'] > 5*1024*1024) {
            $err = 'Photo must be JPG/PNG/WEBP under 5MB.';
        } else {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fn  = 'member_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dest = __DIR__ . '/../assets/uploads/members/' . $fn;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $photoPath = 'assets/uploads/members/' . $fn;
            }
        }
    }

    if (!$err) {
        if ($id) {
            $set = implode(', ', array_map(fn($f) => "$f = ?", $fields));
            $params = array_values($vals);
            $params[] = $photoPath;
            $params[] = $id;
            $db->prepare("UPDATE members SET $set, photo = ? WHERE id = ?")->execute($params);
            $msg = 'Member updated successfully.';
        } else {
            $cols = implode(', ', $fields) . ', photo';
            $phs  = implode(', ', array_fill(0, count($fields)+1, '?'));
            $params = array_values($vals);
            $params[] = $photoPath;
            $db->prepare("INSERT INTO members ($cols) VALUES ($phs)")->execute($params);
            $msg = 'Member added successfully.';
        }
    }
}

// ── Delete ─────────────────────────────────────────────────
if (!empty($_GET['delete']) && can('members','delete')) {
    $db->prepare('DELETE FROM members WHERE id = ?')->execute([(int)$_GET['delete']]);
    header('Location: /admin/members.php?deleted=1'); exit;
}
if (!empty($_GET['deleted'])) $msg = 'Member deleted.';

// ── Load for edit ──────────────────────────────────────────
$editing = null;
if (!empty($_GET['edit'])) {
    $s = $db->prepare('SELECT * FROM members WHERE id = ?');
    $s->execute([(int)$_GET['edit']]);
    $editing = $s->fetch();
}

// ── List ───────────────────────────────────────────────────
$members = $db->query('SELECT * FROM members ORDER BY category, display_order ASC')->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success" data-auto-dismiss="3000">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger">⚠ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="section-header">
  <h2>Members Manager</h2>
  <?php if (can('members','edit')): ?>
    <a href="/admin/members.php" class="btn btn-primary">+ Add New Member</a>
  <?php endif; ?>
</div>

<!-- Add / Edit Form -->
<?php if (can('members','edit')): ?>
<div class="card" style="margin-bottom:24px">
  <div class="card-header"><span class="card-title"><?= $editing ? 'Edit Member' : 'Add New Member' ?></span></div>
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>">
        <input type="hidden" name="existing_photo" value="<?= htmlspecialchars($editing['photo'] ?? '') ?>">
      <?php endif; ?>

      <!-- Lang tabs: Names -->
      <div style="margin-bottom:16px">
        <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--gray-600);margin-bottom:8px">Name (All 3 Languages)</p>
        <div class="lang-tabs" data-group="name">
          <button type="button" class="lang-tab active" data-lang="en" data-group="name">EN</button>
          <button type="button" class="lang-tab" data-lang="hi" data-group="name">HI</button>
          <button type="button" class="lang-tab" data-lang="bn" data-group="name">BN</button>
        </div>
        <?php foreach(['en'=>'English','hi'=>'Hindi','bn'=>'Bengali'] as $lc => $ln): ?>
        <div class="lang-panel <?= $lc==='en'?'active':'' ?>" data-lang="<?=$lc?>" data-group="name">
          <div class="form-grid">
            <div class="form-group">
              <label>Name (<?=$ln?>)</label>
              <input type="text" name="name_<?=$lc?>" value="<?= htmlspecialchars($editing["name_$lc"] ?? '') ?>" placeholder="Full name in <?=$ln?>" <?=$lc==='en'?'required':''?>>
            </div>
            <div class="form-group">
              <label>Designation (<?=$ln?>)</label>
              <input type="text" name="designation_<?=$lc?>" value="<?= htmlspecialchars($editing["designation_$lc"] ?? '') ?>" placeholder="e.g. President" <?=$lc==='en'?'required':''?>>
            </div>
            <div class="form-group full">
              <label>Bio (<?=$ln?>)</label>
              <textarea name="bio_<?=$lc?>" rows="3" placeholder="Short biography in <?=$ln?>"><?= htmlspecialchars($editing["bio_$lc"] ?? '') ?></textarea>
            </div>
            <div class="form-group full">
              <label>Achievements (<?=$ln?>)</label>
              <textarea name="achievements_<?=$lc?>" rows="2" placeholder="Key achievements in <?=$ln?>"><?= htmlspecialchars($editing["achievements_$lc"] ?? '') ?></textarea>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Meta fields -->
      <div class="form-grid">
        <div class="form-group">
          <label>Category</label>
          <select name="category">
            <?php foreach(['executive'=>'Executive Committee','core'=>'Core Members','advisory'=>'Advisory Members'] as $v=>$l): ?>
              <option value="<?=$v?>" <?= ($editing['category']??'core')===$v?'selected':'' ?>><?=$l?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Display Order</label>
          <input type="number" name="display_order" value="<?= (int)($editing['display_order'] ?? 99) ?>" min="1">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($editing['email'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($editing['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>WhatsApp</label>
          <input type="text" name="whatsapp" value="<?= htmlspecialchars($editing['whatsapp'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Facebook URL</label>
          <input type="url" name="facebook_url" value="<?= htmlspecialchars($editing['facebook_url'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Instagram URL</label>
          <input type="url" name="instagram_url" value="<?= htmlspecialchars($editing['instagram_url'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>LinkedIn URL</label>
          <input type="url" name="linkedin_url" value="<?= htmlspecialchars($editing['linkedin_url'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Photo</label>
          <?php if (!empty($editing['photo'])): ?>
            <img src="/<?= htmlspecialchars($editing['photo']) ?>" class="img-preview" id="photo-preview" style="display:block;margin-bottom:8px">
          <?php else: ?>
            <img src="" class="img-preview" id="photo-preview" style="display:none;margin-bottom:8px">
          <?php endif; ?>
          <input type="file" name="photo" accept="image/*" data-preview="photo-preview">
        </div>
        <div class="form-group" style="justify-content:flex-end;align-self:flex-end">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="is_active" value="1" <?= ($editing['is_active'] ?? 1) ? 'checked' : '' ?>>
            Active (shown on website)
          </label>
        </div>
      </div>

      <div style="margin-top:16px;display:flex;gap:10px">
        <button type="submit" class="btn btn-primary"><?= $editing ? 'Update Member' : 'Add Member' ?></button>
        <?php if ($editing): ?><a href="/admin/members.php" class="btn btn-outline">Cancel</a><?php endif; ?>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Members Table -->
<div class="card">
  <div class="card-header"><span class="card-title">All Members (<?= count($members) ?>)</span></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Photo</th><th>Name</th><th>Designation</th><th>Category</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($members as $m): ?>
        <tr>
          <td>
            <?php if ($m['photo']): ?>
              <img src="/<?= htmlspecialchars($m['photo']) ?>" class="img-preview" style="width:44px;height:44px">
            <?php else: ?>
              <div style="width:44px;height:44px;background:var(--gray-200);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px">👤</div>
            <?php endif; ?>
          </td>
          <td><strong><?= htmlspecialchars($m['name_en']) ?></strong><br><small style="color:var(--gray-400)"><?= htmlspecialchars($m['name_hi']) ?></small></td>
          <td><?= htmlspecialchars($m['designation_en']) ?></td>
          <td><span class="status-badge status-upcoming"><?= ucfirst($m['category']) ?></span></td>
          <td><?= $m['display_order'] ?></td>
          <td><span class="status-badge <?= $m['is_active'] ? 'status-approved' : 'status-rejected' ?>"><?= $m['is_active'] ? 'Active' : 'Inactive' ?></span></td>
          <td>
            <div class="td-actions">
              <?php if (can('members','edit')): ?>
                <a href="/admin/members.php?edit=<?= $m['id'] ?>" class="btn btn-sm btn-outline">✏️ Edit</a>
              <?php endif; ?>
              <?php if (can('members','delete')): ?>
                <a href="/admin/members.php?delete=<?= $m['id'] ?>" class="btn btn-sm btn-danger"
                   data-confirm="Delete <?= htmlspecialchars(addslashes($m['name_en'])) ?>? This cannot be undone.">🗑</a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
