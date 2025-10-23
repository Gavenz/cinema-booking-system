<?php require_once __DIR__ ."/../includes/init.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>The Conjuring: Last Rites — Big Premiere Point</title>
  <base href ="<?= rtrim(BASE_URL, '/') ?>/" />
  <meta name="description" content="Movie details, cast, showtimes and reviews for The Conjuring: Last Rites." />
  <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
  <style>
    :root {
      --bg: #0b0b0f;
      --panel: #12121a;
      --muted: #8b8ba1;
      --text: #f3f3f8;
      --accent: #e50914;
      --accent-2: #f5c518;
      --card: #1a1a24;
      --card-hover: #20202c;
      --ring: 0 0 0 2px rgba(229,9,20,.45);
      --radius: 16px;
      --shadow: 0 10px 30px rgba(0,0,0,.45);
    }

    *{ box-sizing: border-box; }
    html, body { height: 100%; }
    body {
      margin: 0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
      color: var(--text);
      background:
        radial-gradient(1200px 600px at 20% -10%, #1b1b28 0%, transparent 60%),
        radial-gradient(800px 500px at 100% 0%, #231c24 0%, transparent 60%),
        var(--bg);
      overflow-x: hidden;
    }

    /* Top Nav (header.php relies on these classes) */
    .nav { position: sticky; top: 0; z-index: 50; backdrop-filter: saturate(1.1) blur(6px);
      background: linear-gradient(180deg, rgba(10,10,14,.85), rgba(10,10,14,.55) 40%, transparent);
      border-bottom: 1px solid rgba(255,255,255,.06); }
    .nav-inner { max-width: 1300px; margin: 0 auto; display: flex; gap: 16px; align-items: center; padding: 12px 20px; }
    .brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
    .logo { width:34px; height:34px; border-radius:8px; background:
      conic-gradient(from 200deg at 70% 40%, #ff4d4d, #ff1f5a 40%, #a20025 60%, #5e0015 80%, #ff4d4d);
      box-shadow: inset 0 0 12px rgba(0,0,0,.35), 0 4px 16px rgba(229,9,20,.35); }
    .brand-title { font-weight: 800; letter-spacing: .4px; font-size: 1.1rem; color: white; }
    .nav-links { display:flex; gap:14px; align-items:center; flex:1; }
    .nav-links a, .more-trigger { color: var(--muted); text-decoration: none; font-weight: 700; font-size: .95rem; padding: 6px 10px; border-radius: 10px; }
    .nav-links a:hover, .more-trigger:hover, .has-dropdown:focus-within .more-trigger { color:#fff; background: rgba(255,255,255,.06); }
    .has-dropdown { position: relative; display: flex; align-items: center; }
    .more-trigger { display:inline-flex; align-items:center; gap:6px; background:transparent; border:0; cursor:pointer; }
    .dropdown { position:absolute; top: calc(100% + 8px); right:0; min-width:220px; z-index:60; background: var(--panel);
      border:1px solid rgba(255,255,255,.12); border-radius:12px; box-shadow: var(--shadow); padding: 8px; display: none; }
    .dropdown a { display:block; padding:10px 12px; border-radius:8px; text-decoration:none; color: var(--text); font-weight:600; font-size:.95rem; }
    .dropdown a:hover { background: rgba(255,255,255,.06); }
    .has-dropdown:hover .dropdown, .has-dropdown:focus-within .dropdown { display:block; }
    .chev { opacity:.7; transform: translateY(1px); transition: transform .15s ease; }
    .has-dropdown:hover .chev, .has-dropdown:focus-within .chev { transform: translateY(1px) rotate(180deg); }

    .search-wrap { display:flex; gap:10px; align-items:center; background: rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.09); border-radius:999px; padding:6px 10px; width: clamp(200px, 35vw, 380px); }
    .search-wrap input { background: transparent; border:0; outline:none; color: var(--text); flex:1; padding:6px 6px; font-size:.95rem; }
    .icon { opacity:.8 }
    .btn { appearance:none; border:0; background: var(--accent); color:#fff; font-weight:700; padding:10px 14px; border-radius:999px; cursor:pointer;
      box-shadow: 0 10px 20px rgba(229,9,20,.25); }
    .btn:hover { filter: brightness(1.05); }
    .btn.small { font-size:.9rem; padding:8px 10px; }
    .btn.ghost { background: rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15); }
    .btn.neutral { background: rgba(255,255,255,.1); }

    /* Page Masthead */
    .mast { position: relative; isolation: isolate; max-width: 1300px; margin: 20px auto; padding: 20px; border-radius: var(--radius);
      background: linear-gradient(180deg, rgba(0,0,0,.55), rgba(0,0,0,.7)), url("assets/images/theconjuringlastritesposter16x9.jpg");
      background-size: cover; background-position: center; box-shadow: var(--shadow);
      display:grid; grid-template-columns: 240px 1fr; gap:20px; }
    .mast .poster { width:100%; aspect-ratio:2/3; object-fit:cover; border-radius:12px; border:1px solid rgba(255,255,255,.08);
      box-shadow: 0 10px 24px rgba(0,0,0,.5); }
    .mast .meta { align-self: end; background: rgba(0,0,0,.35); padding:16px; border-radius:12px; border:1px solid rgba(255,255,255,.12);
      backdrop-filter: blur(4px) saturate(1.05); }
    .badge { display:inline-flex; align-items:center; gap:8px; background: rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.18); padding:6px 10px; border-radius:999px; font-size:.85rem; }
    .title { font-size: clamp(1.6rem, 2.4vw + .6rem, 2.6rem); margin: 10px 0 6px; font-weight: 900; letter-spacing:.3px; }
    .sub { color:#d9d9e7; margin:0 0 12px; line-height:1.55; }
    .chips { display:flex; flex-wrap:wrap; gap:8px; margin: 8px 0 16px; }
    .chip { display:inline-flex; align-items:center; gap:6px; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.16); padding:6px 10px; border-radius:999px; font-weight:700; font-size:.9rem; color: var(--muted); }
    .rating { background:#2a2a07; color:var(--accent-2); border:1px solid rgba(245,197,24,.25); }
    .cta { display:flex; gap:10px; flex-wrap:wrap; }

    /* Content layout */
    .wrap { max-width: 1300px; margin: 0 auto 40px; padding: 0 20px; display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
    .panel { background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
      border: 1px solid rgba(255,255,255,.08); border-radius: 14px; box-shadow: 0 10px 20px rgba(0,0,0,.35); padding: 16px; }
    h2 { font-size:1.2rem; font-weight:900; letter-spacing:.4px; margin: 4px 0 12px; }
    .muted { color: var(--muted); }

    /* Cast grid */
    .cast-grid { display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap:12px; }
    .cast { display:flex; gap:10px; padding:10px; border-radius:12px; background: rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); align-items:center; }
    .cast img { width:44px; height:44px; border-radius:999px; object-fit:cover; border:1px solid rgba(255,255,255,.12); }
    .cast .name { font-weight:800; font-size:.95rem; }
    .cast .role { color: var(--muted); font-size:.85rem; }

    /* Showtimes */
    .controls { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px; }
    .input, select { width:100%; padding:10px 12px; border-radius:10px; background: rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.15); color: var(--text); outline:none; }
    .input:focus, select:focus { box-shadow: var(--ring); border-color: rgba(255,255,255,.35); }
    .date-tabs { display:flex; gap:8px; flex-wrap:wrap; margin: 8px 0 10px; }
    .date-tab { padding:8px 12px; border-radius:999px; background: rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); cursor:pointer; }
    .date-tab.active { background: var(--accent); border-color: transparent; }
    .showtime-row { display:flex; gap:8px; flex-wrap:wrap; }
    .time { padding:8px 12px; border-radius:10px; background: rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.16); cursor:pointer; }
    .time.selected { box-shadow: var(--ring); }

    /* Reviews */
    .reviews { display:grid; gap:12px; }
    .review { background: rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); border-radius:12px; padding:12px; }
    .stars { color: var(--accent-2); font-weight:900; letter-spacing:1px; }
    .review-form { display:grid; gap:10px; margin-top:10px; }
    textarea { min-height: 110px; resize: vertical; }

    /* Aside cards */
    .aside .card { background: rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); border-radius:12px; padding:12px; margin-bottom:12px; }

    /* Modal */
    dialog { border:none; border-radius:16px; width:min(920px, 92vw); background:#0e0e14; color:var(--text); box-shadow: var(--shadow); }
    dialog::backdrop { background: rgba(0,0,0,.6); }
    .modal-header { display:flex; justify-content:space-between; align-items:center; padding:10px 14px; border-bottom:1px solid rgba(255,255,255,.08); }
    .modal-body { padding:14px; }
    .video { width:100%; aspect-ratio:16/9; border-radius:12px; border:1px solid rgba(255,255,255,.08); }

    @media (max-width: 980px) {
      .mast { grid-template-columns: 1fr; }
      .wrap { grid-template-columns: 1fr; }
      .cast-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
      .nav-links { display:none; } /* matches your mobile collapse */
    }
  </style>
</head>
<body>
  <?php include __DIR__ ."/../includes/header.php"; ?>

  <!-- ===== Masthead ===== -->
  <header class="mast" aria-label="Movie header">
    <img class="poster" src="assets/images/theconjuring.jpg" alt="The Conjuring: Last Rites poster" />
    <div class="meta">
      <span class="badge">🍿 Now Showing</span>
      <div class="title">The Conjuring: Last Rites</div>
      <p class="sub">
        The Warrens face their darkest case yet when a malevolent force ties a string of chilling hauntings together —
        threatening to rip apart their faith, family, and everything they’ve saved before.
      </p>
      <div class="chips">
        <span class="chip">2025</span>
        <span class="chip">2h 15m</span>
        <span class="chip">Horror • Thriller</span>
        <span class="chip rating">★ 8.3</span>
        <span id="wlChip" class="chip">Watchlist: <strong id="wlState" style="margin-left:6px;">Add</strong></span>
      </div>
      <div class="cta">
        <button id="playTrailer" class="btn">▶ Play Trailer</button>
        <button id="toggleWatchlist" class="btn ghost">＋ Add to Watchlist</button>
        <a href="#showtimes" class="btn neutral">🎫 Get Tickets</a>
      </div>
    </div>
  </header>

  <main class="wrap" id="content">
    <!-- Left column -->
    <section class="panel" aria-labelledby="summary">
      <h2 id="summary">Summary</h2>
      <p class="muted">
        When a secluded New England parish reports inexplicable phenomena and a cascade of possessions, Ed and Lorraine Warren
        uncover an entity older than any they’ve confronted. To sever its hold, they must revisit a case they swore never to
        open again — risking their bond and the lives of those who sought their help.
      </p>
    </section>

    <aside class="aside">
      <div class="card">
        <strong>Details</strong>
        <div class="muted" style="margin-top:6px;">
          Director: Michael Chaves<br/>
          Writers: David Leslie Johnson-McGoldrick, James Wan<br/>
          Distributor: New Line Cinema<br/>
          Language: English<br/>
          Rating: PG-13 (Fright/Intensity)
        </div>
      </div>
      <div class="card">
        <strong>Where to watch later</strong>
        <p class="muted">After theatrical run, expected on select streamers. Follow our socials for updates.</p>
      </div>
    </aside>

    <section class="panel" aria-labelledby="cast">
      <h2 id="cast">Cast</h2>
      <div class="cast-grid">
        <div class="cast">
          <img src="https://images.unsplash.com/photo-1527980965255-d3b416303d12?w=256&q=80" alt="Vera Farmiga portrait" />
          <div><div class="name">Vera Farmiga</div><div class="role">Lorraine Warren</div></div>
        </div>
        <div class="cast">
          <img src="https://images.unsplash.com/photo-1531123414780-f7423da7a56a?w=256&q=80" alt="Patrick Wilson portrait" />
          <div><div class="name">Patrick Wilson</div><div class="role">Ed Warren</div></div>
        </div>
        <div class="cast">
          <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=256&q=80" alt="Taissa Farmiga portrait" />
          <div><div class="name">Taissa Farmiga</div><div class="role">Sister Irene</div></div>
        </div>
        <div class="cast">
          <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=256&q=80" alt="Storm Reid portrait" />
          <div><div class="name">Storm Reid</div><div class="role">Novice Ann</div></div>
        </div>
      </div>
    </section>

    <section id="showtimes" class="panel" aria-labelledby="cinemas">
      <h2 id="cinemas">Cinemas &amp; Showtimes</h2>
      <div class="controls">
        <label style="flex:1; min-width:220px;">
          <span class="muted" style="display:block; font-size:.9rem; margin-bottom:6px;">City</span>
          <select id="citySelect">
            <option value="Singapore" selected>Singapore</option>
            <option value="Johor Bahru">Johor Bahru</option>
            <option value="Kuala Lumpur">Kuala Lumpur</option>
          </select>
        </label>
        <label style="flex:1; min-width:220px;">
          <span class="muted" style="display:block; font-size:.9rem; margin-bottom:6px;">Format</span>
          <select id="formatSelect">
            <option value="Standard" selected>Standard 2D</option>
            <option value="Atmos">Dolby Atmos</option>
            <option value="IMAX">IMAX</option>
          </select>
        </label>
      </div>

      <div id="dateTabs" class="date-tabs" role="tablist" aria-label="Choose date"></div>
      <div id="cinemaList"></div>
      <div style="margin-top:12px; display:flex; gap:10px;">
        <button id="buyNow" class="btn" disabled>Pay &amp; Reserve</button>
        <span id="selectionSummary" class="muted" aria-live="polite"></span>
      </div>
    </section>

    <section class="panel" aria-labelledby="reviews">
      <h2 id="reviews">Reviews</h2>
      <div id="reviewsList" class="reviews" aria-live="polite"></div>

      <form id="reviewForm" class="review-form" autocomplete="off">
        <div style="display:grid; gap:6px;">
          <label><span class="muted" style="font-size:.9rem;">Your name</span>
            <input type="text" id="revName" class="input" placeholder="e.g., Alex" required />
          </label>
          <label><span class="muted" style="font-size:.9rem;">Your rating</span>
            <select id="revStars" required>
              <option value="5">★★★★★ (5)</option>
              <option value="4" selected>★★★★☆ (4)</option>
              <option value="3">★★★☆☆ (3)</option>
              <option value="2">★★☆☆☆ (2)</option>
              <option value="1">★☆☆☆☆ (1)</option>
            </select>
          </label>
          <label><span class="muted" style="font-size:.9rem;">Comment</span>
            <textarea id="revText" class="input" placeholder="What did you think?" required></textarea>
          </label>
        </div>
        <button class="btn" type="submit" style="width:fit-content;">Post review</button>
      </form>
    </section>
  </main>

  <?php include __DIR__ ."/../includes/footer.php"; ?>

  <!-- Trailer Modal -->
  <dialog id="trailerModal" aria-labelledby="modalTitle">
    <div class="modal-header">
      <strong id="modalTitle">Trailer — The Conjuring: Last Rites</strong>
      <button id="closeModal" class="btn small ghost" aria-label="Close trailer">✕</button>
    </div>
    <div class="modal-body">
      <iframe id="ytPlayer" class="video" src="" title="YouTube trailer" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
    </div>
  </dialog>

  <script>
    // ===== Movie constants =====
    const MOVIE = { id: 1, title: 'The Conjuring: Last Rites', year: 2025, runtime: 135, rating: 8.3, genre: 'Horror, Thriller', trailerId: 'bMgfsdYoEEo' };

    // ===== Watchlist (same key as homepage) =====
    const WATCHLIST_KEY = 'bigpremiere_watchlist';
    const getWatchlist = () => JSON.parse(localStorage.getItem(WATCHLIST_KEY) || '[]');
    const setWatchlist = list => localStorage.setItem(WATCHLIST_KEY, JSON.stringify(list));
    const inWatchlist = id => getWatchlist().includes(id);
    function toggleWatchlist(id){
      const list = getWatchlist();
      const idx = list.indexOf(id);
      if (idx >= 0) list.splice(idx, 1); else list.push(id);
      setWatchlist(list);
      renderWatchlistState();
    }
    function renderWatchlistState(){
      const added = inWatchlist(MOVIE.id);
      document.getElementById('toggleWatchlist').textContent = added ? '✓ In Watchlist' : '＋ Add to Watchlist';
      document.getElementById('wlState').textContent = added ? 'Added' : 'Add';
    }

    // ===== Trailer modal =====
    const modal = document.getElementById('trailerModal');
    const player = document.getElementById('ytPlayer');
    document.getElementById('playTrailer').addEventListener('click', () => {
      player.src = `https://www.youtube.com/embed/${MOVIE.trailerId}?autoplay=1&rel=0`;
      if (typeof modal.showModal === 'function') modal.showModal();
      else alert('Your browser does not support the trailer modal.');
    });
    document.getElementById('closeModal').addEventListener('click', () => { player.src = ''; modal.close(); });
    modal.addEventListener('close', () => { player.src = ''; });

    // ===== Dropdown accessibility sync (matches homepage behavior) =====
    (function setupMoreMenu(){
      const container = document.querySelector('.has-dropdown');
      if (!container) return;
      const trigger = container.querySelector('.more-trigger');
      function openMenu(){ trigger.setAttribute('aria-expanded', 'true'); }
      function closeMenu(){ trigger.setAttribute('aria-expanded', 'false'); }
      container.addEventListener('mouseenter', openMenu);
      container.addEventListener('mouseleave', closeMenu);
      trigger.addEventListener('focus', openMenu);
      container.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeMenu(); trigger.blur(); } });
      document.addEventListener('click', (e) => { if (!container.contains(e.target)) closeMenu(); });
      trigger.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); const expanded = trigger.getAttribute('aria-expanded') === 'true'; expanded ? closeMenu() : openMenu(); }
      });
    })();

    // ===== Search hookup (optional for this page; harmless if unused) =====
    (function setupSearch(){
      const input = document.getElementById('searchInput');
      if (!input) return;
      input.addEventListener('keydown', (e) => {
        // Example: submit to /index.php and let homepage JS filter
        if (e.key === 'Enter') {
          window.location.href = 'index.php#trending';
        }
      });
    })();

    // ===== Showtimes demo data =====
    const SHOWTIMES = {
      'Singapore': [
        { cinema: 'BPP Downtown 1', address: 'Orchard Road', times: ['12:45','15:30','18:10','21:00'] },
        { cinema: 'BPP Harbourfront', address: 'Harbourfront Walk', times: ['11:20','14:00','19:40'] },
        { cinema: 'BPP Northpoint', address: 'Yishun Ave 2', times: ['10:30','13:15','16:00','20:10'] },
      ],
      'Johor Bahru': [
        { cinema: 'BPP Paradigm JB', address: 'Skudai', times: ['13:00','17:10','21:20'] },
      ],
      'Kuala Lumpur': [
        { cinema: 'BPP Pavilion KL', address: 'Bukit Bintang', times: ['12:00','15:00','20:00'] },
        { cinema: 'BPP Mid Valley', address: 'Lingkaran Syed Putra', times: ['11:45','16:30'] },
      ],
    };

    const DAYS_AHEAD = 5;
    const dateTabs = document.getElementById('dateTabs');
    const cinemaList = document.getElementById('cinemaList');
    const buyBtn = document.getElementById('buyNow');
    const selSummary = document.getElementById('selectionSummary');
    const citySelect = document.getElementById('citySelect');
    const formatSelect = document.getElementById('formatSelect');

    let selected = { dateISO: null, city: citySelect.value, cinema: null, time: null, format: formatSelect.value };

    function buildDates(){
      const today = new Date(); const tabs = [];
      for (let i=0; i<DAYS_AHEAD; i++){
        const d = new Date(today); d.setDate(today.getDate() + i);
        const label = d.toLocaleDateString(undefined, { weekday:'short', day:'numeric', month:'short' });
        const iso = d.toISOString().slice(0,10);
        tabs.push({ label, iso });
      }
      return tabs;
    }

    function renderDateTabs(){
      const tabs = buildDates();
      dateTabs.innerHTML = tabs.map((t,i)=>`<button class="date-tab ${i===0?'active':''}" data-iso="${t.iso}" role="tab">${t.label}</button>`).join('');
      selected.dateISO = tabs[0].iso;
    }

    function renderCinemas(){
      const entries = SHOWTIMES[selected.city] || [];
      cinemaList.innerHTML = entries.map(block => {
        const times = block.times.map(tm => `<button class="time" data-cinema="${block.cinema}" data-time="${tm}">${tm}</button>`).join('');
        return `
          <div class="panel" style="margin:10px 0;">
            <div style="display:flex; justify-content:space-between; gap:10px; align-items:center; flex-wrap:wrap;">
              <div>
                <strong>${block.cinema}</strong>
                <div class="muted">${block.address} • ${selected.format}</div>
              </div>
              <a class="btn small ghost" href="#map" aria-label="Open map for ${block.cinema}">🗺️ Map</a>
            </div>
            <div class="showtime-row" style="margin-top:10px;">${times}</div>
          </div>`;
      }).join('') || `<p class="muted">No sessions found for this city and date.</p>`;
    }

    function clearSelection(){ selected.cinema = null; selected.time = null; document.querySelectorAll('.time.selected').forEach(b => b.classList.remove('selected')); updateSummary(); }
    function updateSummary(){
      const parts = [];
      parts.push(`${selected.city}`);
      if (selected.dateISO) parts.push(new Date(selected.dateISO).toDateString());
      if (selected.format) parts.push(selected.format);
      if (selected.cinema && selected.time) parts.push(`${selected.cinema} • ${selected.time}`);
      selSummary.textContent = parts.join('  ·  ');
      buyBtn.disabled = !(selected.cinema && selected.time);
    }

    dateTabs.addEventListener('click', (e) => {
      const b = e.target.closest('.date-tab'); if (!b) return;
      document.querySelectorAll('.date-tab').forEach(t => t.classList.toggle('active', t===b));
      selected.dateISO = b.getAttribute('data-iso'); clearSelection();
    });
    citySelect.addEventListener('change', () => { selected.city = citySelect.value; renderCinemas(); clearSelection(); });
    formatSelect.addEventListener('change', () => { selected.format = formatSelect.value; renderCinemas(); clearSelection(); });
    cinemaList.addEventListener('click', (e) => {
      const t = e.target.closest('.time'); if (!t) return;
      document.querySelectorAll('.time').forEach(x => x.classList.remove('selected'));
      t.classList.add('selected'); selected.cinema = t.getAttribute('data-cinema'); selected.time = t.getAttribute('data-time'); updateSummary();
    });
    buyBtn.addEventListener('click', () => {
      const payload = { movieId: MOVIE.id, title: MOVIE.title, city: selected.city, date: selected.dateISO, time: selected.time, cinema: selected.cinema, format: selected.format };
      alert(`Tickets reserved!\n\n${payload.title}\n${payload.cinema}\n${new Date(payload.date).toDateString()} ${payload.time}\n${payload.city} • ${payload.format}`);
    });

    // ===== Reviews (localStorage demo) =====
    const REV_KEY = `reviews_${MOVIE.id}`;
    const reviewsList = document.getElementById('reviewsList');
    function getReviews(){ return JSON.parse(localStorage.getItem(REV_KEY) || '[]'); }
    function setReviews(arr){ localStorage.setItem(REV_KEY, JSON.stringify(arr)); }
    function renderReviews(){
      const items = getReviews();
      if (!items.length) { reviewsList.innerHTML = `<p class="muted">No reviews yet — be the first!</p>`; return; }
      reviewsList.innerHTML = items.map(r => `
        <div class="review">
          <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
            <strong>${r.name || 'Guest'}</strong>
            <span class="stars" aria-label="${r.stars} out of 5 stars">${'★'.repeat(r.stars)}${'☆'.repeat(5-r.stars)}</span>
          </div>
          <p style="margin:.35rem 0 0;">${r.text.replace(/</g,'&lt;')}</p>
          <div class="muted" style="margin-top:6px; font-size:.85rem;">${new Date(r.ts).toLocaleString()}</div>
        </div>
      `).join('');
    }
    document.getElementById('reviewForm').addEventListener('submit', (e) => {
      e.preventDefault();
      const name = document.getElementById('revName').value.trim();
      const starsVal = Number(document.getElementById('revStars').value);
      const text = document.getElementById('revText').value.trim();
      const next = getReviews(); next.unshift({ name, stars: starsVal, text, ts: Date.now() });
      setReviews(next); e.target.reset(); renderReviews();
    });

    // Init
    document.getElementById('toggleWatchlist').addEventListener('click', () => toggleWatchlist(MOVIE.id));
    renderWatchlistState();
    renderDateTabs(); renderCinemas(); updateSummary(); renderReviews();
  </script>
</body>
</html>
