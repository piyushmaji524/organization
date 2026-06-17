// ============================================================
// Main App JS — Shared utilities across all pages
// ============================================================

// ── Settings cache ────────────────────────────────────────
let _settings = null;
async function getSettings() {
  if (!_settings) {
    const res = await API.settings();
    _settings = res.success ? res.data : {};
  }
  return _settings;
}

// ── Apply site settings to page ──────────────────────────
async function applySiteSettings() {
  const s = await getSettings();
  const lang = I18N.getLang();

  // Logo
  const logoEls = document.querySelectorAll('.site-logo');
  logoEls.forEach(el => {
    if (s.logo_path) {
      el.src = '/' + s.logo_path;
      el.style.display = 'block';
    }
  });

  // Site name
  const siteName = s[`site_name_${lang}`] || s.site_name_en || 'Sarak Youth Development Council';
  document.querySelectorAll('.site-name').forEach(el => el.textContent = siteName);

  // Tagline
  const tagline = s[`tagline_${lang}`] || s.tagline_en || '';
  document.querySelectorAll('.site-tagline').forEach(el => el.textContent = tagline);

  // Motto
  const motto = s[`main_motto_${lang}`] || s.main_motto_en || '';
  document.querySelectorAll('.site-motto').forEach(el => el.textContent = motto);

  // Favicon
  if (s.favicon_path) {
    let fav = document.querySelector("link[rel='icon']");
    if (!fav) { fav = document.createElement('link'); fav.rel = 'icon'; document.head.appendChild(fav); }
    fav.href = '/' + s.favicon_path;
  }

  // Dynamic title
  if (document.title === 'SYDC') document.title = siteName;
}

// ── Alert banner ──────────────────────────────────────────
async function loadAlertBanner() {
  const banner = document.getElementById('alert-banner');
  if (!banner) return;
  const s = await getSettings();
  if (s.alert_banner_active === '1') {
    const lang = I18N.getLang();
    const text = s[`alert_banner_text_${lang}`] || s.alert_banner_text_en || '';
    if (text) {
      banner.textContent = text;
      banner.style.display = 'block';
    }
  }
}

// ── Countdown timer ───────────────────────────────────────
function startCountdown(targetDate, containerEl) {
  const target = new Date(targetDate).getTime();
  function update() {
    const now = Date.now();
    const diff = target - now;
    if (diff <= 0) {
      containerEl.innerHTML = '<span style="color:var(--gold)">Event is now live!</span>';
      return;
    }
    const d = Math.floor(diff / 86400000);
    const h = Math.floor((diff % 86400000) / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    containerEl.innerHTML = `
      <span class="cd-unit"><span class="cd-val">${String(d).padStart(2,'0')}</span><span class="cd-label">${I18N.t('event_countdown_days')}</span></span>
      <span class="cd-sep">:</span>
      <span class="cd-unit"><span class="cd-val">${String(h).padStart(2,'0')}</span><span class="cd-label">${I18N.t('event_countdown_hrs')}</span></span>
      <span class="cd-sep">:</span>
      <span class="cd-unit"><span class="cd-val">${String(m).padStart(2,'0')}</span><span class="cd-label">${I18N.t('event_countdown_min')}</span></span>
      <span class="cd-sep">:</span>
      <span class="cd-unit"><span class="cd-val">${String(s).padStart(2,'0')}</span><span class="cd-label">${I18N.t('event_countdown_sec')}</span></span>`;
  }
  update();
  setInterval(update, 1000);
}

// ── Stats counter animation ───────────────────────────────
function animateCounter(el, target, duration = 1800) {
  const start = 0;
  const step = (target / duration) * 16;
  let current = start;
  const timer = setInterval(() => {
    current = Math.min(current + step, target);
    el.textContent = Math.floor(current).toLocaleString();
    if (current >= target) clearInterval(timer);
  }, 16);
}

function initCounters() {
  const counters = document.querySelectorAll('[data-counter]');
  if (!counters.length) return;
  const obs = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target, parseInt(entry.target.dataset.counter));
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });
  counters.forEach(el => obs.observe(el));
}

// ── Lightbox ──────────────────────────────────────────────
function initLightbox() {
  const lb = document.getElementById('lightbox');
  if (!lb) return;
  const lbImg = lb.querySelector('.lb-img');
  const lbCap = lb.querySelector('.lb-caption');
  document.querySelectorAll('.gallery-item[data-src]').forEach(item => {
    item.addEventListener('click', () => {
      lbImg.src = item.dataset.src;
      lbCap.textContent = item.dataset.caption || '';
      lb.classList.add('open');
    });
  });
  lb.addEventListener('click', e => { if (e.target === lb || e.target.classList.contains('lb-close')) lb.classList.remove('open'); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') lb.classList.remove('open'); });
}

// ── Mobile hamburger nav ──────────────────────────────────
function initNav() {
  const toggle = document.getElementById('nav-toggle');
  const navMenu = document.getElementById('nav-menu');
  if (!toggle || !navMenu) return;
  toggle.addEventListener('click', () => {
    navMenu.classList.toggle('open');
    toggle.setAttribute('aria-expanded', navMenu.classList.contains('open'));
  });
  // Close on nav link click
  navMenu.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => navMenu.classList.remove('open'));
  });
}

// ── Form feedback helpers ─────────────────────────────────
function showFormMsg(formEl, message, type = 'success') {
  let msgEl = formEl.querySelector('.form-msg');
  if (!msgEl) {
    msgEl = document.createElement('div');
    msgEl.className = 'form-msg';
    formEl.prepend(msgEl);
  }
  msgEl.className = `form-msg alert alert-${type}`;
  msgEl.textContent = message;
  msgEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function setFormLoading(btn, loading) {
  btn.disabled = loading;
  btn.dataset.original = btn.dataset.original || btn.textContent;
  btn.textContent = loading ? I18N.t('form_submitting') : btn.dataset.original;
}

// ── Scroll reveal ─────────────────────────────────────────
function initScrollReveal() {
  const els = document.querySelectorAll('.reveal');
  if (!els.length) return;
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); obs.unobserve(e.target); } });
  }, { threshold: 0.1 });
  els.forEach(el => obs.observe(el));
}

// ── Init on load ──────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  initNav();
  initScrollReveal();
  initCounters();
  initLightbox();
  await applySiteSettings();
  await loadAlertBanner();
});

// Re-apply on lang change
document.addEventListener('langchange', () => {
  _settings = null; // clear cache so lang-dependent strings reload
  applySiteSettings();
  loadAlertBanner();
});
