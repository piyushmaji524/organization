<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('news', 'view');

$pageTitle     = 'Blog Manager';
$activeSection = 'blog';
$db  = getDB();
$msg = ''; $err = '';

function makeSlug(string $s): string {
    return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $s), '-'));
}

// ── Category CRUD ──
if ($_SERVER['REQUEST_METHOD']==='POST' && can('news','edit')) {
    $fa = $_POST['form_action'] ?? '';

    if ($fa === 'save_category') {
        $name  = trim($_POST['cat_name'] ?? '');
        $color = trim($_POST['cat_color'] ?? '#C9A84C');
        $cid   = (int)($_POST['cat_id'] ?? 0);
        if ($name) {
            $slug = makeSlug($name);
            if ($cid) {
                $db->prepare('UPDATE blog_categories SET name=?,slug=?,color=? WHERE id=?')->execute([$name,$slug,$color,$cid]);
                $msg = 'Category updated.';
            } else {
                try { $db->prepare('INSERT INTO blog_categories(name,slug,color) VALUES(?,?,?)')->execute([$name,$slug,$color]); $msg='Category added.'; }
                catch(\Exception $e){ $err='Slug already exists. Try a different name.'; }
            }
        }
    }
    if ($fa === 'delete_category' && can('news','delete')) {
        $db->prepare('DELETE FROM blog_categories WHERE id=?')->execute([(int)($_POST['cat_id']??0)]);
        $msg = 'Category deleted.';
    }
    if ($fa === 'save_tag') {
        $name = trim($_POST['tag_name'] ?? '');
        $tid  = (int)($_POST['tag_id'] ?? 0);
        if ($name) {
            $slug = makeSlug($name);
            if ($tid) {
                $db->prepare('UPDATE blog_tags SET name=?,slug=? WHERE id=?')->execute([$name,$slug,$tid]);
                $msg = 'Tag updated.';
            } else {
                try { $db->prepare('INSERT INTO blog_tags(name,slug) VALUES(?,?)')->execute([$name,$slug]); $msg='Tag added.'; }
                catch(\Exception $e){ $err='Tag already exists.'; }
            }
        }
    }
    if ($fa === 'delete_tag' && can('news','delete')) {
        $db->prepare('DELETE FROM blog_tags WHERE id=?')->execute([(int)($_POST['tag_id']??0)]);
        $msg = 'Tag deleted.';
    }
    if ($fa === 'delete_post' && can('news','delete')) {
        $db->prepare('DELETE FROM blog_posts WHERE id=?')->execute([(int)($_POST['post_id']??0)]);
        $msg = 'Post deleted.';
    }
    if ($fa === 'toggle_status' && can('news','edit')) {
        $pid = (int)($_POST['post_id']??0);
        $st  = $db->prepare('SELECT status FROM blog_posts WHERE id=?'); $st->execute([$pid]); $cur=$st->fetchColumn();
        $new = $cur==='published' ? 'draft' : 'published';
        $pa  = $new==='published' ? date('Y-m-d H:i:s') : null;
        $db->prepare('UPDATE blog_posts SET status=?,published_at=? WHERE id=?')->execute([$new,$pa,$pid]);
        $msg = "Post $new.";
    }
}

$cats  = $db->query('SELECT * FROM blog_categories ORDER BY name')->fetchAll();
$tags  = $db->query('SELECT bt.*,(SELECT COUNT(*) FROM blog_post_tags WHERE tag_id=bt.id) AS post_count FROM blog_tags bt ORDER BY name')->fetchAll();

$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['q'] ?? '');
$where  = []; $params = [];
if ($filter === 'published') { $where[] = "status='published'"; }
if ($filter === 'draft')     { $where[] = "status='draft'"; }
if ($search) { $where[] = "(title_en LIKE ? OR title_hi LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
$wStr = $where ? 'WHERE '.implode(' AND ',$where) : '';

$posts = $db->prepare("SELECT p.*,c.name AS cat_name,c.color AS cat_color FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id=c.id $wStr ORDER BY p.created_at DESC");
$posts->execute($params); $posts = $posts->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if($msg):?><div class="alert alert-success" data-auto-dismiss="3000">✅ <?=htmlspecialchars($msg)?></div><?php endif;?>
<?php if($err):?><div class="alert alert-danger">⚠ <?=htmlspecialchars($err)?></div><?php endif;?>

<!-- Page Tabs -->
<div class="section-header" style="margin-bottom:0">
  <h2>Blog Manager</h2>
  <?php if(can('news','edit')):?>
    <a href="/admin/blog-edit.php" class="btn btn-primary">+ New Post</a>
  <?php endif;?>
</div>
<div style="display:flex;gap:0;border-bottom:2px solid var(--gray-200);margin-bottom:20px">
  <button class="page-tab active" data-tab="posts"   style="padding:10px 20px;border:none;background:none;cursor:pointer;font-weight:600;color:var(--maroon);border-bottom:2px solid var(--maroon);margin-bottom:-2px">📝 Posts (<?=count($posts)?>)</button>
  <button class="page-tab"        data-tab="cats"    style="padding:10px 20px;border:none;background:none;cursor:pointer;font-weight:500;color:var(--gray-500)">🗂 Categories (<?=count($cats)?>)</button>
  <button class="page-tab"        data-tab="tags"    style="padding:10px 20px;border:none;background:none;cursor:pointer;font-weight:500;color:var(--gray-500)">🏷 Tags (<?=count($tags)?>)</button>
</div>

<!-- POSTS TAB -->
<div id="tab-posts">
  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;align-items:center">
    <form method="GET" style="display:flex;gap:8px;flex:1;min-width:240px">
      <input type="search" name="q" value="<?=htmlspecialchars($search)?>" placeholder="🔍 Search posts…" style="flex:1;padding:8px 12px;border:1px solid var(--gray-300);border-radius:8px;font-size:14px">
      <button class="btn btn-outline btn-sm" type="submit">Search</button>
    </form>
    <?php foreach(['all'=>'All','published'=>'✅ Published','draft'=>'📝 Draft'] as $k=>$l):?>
      <a href="?filter=<?=$k?>&q=<?=urlencode($search)?>" class="btn btn-sm <?=$filter===$k?'btn-primary':'btn-outline'?>"><?=$l?></a>
    <?php endforeach;?>
  </div>
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Thumbnail</th><th>Title</th><th>Category</th><th>Author</th><th>Status</th><th>Views</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach($posts as $p):?>
          <tr>
            <td><?php if($p['thumbnail']):?><img src="/<?=htmlspecialchars($p['thumbnail'])?>" style="width:60px;height:40px;object-fit:cover;border-radius:6px"><?php else:?><div style="width:60px;height:40px;background:var(--gray-100);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:18px">📝</div><?php endif;?></td>
            <td style="max-width:220px"><strong style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?=htmlspecialchars($p['title_en'])?></strong><small style="color:var(--gray-400)"><?=htmlspecialchars(mb_strimwidth($p['title_hi']??'',0,40,'…'))?></small></td>
            <td><?php if($p['cat_name']):?><span style="background:<?=htmlspecialchars($p['cat_color'])?>22;color:<?=htmlspecialchars($p['cat_color'])?>;padding:2px 8px;border-radius:12px;font-size:12px;font-weight:600"><?=htmlspecialchars($p['cat_name'])?></span><?php else:?>—<?php endif;?></td>
            <td style="font-size:13px"><?=htmlspecialchars($p['author_name'])?></td>
            <td>
              <?php if(can('news','edit')):?>
              <form method="POST" style="display:inline">
                <input type="hidden" name="form_action" value="toggle_status">
                <input type="hidden" name="post_id" value="<?=$p['id']?>">
                <button type="submit" class="status-badge <?=$p['status']==='published'?'status-approved':'status-pending'?>" style="border:none;cursor:pointer;background:none;padding:0">
                  <?=$p['status']==='published'?'✅ Published':'📝 Draft'?>
                </button>
              </form>
              <?php else:?>
                <span class="status-badge <?=$p['status']==='published'?'status-approved':'status-pending'?>"><?=ucfirst($p['status'])?></span>
              <?php endif;?>
            </td>
            <td style="font-size:13px;color:var(--gray-400)"><?=$p['views']?></td>
            <td style="font-size:12px;color:var(--gray-400)"><?=date('d M Y',strtotime($p['created_at']))?></td>
            <td>
              <div class="td-actions">
                <?php if(can('news','edit')):?>
                  <a href="/admin/blog-edit.php?id=<?=$p['id']?>" class="btn btn-sm btn-outline">✏️ Edit</a>
                <?php endif;?>
                <?php if(can('news','delete')):?>
                  <form method="POST" style="display:inline">
                    <input type="hidden" name="form_action" value="delete_post">
                    <input type="hidden" name="post_id" value="<?=$p['id']?>">
                    <button type="submit" class="btn btn-sm btn-danger" data-confirm="Delete '<?=htmlspecialchars(addslashes($p['title_en']))?>'?">🗑</button>
                  </form>
                <?php endif;?>
              </div>
            </td>
          </tr>
          <?php endforeach;?>
          <?php if(empty($posts)):?><tr><td colspan="8" style="text-align:center;color:var(--gray-400);padding:32px">No posts found.</td></tr><?php endif;?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- CATEGORIES TAB -->
<div id="tab-cats" style="display:none">
  <?php if(can('news','edit')):?>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">Add / Edit Category</span></div>
    <div class="card-body">
      <form method="POST" id="cat-form">
        <input type="hidden" name="form_action" value="save_category">
        <input type="hidden" name="cat_id" id="cat_id" value="">
        <div class="form-grid">
          <div class="form-group"><label>Category Name</label><input type="text" name="cat_name" id="cat_name" required placeholder="e.g. Youth Affairs"></div>
          <div class="form-group"><label>Color</label><input type="color" name="cat_color" id="cat_color" value="#C9A84C" style="height:40px;padding:4px"></div>
        </div>
        <div style="display:flex;gap:8px;margin-top:12px">
          <button type="submit" class="btn btn-primary">Save Category</button>
          <button type="button" onclick="resetCatForm()" class="btn btn-outline">Reset</button>
        </div>
      </form>
    </div>
  </div>
  <?php endif;?>
  <div class="card">
    <div class="table-wrap"><table>
      <thead><tr><th>Color</th><th>Name</th><th>Slug</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach($cats as $c):?>
        <tr>
          <td><span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:<?=htmlspecialchars($c['color'])?>"></span></td>
          <td><strong><?=htmlspecialchars($c['name'])?></strong></td>
          <td><code style="font-size:12px"><?=htmlspecialchars($c['slug'])?></code></td>
          <td>
            <div class="td-actions">
              <?php if(can('news','edit')):?>
                <button onclick="editCat(<?=$c['id']?>,<?=htmlspecialchars(json_encode($c['name']))?>,<?=htmlspecialchars(json_encode($c['color']))?> )" class="btn btn-sm btn-outline">✏️ Edit</button>
              <?php endif;?>
              <?php if(can('news','delete')):?>
                <form method="POST" style="display:inline"><input type="hidden" name="form_action" value="delete_category"><input type="hidden" name="cat_id" value="<?=$c['id']?>"><button type="submit" class="btn btn-sm btn-danger" data-confirm="Delete category '<?=htmlspecialchars(addslashes($c['name']))?>'?">🗑</button></form>
              <?php endif;?>
            </div>
          </td>
        </tr>
        <?php endforeach;?>
        <?php if(empty($cats)):?><tr><td colspan="4" style="text-align:center;color:var(--gray-400);padding:24px">No categories yet.</td></tr><?php endif;?>
      </tbody>
    </table></div>
  </div>
</div>

<!-- TAGS TAB -->
<div id="tab-tags" style="display:none">
  <?php if(can('news','edit')):?>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">Add / Edit Tag</span></div>
    <div class="card-body">
      <form method="POST" id="tag-form">
        <input type="hidden" name="form_action" value="save_tag">
        <input type="hidden" name="tag_id" id="tag_id" value="">
        <div class="form-grid">
          <div class="form-group"><label>Tag Name</label><input type="text" name="tag_name" id="tag_name" required placeholder="e.g. Youth, Education, Sports"></div>
        </div>
        <div style="display:flex;gap:8px;margin-top:12px">
          <button type="submit" class="btn btn-primary">Save Tag</button>
          <button type="button" onclick="resetTagForm()" class="btn btn-outline">Reset</button>
        </div>
      </form>
    </div>
  </div>
  <?php endif;?>
  <div class="card">
    <div class="table-wrap"><table>
      <thead><tr><th>Tag Name</th><th>Slug</th><th>Posts</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach($tags as $t):?>
        <tr>
          <td><span style="background:var(--gray-100);padding:3px 10px;border-radius:20px;font-size:13px">#<?=htmlspecialchars($t['name'])?></span></td>
          <td><code style="font-size:12px"><?=htmlspecialchars($t['slug'])?></code></td>
          <td><?=$t['post_count']?></td>
          <td>
            <div class="td-actions">
              <?php if(can('news','edit')):?>
                <button onclick="editTag(<?=$t['id']?>,<?=htmlspecialchars(json_encode($t['name']))?> )" class="btn btn-sm btn-outline">✏️ Edit</button>
              <?php endif;?>
              <?php if(can('news','delete')):?>
                <form method="POST" style="display:inline"><input type="hidden" name="form_action" value="delete_tag"><input type="hidden" name="tag_id" value="<?=$t['id']?>"><button type="submit" class="btn btn-sm btn-danger" data-confirm="Delete tag #<?=htmlspecialchars(addslashes($t['name']))?>'?">🗑</button></form>
              <?php endif;?>
            </div>
          </td>
        </tr>
        <?php endforeach;?>
        <?php if(empty($tags)):?><tr><td colspan="4" style="text-align:center;color:var(--gray-400);padding:24px">No tags yet.</td></tr><?php endif;?>
      </tbody>
    </table></div>
  </div>
</div>

<script>
// Tab switching
document.querySelectorAll('.page-tab').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.page-tab').forEach(b => {
      b.style.color = 'var(--gray-500)'; b.style.borderBottom = 'none'; b.style.fontWeight = '500';
    });
    this.style.color = 'var(--maroon)'; this.style.borderBottom = '2px solid var(--maroon)'; this.style.marginBottom = '-2px'; this.style.fontWeight = '600';
    ['posts','cats','tags'].forEach(t => document.getElementById('tab-'+t).style.display = 'none');
    document.getElementById('tab-'+this.dataset.tab).style.display = 'block';
  });
});

function editCat(id, name, color) {
  document.getElementById('cat_id').value   = id;
  document.getElementById('cat_name').value = name;
  document.getElementById('cat_color').value = color;
  document.querySelector('[data-tab="cats"]').click();
  document.getElementById('cat_name').focus();
}
function resetCatForm() {
  document.getElementById('cat_id').value = '';
  document.getElementById('cat-form').reset();
}
function editTag(id, name) {
  document.getElementById('tag_id').value   = id;
  document.getElementById('tag_name').value = name;
  document.querySelector('[data-tab="tags"]').click();
  document.getElementById('tag_name').focus();
}
function resetTagForm() {
  document.getElementById('tag_id').value = '';
  document.getElementById('tag-form').reset();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
