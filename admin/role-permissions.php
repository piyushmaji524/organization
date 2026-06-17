<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('role_permissions', 'view');

$pageTitle     = 'Role Permissions Editor';
$activeSection = 'role_permissions';
$db = getDB();
$msg = '';

$sections = ['dashboard','members','events','rsvp','news','gallery','messages','applications','donate','settings','content','role_permissions','admin_users'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && can('role_permissions','edit')) {
    $roleId = (int)($_POST['role_id'] ?? 0);
    if ($roleId && $roleId !== 1) { // Never edit super_admin perms (role_id=1)
        foreach ($sections as $sec) {
            $view   = isset($_POST["perm_{$sec}_view"])   ? 1 : 0;
            $edit   = isset($_POST["perm_{$sec}_edit"])   ? 1 : 0;
            $delete = isset($_POST["perm_{$sec}_delete"]) ? 1 : 0;
            $db->prepare('INSERT INTO role_permissions (role_id,section_key,can_view,can_edit,can_delete) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE can_view=?,can_edit=?,can_delete=?')
               ->execute([$roleId,$sec,$view,$edit,$delete,$view,$edit,$delete]);
        }
        $msg = 'Permissions updated — changes take effect immediately.';
    }
}

$roles = $db->query('SELECT * FROM roles WHERE id != 1 ORDER BY id')->fetchAll(); // exclude super_admin from editing

// Load all permissions for all non-super-admin roles
$permsRaw = $db->query('SELECT role_id, section_key, can_view, can_edit, can_delete FROM role_permissions WHERE role_id != 1')->fetchAll();
$perms = [];
foreach ($permsRaw as $p) $perms[$p['role_id']][$p['section_key']] = $p;

$selectedRole = (int)($_GET['role'] ?? ($roles[0]['id'] ?? 2));

require_once __DIR__ . '/includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success" data-auto-dismiss="4000">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="section-header"><h2>Role Permissions Editor</h2><p style="color:var(--gray-400);font-size:13px">Changes apply instantly — no restart needed.</p></div>

<div style="display:grid;grid-template-columns:220px 1fr;gap:20px;align-items:start">
  <!-- Role selector -->
  <div class="card">
    <div class="card-header"><span class="card-title">Roles</span></div>
    <div>
      <?php foreach ($roles as $r): ?>
      <a href="?role=<?=$r['id']?>" style="display:block;padding:12px 16px;text-decoration:none;color:inherit;border-left:3px solid <?= $selectedRole===$r['id']?'var(--maroon)':'transparent' ?>;background:<?= $selectedRole===$r['id']?'var(--cream)':'transparent' ?>;font-weight:<?= $selectedRole===$r['id']?'600':'400' ?>">
        <?= htmlspecialchars($r['display_name']) ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Permission matrix for selected role -->
  <?php
  $selRole = array_filter($roles, fn($r) => $r['id'] === $selectedRole);
  $selRole = reset($selRole);
  ?>
  <div class="card">
    <div class="card-header">
      <span class="card-title">Permissions: <?= $selRole ? htmlspecialchars($selRole['display_name']) : '' ?></span>
    </div>
    <div class="card-body">
      <?php if ($selRole): ?>
      <form method="POST">
        <input type="hidden" name="role_id" value="<?=$selectedRole?>">
        <div class="table-wrap">
          <table class="perm-table">
            <thead>
              <tr>
                <th style="min-width:180px">Section</th>
                <th style="text-align:center">View</th>
                <th style="text-align:center">Edit / Create</th>
                <th style="text-align:center">Delete</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sectionLabels = [
                'dashboard'        => '📊 Dashboard',
                'members'          => '👥 Members',
                'events'           => '📅 Events',
                'rsvp'             => '✅ RSVP',
                'news'             => '📰 News',
                'gallery'          => '🖼️ Gallery',
                'messages'         => '✉️ Messages',
                'applications'     => '📋 Applications',
                'donate'           => '💛 Donate Settings',
                'settings'         => '⚙️ Site Settings',
                'content'          => '🌐 Content/Translations',
                'role_permissions' => '🔐 Role Permissions',
                'admin_users'      => '👤 Admin Users',
              ];
              foreach ($sections as $sec):
                $rp = $perms[$selectedRole][$sec] ?? ['can_view'=>0,'can_edit'=>0,'can_delete'=>0];
              ?>
              <tr>
                <td><?= $sectionLabels[$sec] ?? $sec ?></td>
                <td style="text-align:center">
                  <input type="checkbox" name="perm_<?=$sec?>_view"   <?=$rp['can_view']  ?'checked':''?>>
                </td>
                <td style="text-align:center">
                  <input type="checkbox" name="perm_<?=$sec?>_edit"   <?=$rp['can_edit']  ?'checked':''?>>
                </td>
                <td style="text-align:center">
                  <input type="checkbox" name="perm_<?=$sec?>_delete" <?=$rp['can_delete']?'checked':''?>>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php if (can('role_permissions','edit')): ?>
          <div style="margin-top:16px">
            <button type="submit" class="btn btn-primary">💾 Save Permissions for <?= htmlspecialchars($selRole['display_name']) ?></button>
          </div>
        <?php endif; ?>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
