// ============================================================
// i18n — Trilingual Language System (EN / HI / BN)
// ============================================================

const I18N = (() => {
  let _lang = localStorage.getItem('sydc_lang') || 'en';
  let _strings = {};

  async function load(lang) {
    try {
      const res = await fetch(`/lang/${lang}.json?v=1`);
      if (!res.ok) throw new Error();
      _strings = await res.json();
    } catch {
      console.warn('i18n: failed to load', lang);
    }
  }

  function t(key, fallback = '') {
    return _strings[key] || fallback || key;
  }

  function applyToDOM() {
    document.querySelectorAll('[data-i18n]').forEach(el => {
      const key = el.dataset.i18n;
      const val = t(key);
      if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
        el.placeholder = val;
      } else {
        el.textContent = val;
      }
    });
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
      el.placeholder = t(el.dataset.i18nPlaceholder);
    });
    // Update <html lang>
    document.documentElement.lang = _lang;
    // Update lang toggle button states
    document.querySelectorAll('.lang-btn').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.lang === _lang);
    });
  }

  async function setLang(lang) {
    _lang = lang;
    localStorage.setItem('sydc_lang', lang);
    await load(lang);
    applyToDOM();
    // Dispatch event so pages can re-render dynamic content
    document.dispatchEvent(new CustomEvent('langchange', { detail: { lang } }));
  }

  function getLang() { return _lang; }

  // Return the correct lang-specific field from an API object
  // e.g. getLangField(member, 'name') → member.name_en / name_hi / name_bn
  function field(obj, key) {
    return obj[`${key}_${_lang}`] || obj[`${key}_en`] || '';
  }

  return { load, t, setLang, getLang, field, applyToDOM };
})();

// Bootstrap on page load
document.addEventListener('DOMContentLoaded', async () => {
  const savedLang = localStorage.getItem('sydc_lang') || 'en';
  await I18N.setLang(savedLang);

  // Wire lang toggle buttons
  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.addEventListener('click', () => I18N.setLang(btn.dataset.lang));
  });
});
