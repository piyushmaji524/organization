<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('applications', 'view');

$pageTitle     = 'Membership Applications';
$activeSection = 'applications';
$db = getDB();
$msg = '';

// Approve / Reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && can('applications','edit')) {
    $id     = (int)($_POST['app_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $note   = trim($_POST['admin_note'] ?? '');
    if ($id && in_array($action, ['approved','rejected'])) {
        $memberId = null;
        if ($action === 'approved') {
            // Generate unique SYDC + 6-digit member ID
            do {
                $memberId = 'SYDC' . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $chk = $db->prepare('SELECT id FROM applications WHERE member_id = ?');
                $chk->execute([$memberId]);
            } while ($chk->fetch());
        }
        $db->prepare('UPDATE applications SET status=?, admin_note=?, member_id=COALESCE(member_id,?) WHERE id=?')
           ->execute([$action, $note, $memberId, $id]);
        // Notify applicant
        require_once __DIR__ . '/../config/mailer.php';
        $s = $db->prepare('SELECT full_name, email FROM applications WHERE id=?'); $s->execute([$id]); $app = $s->fetch();
        if ($app && $app['email']) notify_application_status($app['email'], $app['full_name'], $action, $note);
        $msg = "Application $action successfully." . ($memberId ? " Member ID: $memberId" : '');
    }
}

// View single
$viewing = null;
if (!empty($_GET['view'])) {
    $s = $db->prepare('SELECT * FROM applications WHERE id=?'); $s->execute([(int)$_GET['view']]); $viewing = $s->fetch();
}

$filter = $_GET['filter'] ?? 'pending';
$where  = $filter !== 'all' ? "WHERE status = '$filter'" : '';
$apps   = $db->query("SELECT * FROM applications $where ORDER BY applied_at DESC")->fetchAll();
$counts = $db->query("SELECT status, COUNT(*) as c FROM applications GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

require_once __DIR__ . '/includes/header.php';
?>

<div class="section-header">
  <h2>Membership Applications</h2>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <?php foreach(['pending'=>'⏳ Pending','approved'=>'✅ Approved','rejected'=>'❌ Rejected','all'=>'All'] as $k=>$l): ?>
      <a href="?filter=<?=$k?>" class="btn btn-sm <?= $filter===$k?'btn-primary':'btn-outline' ?>"><?=$l?> <?php if(isset($counts[$k])): ?>(<?=$counts[$k]?>)<?php endif; ?></a>
    <?php endforeach; ?>
  </div>
</div>

<?php if ($msg): ?><div class="alert alert-success" data-auto-dismiss="3000">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<?php if ($viewing): ?>
<div class="card" style="margin-bottom:24px">
  <div class="card-header">
    <span class="card-title">Application: <?= htmlspecialchars($viewing['full_name']) ?></span>
    <div style="display:flex;gap:8px">
      <span class="status-badge status-<?= $viewing['status'] ?>"><?= ucfirst($viewing['status']) ?></span>
      <a href="/admin/applications.php?filter=<?=$filter?>" class="btn btn-sm btn-outline">← Back</a>
    </div>
  </div>
  <div class="card-body">
    <div class="form-grid">
      <?php $fields = ['full_name'=>'Full Name','father_name'=>'Father\'s Name','age'=>'Age','phone'=>'Phone','email'=>'Email','address'=>'Address','education'=>'Education','occupation'=>'Occupation','referral'=>'Referral']; ?>
      <?php foreach($fields as $k=>$l): ?>
        <div><strong><?=$l?>:</strong> <?= htmlspecialchars($viewing[$k] ?: '—') ?></div>
      <?php endforeach; ?>
      <div><strong>Applied:</strong> <?= date('d M Y h:i A', strtotime($viewing['applied_at'])) ?></div>
    </div>
    <?php if ($viewing['photo']): ?>
      <div style="margin-top:16px"><strong>Photo:</strong><br><img src="/<?= htmlspecialchars($viewing['photo']) ?>" class="img-preview-lg" style="margin-top:8px"></div>
    <?php endif; ?>
    <?php if ($viewing['admin_note']): ?>
      <div style="margin-top:16px;background:var(--gray-50);padding:12px;border-radius:8px"><strong>Admin Note:</strong> <?= htmlspecialchars($viewing['admin_note']) ?></div>
    <?php endif; ?>

    <?php if (can('applications','edit') && $viewing['status'] === 'pending'): ?>
    <div style="margin-top:20px;border-top:1px solid var(--gray-200);padding-top:20px">
      <form method="POST">
        <input type="hidden" name="app_id" value="<?= $viewing['id'] ?>">
        <div class="form-group" style="margin-bottom:12px">
          <label>Admin Note (sent to applicant in email)</label>
          <textarea name="admin_note" rows="2" placeholder="Optional note for applicant..."></textarea>
        </div>
        <div style="display:flex;gap:10px">
          <button type="submit" name="action" value="approved" class="btn btn-success">✅ Approve</button>
          <button type="submit" name="action" value="rejected" class="btn btn-danger">❌ Reject</button>
        </div>
      </form>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Name</th><th>Age</th><th>Phone</th><th>Education</th><th>Status</th><th>Applied</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach($apps as $a): ?>
        <tr>
          <td><strong><?= htmlspecialchars($a['full_name']) ?></strong><br><small><?= htmlspecialchars($a['father_name']) ?></small></td>
          <td><?= $a['age'] ?></td>
          <td><?= htmlspecialchars($a['phone']) ?></td>
          <td><?= htmlspecialchars($a['education'] ?: '—') ?></td>
          <td><span class="status-badge status-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
          <td><?= date('d M Y', strtotime($a['applied_at'])) ?></td>
          <td>
            <div class="td-actions">
              <a href="?view=<?=$a['id']?>&filter=<?=$filter?>" class="btn btn-sm btn-outline">👁 Review</a>
              <?php if(can('applications','edit') && $a['status']==='pending'): ?>
                <form method="POST" style="display:inline">
                  <input type="hidden" name="app_id" value="<?=$a['id']?>">
                  <button type="submit" name="action" value="approved" class="btn btn-sm btn-success">✅</button>
                  <button type="submit" name="action" value="rejected" class="btn btn-sm btn-danger">❌</button>
                </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($apps)): ?><tr><td colspan="7" style="text-align:center;color:var(--gray-400);padding:32px">No <?=$filter?> applications.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
