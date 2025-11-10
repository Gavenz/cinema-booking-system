<?php require_once __DIR__ ."/../includes/init.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>F1: The Movie — Big Premiere Point</title>
  <base href ="<?= rtrim(BASE_URL, '/') ?>/" />
  <meta name="description" content="Movie details, cast, showtimes and reviews for F1: The Movie." />
  <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
  <style>
    :root{
      --bg:#0b0b0f; --panel:#12121a; --muted:#8b8ba1; --text:#f3f3f8;
      --accent:#e50914; --accent-2:#f5c518; --card:#1a1a24; --card-hover:#20202c;
      --ring:0 0 0 2px rgba(229,9,20,.45); --radius:16px; --shadow:0 10px 30px rgba(0,0,0,.45);
    }

    *{ box-sizing:border-box; }
    html, body { height:100%; }
    body{
      margin:0; font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,"Apple Color Emoji","Segoe UI Emoji";
      color:var(--text);
      /* keep nice vignette but even it out with a single centered gradient */
      background:
        radial-gradient(1200px 700px at 50% -20%, #1b1b28 0%, transparent 65%),
        var(--bg);
      overflow-x:hidden;
    }

    /* Top Nav (header.php relies on these classes) */
    .nav{position:sticky;top:0;z-index:50;backdrop-filter:saturate(1.1) blur(6px);
      background:linear-gradient(180deg,rgba(10,10,14,.85),rgba(10,10,14,.55) 40%,transparent);
      border-bottom:1px solid rgba(255,255,255,.06);}
    .nav-inner{max-width:1300px;margin:0 auto;display:flex;gap:16px;align-items:center;padding:12px 20px;}
    .brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
    .logo{width:34px;height:34px;border-radius:8px;background:
      conic-gradient(from 200deg at 70% 40%,#ff4d4d,#ff1f5a 40%,#a20025 60%,#5e0015 80%,#ff4d4d);
      box-shadow:inset 0 0 12px rgba(0,0,0,.35),0 4px 16px rgba(229,9,20,.35);}
    .brand-title{font-weight:800;letter-spacing:.4px;font-size:1.1rem;color:white;}
    .nav-links{display:flex;gap:14px;align-items:center;flex:1;}
    .nav-links a,.more-trigger{color:var(--muted);text-decoration:none;font-weight:700;font-size:.95rem;padding:6px 10px;border-radius:10px;}
    .nav-links a:hover,.more-trigger:hover,.has-dropdown:focus-within .more-trigger{color:#fff;background:rgba(255,255,255,.06);}
    .has-dropdown{position:relative;display:flex;align-items:center;}
    .more-trigger{display:inline-flex;align-items:center;gap:6px;background:transparent;border:0;cursor:pointer;}
    .dropdown{position:absolute;top:calc(100% + 8px);right:0;min-width:220px;z-index:60;background:var(--panel);
      border:1px solid rgba(255,255,255,.12);border-radius:12px;box-shadow:var(--shadow);padding:8px;display:none;}
    .dropdown a{display:block;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text);font-weight:600;font-size:.95rem;}
    .dropdown a:hover{background:rgba(255,255,255,.06);}
    .has-dropdown:hover .dropdown,.has-dropdown:focus-within .dropdown{display:block;}
    .chev{opacity:.7;transform:translateY(1px);transition:transform .15s ease;}
    .has-dropdown:hover .chev,.has-dropdown:focus-within .chev{transform:translateY(1px) rotate(180deg);}

    .search-wrap{display:flex;gap:10px;align-items:center;background:rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.09);border-radius:999px;padding:6px 10px;width:clamp(200px,35vw,380px);}
    .search-wrap input{background:transparent;border:0;outline:none;color:var(--text);flex:1;padding:6px 6px;font-size:.95rem;}
    .icon{opacity:.8;}
    .btn{appearance:none;border:0;background:var(--accent);color:#fff;font-weight:700;padding:10px 14px;border-radius:999px;cursor:pointer;
      box-shadow:0 10px 20px rgba(229,9,20,.25);}
    .btn:hover{filter:brightness(1.05);}
    .btn.small{font-size:.9rem;padding:8px 10px;}
    .btn.ghost{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);}
    .btn.neutral{background:rgba(255,255,255,.1);}

    /* Page Masthead */
    .mast{
      position:relative;isolation:isolate;max-width:1300px;margin:20px auto;padding:20px;border-radius:var(--radius);
      background:linear-gradient(180deg,rgba(0,0,0,.55),rgba(0,0,0,.7)),url("assets/images/f1_movie_poster16x9.jpg");
      background-size:cover;background-position:center;box-shadow:var(--shadow);
      display:grid;grid-template-columns:240px 1fr;gap:20px;
    }
    .mast .poster{width:100%;aspect-ratio:2/3;object-fit:cover;border-radius:12px;border:1px solid rgba(255,255,255,.08);
      box-shadow:0 10px 24px rgba(0,0,0,.5);}
    .mast .meta{align-self:end;background:rgba(0,0,0,.35);padding:16px;border-radius:12px;border:1px solid rgba(255,255,255,.12);
      backdrop-filter:blur(4px) saturate(1.05);}
    .badge{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);padding:6px 10px;border-radius:999px;font-size:.85rem;}
    .title{font-size:clamp(1.6rem,2.4vw + .6rem,2.6rem);margin:10px 0 6px;font-weight:900;letter-spacing:.3px;}
    .sub{color:#d9d9e7;margin:0 0 12px;line-height:1.55;}
    .chips{display:flex;flex-wrap:wrap;gap:8px;margin:8px 0 16px;}
    .chip{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.16);padding:6px 10px;border-radius:999px;font-weight:700;font-size:.9rem;color:var(--muted);}
    .rating{background:#2a2a07;color:var(--accent-2);border:1px solid rgba(245,197,24,.25);}
    .cta{display:flex;gap:10px;flex-wrap:wrap;}

    /* Content layout */
    .wrap{max-width:1300px;margin:0 auto 40px;padding:0 20px;display:grid;grid-template-columns:2fr 1fr;gap:20px;}

    /* Make panels/cards opaque to remove background color differences */
    .panel{
      background: var(--panel);
      border:1px solid rgba(255,255,255,.08);
      border-radius:14px;box-shadow:0 10px 20px rgba(0,0,0,.35);padding:16px;
    }
    h2{font-size:1.2rem;font-weight:900;letter-spacing:.4px;margin:4px 0 12px;}
    .muted{color:var(--muted);}
    .aside .card{
      background: var(--panel);
      border:1px solid rgba(255,255,255,.08);
      border-radius:12px;padding:12px;margin-bottom:12px;
    }

    /* Cast list */
    .cast-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;}
    .cast-item{padding:10px;border-radius:12px;background:#1a1a24;border:1px solid rgba(255,255,255,.08);}
    .cast-item .name{font-weight:800;}
    .cast-item .role{color:var(--muted);font-size:.92rem;}

    /* Showtimes */
    .controls{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px;}
    .input, select{width:100%;padding:10px 12px;border-radius:10px;background:rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.15);color:var(--text);outline:none;}
    .input:focus, select:focus{box-shadow:var(--ring);border-color:rgba(255,255,255,.35);}

    /* Format dropdown readable */
    #formatSelect{color:#000;background:#fff;border-color:rgba(255,255,255,.15);}
    #formatSelect option{color:#000;background:#fff;}

    /* Dates/times */
    .date-tabs{display:flex;gap:8px;flex-wrap:wrap;margin:8px 0 10px;}
    .date-tab{color:#fff;padding:8px 12px;border-radius:999px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);cursor:pointer;}
    .date-tab.active{color:#fff;background:var(--accent);border-color:transparent;}
    .showtime-row{display:flex;gap:8px;flex-wrap:wrap;}
    .time{color:#fff;padding:8px 12px;border-radius:10px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.16);cursor:pointer;}
    .time.selected{box-shadow:var(--ring);}

    /* Modal */
    dialog{border:none;border-radius:16px;width:min(920px,92vw);background:#0e0e14;color:var(--text);box-shadow:var(--shadow);}
    dialog::backdrop{background:rgba(0,0,0,.6);}
    .modal-header{display:flex;justify-content:space-between;align-items:center;padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.08);}
    .modal-body{padding:14px;}
    .video{width:100%;aspect-ratio:16/9;border-radius:12px;border:1px solid rgba(255,255,255,.08);}

    @media (max-width:980px){
      .mast{grid-template-columns:1fr;}
      .wrap{grid-template-columns:1fr;}
      .cast-list{grid-template-columns:1fr;}
      .nav-links{display:none;}
    }
  </style>
</head>
<body>
  <?php include __DIR__ ."/../includes/header.php"; ?>

  <!-- ===== Masthead ===== -->
  <header class="mast" aria-label="Movie header">
    <img class="poster" src="assets/images/f1movie.jpg" alt="F1: The Movie poster" />
    <div class="meta">
      <span class="badge">🎬 Now Showing</span>
      <div class="title">F1: The Movie</div>
      <p class="sub">
        “The greatest that never was.” Once a 1990s F1 phenom, Sonny Hayes returns to the grid decades after a career-ending crash.
        Recruited by old teammate-turned-owner Rubén Cervantes to help save underdog team APXGP, Sonny must mentor fiery rookie
        Joshua Pearce while confronting the ghosts that pushed him off the track in the first place.
      </p>
      <div class="chips">
        <span class="chip">2025</span>
        <span class="chip">2h 36m</span>
        <span class="chip">Sports • Drama</span>
        <span class="chip rating">★ 8.2</span>
      </div>
      <div class="cta">
        <button id="playTrailer" class="btn">▶ Play Trailer</button>
        <a href="pages/showtimes.php" class="btn neutral">🎫 Get Tickets</a>
      </div>
    </div>
  </header>

  <main class="wrap" id="content">
    <!-- Left column -->
    <section class="panel" aria-labelledby="summary">
      <h2 id="summary">Summary</h2>
      <p class="muted">
        After an accident derailed his rise, Sonny Hayes has spent years drifting from series to series. When APXGP teeters on the brink,
        team owner Rubén Cervantes persuades him to return to Formula 1 for one last run — pairing him with prodigious rookie Joshua Pearce
        and hard-nosed technical director Kate McKenna. As the season unfolds, rivalries, redemption, and split-second decisions blaze toward
        a final showdown that could save the team — or finish Sonny’s story for good.
      </p>
    </section>

    <!-- Cast -->
    <section class="panel" aria-labelledby="cast">
      <h2 id="cast">Cast</h2>
      <div class="cast-list">
        <div class="cast-item"><div class="name">Brad Pitt</div><div class="role">as Sonny Hayes (veteran driver)</div></div>
        <div class="cast-item"><div class="name">Damson Idris</div><div class="role">as Joshua Pearce (rookie, APXGP)</div></div>
        <div class="cast-item"><div class="name">Javier Bardem</div><div class="role">as Rubén Cervantes (team owner)</div></div>
        <div class="cast-item"><div class="name">Kerry Condon</div><div class="role">as Kate McKenna (technical director)</div></div>
        <div class="cast-item"><div class="name">Tobias Menzies</div><div class="role">as Peter Banning (board member)</div></div>
      </div>
    </section>

    <aside class="aside">
      <div class="card">
        <strong>Details</strong>
        <div class="muted" style="margin-top:6px;">
          Director: Joseph Kosinski<br/>
          Writer: Ehren Kruger (screenplay); Story by Joseph Kosinski &amp; Ehren Kruger<br/>
          Distributor: Warner Bros. Pictures (theatrical) • Apple Original Films (SVOD)<br/>
          Language: English<br/>
          Rating: PG-13 (strong language &amp; action)
        </div>
      </div>
      <div class="card">
        <strong>Where to watch later</strong>
        <p class="muted">Streaming on Apple TV starting <strong>December 12, 2025</strong> after the theatrical run.</p>
      </div>
    </aside>

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
  </main>

  <?php include __DIR__ ."/../includes/footer.php"; ?>

  <!-- Trailer Modal -->
  <dialog id="trailerModal" aria-labelledby="modalTitle">
    <div class="modal-header">
      <strong id="modalTitle">Trailer — F1: The Movie</strong>
      <button id="closeModal" class="btn small ghost" aria-label="Close trailer">✕</button>
    </div>
    <div class="modal-body">
      <iframe id="ytPlayer" class="video" src="" title="YouTube trailer" frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowfullscreen></iframe>
    </div>
  </dialog>

  <script>
    // ===== Movie constants =====
    const MOVIE = {
      id: 1,
      title: 'F1: The Movie',
      year: 2025,
      runtime: 156,
      rating: 8.2,
      genre: 'Sports, Drama',
      trailerId: 'CT2_P2DZBR0'
    };

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

    // ===== Dropdown a11y sync =====
    (function setupMoreMenu(){
      const container = document.querySelector('.has-dropdown');
      if (!container) return;
      const trigger = container.querySelector('.more-trigger');
      function openMenu(){ trigger.setAttribute('aria-expanded','true'); }
      function closeMenu(){ trigger.setAttribute('aria-expanded','false'); }
      container.addEventListener('mouseenter', openMenu);
      container.addEventListener('mouseleave', closeMenu);
      trigger.addEventListener('focus', openMenu);
      container.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeMenu(); trigger.blur(); } });
      document.addEventListener('click', (e) => { if (!container.contains(e.target)) closeMenu(); });
      trigger.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          const expanded = trigger.getAttribute('aria-expanded') === 'true';
          expanded ? closeMenu() : openMenu();
        }
      });
    })();

    // ===== Search hookup (optional) =====
    (function setupSearch(){
      const input = document.getElementById('searchInput');
      if (!input) return;
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { window.location.href = 'index.php#trending'; }
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

    function clearSelection(){
      selected.cinema = null; selected.time = null;
      document.querySelectorAll('.time.selected').forEach(b => b.classList.remove('selected'));
      updateSummary();
    }

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

    // Init
    renderDateTabs(); renderCinemas(); updateSummary();
  </script>
</body>
</html>
