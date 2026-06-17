<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('donate', 'view');

$pageTitle     = 'Donate Settings';
$activeSection = 'donate';
$db = getDB();
$msg = '';

function get_setting(PDO $db, string $key): string {
    $s = $db->prepare('SELECT setting_value FROM site_settings WHERE setting_key=?'); $s->execute([$key]); return $s->fetchColumn() ?: '';
}
function save_setting(PDO $db, string $key, string $value): void {
    $db->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?')->execute([$key, $value, $value]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && can('donate','edit')) {
    $keys = ['donate_bank_name','donate_account_no','donate_ifsc','donate_upi'];
    foreach ($keys as $k) save_setting($db, $k, trim($_POST[$k] ?? ''));

    // QR code upload
    if (!empty($_FILES['donate_qr_image']['tmp_name'])) {
        $file = $_FILES['donate_qr_image'];
        if (in_array($file['type'], ['image/jpeg','image/png','image/webp']) && $file['size'] <= 5*1024*1024) {
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fn   = 'qr_' . time() . '.' . $ext;
            $dest = __DIR__ . '/../assets/uploads/donate/' . $fn;
            if (move_uploaded_file($file['tmp_name'], $dest)) save_setting($db, 'donate_qr_image', 'assets/uploads/donate/' . $fn);
        }
    }
    $msg = 'Donate settings saved.';
}

$settings = [];
foreach (['donate_bank_name','donate_account_no','donate_ifsc','donate_upi','donate_qr_image'] as $k) {
    $settings[$k] = get_setting($db, $k);
}

require_once __DIR__ . '/includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success" data-auto-dismiss="3000">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="section-header"><h2>Donate / Sahyog Settings</h2></div>

<div class="card" style="max-width:680px">
  <div class="card-header"><span class="card-title">💛 Bank & UPI Details</span></div>
  <div class="card-body">
    <?php if (!can('donate','edit')): ?><div class="alert alert-info">You have view-only access.</div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
      <div class="form-grid">
        <div class="form-group">
          <label>Bank Name</label>
          <input type="text" name="donate_bank_name" value="<?= htmlspecialchars($settings['donate_bank_name']) ?>" <?= !can('donate','edit')?'readonly':'' ?>>
        </div>
        <div class="form-group">
          <label>Account Number</label>
          <input type="text" name="donate_account_no" value="<?= htmlspecialchars($settings['donate_account_no']) ?>" <?= !can('donate','edit')?'readonly':'' ?>>
        </div>
        <div class="form-group">
          <label>IFSC Code</label>
          <input type="text" name="donate_ifsc" value="<?= htmlspecialchars($settings['donate_ifsc']) ?>" <?= !can('donate','edit')?'readonly':'' ?>>
        </div>
        <div class="form-group">
          <label>UPI ID</label>
          <input type="text" name="donate_upi" value="<?= htmlspecialchars($settings['donate_upi']) ?>" <?= !can('donate','edit')?'readonly':'' ?>>
        </div>
        <div class="form-group">
          <label>UPI QR Code Image</label>
          <?php if ($settings['donate_qr_image']): ?>
            <img src="/<?= htmlspecialchars($settings['donate_qr_image']) ?>" id="qr-prev" class="img-preview-lg" style="display:block;margin-bottom:8px">
          <?php else: ?>
            <img src="" id="qr-prev" class="img-preview-lg" style="display:none;margin-bottom:8px">
          <?php endif; ?>
          <?php if (can('donate','edit')): ?>
            <input type="file" name="donate_qr_image" accept="image/*" data-preview="qr-prev">
          <?php endif; ?>
        </div>
      </div>
      <?php if (can('donate','edit')): ?>
        <div style="margin-top:16px"><button type="submit" class="btn btn-primary">Save Donate Settings</button></div>
      <?php endif; ?>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
