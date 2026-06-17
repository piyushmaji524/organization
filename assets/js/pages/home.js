// ============================================================
// Home page — Dynamic loader
// ============================================================

async function loadHomePage() {
  await Promise.all([loadHomeEvents(), loadHomeNews()]);
  initScrollReveal();
}

async function loadHomeEvents() {
  const container = document.getElementById('home-events');
  if (!container) return;
  container.innerHTML = '<div class="loading-block"><div class="spinner"></div></div>';
  const res = await API.events({ filter: 'upcoming', limit: 3 });
  if (!res.success || !res.data.length) {
    container.innerHTML = '<p style="text-align:center;color:var(--text-muted)">No upcoming events at the moment.</p>';
    return;
  }
  container.innerHTML = res.data.map(ev => buildEventCard(ev)).join('');
}

async function loadHomeNews() {
  const container = document.getElementById('home-news');
  if (!container) return;
  container.innerHTML = '<div class="loading-block"><div class="spinner"></div></div>';
  const res = await API.news({ limit: 3 });
  if (!res.success || !res.data.length) {
    container.innerHTML = '<p style="text-align:center;color:var(--text-muted)">No news articles yet.</p>';
    return;
  }
  container.innerHTML = res.data.map(a => buildNewsCard(a)).join('');
}

function buildEventCard(ev) {
  const lang = I18N.getLang();
  const title = I18N.field(ev, 'title');
  const loc   = I18N.field(ev, 'location');
  const cover = ev.cover_image
    ? `<img src="/${ev.cover_image}" alt="${title}" style="width:100%;height:200px;object-fit:cover">`
    : `<div style="height:200px;display:flex;align-items:center;justify-content:center;font-size:48px;background:linear-gradient(135deg,#5A1220,#1A0A0F)">📅</div>`;
  const dateStr = ev.event_date ? new Date(ev.event_date).toLocaleDateString('en-IN', { day:'numeric',month:'short',year:'numeric' }) : '';
  return `
  <div class="card event-card reveal">
    <div class="event-cover-wrap">
      ${cover}
      <span class="event-badge">${I18N.t('event_type_' + ev.type, ev.type)}</span>
    </div>
    <div class="event-body">
      <div class="event-title">${title}</div>
      <div class="event-meta">
        <span>📅 ${dateStr}</span>
        ${ev.event_time ? `<span>⏰ ${ev.event_time}</span>` : ''}
        ${loc ? `<span>📍 ${loc}</span>` : ''}
      </div>
      ${ev.rsvp_enabled ? `<a href="/events.html?id=${ev.id}" class="btn btn-maroon btn-sm">${I18N.t('event_rsvp')}</a>` : `<a href="/events.html?id=${ev.id}" class="btn btn-maroon btn-sm">View Details</a>`}
    </div>
  </div>`;
}

function buildNewsCard(a) {
  const title = I18N.field(a, 'title');
  const cover = a.cover_image
    ? `<img src="/${a.cover_image}" alt="${title}" style="width:100%;height:180px;object-fit:cover">`
    : `<div style="height:180px;display:flex;align-items:center;justify-content:center;font-size:36px;background:var(--gold-pale)">📰</div>`;
  const dateStr = a.published_at ? new Date(a.published_at).toLocaleDateString('en-IN', { day:'numeric',month:'short',year:'numeric' }) : '';
  return `
  <div class="card news-card reveal">
    <div class="news-cover">${cover}</div>
    <div class="news-body">
      <div class="news-cat">${I18N.t('news_category_' + a.category, a.category)}</div>
      <div class="news-title">${title}</div>
      <div class="news-date">📆 ${dateStr}</div>
      <a href="/news.html?id=${a.id}" class="btn btn-maroon btn-sm" style="margin-top:12px">${I18N.t('news_read_more')}</a>
    </div>
  </div>`;
}

// Stats from settings
async function loadStats() {
  const s = await getSettings();
  const members = document.getElementById('stat-members');
  const events  = document.getElementById('stat-events');
  const benef   = document.getElementById('stat-beneficiaries');
  if (members) members.dataset.counter = s.stats_members || 50;
  if (events)  events.dataset.counter  = s.stats_events  || 12;
  if (benef)   benef.dataset.counter   = s.stats_beneficiaries || 500;
  initCounters();
}

document.addEventListener('DOMContentLoaded', async () => {
  await loadHomePage();
  await loadStats();
});

document.addEventListener('langchange', () => {
  loadHomePage();
});
