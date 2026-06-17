<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('admin_users', 'view');

$pageTitle     = 'Admin User Manager';
$activeSection = 'admin_users';
$db = getDB();
$msg = ''; $err = '';

// Save (add / edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && can('admin_users','edit')) {
    $id       = (int)($_POST['id']       ?? 0);
    $username = trim($_POST['username']  ?? '');
    $name     = trim($_POST['name']      ?? '');
    $roleId   = (int)($_POST['role_id']  ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $password = $_POST['password'] ?? '';

    if (!$username || !$name || !$roleId) {
        $err = 'Username, name, and role are required.';
    } else {
        if ($id) {
            // Update (password only if provided)
            if ($password) {
                $db->prepare('UPDATE admins SET username=?,name=?,role_id=?,is_active=?,password_hash=? WHERE id=?')
                   ->execute([$username,$name,$roleId,$isActive,password_hash($password, PASSWORD_BCRYPT),$id]);
            } else {
                $db->prepare('UPDATE admins SET username=?,name=?,role_id=?,is_active=? WHERE id=?')
                   ->execute([$username,$name,$roleId,$isActive,$id]);
            }
            $msg = 'Admin user updated.';
        } else {
            if (!$password) { $err = 'Password is required for new admin users.'; }
            else {
                // Check username unique
                $check = $db->prepare('SELECT id FROM admins WHERE username=?'); $check->execute([$username]);
                if ($check->fetch()) { $err = "Username '$username' is already taken."; }
                else {
                    $db->prepare('INSERT INTO admins (username,password_hash,name,role_id,is_active) VALUES(?,?,?,?,?)')
                       ->execute([$username,password_hash($password, PASSWORD_BCRYPT),$name,$roleId,$isActive]);
                    $msg = 'Admin user created.';
                }
            }
        }
    }
}

// Toggle active
if (!empty($_GET['toggle']) && can('admin_users','edit')) {
    $tid = (int)$_GET['toggle'];
    if ($tid !== 1) { // Never deactivate super admin (id=1)
        $db->prepare('UPDATE admins SET is_active = 1 - is_active WHERE id=?')->execute([$tid]);
    }
    header('Location: /admin/admin-users.php'); exit;
}

// Delete
if (!empty($_GET['delete']) && can('admin_users','delete')) {
    $did = (int)$_GET['delete'];
    if ($did === 1) { $err = 'Cannot delete the Super Admin account.'; }
    else { $db->prepare('DELETE FROM admins WHERE id=?')->execute([$did]); header('Location: /admin/admin-users.php?deleted=1'); exit; }
}
if (!empty($_GET['deleted'])) $msg = 'Admin user deleted.';

$editing = null;
if (!empty($_GET['edit'])) {
    $s = $db->prepare('SELECT id,username,name,role_id,is_active FROM admins WHERE id=?'); $s->execute([(int)$_GET['edit']]); $editing = $s->fetch();
}

$roles    = $db->query('SELECT * FROM roles ORDER BY id')->fetchAll();
$admins   = $db->query('SELECT a.*, r.display_name AS role_name FROM admins a JOIN roles r ON a.role_id=r.id ORDER BY a.id')->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success" data-auto-dismiss="3000">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger">⚠ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="section-header">
  <h2>Admin User Manager</h2>
  <a href="/admin/admin-users.php" class="btn btn-primary">+ Add New Admin</a>
</div>

<!-- Form -->
<div class="card" style="margin-bottom:24px;max-width:680px">
  <div class="card-header"><span class="card-title"><?= $editing ? 'Edit Admin User' : 'Add New Admin User' ?></span></div>
  <div class="card-body">
    <form method="POST">
      <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"><?php endif; ?>
      <div class="form-grid">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($editing['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" value="<?= htmlspecialchars($editing['username'] ?? '') ?>" required autocomplete="off">
        </div>
        <div class="form-group">
          <label>Password <?= $editing ? '(leave blank to keep current)' : '' ?></label>
          <input type="password" name="password" autocomplete="new-password" <?= $editing ? '' : 'required' ?>>
        </div>
        <div class="form-group">
          <label>Role</label>
          <select name="role_id" required>
            <option value="">— Select Role —</option>
            <?php foreach ($roles as $r): ?>
              <option value="<?=$r['id']?>" <?= ($editing['role_id'] ?? 0) == $r['id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['display_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="align-self:flex-end">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="is_active" value="1" <?= ($editing['is_active'] ?? 1) ? 'checked' : '' ?>>
            Active (can login)
          </label>
        </div>
      </div>
      <div style="margin-top:16px;display:flex;gap:10px">
        <button type="submit" class="btn btn-primary"><?= $editing ? 'Update User' : 'Create Admin User' ?></button>
        <?php if ($editing): ?><a href="/admin/admin-users.php" class="btn btn-outline">Cancel</a><?php endif; ?>
      </div>
    </form>
  </div>
</div>

<!-- Table -->
<div class="card">
  <div class="card-header"><span class="card-title">All Admin Users (<?= count($admins) ?>)</span></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($admins as $a): ?>
        <tr>
          <td><strong><?= htmlspecialchars($a['name']) ?></strong><?= $a['id']===1?'<span style="color:var(--gold);margin-left:6px">⭐ Super Admin</span>':'' ?></td>
          <td><code><?= htmlspecialchars($a['username']) ?></code></td>
          <td><?= htmlspecialchars($a['role_name']) ?></td>
          <td><span class="status-badge <?= $a['is_active']?'status-approved':'status-rejected' ?>"><?= $a['is_active']?'Active':'Inactive' ?></span></td>
          <td><?= date('d M Y', strtotime($a['created_at'])) ?></td>
          <td>
            <div class="td-actions">
              <a href="/admin/admin-users.php?edit=<?=$a['id']?>" class="btn btn-sm btn-outline">✏️ Edit</a>
              <?php if($a['id'] !== 1): ?>
                <a href="/admin/admin-users.php?toggle=<?=$a['id']?>" class="btn btn-sm btn-outline"><?= $a['is_active']?'Deactivate':'Activate' ?></a>
                <a href="/admin/admin-users.php?delete=<?=$a['id']?>" class="btn btn-sm btn-danger" data-confirm="Delete <?= htmlspecialchars(addslashes($a['name'])) ?>?">🗑</a>
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
