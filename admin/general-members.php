<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('applications', 'view');

$pageTitle     = 'General Members';
$activeSection = 'general_members';
$db  = getDB();
$msg = '';
$err = '';

$ALL_FIELDS = [
    'father_name' => "Father's Name",
    'age'         => 'Age',
    'phone'       => 'Phone',
    'email'       => 'Email',
    'education'   => 'Education',
    'occupation'  => 'Occupation',
    'address'     => 'Address',
    'referral'    => 'Referred By',
];

// ── Save edits ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && can('applications','edit')) {
    $id     = (int)($_POST['app_id'] ?? 0);
    $action = $_POST['form_action'] ?? '';

    if ($id && $action === 'update') {
        // Editable submitted fields
        $fullName   = trim($_POST['full_name']   ?? '');
        $fatherName = trim($_POST['father_name'] ?? '');
        $age        = (int)($_POST['age']        ?? 0);
        $address    = trim($_POST['address']     ?? '');
        $phone      = trim($_POST['phone']       ?? '');
        $email      = trim($_POST['email']       ?? '');
        $education  = trim($_POST['education']   ?? '');
        $occupation = trim($_POST['occupation']  ?? '');
        $referral   = trim($_POST['referral']    ?? '');

        // Display settings
        $showOnWebsite  = isset($_POST['show_on_website']) ? 1 : 0;
        $badgeName      = trim($_POST['badge_name'] ?? '') ?: null;
        $visibleFields  = isset($_POST['visible_fields']) ? json_encode(array_values($_POST['visible_fields'])) : json_encode([]);

        // Photo upload
        $photoPath = trim($_POST['existing_photo'] ?? '');
        if (!empty($_FILES['photo']['tmp_name'])) {
            $file    = $_FILES['photo'];
            $allowed = ['image/jpeg','image/png','image/webp'];
            if (!in_array($file['type'], $allowed) || $file['size'] > 5*1024*1024) {
                $err = 'Photo must be JPG/PNG/WEBP under 5MB.';
            } else {
                $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $fn   = 'app_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest = __DIR__ . '/../assets/uploads/members/' . $fn;
                if (move_uploaded_file($file['tmp_name'], $dest)) $photoPath = 'assets/uploads/members/' . $fn;
            }
        }

        if (!$err) {
            $db->prepare('UPDATE applications SET
                full_name=?, father_name=?, age=?, address=?, phone=?, email=?,
                education=?, occupation=?, referral=?, photo=?,
                show_on_website=?, badge_name=?, visible_fields=?
                WHERE id=? AND status=\'approved\'')
               ->execute([$fullName,$fatherName,$age,$address,$phone,$email,
                          $education,$occupation,$referral,$photoPath ?: null,
                          $showOnWebsite,$badgeName,$visibleFields,$id]);
            $msg = 'Member updated successfully.';
        }
    }
}

// ── Load for edit ───────────────────────────────────────────
$editing = null;
if (!empty($_GET['edit'])) {
    $s = $db->prepare('SELECT * FROM applications WHERE id=? AND status=\'approved\'');
    $s->execute([(int)$_GET['edit']]);
    $editing = $s->fetch();
    if ($editing) {
        $editing['_visible'] = $editing['visible_fields'] ? json_decode($editing['visible_fields'], true) : [];
    }
}

// ── List ────────────────────────────────────────────────────
$filter  = $_GET['filter'] ?? 'all';
$where   = match($filter) {
    'visible'  => "AND show_on_website = 1",
    'hidden'   => "AND show_on_website = 0",
    'badge'    => "AND badge_name IS NOT NULL AND badge_name != ''",
    default    => ''
};
$members = $db->query("SELECT * FROM applications WHERE status='approved' $where
                       ORDER BY (badge_name IS NOT NULL AND badge_name != '') DESC, applied_at DESC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success" data-auto-dismiss="3000">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger">⚠ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="section-header">
  <h2>General Members</h2>
  <p style="font-size:13px;color:var(--gray-500);margin-top:4px">Manage approved applicants shown on the website's General Members section.</p>
</div>

<!-- Edit Form -->
<?php if ($editing && can('applications','edit')): ?>
<div class="card" style="margin-bottom:24px">
  <div class="card-header">
    <span class="card-title">✏️ Editing: <?= htmlspecialchars($editing['full_name']) ?> &nbsp;<span style="font-size:12px;color:var(--gray-400)"><?= htmlspecialchars($editing['member_id'] ?? '') ?></span></span>
    <a href="/admin/general-members.php" class="btn btn-sm btn-outline">← Back to List</a>
  </div>
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="app_id" value="<?= $editing['id'] ?>">
      <input type="hidden" name="form_action" value="update">
      <input type="hidden" name="existing_photo" value="<?= htmlspecialchars($editing['photo'] ?? '') ?>">

      <!-- Submitted Fields -->
      <h4 style="font-size:13px;text-transform:uppercase;letter-spacing:.5px;color:var(--gray-500);margin-bottom:14px">Member Details</h4>
      <div class="form-grid">
        <div class="form-group">
          <label>Full Name *</label>
          <input type="text" name="full_name" value="<?= htmlspecialchars($editing['full_name']) ?>" required>
        </div>
        <div class="form-group">
          <label>Father's Name</label>
          <input type="text" name="father_name" value="<?= htmlspecialchars($editing['father_name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Age</label>
          <input type="number" name="age" value="<?= (int)($editing['age'] ?? 0) ?>" min="1" max="100">
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($editing['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($editing['email'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Education</label>
          <input type="text" name="education" value="<?= htmlspecialchars($editing['education'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Occupation</label>
          <input type="text" name="occupation" value="<?= htmlspecialchars($editing['occupation'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Referred By</label>
          <input type="text" name="referral" value="<?= htmlspecialchars($editing['referral'] ?? '') ?>">
        </div>
        <div class="form-group full">
          <label>Address</label>
          <textarea name="address" rows="2"><?= htmlspecialchars($editing['address'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
          <label>Photo (JPG/PNG/WEBP, max 5MB)</label>
          <?php if (!empty($editing['photo'])): ?>
            <img src="/<?= htmlspecialchars($editing['photo']) ?>" class="img-preview" id="photo-preview" style="display:block;margin-bottom:8px;width:80px;height:80px;object-fit:cover;border-radius:50%">
          <?php else: ?>
            <img src="" class="img-preview" id="photo-preview" style="display:none;margin-bottom:8px">
          <?php endif; ?>
          <input type="file" name="photo" accept="image/*" data-preview="photo-preview">
        </div>
      </div>

      <div style="height:1px;background:var(--gray-200);margin:20px 0"></div>

      <!-- Website Display Settings -->
      <h4 style="font-size:13px;text-transform:uppercase;letter-spacing:.5px;color:var(--gray-500);margin-bottom:14px">Website Display Settings</h4>
      <div class="form-grid">
        <div class="form-group">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
            <input type="checkbox" name="show_on_website" value="1" <?= $editing['show_on_website'] ? 'checked' : '' ?> style="width:18px;height:18px">
            <span><strong>Show on Website</strong><br><small style="color:var(--gray-400)">Display this member in the General Members section</small></span>
          </label>
        </div>
        <div class="form-group">
          <label>Badge Name <small style="color:var(--gray-400)">(leave blank for no badge)</small></label>
          <input type="text" name="badge_name" value="<?= htmlspecialchars($editing['badge_name'] ?? '') ?>" placeholder="e.g. District Champion, Youth Leader...">
          <small style="color:var(--gray-400);margin-top:4px;display:block">Badge members show first with a gold border card</small>
        </div>
      </div>

      <div class="form-group" style="margin-top:12px">
        <label style="font-weight:700;margin-bottom:10px;display:block">Visible Fields on Website Card</label>
        <small style="color:var(--gray-400);display:block;margin-bottom:12px">Photo and Full Name always show. Select additional fields to display:</small>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px">
          <?php foreach($ALL_FIELDS as $fkey => $flabel): ?>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:8px 12px;border:1px solid var(--gray-200);border-radius:8px;background:var(--gray-50)">
            <input type="checkbox" name="visible_fields[]" value="<?= $fkey ?>"
              <?= in_array($fkey, $editing['_visible']) ? 'checked' : '' ?>>
            <?= $flabel ?>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div style="margin-top:20px;display:flex;gap:10px">
        <button type="submit" class="btn btn-primary">💾 Save Changes</button>
        <a href="/admin/general-members.php" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php else: ?>

<!-- Search + Filter bar -->
<div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:14px">
  <input type="search" id="gm-search" placeholder="🔍 Search by name, phone, member ID…"
    style="flex:1;min-width:220px;padding:8px 12px;border:1px solid var(--gray-300);border-radius:8px;font-size:14px;outline:none">
  <select id="gm-filter-visible" style="padding:8px 10px;border:1px solid var(--gray-300);border-radius:8px;font-size:13px">
    <option value="">👁 All Visibility</option>
    <option value="1">✅ Visible</option>
    <option value="0">❌ Hidden</option>
  </select>
  <select id="gm-filter-badge" style="padding:8px 10px;border:1px solid var(--gray-300);border-radius:8px;font-size:13px">
    <option value="">🏅 All Badges</option>
    <option value="1">With Badge</option>
    <option value="0">No Badge</option>
  </select>
  <button onclick="resetFilters()" class="btn btn-sm btn-outline">↺ Reset</button>
</div>

<!-- Members Table -->
<div class="card">
  <div class="card-header">
    <span class="card-title">General Members (<span id="gm-count"><?= count($members) ?></span>)</span>
  </div>
  <div class="table-wrap">
    <table id="gm-table">
      <thead>
        <tr>
          <th>Photo</th>
          <th class="sortable" data-col="name" style="cursor:pointer;user-select:none">Name <span class="sort-icon">⇅</span></th>
          <th class="sortable" data-col="memberid" style="cursor:pointer;user-select:none">Member ID <span class="sort-icon">⇅</span></th>
          <th class="sortable" data-col="badge" style="cursor:pointer;user-select:none">Badge <span class="sort-icon">⇅</span></th>
          <th class="sortable" data-col="visible" style="cursor:pointer;user-select:none">Visible <span class="sort-icon">⇅</span></th>
          <th class="sortable" data-col="applied" style="cursor:pointer;user-select:none">Applied <span class="sort-icon">⇅</span></th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="gm-tbody">
        <?php foreach($members as $m): ?>
        <tr data-name="<?= strtolower(htmlspecialchars($m['full_name'])) ?>"
            data-phone="<?= htmlspecialchars($m['phone'] ?? '') ?>"
            data-memberid="<?= strtolower(htmlspecialchars($m['member_id'] ?? '')) ?>"
            data-badge="<?= $m['badge_name'] ? '1' : '0' ?>"
            data-badgename="<?= strtolower(htmlspecialchars($m['badge_name'] ?? '')) ?>"
            data-visible="<?= $m['show_on_website'] ? '1' : '0' ?>"
            data-applied="<?= strtotime($m['applied_at']) ?>">
          <td>
            <?php if ($m['photo']): ?>
              <img src="/<?= htmlspecialchars($m['photo']) ?>" style="width:44px;height:44px;object-fit:cover;border-radius:50%;border:2px solid <?= $m['badge_name'] ? '#C9A84C' : 'var(--gray-200)' ?>">
            <?php else: ?>
              <div style="width:44px;height:44px;background:var(--gray-200);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px">👤</div>
            <?php endif; ?>
          </td>
          <td>
            <strong><?= htmlspecialchars($m['full_name']) ?></strong><br>
            <small style="color:var(--gray-400)"><?= htmlspecialchars($m['phone'] ?? '') ?></small>
          </td>
          <td>
            <?php if ($m['member_id']): ?>
              <code style="font-size:12px;background:var(--gray-100);padding:2px 6px;border-radius:4px"><?= htmlspecialchars($m['member_id']) ?></code>
            <?php else: ?>
              <span style="color:var(--gray-400)">—</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($m['badge_name']): ?>
              <span style="background:#FDF3DC;color:#7B4F00;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;border:1px solid #C9A84C">🏅 <?= htmlspecialchars($m['badge_name']) ?></span>
            <?php else: ?>
              <span style="color:var(--gray-300)">—</span>
            <?php endif; ?>
          </td>
          <td>
            <span class="status-badge <?= $m['show_on_website'] ? 'status-approved' : 'status-rejected' ?>">
              <?= $m['show_on_website'] ? '✅ Yes' : '❌ No' ?>
            </span>
          </td>
          <td style="font-size:13px;color:var(--gray-400)"><?= date('d M Y', strtotime($m['applied_at'])) ?></td>
          <td>
            <div class="td-actions">
              <?php if(can('applications','edit')): ?>
                <a href="?edit=<?= $m['id'] ?>" class="btn btn-sm btn-outline">✏️ Edit</a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($members)): ?>
          <tr id="gm-empty"><td colspan="7" style="text-align:center;color:var(--gray-400);padding:32px">No approved members yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <p id="gm-no-results" style="display:none;text-align:center;color:var(--gray-400);padding:32px">No members match your search.</p>
  </div>
</div>

<script>
(function() {
  let sortCol = '', sortDir = 1;

  function applyFilters() {
    const q       = document.getElementById('gm-search').value.toLowerCase().trim();
    const vis     = document.getElementById('gm-filter-visible').value;
    const badge   = document.getElementById('gm-filter-badge').value;
    const tbody   = document.getElementById('gm-tbody');
    const rows    = Array.from(tbody.querySelectorAll('tr[data-name]'));

    let visible = 0;
    rows.forEach(row => {
      const nameMatch  = row.dataset.name.includes(q) || row.dataset.phone.includes(q) || row.dataset.memberid.includes(q) || row.dataset.badgename.includes(q);
      const visMatch   = vis   === '' || row.dataset.visible === vis;
      const badgeMatch = badge === '' || row.dataset.badge   === badge;
      const show = nameMatch && visMatch && badgeMatch;
      row.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    document.getElementById('gm-count').textContent = visible;
    const noRes = document.getElementById('gm-no-results');
    if (noRes) noRes.style.display = visible === 0 && rows.length > 0 ? 'block' : 'none';
  }

  function sortTable(col) {
    if (sortCol === col) sortDir *= -1; else { sortCol = col; sortDir = 1; }

    // Update sort icons
    document.querySelectorAll('#gm-table .sortable').forEach(th => {
      th.querySelector('.sort-icon').textContent = th.dataset.col === col
        ? (sortDir === 1 ? ' ↑' : ' ↓') : ' ⇅';
    });

    const tbody = document.getElementById('gm-tbody');
    const rows  = Array.from(tbody.querySelectorAll('tr[data-name]'));
    rows.sort((a, b) => {
      let va = a.dataset[col] || '', vb = b.dataset[col] || '';
      if (col === 'applied') { va = +va; vb = +vb; return (va - vb) * sortDir; }
      if (col === 'visible' || col === 'badge') { va = +va; vb = +vb; return (vb - va) * sortDir; }
      return va.localeCompare(vb) * sortDir;
    });
    rows.forEach(r => tbody.appendChild(r));
    applyFilters();
  }

  window.resetFilters = function() {
    document.getElementById('gm-search').value = '';
    document.getElementById('gm-filter-visible').value = '';
    document.getElementById('gm-filter-badge').value = '';
    sortCol = ''; sortDir = 1;
    document.querySelectorAll('#gm-table .sort-icon').forEach(el => el.textContent = ' ⇅');
    applyFilters();
  };

  document.getElementById('gm-search').addEventListener('input', applyFilters);
  document.getElementById('gm-filter-visible').addEventListener('change', applyFilters);
  document.getElementById('gm-filter-badge').addEventListener('change', applyFilters);
  document.querySelectorAll('#gm-table .sortable').forEach(th => {
    th.addEventListener('click', () => sortTable(th.dataset.col));
  });
})();
</script>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
