<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('settings', 'view');

$pageTitle     = 'Site Settings';
$activeSection = 'settings';
$db = getDB();
$msg = '';

function get_set(PDO $db, string $k): string {
    $s = $db->prepare('SELECT setting_value FROM site_settings WHERE setting_key=?'); $s->execute([$k]); return $s->fetchColumn() ?: '';
}
function save_set(PDO $db, string $k, string $v): void {
    $db->prepare('INSERT INTO site_settings (setting_key,setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=?')->execute([$k,$v,$v]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && can('settings','edit')) {
    $tab = $_POST['tab'] ?? 'general';

    if ($tab === 'general' || is_super_admin()) {
        $textKeys = ['site_name_en','site_name_hi','site_name_bn','tagline_en','tagline_hi','tagline_bn',
                     'main_motto_en','main_motto_hi','main_motto_bn','founding_year','notification_email',
                     'contact_address_en','contact_address_hi','contact_address_bn',
                     'contact_email','contact_phone','maps_embed',
                     'stats_members','stats_events','stats_beneficiaries'];
        foreach ($textKeys as $k) save_set($db, $k, trim($_POST[$k] ?? ''));

        // Alert banner
        save_set($db, 'alert_banner_active', isset($_POST['alert_banner_active']) ? '1' : '0');
        foreach (['alert_banner_text_en','alert_banner_text_hi','alert_banner_text_bn'] as $k) {
            save_set($db, $k, trim($_POST[$k] ?? ''));
        }
    }

    // Social links — also allowed for SM Manager
    if ($tab === 'social' || $tab === 'general' || is_super_admin()) {
        foreach (['whatsapp_link','facebook_url','instagram_url','youtube_url','twitter_url'] as $k) {
            save_set($db, $k, trim($_POST[$k] ?? ''));
        }
    }

    // Logo & Favicon uploads
    foreach (['logo' => 'site', 'favicon' => 'site'] as $field => $folder) {
        if (!empty($_FILES[$field]['tmp_name'])) {
            $file = $_FILES[$field];
            if (in_array($file['type'], ['image/jpeg','image/png','image/webp','image/x-icon','image/svg+xml']) && $file['size'] <= 2*1024*1024) {
                $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $fn   = $field . '_' . time() . '.' . $ext;
                $dest = __DIR__ . '/../assets/uploads/' . $folder . '/' . $fn;
                if (move_uploaded_file($file['tmp_name'], $dest)) save_set($db, $field . '_path', 'assets/uploads/' . $folder . '/' . $fn);
            }
        }
    }

    $msg = 'Settings saved successfully.';
}

// Load all settings
$s = [];
$rows = $db->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
foreach ($rows as $r) $s[$r['setting_key']] = $r['setting_value'];

$tab = $_GET['tab'] ?? 'general';

require_once __DIR__ . '/includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success" data-auto-dismiss="3000">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="section-header"><h2>Site Settings</h2></div>

<!-- Tab nav -->
<div style="display:flex;gap:6px;margin-bottom:20px;flex-wrap:wrap">
  <?php foreach(['general'=>'⚙️ General','branding'=>'🎨 Branding','social'=>'📱 Social Links','stats'=>'📊 Stats & Alerts'] as $t=>$l): ?>
    <a href="?tab=<?=$t?>" class="btn btn-sm <?= $tab===$t?'btn-primary':'btn-outline' ?>"><?=$l?></a>
  <?php endforeach; ?>
</div>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">

  <?php if ($tab === 'general'): ?>
  <div class="card">
    <div class="card-header"><span class="card-title">⚙️ General Settings</span></div>
    <div class="card-body">
      <div class="form-grid">
        <div class="form-group"><label>Site Name (English)</label><input type="text" name="site_name_en" value="<?= htmlspecialchars($s['site_name_en']??'') ?>"></div>
        <div class="form-group"><label>Site Name (Hindi)</label><input type="text" name="site_name_hi" value="<?= htmlspecialchars($s['site_name_hi']??'') ?>"></div>
        <div class="form-group"><label>Site Name (Bengali)</label><input type="text" name="site_name_bn" value="<?= htmlspecialchars($s['site_name_bn']??'') ?>"></div>
        <div class="form-group"><label>Tagline (English)</label><input type="text" name="tagline_en" value="<?= htmlspecialchars($s['tagline_en']??'') ?>"></div>
        <div class="form-group"><label>Tagline (Hindi)</label><input type="text" name="tagline_hi" value="<?= htmlspecialchars($s['tagline_hi']??'') ?>"></div>
        <div class="form-group"><label>Tagline (Bengali)</label><input type="text" name="tagline_bn" value="<?= htmlspecialchars($s['tagline_bn']??'') ?>"></div>
        <div class="form-group"><label>Main Motto (English)</label><input type="text" name="main_motto_en" value="<?= htmlspecialchars($s['main_motto_en']??'') ?>"></div>
        <div class="form-group"><label>Main Motto (Hindi)</label><input type="text" name="main_motto_hi" value="<?= htmlspecialchars($s['main_motto_hi']??'') ?>"></div>
        <div class="form-group"><label>Main Motto (Bengali)</label><input type="text" name="main_motto_bn" value="<?= htmlspecialchars($s['main_motto_bn']??'') ?>"></div>
        <div class="form-group"><label>Founding Year</label><input type="number" name="founding_year" value="<?= htmlspecialchars($s['founding_year']??'2024') ?>" min="2000" max="2099"></div>
        <div class="form-group"><label>Notification Email</label><input type="email" name="notification_email" value="<?= htmlspecialchars($s['notification_email']??'') ?>"></div>
        <div class="form-group"><label>Contact Email (Public)</label><input type="email" name="contact_email" value="<?= htmlspecialchars($s['contact_email']??'') ?>"></div>
        <div class="form-group"><label>Contact Phone (Public)</label><input type="text" name="contact_phone" value="<?= htmlspecialchars($s['contact_phone']??'') ?>"></div>
        <div class="form-group full"><label>Address (English)</label><textarea name="contact_address_en" rows="2"><?= htmlspecialchars($s['contact_address_en']??'') ?></textarea></div>
        <div class="form-group full"><label>Address (Hindi)</label><textarea name="contact_address_hi" rows="2"><?= htmlspecialchars($s['contact_address_hi']??'') ?></textarea></div>
        <div class="form-group full"><label>Address (Bengali)</label><textarea name="contact_address_bn" rows="2"><?= htmlspecialchars($s['contact_address_bn']??'') ?></textarea></div>
        <div class="form-group full"><label>Google Maps Embed (paste iframe code)</label><textarea name="maps_embed" rows="3"><?= htmlspecialchars($s['maps_embed']??'') ?></textarea></div>
      </div>
    </div>
  </div>

  <?php elseif ($tab === 'branding'): ?>
  <div class="card">
    <div class="card-header"><span class="card-title">🎨 Logo & Favicon</span></div>
    <div class="card-body">
      <div class="form-grid">
        <div class="form-group">
          <label>Logo</label>
          <?php if(!empty($s['logo_path'])): ?><img src="/<?= htmlspecialchars($s['logo_path']) ?>" class="img-preview-lg" style="display:block;margin-bottom:8px"><?php endif; ?>
          <input type="file" name="logo" accept="image/*">
          <span class="form-hint">PNG/SVG with transparent background recommended</span>
        </div>
        <div class="form-group">
          <label>Favicon</label>
          <?php if(!empty($s['favicon_path'])): ?><img src="/<?= htmlspecialchars($s['favicon_path']) ?>" class="img-preview" style="display:block;margin-bottom:8px"><?php endif; ?>
          <input type="file" name="favicon" accept="image/*">
          <span class="form-hint">ICO or PNG 32x32 recommended</span>
        </div>
      </div>
    </div>
  </div>

  <?php elseif ($tab === 'social'): ?>
  <div class="card">
    <div class="card-header"><span class="card-title">📱 Social Media Links</span></div>
    <div class="card-body">
      <div class="form-grid">
        <div class="form-group"><label>WhatsApp Link</label><input type="url" name="whatsapp_link" value="<?= htmlspecialchars($s['whatsapp_link']??'') ?>" placeholder="https://wa.me/91XXXXXXXXXX"></div>
        <div class="form-group"><label>Facebook URL</label><input type="url" name="facebook_url" value="<?= htmlspecialchars($s['facebook_url']??'') ?>" placeholder="https://facebook.com/..."></div>
        <div class="form-group"><label>Instagram URL</label><input type="url" name="instagram_url" value="<?= htmlspecialchars($s['instagram_url']??'') ?>" placeholder="https://instagram.com/..."></div>
        <div class="form-group"><label>YouTube URL</label><input type="url" name="youtube_url" value="<?= htmlspecialchars($s['youtube_url']??'') ?>" placeholder="https://youtube.com/@..."></div>
        <div class="form-group"><label>Twitter / X URL</label><input type="url" name="twitter_url" value="<?= htmlspecialchars($s['twitter_url']??'') ?>" placeholder="https://x.com/..."></div>
      </div>
    </div>
  </div>

  <?php elseif ($tab === 'stats'): ?>
  <div class="card">
    <div class="card-header"><span class="card-title">📊 Homepage Stats & Alert Banner</span></div>
    <div class="card-body">
      <p style="color:var(--gray-400);margin-bottom:16px;font-size:13px">These stats are displayed as counters on the homepage hero section.</p>
      <div class="form-grid">
        <div class="form-group"><label>Total Members Count</label><input type="number" name="stats_members" value="<?= htmlspecialchars($s['stats_members']??'50') ?>"></div>
        <div class="form-group"><label>Events Conducted Count</label><input type="number" name="stats_events" value="<?= htmlspecialchars($s['stats_events']??'12') ?>"></div>
        <div class="form-group"><label>Beneficiaries Count</label><input type="number" name="stats_beneficiaries" value="<?= htmlspecialchars($s['stats_beneficiaries']??'500') ?>"></div>
      </div>
      <hr style="margin:20px 0;border-color:var(--gray-200)">
      <p style="font-weight:600;margin-bottom:12px">🔔 Alert Banner (shows at top of website)</p>
      <div class="form-grid">
        <div class="form-group" style="align-self:flex-end">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="alert_banner_active" value="1" <?= ($s['alert_banner_active']??'0')==='1'?'checked':'' ?>>
            Enable Alert Banner
          </label>
        </div>
        <div class="form-group"><label>Banner Text (English)</label><input type="text" name="alert_banner_text_en" value="<?= htmlspecialchars($s['alert_banner_text_en']??'') ?>"></div>
        <div class="form-group"><label>Banner Text (Hindi)</label><input type="text" name="alert_banner_text_hi" value="<?= htmlspecialchars($s['alert_banner_text_hi']??'') ?>"></div>
        <div class="form-group"><label>Banner Text (Bengali)</label><input type="text" name="alert_banner_text_bn" value="<?= htmlspecialchars($s['alert_banner_text_bn']??'') ?>"></div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if (can('settings','edit')): ?>
    <div style="margin-top:16px"><button type="submit" class="btn btn-primary">💾 Save Settings</button></div>
  <?php endif; ?>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
