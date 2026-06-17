// ============================================================
// API Helper — Centralized fetch wrapper for all endpoints
// ============================================================

const API = (() => {
  const BASE = '/api';

  async function get(endpoint, params = {}) {
    const qs = new URLSearchParams(params).toString();
    const url = `${BASE}/${endpoint}${qs ? '?' + qs : ''}`;
    try {
      const res = await fetch(url);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return await res.json();
    } catch (err) {
      console.error('API GET error:', endpoint, err);
      return { success: false, error: err.message };
    }
  }

  async function post(endpoint, data, isFormData = false) {
    try {
      const res = await fetch(`${BASE}/${endpoint}`, {
        method: 'POST',
        headers: isFormData ? {} : { 'Content-Type': 'application/json' },
        body: isFormData ? data : JSON.stringify(data),
      });
      return await res.json();
    } catch (err) {
      console.error('API POST error:', endpoint, err);
      return { success: false, error: err.message };
    }
  }

  return {
    settings:   ()           => get('settings.php'),
    members:    (id)         => id ? get('members.php', { id }) : get('members.php'),
    generalMembers: ()        => get('members.php', { type: 'general' }),
    blogList:       (params)  => get('blog.php', params || {}),
    blogPost:       (slug)    => get('blog.php', { slug }),
    blogRelated:    (cat, ex) => get('blog.php', { related: cat, exclude: ex }),
    blogCategories: ()        => get('blog.php', { categories: 1 }),
    events:     (params)     => get('events.php', params || {}),
    event:      (id)         => get('events.php', { id }),
    gallery:    (params)     => get('gallery.php', params || {}),
    news:       (params)     => get('news.php', params || {}),
    newsAlert:  ()           => get('news.php', { alert: 1 }),
    newsItem:   (id)         => get('news.php', { id }),
    rsvp:       (data)       => post('rsvp.php', data),
    contact:    (data)       => post('contact.php', data),
    apply:      (formData)   => post('apply.php', formData, true),
  };
})();
