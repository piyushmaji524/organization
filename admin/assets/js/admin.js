// ============================================================
// Admin Panel — JavaScript Utilities
// ============================================================

// ── Language tabs ─────────────────────────────────────────
document.querySelectorAll('.lang-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    const group = tab.closest('.lang-tabs').dataset.group;
    document.querySelectorAll(`[data-group="${group}"] .lang-tab`).forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    const lang = tab.dataset.lang;
    document.querySelectorAll(`.lang-panel[data-lang="${lang}"][data-group="${group}"]`).forEach(p => p.classList.add('active'));
    document.querySelectorAll(`.lang-panel[data-group="${group}"]`).forEach(p => { if (p.dataset.lang !== lang) p.classList.remove('active'); });
  });
});

// ── Image preview on file input change ───────────────────
document.querySelectorAll('input[type=file][data-preview]').forEach(input => {
  input.addEventListener('change', () => {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.getElementById(input.dataset.preview);
      if (img) img.src = e.target.result;
    };
    reader.readAsDataURL(file);
  });
});

// ── Confirm delete ────────────────────────────────────────
document.querySelectorAll('[data-confirm]').forEach(el => {
  el.addEventListener('click', e => {
    if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault();
  });
});

// ── Auto-dismiss alerts ───────────────────────────────────
document.querySelectorAll('.alert[data-auto-dismiss]').forEach(alert => {
  setTimeout(() => {
    alert.style.transition = 'opacity .4s';
    alert.style.opacity = '0';
    setTimeout(() => alert.remove(), 400);
  }, parseInt(alert.dataset.autoDismiss) || 3000);
});

// ── Modal helpers ─────────────────────────────────────────
function openModal(id) {
  const m = document.getElementById(id);
  if (m) m.classList.add('open');
}
function closeModal(id) {
  const m = document.getElementById(id);
  if (m) m.classList.remove('open');
}
document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
  backdrop.addEventListener('click', e => {
    if (e.target === backdrop) backdrop.classList.remove('open');
  });
});
document.querySelectorAll('.modal-close').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.modal-backdrop')?.classList.remove('open');
  });
});

// ── Toggle checkbox row highlight ────────────────────────
document.querySelectorAll('table input[type=checkbox]').forEach(cb => {
  cb.addEventListener('change', () => {
    cb.closest('tr')?.classList.toggle('selected', cb.checked);
  });
});

// ── Toast notification ────────────────────────────────────
function showToast(msg, type = 'success') {
  const t = document.createElement('div');
  t.className = `alert alert-${type}`;
  t.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;min-width:280px;box-shadow:0 8px 24px rgba(0,0,0,.15)';
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .4s'; setTimeout(() => t.remove(), 400); }, 3000);
}
