<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Already logged in → redirect to dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: /admin/index.php'); exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error = 'Please enter username and password.';
    } else {
        $db = getDB();
        $stmt = $db->prepare('
            SELECT a.*, r.role_key, r.display_name AS role_display
            FROM admins a
            JOIN roles r ON a.role_id = r.id
            WHERE a.username = ? AND a.is_active = 1
        ');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']     = $admin['id'];
            $_SESSION['admin_name']   = $admin['name'];
            $_SESSION['role_id']      = $admin['role_id'];
            $_SESSION['role_key']     = $admin['role_key'];
            $_SESSION['role_display'] = $admin['role_display'];
            $_SESSION['last_activity'] = time();
            $_SESSION['permissions']  = get_permissions($admin['role_id']);

            header('Location: /admin/index.php'); exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

$timeout = !empty($_GET['timeout']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — SYDC</title>
<link rel="stylesheet" href="/admin/assets/css/admin.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="login-page">
  <div class="login-card">
    <div class="login-logo">
      <span class="login-logo-icon">⚜</span>
      <h1>Sarak Youth Development Council</h1>
      <p>Admin Panel Login</p>
    </div>

    <?php if ($timeout): ?>
      <div class="alert alert-warning">⏱ Session expired. Please log in again.</div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="login-form">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autocomplete="username"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" placeholder="Enter your username">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
      </div>
      <button type="submit" class="btn btn-primary login-btn">Sign In →</button>
    </form>

    <p style="text-align:center;margin-top:20px;font-size:12px;color:var(--gray-400);">
      © <?= date('Y') ?> Sarak Youth Development Council · Powered by Gunayatan
    </p>
  </div>
</div>
</body>
</html>
