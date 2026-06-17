<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('rsvp', 'view');

$pageTitle     = 'RSVP Manager';
$activeSection = 'rsvp';
$db = getDB();

// CSV Export
if (!empty($_GET['export']) && can('rsvp','view')) {
    $eid = (int)$_GET['export'];
    $evStmt = $db->prepare('SELECT title_en FROM events WHERE id=?'); $evStmt->execute([$eid]); $evTitle = $evStmt->fetchColumn();
    $rows = $db->prepare('SELECT name, email, phone, attendee_count, registered_at FROM rsvp WHERE event_id=? ORDER BY registered_at ASC');
    $rows->execute([$eid]);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="rsvp_' . $eid . '_' . date('Ymd') . '.csv"');
    $f = fopen('php://output','w');
    fputcsv($f, ['Name','Email','Phone','Attendees','Registered At']);
    foreach ($rows->fetchAll() as $r) fputcsv($f, $r);
    fclose($f); exit;
}

// Delete RSVP
if (!empty($_GET['delete']) && can('rsvp','delete')) {
    $db->prepare('DELETE FROM rsvp WHERE id=?')->execute([(int)$_GET['delete']]);
    header('Location: /admin/rsvp.php' . (!empty($_GET['event']) ? '?event='.(int)$_GET['event'] : '')); exit;
}

$events = $db->query('SELECT e.*, COALESCE(SUM(r.attendee_count),0) AS total_rsvp FROM events e LEFT JOIN rsvp r ON r.event_id=e.id GROUP BY e.id ORDER BY e.event_date DESC')->fetchAll();

$selectedEvent = !empty($_GET['event']) ? (int)$_GET['event'] : null;
$rsvpList = [];
$selEvTitle = '';
if ($selectedEvent) {
    $s = $db->prepare('SELECT * FROM rsvp WHERE event_id=? ORDER BY registered_at DESC'); $s->execute([$selectedEvent]); $rsvpList = $s->fetchAll();
    $es = $db->prepare('SELECT title_en FROM events WHERE id=?'); $es->execute([$selectedEvent]); $selEvTitle = $es->fetchColumn();
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="section-header"><h2>RSVP Manager</h2></div>

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start">
  <!-- Event list -->
  <div class="card">
    <div class="card-header"><span class="card-title">Events</span></div>
    <div style="overflow-y:auto;max-height:600px">
      <?php foreach ($events as $ev): ?>
      <a href="?event=<?=$ev['id']?>" style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-bottom:1px solid var(--gray-100);text-decoration:none;color:inherit;<?= $selectedEvent===$ev['id']?'background:var(--cream);border-left:3px solid var(--maroon);':'' ?>">
        <div>
          <div style="font-size:13.5px;font-weight:500"><?= htmlspecialchars($ev['title_en']) ?></div>
          <div style="font-size:11px;color:var(--gray-400)"><?= date('d M Y', strtotime($ev['event_date'])) ?></div>
        </div>
        <span class="badge" style="background:var(--maroon)"><?= $ev['total_rsvp'] ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- RSVP list -->
  <div class="card">
    <?php if ($selectedEvent): ?>
    <div class="card-header">
      <span class="card-title">RSVPs: <?= htmlspecialchars($selEvTitle) ?> (<?= count($rsvpList) ?>)</span>
      <a href="?event=<?=$selectedEvent?>&export=<?=$selectedEvent?>" class="btn btn-sm btn-gold">📥 Export CSV</a>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Attendees</th><th>Registered</th><?php if(can('rsvp','delete')): ?><th>Action</th><?php endif; ?></tr></thead>
        <tbody>
          <?php foreach ($rsvpList as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= htmlspecialchars($r['phone']) ?></td>
            <td><?= htmlspecialchars($r['email'] ?: '—') ?></td>
            <td><strong><?= $r['attendee_count'] ?></strong></td>
            <td><?= date('d M Y h:i A', strtotime($r['registered_at'])) ?></td>
            <?php if(can('rsvp','delete')): ?><td><a href="?delete=<?=$r['id']?>&event=<?=$selectedEvent?>" class="btn btn-sm btn-danger" data-confirm="Remove this RSVP?">🗑</a></td><?php endif; ?>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($rsvpList)): ?><tr><td colspan="6" style="text-align:center;color:var(--gray-400);padding:32px">No RSVPs for this event.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="card-body" style="text-align:center;padding:60px;color:var(--gray-400)">
      <div style="font-size:48px;margin-bottom:12px">📋</div>
      <p>Select an event from the left to view RSVPs.</p>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
