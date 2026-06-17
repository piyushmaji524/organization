<?php
// Admin shared header — requires $pageTitle and $activeSection to be set
if (!isset($pageTitle)) $pageTitle = 'Admin Panel';
if (!isset($activeSection)) $activeSection = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?> — SYDC Admin</title>
<link rel="stylesheet" href="/admin/assets/css/admin.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo">
      <span class="brand-icon">⚜</span>
      <div>
        <div class="brand-name">SYDC</div>
        <div class="brand-sub">Admin Panel</div>
      </div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <a href="/admin/index.php"              class="nav-item <?= $activeSection==='dashboard'         ?'active':'' ?>"><span class="ni">📊</span> Dashboard</a>

    <?php if(can('members','view')): ?>
    <a href="/admin/members.php"            class="nav-item <?= $activeSection==='members'           ?'active':'' ?>"><span class="ni">👥</span> Members</a>
    <?php endif; ?>

    <?php if(can('events','view')): ?>
    <a href="/admin/events.php"             class="nav-item <?= $activeSection==='events'            ?'active':'' ?>"><span class="ni">📅</span> Events</a>
    <?php endif; ?>

    <?php if(can('rsvp','view')): ?>
    <a href="/admin/rsvp.php"               class="nav-item <?= $activeSection==='rsvp'              ?'active':'' ?>"><span class="ni">✅</span> RSVP</a>
    <?php endif; ?>

    <?php if(can('news','view')): ?>
    <a href="/admin/news.php"               class="nav-item <?= $activeSection==='news'              ?'active':'' ?>"><span class="ni">📰</span> News</a>
    <a href="/admin/blog.php"               class="nav-item <?= $activeSection==='blog'              ?'active':'' ?>"><span class="ni">✍️</span> Blog</a>
    <?php endif; ?>

    <?php if(can('gallery','view')): ?>
    <a href="/admin/gallery.php"            class="nav-item <?= $activeSection==='gallery'           ?'active':'' ?>"><span class="ni">🖼️</span> Gallery</a>
    <?php endif; ?>

    <?php if(can('messages','view')): ?>
    <a href="/admin/messages.php"           class="nav-item <?= $activeSection==='messages'          ?'active':'' ?>">
      <span class="ni">✉️</span> Messages
      <?php
        $unread = 0;
        try { $u=$GLOBALS['_db']??null; if(!$u){require_once __DIR__.'/../../config/db.php';$u=getDB();}
          $unread=(int)$u->query("SELECT COUNT(*) FROM messages WHERE is_read=0")->fetchColumn(); } catch(Exception $e){}
        if($unread>0) echo "<span class='badge'>$unread</span>";
      ?>
    </a>
    <?php endif; ?>

    <?php if(can('applications','view')): ?>
    <a href="/admin/applications.php"       class="nav-item <?= $activeSection==='applications'      ?'active':'' ?>">
      <span class="ni">📋</span> Applications
      <?php
        $pending = 0;
        try { $u=$GLOBALS['_db']??null; if(!$u){$u=getDB();}
          $pending=(int)$u->query("SELECT COUNT(*) FROM applications WHERE status='pending'")->fetchColumn(); } catch(Exception $e){}
        if($pending>0) echo "<span class='badge badge-gold'>$pending</span>";
      ?>
    </a>
    <?php endif; ?>

    <?php if(can('applications','view')): ?>
    <a href="/admin/general-members.php"    class="nav-item <?= $activeSection==='general_members'   ?'active':'' ?>"><span class="ni">🪪</span> General Members</a>
    <?php endif; ?>

    <?php if(can('donate','view')): ?>
    <a href="/admin/donate.php"             class="nav-item <?= $activeSection==='donate'            ?'active':'' ?>"><span class="ni">💛</span> Donate Settings</a>
    <?php endif; ?>

    <?php if(can('settings','view')): ?>
    <a href="/admin/settings.php"           class="nav-item <?= $activeSection==='settings'          ?'active':'' ?>"><span class="ni">⚙️</span> Site Settings</a>
    <?php endif; ?>

    <?php if(can('content','view')): ?>
    <a href="/admin/content.php"            class="nav-item <?= $activeSection==='content'           ?'active':'' ?>"><span class="ni">🌐</span> Content/Translations</a>
    <?php endif; ?>

    <?php if(is_super_admin()): ?>
    <div class="nav-divider">Super Admin</div>
    <a href="/admin/role-permissions.php"   class="nav-item <?= $activeSection==='role_permissions'  ?'active':'' ?>"><span class="ni">🔐</span> Role Permissions</a>
    <a href="/admin/admin-users.php"        class="nav-item <?= $activeSection==='admin_users'       ?'active':'' ?>"><span class="ni">👤</span> Admin Users</a>
    <?php endif; ?>

    <div class="nav-divider"></div>
    <a href="/" target="_blank" class="nav-item"><span class="ni">🌐</span> View Website</a>
    <a href="/admin/logout.php" class="nav-item nav-logout"><span class="ni">🚪</span> Logout</a>
  </nav>
</aside>

<!-- Main content wrapper -->
<div class="main-wrapper">
  <!-- Top bar -->
  <header class="topbar">
    <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
    <div class="topbar-title"><?= htmlspecialchars($pageTitle) ?></div>
    <div class="topbar-user">
      <span class="user-role"><?= admin_role_label() ?></span>
      <span class="user-name">👋 <?= admin_display_name() ?></span>
    </div>
  </header>
  <main class="page-content">
