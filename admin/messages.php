<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('messages', 'view');

$pageTitle     = 'Messages Inbox';
$activeSection = 'messages';
$db = getDB();

// Mark read
if (!empty($_GET['read']) && can('messages','edit')) {
    $db->prepare('UPDATE messages SET is_read=1 WHERE id=?')->execute([(int)$_GET['read']]);
}
// Mark unread
if (!empty($_GET['unread']) && can('messages','edit')) {
    $db->prepare('UPDATE messages SET is_read=0 WHERE id=?')->execute([(int)$_GET['unread']]);
}
// Delete
if (!empty($_GET['delete']) && can('messages','delete')) {
    $db->prepare('DELETE FROM messages WHERE id=?')->execute([(int)$_GET['delete']]);
    header('Location: /admin/messages.php'); exit;
}

// View single
$viewing = null;
if (!empty($_GET['view'])) {
    $s = $db->prepare('SELECT * FROM messages WHERE id=?'); $s->execute([(int)$_GET['view']]); $viewing = $s->fetch();
    if ($viewing && !$viewing['is_read'] && can('messages','edit')) {
        $db->prepare('UPDATE messages SET is_read=1 WHERE id=?')->execute([$viewing['id']]);
    }
}

$filter = $_GET['filter'] ?? 'all';
$where = $filter === 'unread' ? 'WHERE is_read=0' : ($filter === 'read' ? 'WHERE is_read=1' : '');
$messages = $db->query("SELECT * FROM messages $where ORDER BY created_at DESC")->fetchAll();
$unreadCount = (int)$db->query("SELECT COUNT(*) FROM messages WHERE is_read=0")->fetchColumn();

require_once __DIR__ . '/includes/header.php';
?>

<div class="section-header">
  <h2>Messages Inbox <?php if($unreadCount): ?><span class="badge"><?=$unreadCount?></span><?php endif; ?></h2>
  <div style="display:flex;gap:8px">
    <a href="?filter=all"    class="btn btn-sm <?= $filter==='all'    ?'btn-primary':'btn-outline' ?>">All</a>
    <a href="?filter=unread" class="btn btn-sm <?= $filter==='unread' ?'btn-primary':'btn-outline' ?>">Unread</a>
    <a href="?filter=read"   class="btn btn-sm <?= $filter==='read'   ?'btn-primary':'btn-outline' ?>">Read</a>
  </div>
</div>

<?php if ($viewing): ?>
<div class="card" style="margin-bottom:24px">
  <div class="card-header">
    <span class="card-title">✉️ <?= htmlspecialchars($viewing['subject'] ?: '(no subject)') ?></span>
    <a href="/admin/messages.php" class="btn btn-sm btn-outline">← Back</a>
  </div>
  <div class="card-body">
    <div class="form-grid" style="margin-bottom:16px">
      <div><strong>From:</strong> <?= htmlspecialchars($viewing['name']) ?> &lt;<a href="mailto:<?= htmlspecialchars($viewing['email']) ?>"><?= htmlspecialchars($viewing['email']) ?></a>&gt;</div>
      <div><strong>Phone:</strong> <?= htmlspecialchars($viewing['phone'] ?: 'N/A') ?></div>
      <div><strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($viewing['created_at'])) ?></div>
    </div>
    <div style="background:var(--gray-50);padding:16px;border-radius:8px;line-height:1.8">
      <?= nl2br(htmlspecialchars($viewing['message'])) ?>
    </div>
    <div style="margin-top:16px;display:flex;gap:10px">
      <a href="mailto:<?= htmlspecialchars($viewing['email']) ?>?subject=Re: <?= urlencode($viewing['subject'] ?? '') ?>" class="btn btn-primary">✉️ Reply via Email</a>
      <?php if($viewing['phone']): ?>
        <a href="https://wa.me/<?= preg_replace('/\D/','',$viewing['phone']) ?>" target="_blank" class="btn btn-success">💬 WhatsApp</a>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Status</th><th>From</th><th>Subject</th><th>Phone</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($messages as $m): ?>
        <tr style="<?= !$m['is_read']?'font-weight:600':'' ?>">
          <td><span class="status-badge <?= $m['is_read']?'status-completed':'status-upcoming' ?>"><?= $m['is_read']?'Read':'New' ?></span></td>
          <td><?= htmlspecialchars($m['name']) ?><br><small><?= htmlspecialchars($m['email']) ?></small></td>
          <td><?= htmlspecialchars($m['subject'] ?: '(no subject)') ?></td>
          <td><?= htmlspecialchars($m['phone'] ?: '—') ?></td>
          <td><?= date('d M Y', strtotime($m['created_at'])) ?></td>
          <td>
            <div class="td-actions">
              <a href="?view=<?=$m['id']?>" class="btn btn-sm btn-outline">👁 View</a>
              <?php if(can('messages','edit')): ?>
                <?php if($m['is_read']): ?><a href="?unread=<?=$m['id']?>" class="btn btn-sm btn-outline">Mark Unread</a>
                <?php else: ?><a href="?read=<?=$m['id']?>" class="btn btn-sm btn-outline">Mark Read</a><?php endif; ?>
              <?php endif; ?>
              <?php if(can('messages','delete')): ?>
                <a href="?delete=<?=$m['id']?>" class="btn btn-sm btn-danger" data-confirm="Delete this message?">🗑</a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($messages)): ?><tr><td colspan="6" style="text-align:center;color:var(--gray-400);padding:32px">No messages found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
