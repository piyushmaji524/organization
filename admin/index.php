<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_auth();

$pageTitle    = 'Dashboard';
$activeSection = 'dashboard';

$db = getDB();

// Stats
$totalMembers     = (int)$db->query("SELECT COUNT(*) FROM members WHERE is_active=1")->fetchColumn();
$totalEvents      = (int)$db->query("SELECT COUNT(*) FROM events")->fetchColumn();
$upcomingEvents   = (int)$db->query("SELECT COUNT(*) FROM events WHERE status='upcoming' AND event_date >= CURDATE()")->fetchColumn();
$totalRsvp        = (int)$db->query("SELECT COALESCE(SUM(attendee_count),0) FROM rsvp")->fetchColumn();
$unreadMessages   = (int)$db->query("SELECT COUNT(*) FROM messages WHERE is_read=0")->fetchColumn();
$pendingApps      = (int)$db->query("SELECT COUNT(*) FROM applications WHERE status='pending'")->fetchColumn();
$totalNews        = (int)$db->query("SELECT COUNT(*) FROM news")->fetchColumn();
$totalGallery     = (int)$db->query("SELECT COUNT(*) FROM gallery")->fetchColumn();

// Recent items
$recentMessages  = $db->query("SELECT name, subject, created_at FROM messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentApps      = $db->query("SELECT full_name, phone, status, applied_at FROM applications ORDER BY applied_at DESC LIMIT 5")->fetchAll();
$upcomingList    = $db->query("SELECT title_en, event_date, type FROM events WHERE status='upcoming' AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="section-header">
  <h2>Welcome back, <?= admin_display_name() ?> 👋</h2>
  <span class="user-role"><?= admin_role_label() ?></span>
</div>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card">
    <span class="stat-icon">👥</span>
    <div><div class="stat-value"><?= $totalMembers ?></div><div class="stat-label">Active Members</div></div>
  </div>
  <div class="stat-card gold">
    <span class="stat-icon">📅</span>
    <div><div class="stat-value"><?= $upcomingEvents ?></div><div class="stat-label">Upcoming Events</div></div>
  </div>
  <div class="stat-card green">
    <span class="stat-icon">✅</span>
    <div><div class="stat-value"><?= $totalRsvp ?></div><div class="stat-label">Total RSVPs</div></div>
  </div>
  <div class="stat-card blue">
    <span class="stat-icon">📋</span>
    <div><div class="stat-value"><?= $pendingApps ?></div><div class="stat-label">Pending Applications</div></div>
  </div>
  <div class="stat-card">
    <span class="stat-icon">✉️</span>
    <div><div class="stat-value"><?= $unreadMessages ?></div><div class="stat-label">Unread Messages</div></div>
  </div>
  <div class="stat-card gold">
    <span class="stat-icon">📰</span>
    <div><div class="stat-value"><?= $totalNews ?></div><div class="stat-label">News Articles</div></div>
  </div>
  <div class="stat-card green">
    <span class="stat-icon">🖼️</span>
    <div><div class="stat-value"><?= $totalGallery ?></div><div class="stat-label">Gallery Photos</div></div>
  </div>
  <div class="stat-card blue">
    <span class="stat-icon">📅</span>
    <div><div class="stat-value"><?= $totalEvents ?></div><div class="stat-label">Total Events</div></div>
  </div>
</div>

<!-- Quick grids -->
<div class="quick-grid">

  <!-- Upcoming events -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">📅 Upcoming Events</span>
      <?php if(can('events','view')): ?><a href="/admin/events.php" class="btn btn-sm btn-outline">View All</a><?php endif; ?>
    </div>
    <div class="card-body">
      <?php if ($upcomingList): foreach ($upcomingList as $ev): ?>
        <div class="recent-item">
          <div class="recent-icon">📅</div>
          <div class="recent-text">
            <div class="recent-title"><?= htmlspecialchars($ev['title_en']) ?></div>
            <div class="recent-meta"><?= date('d M Y', strtotime($ev['event_date'])) ?> · <?= ucfirst($ev['type']) ?></div>
          </div>
        </div>
      <?php endforeach; else: ?>
        <p style="color:var(--gray-400);font-size:13px">No upcoming events.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Recent messages -->
  <?php if (can('messages','view')): ?>
  <div class="card">
    <div class="card-header">
      <span class="card-title">✉️ Recent Messages <?php if($unreadMessages): ?><span class="badge"><?= $unreadMessages ?></span><?php endif; ?></span>
      <a href="/admin/messages.php" class="btn btn-sm btn-outline">View All</a>
    </div>
    <div class="card-body">
      <?php if ($recentMessages): foreach ($recentMessages as $msg): ?>
        <div class="recent-item">
          <div class="recent-icon">✉️</div>
          <div class="recent-text">
            <div class="recent-title"><?= htmlspecialchars($msg['name']) ?></div>
            <div class="recent-meta"><?= htmlspecialchars($msg['subject'] ?: '(no subject)') ?> · <?= date('d M', strtotime($msg['created_at'])) ?></div>
          </div>
        </div>
      <?php endforeach; else: ?>
        <p style="color:var(--gray-400);font-size:13px">No messages yet.</p>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Pending applications -->
  <?php if (can('applications','view')): ?>
  <div class="card">
    <div class="card-header">
      <span class="card-title">📋 Recent Applications <?php if($pendingApps): ?><span class="badge badge-gold"><?= $pendingApps ?></span><?php endif; ?></span>
      <a href="/admin/applications.php" class="btn btn-sm btn-outline">View All</a>
    </div>
    <div class="card-body">
      <?php if ($recentApps): foreach ($recentApps as $app): ?>
        <div class="recent-item">
          <div class="recent-icon">👤</div>
          <div class="recent-text">
            <div class="recent-title"><?= htmlspecialchars($app['full_name']) ?></div>
            <div class="recent-meta">
              <?= htmlspecialchars($app['phone']) ?> ·
              <span class="status-badge status-<?= $app['status'] ?>"><?= ucfirst($app['status']) ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; else: ?>
        <p style="color:var(--gray-400);font-size:13px">No applications yet.</p>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

</div><!-- /.quick-grid -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>
