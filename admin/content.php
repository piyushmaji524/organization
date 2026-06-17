<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('content', 'view');

$pageTitle     = 'Content & Translations';
$activeSection = 'content';
$db = getDB();
$msg = '';

function save_s(PDO $db, string $k, string $v): void {
    $db->prepare('INSERT INTO site_settings (setting_key,setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=?')->execute([$k,$v,$v]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && can('content','edit')) {
    $keys = [
        'about_gunayatan_en','about_gunayatan_hi','about_gunayatan_bn',
        'about_sarak_history_en','about_sarak_history_hi','about_sarak_history_bn',
        'vision_en','vision_hi','vision_bn',
        'mission_en','mission_hi','mission_bn',
    ];
    foreach ($keys as $k) save_s($db, $k, trim($_POST[$k] ?? ''));
    $msg = 'Content saved.';
}

$rows = $db->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
$s = [];
foreach ($rows as $r) $s[$r['setting_key']] = $r['setting_value'];

require_once __DIR__ . '/includes/header.php';
?>
<?php if ($msg): ?><div class="alert alert-success" data-auto-dismiss="3000">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="section-header"><h2>Content & Translations</h2></div>

<?php if (!can('content','edit')): ?><div class="alert alert-info">👁 View-only access.</div><?php endif; ?>

<form method="POST">
  <?php
  $sections = [
    'About Gunayatan' => ['about_gunayatan_en','about_gunayatan_hi','about_gunayatan_bn'],
    'Sarak Community History' => ['about_sarak_history_en','about_sarak_history_hi','about_sarak_history_bn'],
    'Vision' => ['vision_en','vision_hi','vision_bn'],
    'Mission' => ['mission_en','mission_hi','mission_bn'],
  ];
  foreach ($sections as $title => $keys):
    $group = str_replace(' ','_', strtolower($title));
  ?>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title"><?= $title ?></span></div>
    <div class="card-body">
      <div class="lang-tabs" data-group="<?=$group?>">
        <button type="button" class="lang-tab active" data-lang="en" data-group="<?=$group?>">English</button>
        <button type="button" class="lang-tab" data-lang="hi" data-group="<?=$group?>">हिंदी</button>
        <button type="button" class="lang-tab" data-lang="bn" data-group="<?=$group?>">বাংলা</button>
      </div>
      <?php foreach(['en','hi','bn'] as $i=>$lc): ?>
      <div class="lang-panel <?=$lc==='en'?'active':''?>" data-lang="<?=$lc?>" data-group="<?=$group?>">
        <textarea name="<?=$keys[$i]?>" rows="5" <?= !can('content','edit')?'readonly':'' ?>><?= htmlspecialchars($s[$keys[$i]]??'') ?></textarea>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if (can('content','edit')): ?>
    <button type="submit" class="btn btn-primary">💾 Save All Content</button>
  <?php endif; ?>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
