<?php
http_response_code(403);
$pageTitle = 'Access Denied';
$activeSection = '';
require_once __DIR__ . '/../config/auth.php';
require_auth();
require_once __DIR__ . '/includes/header.php';
?>
<div style="text-align:center;padding:60px 20px">
  <div style="font-size:64px;margin-bottom:16px">🔒</div>
  <h2 style="color:var(--maroon);font-size:24px;margin-bottom:8px">Access Denied</h2>
  <p style="color:var(--gray-600);max-width:400px;margin:0 auto 24px">You don't have permission to access this section. Contact the Super Admin to request access.</p>
  <a href="/admin/index.php" class="btn btn-primary">← Back to Dashboard</a>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
