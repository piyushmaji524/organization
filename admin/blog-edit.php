<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_permission('news', 'edit');

$pageTitle     = 'Blog Editor';
$activeSection = 'blog';
$db  = getDB();
$msg = ''; $err = '';

function makeSlug(string $s): string {
    return strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',$s),'-'));
}

$cats = $db->query('SELECT * FROM blog_categories ORDER BY name')->fetchAll();
$tags = $db->query('SELECT * FROM blog_tags ORDER BY name')->fetchAll();

// ── Load for edit ──
$post = null;
if (!empty($_GET['id'])) {
    $s = $db->prepare('SELECT * FROM blog_posts WHERE id=?'); $s->execute([(int)$_GET['id']]); $post = $s->fetch();
    if (!$post) { header('Location: /admin/blog.php'); exit; }
    $ts = $db->prepare('SELECT tag_id FROM blog_post_tags WHERE post_id=?'); $ts->execute([$post['id']]);
    $postTagIds = array_column($ts->fetchAll(), 'tag_id');
} else {
    $postTagIds = [];
}

// ── Save ──
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id          = (int)($_POST['id']??0);
    $titleEn     = trim($_POST['title_en']??'');
    $titleHi     = trim($_POST['title_hi']??'');
    $titleBn     = trim($_POST['title_bn']??'');
    $contentEn   = $_POST['content_en']??'';
    $contentHi   = $_POST['content_hi']??'';
    $contentBn   = $_POST['content_bn']??'';
    $excerptEn   = trim($_POST['excerpt_en']??'');
    $excerptHi   = trim($_POST['excerpt_hi']??'');
    $excerptBn   = trim($_POST['excerpt_bn']??'');
    $slug        = makeSlug(trim($_POST['slug']??'') ?: $titleEn);
    $catId       = (int)($_POST['category_id']??0) ?: null;
    $author      = trim($_POST['author_name']??'SYDC Team');
    $status      = $_POST['status']==='published' ? 'published' : 'draft';
    $pubAt       = $status==='published' ? ($post['published_at']??null) ?? date('Y-m-d H:i:s') : null;
    $selTags     = $_POST['tags']??[];

    if (!$titleEn) { $err = 'English title is required.'; }
    else {
        $thumb = $post['thumbnail']??null;
        if (!empty($_FILES['thumbnail']['tmp_name'])) {
            $f=$_FILES['thumbnail']; $allowed=['image/jpeg','image/png','image/webp'];
            if (in_array($f['type'],$allowed) && $f['size']<=5*1024*1024) {
                $dir = __DIR__.'/../assets/uploads/blog/';
                if (!is_dir($dir)) mkdir($dir,0755,true);
                $ext=strtolower(pathinfo($f['name'],PATHINFO_EXTENSION));
                $fn='thumb_'.time().'_'.bin2hex(random_bytes(4)).'.'.$ext;
                if (move_uploaded_file($f['tmp_name'],$dir.$fn)) $thumb='assets/uploads/blog/'.$fn;
            }
        }
        if (!$thumb && !empty($_POST['existing_thumbnail'])) $thumb = $_POST['existing_thumbnail'];

        try {
            if ($id) {
                $db->prepare('UPDATE blog_posts SET slug=?,title_en=?,title_hi=?,title_bn=?,content_en=?,content_hi=?,content_bn=?,excerpt_en=?,excerpt_hi=?,excerpt_bn=?,thumbnail=?,category_id=?,author_name=?,status=?,published_at=? WHERE id=?')
                   ->execute([$slug,$titleEn,$titleHi,$titleBn,$contentEn,$contentHi,$contentBn,$excerptEn,$excerptHi,$excerptBn,$thumb,$catId,$author,$status,$pubAt,$id]);
            } else {
                $db->prepare('INSERT INTO blog_posts(slug,title_en,title_hi,title_bn,content_en,content_hi,content_bn,excerpt_en,excerpt_hi,excerpt_bn,thumbnail,category_id,author_name,status,published_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
                   ->execute([$slug,$titleEn,$titleHi,$titleBn,$contentEn,$contentHi,$contentBn,$excerptEn,$excerptHi,$excerptBn,$thumb,$catId,$author,$status,$pubAt]);
                $id = (int)$db->lastInsertId();
            }
            // Sync tags
            $db->prepare('DELETE FROM blog_post_tags WHERE post_id=?')->execute([$id]);
            foreach ($selTags as $tid) {
                $tid = (int)$tid;
                if ($tid) $db->prepare('INSERT IGNORE INTO blog_post_tags(post_id,tag_id) VALUES(?,?)')->execute([$id,$tid]);
            }
            header('Location: /admin/blog-edit.php?id='.$id.'&saved=1'); exit;
        } catch(\Exception $e) {
            $err = 'Slug already in use. Change the slug field.';
        }
    }
}
if (!empty($_GET['saved'])) $msg = 'Post saved successfully.';

require_once __DIR__ . '/includes/header.php';
?>

<?php if($msg):?><div class="alert alert-success" data-auto-dismiss="3000">✅ <?=htmlspecialchars($msg)?></div><?php endif;?>
<?php if($err):?><div class="alert alert-danger">⚠ <?=htmlspecialchars($err)?></div><?php endif;?>

<div class="section-header">
  <h2><?=$post?'Edit Post':'New Blog Post'?></h2>
  <a href="/admin/blog.php" class="btn btn-outline btn-sm">← Back to Blog</a>
</div>

<form method="POST" enctype="multipart/form-data" id="blog-form">
  <?php if($post):?><input type="hidden" name="id" value="<?=$post['id']?>">
    <input type="hidden" name="existing_thumbnail" value="<?=htmlspecialchars($post['thumbnail']??'')?>">
  <?php endif;?>
  <!-- Hidden content inputs filled by Quill on submit -->
  <input type="hidden" name="content_en" id="c_en">
  <input type="hidden" name="content_hi" id="c_hi">
  <input type="hidden" name="content_bn" id="c_bn">

  <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

    <!-- ── Main Editor ── -->
    <div>
      <!-- Lang Tabs -->
      <div style="background:var(--gray-50);border:1px solid var(--gray-200);border-radius:12px;padding:20px;margin-bottom:16px">
        <div style="display:flex;gap:0;margin-bottom:16px;border-bottom:1px solid var(--gray-200)">
          <?php foreach(['en'=>'🇬🇧 English','hi'=>'🇮🇳 हिंदी','bn'=>'🇧🇩 বাংলা'] as $lc=>$ll):?>
          <button type="button" class="ql-lang-tab <?=$lc==='en'?'active':''?>" data-lang="<?=$lc?>"
            style="padding:8px 18px;border:none;background:none;cursor:pointer;font-weight:<?=$lc==='en'?700:400?>;color:<?=$lc==='en'?'var(--maroon)':'var(--gray-500)'?>;border-bottom:<?=$lc==='en'?'2px solid var(--maroon)':'none'?>;margin-bottom:-1px"><?=$ll?></button>
          <?php endforeach;?>
        </div>

        <?php foreach(['en'=>'English','hi'=>'Hindi','bn'=>'Bengali'] as $lc=>$ln):?>
        <div class="ql-lang-panel" data-lang="<?=$lc?>" style="<?=$lc==='en'?'':'display:none'?>">
          <div class="form-group" style="margin-bottom:12px">
            <label>Title (<?=$ln?>) <?=$lc==='en'?'*':''?></label>
            <input type="text" name="title_<?=$lc?>" id="title_<?=$lc?>"
              value="<?=htmlspecialchars($post['title_'.$lc]??'')?>"
              placeholder="Blog title in <?=$ln?>"
              <?=$lc==='en'?'required':''?> style="font-size:16px;font-weight:600">
          </div>
          <div class="form-group" style="margin-bottom:12px">
            <label>Content (<?=$ln?>)</label>
            <!-- Custom Quill toolbar -->
            <div id="toolbar-<?=$lc?>" style="border:1px solid var(--gray-300);border-bottom:none;border-radius:8px 8px 0 0;background:var(--white)">
              <span class="ql-formats"><button class="ql-bold"></button><button class="ql-italic"></button><button class="ql-underline"></button><button class="ql-strike"></button></span>
              <span class="ql-formats"><select class="ql-header"><option selected></option><option value="1"></option><option value="2"></option><option value="3"></option></select></span>
              <span class="ql-formats"><button class="ql-blockquote"></button><button class="ql-code-block"></button></span>
              <span class="ql-formats"><button class="ql-list" value="ordered"></button><button class="ql-list" value="bullet"></button></span>
              <span class="ql-formats"><select class="ql-color"></select><select class="ql-background"></select></span>
              <span class="ql-formats"><select class="ql-align"></select></span>
              <span class="ql-formats"><button class="ql-link"></button><button class="ql-image" title="Insert Image"></button></span>
              <span class="ql-formats">
                <button type="button" id="yt-btn-<?=$lc?>" title="Embed YouTube" style="padding:0 8px;font-size:13px;font-weight:600;color:#FF0000" onclick="insertYouTube('<?=$lc?>')">▶ YT</button>
                <button type="button" id="ev-btn-<?=$lc?>" title="Link Event/News" style="padding:0 8px;font-size:13px;font-weight:600;color:var(--maroon)" onclick="openLinkModal('<?=$lc?>')">🔗 Link</button>
              </span>
              <span class="ql-formats"><button class="ql-clean"></button></span>
            </div>
            <div id="editor-<?=$lc?>" style="min-height:320px;border:1px solid var(--gray-300);border-radius:0 0 8px 8px;background:var(--white);font-size:15px;line-height:1.7"></div>
          </div>
          <div class="form-group">
            <label>Excerpt (<?=$ln?>) <small style="color:var(--gray-400)">~150 words shown in blog list</small></label>
            <textarea name="excerpt_<?=$lc?>" rows="3" placeholder="Short summary for the blog list card…"><?=htmlspecialchars($post['excerpt_'.$lc]??'')?></textarea>
          </div>
        </div>
        <?php endforeach;?>
      </div>
    </div>

    <!-- ── Right Sidebar ── -->
    <div style="position:sticky;top:80px">

      <!-- Publish -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header"><span class="card-title">Publish</span></div>
        <div class="card-body" style="padding:16px">
          <div class="form-group" style="margin-bottom:12px">
            <label>Status</label>
            <select name="status">
              <option value="draft" <?=(!$post||$post['status']==='draft')?'selected':''?>>📝 Draft</option>
              <option value="published" <?=($post&&$post['status']==='published')?'selected':''?>>✅ Published</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:12px">
            <label>Author</label>
            <input type="text" name="author_name" value="<?=htmlspecialchars($post['author_name']??'SYDC Team')?>">
          </div>
          <div class="form-group" style="margin-bottom:16px">
            <label>URL Slug <small style="color:var(--gray-400)">(auto-generated)</small></label>
            <input type="text" name="slug" id="slug-field" value="<?=htmlspecialchars($post['slug']??'')?>" placeholder="url-friendly-slug" style="font-family:monospace;font-size:13px">
          </div>
          <div style="display:flex;gap:8px">
            <button type="submit" class="btn btn-primary" style="flex:1">💾 Save</button>
            <?php if($post&&$post['status']==='published'):?>
              <a href="/blog-post.html?slug=<?=htmlspecialchars($post['slug'])?>" target="_blank" class="btn btn-outline btn-sm">👁 View</a>
            <?php endif;?>
          </div>
        </div>
      </div>

      <!-- Thumbnail -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header"><span class="card-title">Thumbnail</span></div>
        <div class="card-body" style="padding:16px">
          <?php if(!empty($post['thumbnail'])):?>
            <img src="/<?=htmlspecialchars($post['thumbnail'])?>" id="thumb-preview" style="width:100%;height:120px;object-fit:cover;border-radius:8px;margin-bottom:8px">
          <?php else:?>
            <img src="" id="thumb-preview" style="display:none;width:100%;height:120px;object-fit:cover;border-radius:8px;margin-bottom:8px">
          <?php endif;?>
          <input type="file" name="thumbnail" accept="image/*" data-preview="thumb-preview">
          <small style="color:var(--gray-400);display:block;margin-top:4px">JPG/PNG/WEBP, max 5MB</small>
        </div>
      </div>

      <!-- Category -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header"><span class="card-title">Category</span></div>
        <div class="card-body" style="padding:16px">
          <select name="category_id">
            <option value="">— No Category —</option>
            <?php foreach($cats as $c):?>
              <option value="<?=$c['id']?>" <?=($post&&$post['category_id']==$c['id'])?'selected':''?>><?=htmlspecialchars($c['name'])?></option>
            <?php endforeach;?>
          </select>
        </div>
      </div>

      <!-- Tags -->
      <div class="card">
        <div class="card-header"><span class="card-title">Tags</span></div>
        <div class="card-body" style="padding:16px">
          <div style="display:flex;flex-wrap:wrap;gap:8px">
            <?php foreach($tags as $t):?>
            <label style="display:flex;align-items:center;gap:5px;cursor:pointer;padding:4px 10px;border:1px solid var(--gray-200);border-radius:20px;font-size:13px;background:var(--gray-50)">
              <input type="checkbox" name="tags[]" value="<?=$t['id']?>" <?=in_array($t['id'],$postTagIds)?'checked':''?> style="width:14px;height:14px">
              #<?=htmlspecialchars($t['name'])?>
            </label>
            <?php endforeach;?>
          </div>
          <?php if(empty($tags)):?><p style="color:var(--gray-400);font-size:13px">No tags yet. <a href="/admin/blog.php">Add tags →</a></p><?php endif;?>
        </div>
      </div>

    </div>
  </div>
</form>

<!-- Internal Link Modal -->
<div class="modal-backdrop" id="link-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center">
  <div style="background:var(--white);border-radius:12px;width:560px;max-width:95vw;max-height:80vh;display:flex;flex-direction:column">
    <div style="padding:16px 20px;border-bottom:1px solid var(--gray-200);display:flex;justify-content:space-between;align-items:center">
      <strong>Link Event or News</strong>
      <button onclick="closeLinkModal()" style="border:none;background:none;font-size:20px;cursor:pointer">×</button>
    </div>
    <div style="padding:12px 20px;border-bottom:1px solid var(--gray-200)">
      <div style="display:flex;gap:8px;margin-bottom:10px">
        <button onclick="loadLinks('events')" class="btn btn-sm btn-outline link-type-btn" data-type="events">📅 Events</button>
        <button onclick="loadLinks('news')"   class="btn btn-sm btn-outline link-type-btn" data-type="news">📰 News</button>
      </div>
      <input type="text" id="link-search" placeholder="Search…" oninput="filterLinks()" style="width:100%;padding:7px 10px;border:1px solid var(--gray-300);border-radius:6px;font-size:13px">
    </div>
    <div id="link-results" style="overflow-y:auto;flex:1;padding:12px 20px"></div>
  </div>
</div>

<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<style>
.ql-editor { font-size:15px; line-height:1.7; }
.ql-editor iframe { max-width:100%; height:320px; }
</style>
<script>
const editors = {};
let _activeLang = 'en';
let _slugManual = <?=($post&&$post['slug'])?'true':'false'?>;

// ── Quill init ──
<?php foreach(['en','hi','bn'] as $lc): ?>
editors['<?=$lc?>'] = new Quill('#editor-<?=$lc?>', {
  theme: 'snow',
  modules: {
    toolbar: { container: '#toolbar-<?=$lc?>', handlers: { image: () => imageUploadHandler('<?=$lc?>') } }
  }
});
<?php $content = $post ? ($post['content_'.$lc] ?? '') : ''; ?>
<?php if ($content): ?>
editors['<?=$lc?>'].root.innerHTML = <?=json_encode($content)?>;
<?php endif; ?>
<?php endforeach; ?>

// ── Lang tabs ──
document.querySelectorAll('.ql-lang-tab').forEach(btn => {
  btn.addEventListener('click', function() {
    _activeLang = this.dataset.lang;
    document.querySelectorAll('.ql-lang-tab').forEach(b => {
      b.style.fontWeight = '400'; b.style.color = 'var(--gray-500)'; b.style.borderBottom = 'none';
    });
    this.style.fontWeight = '700'; this.style.color = 'var(--maroon)'; this.style.borderBottom = '2px solid var(--maroon)'; this.style.marginBottom = '-1px';
    document.querySelectorAll('.ql-lang-panel').forEach(p => p.style.display = p.dataset.lang === _activeLang ? '' : 'none');
    editors[_activeLang].focus();
  });
});

// ── Auto-slug from EN title ──
document.getElementById('title_en').addEventListener('input', function() {
  if (_slugManual) return;
  document.getElementById('slug-field').value = this.value.toLowerCase().replace(/[^a-z0-9\s]/g,'').trim().replace(/\s+/g,'-');
});
document.getElementById('slug-field').addEventListener('input', () => _slugManual = true);

// ── On submit: copy Quill HTML to hidden inputs ──
document.getElementById('blog-form').addEventListener('submit', function() {
  document.getElementById('c_en').value = editors['en'].root.innerHTML;
  document.getElementById('c_hi').value = editors['hi'].root.innerHTML;
  document.getElementById('c_bn').value = editors['bn'].root.innerHTML;
});

// ── Image upload handler ──
function imageUploadHandler(lang) {
  const input = document.createElement('input');
  input.type = 'file'; input.accept = 'image/*';
  input.click();
  input.addEventListener('change', async () => {
    const file = input.files[0]; if (!file) return;
    const fd = new FormData(); fd.append('image', file);
    const res = await fetch('/api/blog-upload.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
      const range = editors[lang].getSelection(true);
      editors[lang].insertEmbed(range.index, 'image', data.url);
    } else { alert('Upload failed: ' + data.error); }
  });
}

// ── YouTube embed ──
function insertYouTube(lang) {
  const url = prompt('Paste YouTube video URL:');
  if (!url) return;
  let embed = url.replace('watch?v=','embed/').replace('youtu.be/','www.youtube.com/embed/');
  if (!embed.includes('youtube.com/embed/')) { alert('Invalid YouTube URL'); return; }
  const range = editors[lang].getSelection(true);
  editors[lang].insertEmbed(range.index, 'video', embed);
  editors[lang].insertText(range.index + 1, '\n');
}

// ── Internal link modal ──
let _linkLang = 'en'; let _linkData = []; let _linkType = 'events';
function openLinkModal(lang) { _linkLang = lang; document.getElementById('link-modal').style.display = 'flex'; loadLinks('events'); }
function closeLinkModal() { document.getElementById('link-modal').style.display = 'none'; }
async function loadLinks(type) {
  _linkType = type;
  document.querySelectorAll('.link-type-btn').forEach(b => { b.classList.toggle('btn-primary', b.dataset.type===type); b.classList.toggle('btn-outline', b.dataset.type!==type); });
  const res = await fetch('/api/' + type + '.php');
  const data = await res.json();
  _linkData = type==='events' ? (Array.isArray(data.data)?data.data:[]) : (data.data||[]);
  renderLinks();
}
function filterLinks() {
  renderLinks(document.getElementById('link-search').value.toLowerCase());
}
function renderLinks(q='') {
  const el = document.getElementById('link-results');
  const filtered = _linkData.filter(i => {
    const t = (i.title_en||i.name_en||i.title||'').toLowerCase();
    return t.includes(q);
  });
  el.innerHTML = filtered.length ? filtered.map(i => {
    const title = i.title_en || i.name_en || i.title || 'Untitled';
    const href = _linkType==='events' ? `/events.html?id=${i.id}` : `/news.html?id=${i.id}`;
    return `<div onclick="insertLink('${href}','${title.replace(/'/g,"\\'")}')" style="padding:10px 0;border-bottom:1px solid var(--gray-100);cursor:pointer;font-size:14px" onmouseover="this.style.color='var(--maroon)'" onmouseout="this.style.color=''">
      <strong>${title}</strong>
    </div>`;
  }).join('') : '<p style="color:var(--gray-400);text-align:center;padding:24px">Nothing found.</p>';
}
function insertLink(href, text) {
  const range = editors[_linkLang].getSelection(true);
  editors[_linkLang].insertText(range.index, text, 'link', href);
  closeLinkModal();
}
document.getElementById('link-modal').addEventListener('click', function(e){ if(e.target===this) closeLinkModal(); });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
