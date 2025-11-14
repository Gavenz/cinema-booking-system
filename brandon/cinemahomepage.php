<?php 
/**
 * cinemahomepage.php
 *
 * Public landing page and movie overview.
 *
 * Responsibilities:
 * - Displays featured movies and basic details (poster, title, rating, synopsis).
 * - Provides navigation to detailed movie information and showtimes.
 *
 * Supports Functional Requirement F6 (Movie Detail Page) by acting as the
 * starting point for users to browse movies and proceed to showtimes.
 */
require_once __DIR__ ."/../includes/init.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Big Premiere Point — Student Cinema</title>
  <base href ="<?= rtrim(BASE_URL, '/') ?>/" />
  <meta name="description" content="A student-built cinema site inspired by Netflix and AMC." />
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

    * { box-sizing: border-box; }
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

    /* Top Nav */
    .nav {
      position: sticky; top: 0; z-index: 50; backdrop-filter: saturate(1.1) blur(6px);
      background: linear-gradient(180deg, rgba(10,10,14,.85), rgba(10,10,14,.55) 40%, transparent);
      border-bottom: 1px solid rgba(255,255,255,.06);
    }
    .nav-inner { max-width: 1300px; margin: 0 auto; display: flex; gap: 16px; align-items: center; padding: 12px 20px; }
    .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .logo {
      width: 34px; height: 34px; border-radius: 8px; background:
        conic-gradient(from 200deg at 70% 40%, #ff4d4d, #ff1f5a 40%, #a20025 60%, #5e0015 80%, #ff4d4d);
      box-shadow: inset 0 0 12px rgba(0,0,0,.35), 0 4px 16px rgba(229,9,20,.35);
    }
    .brand-title { font-weight: 800; letter-spacing: .4px; font-size: 1.1rem; color: white; }

    .nav-links { display: flex; gap: 14px; align-items: center; flex: 1; }
    .nav-links a, .more-trigger {
      color: var(--muted); text-decoration: none; font-weight: 700; font-size: .95rem; padding: 6px 10px; border-radius: 10px;
    }
    .nav-links a.active, .nav-links a:hover, .more-trigger:hover, .has-dropdown:focus-within .more-trigger {
      color:#fff; background: rgba(255,255,255,.06);
    }

    .search-wrap {
      display: flex; gap: 10px; align-items: center;
      background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.09); border-radius: 999px; padding: 6px 10px; width: clamp(200px, 35vw, 380px);
    }
    .search-wrap input { background: transparent; border: 0; outline: none; color: var(--text); flex: 1; padding: 6px 6px; font-size: .95rem; }

    .btn {
      appearance: none; border: 0; background: var(--accent); color: white; font-weight: 700; padding: 10px 14px; border-radius: 999px; cursor: pointer;
      box-shadow: 0 10px 20px rgba(229,9,20,.25);
    }
    .btn:hover { filter: brightness(1.05); }
    .btn.small { font-size: .9rem; padding: 8px 10px; }
    .btn.ghost { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15); }

    /* Compact, two-line Buy Tickets button */
    .btn.tix{
      background: linear-gradient(180deg, #ff2e38, #e50914);
      border: 1px solid rgba(255,255,255,.18);
      box-shadow: 0 10px 24px rgba(229,9,20,.28), inset 0 1px 0 rgba(255,255,255,.15);
      border-radius: 999px;
      display: inline-flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      line-height: 1.05;
      padding: 8px 10px;
      font-weight: 800;
      letter-spacing:.1px;
      min-width: 90px;
      white-space: normal;
    }
    .btn.tix:hover{
      filter: none;
      transform: translateY(-1px);
      box-shadow: 0 16px 30px rgba(229,9,20,.34), inset 0 1px 0 rgba(255,255,255,.2);
    }
    .btn.tix:focus-visible{ outline: none; box-shadow: 0 0 0 2px rgba(255,255,255,.35), 0 0 0 4px rgba(229,9,20,.55); }
    .btn.tix .top{ font-size: .9rem; }
    .btn.tix .bottom{ font-size: .9rem; }

    /* --- More dropdown --- */
    .has-dropdown { position: relative; display: flex; align-items: center; }
    .more-trigger { display: inline-flex; align-items: center; gap: 6px; background: transparent; border: 0; cursor: pointer; }
    .dropdown {
      position: absolute; top: calc(100% + 8px); right: 0; min-width: 220px; z-index: 60;
      background: var(--panel); border: 1px solid rgba(255,255,255,.12); border-radius: 12px;
      box-shadow: var(--shadow); padding: 8px; display: none;
    }
    .dropdown a { display: block; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: var(--text); font-weight: 600; font-size: .95rem; }
    .dropdown a:hover { background: rgba(255,255,255,.06); }
    .has-dropdown:hover .dropdown, .has-dropdown:focus-within .dropdown { display: block; }
    .chev { opacity: .7; transform: translateY(1px); transition: transform .15s ease; }
    .has-dropdown:hover .chev, .has-dropdown:focus-within .chev { transform: translateY(1px) rotate(180deg); }

    /* ===== Hero (base for slides) ===== */
    .hero {
      position: relative; isolation: isolate; max-width: 1300px; margin: 24px auto; padding: 20px; border-radius: var(--radius);
      background:
        linear-gradient(180deg, rgba(0,0,0,.55), rgba(0,0,0,.7)),
        url("../assets/images/f1_movie_poster16x9.jpg");
      background-size: cover; background-position: center; min-height: 46vh; display: grid; align-content: end;
      box-shadow: var(--shadow);
    }
    .hero-content { max-width: 720px; }
    .badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.18); padding: 6px 10px; border-radius: 999px; font-size: .85rem; }
    .hero h1 { margin: 10px 0 6px; font-size: clamp(1.75rem, 3vw + .5rem, 3rem); }
    .hero p { margin: 0 0 16px; color: #d9d9e7; line-height: 1.5; }
    .hero-cta { display: flex; gap: 12px; align-items: center; }
    .hero .btn.secondary { background: rgba(255,255,255,.1); box-shadow: none; border: 1px solid rgba(255,255,255,.16) }

    /* === Hero carousel === */
    .hero-wrap { position: relative; width: 100vw; max-width: none; margin: 16px 0 10px; padding: 0; }
    .hero-track {
      display: grid; grid-auto-flow: column; grid-auto-columns: 100%;
      gap: 16px; overflow-x: auto; scroll-snap-type: x mandatory; scroll-behavior: smooth; border-radius: var(--radius);
    }
    .hero-track::-webkit-scrollbar { height: 10px; }
    .hero-track::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 999px; }

    .hero-slide { scroll-snap-align: start; }
    .hero-slide.hero{
      height: min(calc(100vw * 9 / 16), 90dvh);
      margin: 0; max-width: 100vw; border-radius: 0;
      display: grid; align-content: end; padding: 32px 24px;
      background-size: cover; background-position: center;
    }
    @media (min-width: 1200px){ .hero-slide .hero-content{ max-width: 900px; } }

    .hero-ctrl {
      position: absolute; top: 50%; transform: translateY(-50%);
      width: 42px; height: 42px; display: grid; place-items: center;
      border-radius: 999px; background: rgba(0,0,0,.45);
      border: 1px solid rgba(255,255,255,.12); cursor: pointer; z-index: 3; color: #fff;
    }
    .hero-ctrl:hover { filter: brightness(1.2); }
    .hero-ctrl.prev { left: 28px; }
    .hero-ctrl.next { right: 28px; }

    .hero-dots { display: flex; justify-content: center; gap: 8px; margin-top: 10px; }
    .hero-dot { width: 8px; height: 8px; border-radius: 999px; background: rgba(255,255,255,.35); border: 1px solid rgba(255,255,255,.5); }
    .hero-dot.active { background: #fff; }

    /* Rows */
    .section { max-width: 1300px; margin: 0 auto; padding: 8px 20px 24px; }
    .section h2 { font-size: 1.2rem; font-weight: 800; letter-spacing: .4px; margin: 18px 2px; }

    .carousel { position: relative; overflow: hidden; padding: 4px 0 18px; }
    .row { display: grid; grid-auto-flow: column; grid-auto-columns: 200px; gap: 16px; overflow-x: auto; scroll-behavior: smooth; padding-bottom: 2px; }
    .row::-webkit-scrollbar { height: 10px; }
    .row::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 999px; }

    .card {
      background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
      border: 1px solid rgba(255,255,255,.08); border-radius: 14px; overflow: hidden; position: relative; box-shadow: 0 10px 20px rgba(0,0,0,.35);
      transition: transform .25s ease, box-shadow .25s ease, background .25s ease;
    }
    .card:hover { transform: translateY(-4px); box-shadow: 0 16px 34px rgba(0,0,0,.55); background: var(--card-hover); }

    .poster { width: 100%; aspect-ratio: 2/3; object-fit: cover; display: block; }
    .card-body { padding: 10px 10px 12px; }
    .title { font-weight: 700; font-size: .95rem; margin: 6px 0 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .meta { display: flex; justify-content: space-between; align-items: center; gap: 8px; color: var(--muted); font-size: .85rem; }

    .rating { display: inline-flex; align-items: center; gap: 6px; background: #2a2a07; color: var(--accent-2); font-weight: 800; padding: 4px 8px; border-radius: 999px; border: 1px solid rgba(245, 197, 24, .25); }

    .card-actions { display: flex; gap: 8px; margin-top: 10px; }

    /* Modal */
    dialog { border: none; border-radius: 16px; width: min(920px, 92vw); background: #0e0e14; color: var(--text); box-shadow: var(--shadow); }
    dialog::backdrop { background: rgba(0,0,0,.6); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; border-bottom: 1px solid rgba(255,255,255,.08); }
    .modal-body { padding: 14px; }
    .video { width: 100%; aspect-ratio: 16/9; border-radius: 12px; border: 1px solid rgba(255,255,255,.08); }

    /* Footer */
    footer { max-width: 1300px; margin: 14px auto 40px; padding: 0 20px; color: var(--muted); display: flex; justify-content: space-between; align-items: center; }

    /* Responsive */
    @media (max-width: 800px) {
      .nav-links { display: none; }
      .row { grid-auto-columns: 160px; }
      .hero { min-height: 44vh; }
      .dropdown { position: fixed; top: 60px; right: 16px; left: 16px; }
    }
  </style>
</head>
<body>
  <?php include __DIR__ ."/../includes/header.php"; ?>

  <!-- ===== Hero carousel ===== -->
  <section class="hero-wrap" aria-label="Featured movies">
    <button class="hero-ctrl prev" aria-label="Previous featured">◀</button>
    <div class="hero-track" id="heroTrack" tabindex="0" aria-label="Featured movies carousel"></div>
    <button class="hero-ctrl next" aria-label="Next featured">▶</button>
    <div class="hero-dots" id="heroDots" aria-hidden="true"></div>
  </section>

  <main id="movies" tabindex="-1">
    <div class="section" id="trending">
      <h2>Trending Now</h2>
      <div class="carousel">
        <button class="ctrl prev" aria-label="Scroll left" data-target="row-trending">◀</button>
        <div class="row" id="row-trending" tabindex="0" aria-label="Trending movies"></div>
        <button class="ctrl next" aria-label="Scroll right" data-target="row-trending">▶</button>
      </div>
    </div>

    <div class="section" id="top">
      <h2>Top Rated</h2>
      <div class="carousel">
        <button class="ctrl prev" aria-label="Scroll left" data-target="row-top">◀</button>
        <div class="row" id="row-top" tabindex="0" aria-label="Top rated movies"></div>
        <button class="ctrl next" aria-label="Scroll right" data-target="row-top">▶</button>
      </div>
    </div>

    <div class="section" id="genres">
      <h2>Action &amp; Adventure</h2>
      <div class="carousel">
        <button class="ctrl prev" aria-label="Scroll left" data-target="row-action">◀</button>
        <div class="row" id="row-action" tabindex="0" aria-label="Action and adventure movies"></div>
        <button class="ctrl next" aria-label="Scroll right" data-target="row-action">▶</button>
      </div>
    </div>

  <?php include __DIR__ ."/../includes/footer.php"; ?>

  <!-- Trailer Modal -->
  <dialog id="trailerModal" aria-labelledby="modalTitle">
    <div class="modal-header">
      <strong id="modalTitle">Trailer</strong>
      <button id="closeModal" class="btn small ghost" aria-label="Close trailer">✕</button>
    </div>
    <div class="modal-body">
      <iframe id="ytPlayer" class="video" src="" title="YouTube trailer" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
    </div>
  </dialog>

  <!-- Login Modal -->
  <dialog id="loginModal" aria-labelledby="loginTitle">
    <div class="modal-header">
      <strong id="loginTitle">Log in</strong>
      <button id="closeLogin" class="btn small ghost" aria-label="Close login">✕</button>
    </div>
    <div class="modal-body">
      <form id="loginForm">
        <div style="display:grid; gap:10px;">
          <label>
            <span style="display:block; font-size:.9rem; color:var(--muted); margin-bottom:6px;">Email</span>
            <input id="loginEmail" type="email" required class="input" placeholder="you@example.com" />
          </label>
          <label>
            <span style="display:block; font-size:.9rem; color:var(--muted); margin-bottom:6px;">Password</span>
            <input id="loginPass" type="password" required class="input" placeholder="••••••••" />
          </label>
          <button type="submit" class="btn" style="width:fit-content;">Log in</button>
        </div>
      </form>
    </div>
  </dialog>

  <script>
    // ======== Movie Data ========
    const MOVIES = [
      { id: 1,  title: 'The Conjuring: Last Rites', year: 2025, runtime: 135, rating: 8.3, genre: 'Thriller',   poster: "assets/images/theconjuring.jpg",   trailer: 'bMgfsdYoEEo', url: 'pages/movies/conjuring.php' },
      { id: 2,  title:'Tron: Ares',                 year: 2025, runtime: 119, rating: 6.6, genre:'Action',     poster: "assets/images/tronares.jpg",       trailer:'YShVEXb7-ic', url: 'pages/movies/tronares.php' },
      { id: 3,  title: 'Parasite',                  year: 2019, runtime: 131, rating: 8.5, genre: 'Drama',     poster: "assets/images/parasite.jpg",       trailer: 'isOGD_7hNIY', url: 'pages/movies/parasite.php' },
      { id: 4,  title: 'Interstellar',              year: 2014, runtime: 169, rating: 8.7, genre: 'Sci-Fi',    poster: "assets/images/interstellar.jpg",   trailer: 'zSWdZVtXT7E', url: 'pages/movies/interstellar.php' },
      { id: 5,  title: 'The Fanastic Four',         year: 2025, runtime: 115, rating: 7.0, genre: 'Action',    poster: "assets/images/f4.jpg",             trailer: 'pAsmrKyMqaA', url: 'pages/movies/fantasticfour.php' },
      { id: 6,  title: 'Anyone but You',            year: 2025, runtime: 103, rating: 6.1, genre: 'Romance',   poster: "assets/images/anyonebutyou.jpg",   trailer: 'UtjH6Sk7Gxs', url: 'pages/movies/anyonebutyou.php' },
      { id: 7,  title: 'How to Train your Dragon',  year: 2010, runtime: 98,  rating: 8.1, genre: 'Adventure', poster: "assets/images/httyd.jpg",          trailer: '2AKsAxrhqgM', url: 'pages/movies/httyd.php' },
      { id: 8,  title: 'Inception',                 year: 2010, runtime: 162, rating: 8.8, genre: 'Thriller',  poster: "assets/images/inception.jpg",      trailer: 'YoHD9XEInc0', url: 'pages/movies/inception.php' },
      { id: 9,  title: 'Avatar',                    year: 2022, runtime: 192, rating: 7.5, genre: 'Sci-Fi',    poster: "assets/images/avatar.jpg",         trailer: 'd9MyW72ELq0', url: 'pages/movies/avatar.php' },
      { id:10,  title:'F1: The Movie',              year: 2025, runtime: 155, rating: 7.7, genre:'Action',     poster: "assets/images/f1movie.jpg",        trailer:'8yh9BPUBbbQ', url: 'pages/movies/f1.php' },
      { id:11,  title: 'Toy Story',                 year: 1995, runtime: 81,  rating: 8.3, genre: 'Family',    poster: "assets/images/toystory.jpg",       trailer: 'v-PjgYDrg70', url: 'pages/movies/toystory.php' },
      { id:12,  title: '300',                       year: 2006, runtime: 117, rating: 7.6, genre: 'Action',    poster: "assets/images/300.jpg",            trailer: 'ZJ6yq-oVKPc', url: 'pages/movies/300.php' },
      { id:13,  title: 'Gran Turismo',              year: 2023, runtime: 134, rating: 7.1, genre: 'Adventure', poster: "assets/images/gt.jpg",             trailer: 'GVPzGBvPrzw', url: 'pages/movies/granturismo.php' },
      { id:14,  title: 'Skyfall',                   year: 2012, runtime: 143, rating: 7.8, genre: 'Action',    poster: "assets/images/skyfall.jpg",        trailer: '6kw1UVovByw', url: 'pages/movies/skyfall.php' },
      { id:15,  title: 'IT',                        year: 2017, runtime: 135, rating: 7.3, genre: 'Horror',    poster: "assets/images/it.jpg",             trailer: 'hAUTdjf9rko', url: 'pages/movies/it.php' }
    ];

    const rows = {
      trending: [1,2,3,4,5,6,7].map(id => MOVIES.find(m => m.id === id)),
      top: [...MOVIES].sort((a,b) => b.rating - a.rating).slice(0,8),
      action: MOVIES.filter(m => ['Action','Adventure','Sci-Fi'].includes(m.genre)).slice(0,10)
    };

    const $ = (sel, root=document) => root.querySelector(sel);
    const $$ = (sel, root=document) => [...root.querySelectorAll(sel)];
    const mins = m => `${Math.floor(m/60)}h ${m%60}m`;

    // ======== HERO ========
    const HEROES = [
      {
        title: 'F1: The Movie',
        overview: 'Experience the high-octane world of Formula 1 in this blockbuster movie starring Brad Pitt as a retired driver forced back into the sport to race against a new generation of talent.',
        meta: '2h 35m • Action • Sports',
        trailer: '8yh9BPUBbbQ',
        bg: 'assets/images/f1_movie_poster16x9.jpg'
      },
      {
        title: 'The Conjuring: Last Rites',
        overview: 'The Warrens face their darkest case yet as a malevolent force threatens to tear their world apart.',
        meta: '2h 15m • Horror • Thriller',
        trailer: 'bMgfsdYoEEo',
        bg: 'assets/images/theconjuringlastritesposter16x9.jpg'
      }
    ];

    function heroSlideTemplate(h) {
      return `
        <section class="hero hero-slide" aria-label="${h.title}"
          style="background:
            linear-gradient(180deg, rgba(0,0,0,.55), rgba(0,0,0,.7)),
            url('${h.bg}');
            background-size: cover; background-position: center;">
          <div class="hero-content">
            <span class="badge"><span aria-hidden>🍿</span> Featured</span>
            <h1>${h.title}</h1>
            <p>${h.overview}</p>
            <div class="hero-cta">
              <button class="btn" data-hero-trailer="${h.trailer}">▶ Play Trailer</button>
              <span class="badge">${h.meta}</span>
            </div>
          </div>
        </section>
      `;
    }

    function renderHeroCarousel() {
      const track = document.getElementById('heroTrack');
      const dots = document.getElementById('heroDots');
      track.innerHTML = HEROES.map(heroSlideTemplate).join('');
      dots.innerHTML = HEROES.map((_, i) => `<span class="hero-dot${i===0?' active':''}"></span>`).join('');
    }

    function setupHeroCarousel() {
      const track = document.getElementById('heroTrack');
      const prev = document.querySelector('.hero-ctrl.prev');
      const next = document.querySelector('.hero-ctrl.next');
      const dotsWrap = document.getElementById('heroDots');

      const slideWidth = () => track.clientWidth;

      prev.addEventListener('click', () => track.scrollBy({ left: -slideWidth(), behavior: 'smooth' }));
      next.addEventListener('click', () => track.scrollBy({ left:  slideWidth(), behavior: 'smooth' }));

      track.addEventListener('scroll', () => {
        const idx = Math.round(track.scrollLeft / slideWidth());
        [...dotsWrap.children].forEach((dot, i) => dot.classList.toggle('active', i === idx));
      });

      track.addEventListener('click', (e) => {
        const t = e.target;
        if (t.matches('[data-hero-trailer]')) openTrailer(t.getAttribute('data-hero-trailer'));
      });

      track.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') next.click();
        if (e.key === 'ArrowLeft')  prev.click();
      });
    }

    // ======== Rows ========
    function movieCard(m) {
      const url = m.url || `pages/movie.php?id=${m.id}`;
      return `
        <article class="card" data-id="${m.id}">
          <img class="poster" src="${m.poster}" alt="${m.title} poster" loading="lazy" />
          <div class="card-body">
            <div class="title" title="${m.title}">${m.title}</div>
            <div class="meta">
              <span>${m.year} • ${mins(m.runtime)}</span>
              <span class="rating">★ ${m.rating.toFixed(1)}</span>
            </div>
            <div class="card-actions">
              <button class="btn small" data-trailer="${m.trailer}" aria-label="Play trailer for ${m.title}">▶ Trailer</button>
              <a class="btn tix small" href="${url}" aria-label="Buy tickets for ${m.title}">
                <span class="top">Buy</span>
                <span class="bottom">Tickets</span>
              </a>
            </div>
            <div class="badge" style="margin-top:8px">${m.genre}</div>
          </div>
        </article>`;
    }

    function renderRow(el, items) { el.innerHTML = items.map(movieCard).join(''); }
    function renderAllRows() {
      renderRow($('#row-trending'), rows.trending);
      renderRow($('#row-top'), rows.top);
      renderRow($('#row-action'), rows.action);
    }

    // Carousel controls
    function setupCarouselControls() {
      $$('.ctrl').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-target');
          const row = document.getElementById(id);
          const shift = row.clientWidth * 0.9 * (btn.classList.contains('next') ? 1 : -1);
          row.scrollBy({ left: shift, behavior: 'smooth' });
        });
      });
    }

    function setupRowDelegates() {
      ['row-trending','row-top','row-action'].forEach(id => {
        const row = document.getElementById(id);
        row.addEventListener('click', (e) => {
          const t = e.target;
          if (t.matches('[data-trailer]')) openTrailer(t.getAttribute('data-trailer'));
        });
      });
    }

    // Search
    function setupSearch() {
      const input = document.getElementById('searchInput');
      if (!input) return;
      input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();
        if (!q) return renderAllRows();
        const filtered = MOVIES.filter(m => `${m.title} ${m.genre}`.toLowerCase().includes(q));
        renderRow($('#row-trending'), filtered);
        renderRow($('#row-top'), filtered);
        renderRow($('#row-action'), filtered.filter(m => ['Action','Adventure','Sci-Fi'].includes(m.genre)));
      });
    }

    // Trailer modal
    const modal = document.getElementById('trailerModal');
    const player = document.getElementById('ytPlayer');
    const closeModal = document.getElementById('closeModal');
    function openTrailer(videoId) {
      const id = videoId || '8yh9BPUBbbQ';
      player.src = `https://www.youtube.com/embed/${id}?autoplay=1&rel=0`;
      if (typeof modal.showModal === 'function') modal.showModal(); else alert('Your browser does not support the trailer modal.');
    }
    closeModal.addEventListener('click', () => { player.src = ''; modal.close(); });
    modal.addEventListener('close', () => { player.src = ''; });

    // Dropdown ARIA sync
    (function setupMoreMenu(){
      const container = document.querySelector('.has-dropdown');
      if (!container) return;
      const trigger = container.querySelector('.more-trigger');
      if (!trigger) return;
      const openMenu = () => trigger.setAttribute('aria-expanded','true');
      const closeMenu= () => trigger.setAttribute('aria-expanded','false');
      container.addEventListener('mouseenter', openMenu);
      container.addEventListener('mouseleave', closeMenu);
      trigger.addEventListener('focus', openMenu);
      container.addEventListener('keydown', (e)=>{ if(e.key==='Escape'){ closeMenu(); trigger.blur(); }});
      document.addEventListener('click', (e)=>{ if(!container.contains(e.target)) closeMenu(); });
      trigger.addEventListener('keydown', (e)=>{ if(e.key==='Enter' || e.key===' '){ e.preventDefault(); (trigger.getAttribute('aria-expanded')==='true'?closeMenu:openMenu)(); }});
    })();

    // Login modal
    (function setupLogin(){
      const loginBtn   = document.getElementById('loginBtn');
      const loginModal = document.getElementById('loginModal');
      const closeLogin = document.getElementById('closeLogin');
      const loginForm  = document.getElementById('loginForm');
      if (!loginBtn || !loginModal) return;
      loginBtn.addEventListener('click', () => { if (typeof loginModal.showModal === 'function') loginModal.showModal(); else alert('Your browser does not support the login modal.'); });
      closeLogin.addEventListener('click', () => loginModal.close());
      loginModal.addEventListener('close', () => loginForm.reset());
      loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = document.getElementById('loginEmail').value.trim();
        loginModal.close();
        alert(`Welcome back, ${email || 'guest'}!`);
      });
    })();

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
      if (!['ArrowLeft','ArrowRight'].includes(e.key)) return;
      const activeRow = document.activeElement?.classList.contains('row') ? document.activeElement : document.getElementById('row-trending');
      const shift = activeRow.clientWidth * 0.9 * (e.key === 'ArrowRight' ? 1 : -1);
      activeRow.scrollBy({ left: shift, behavior: 'smooth' });
    });

    // Init
    renderHeroCarousel();
    setupHeroCarousel();
    renderAllRows();
    setupCarouselControls();
    setupRowDelegates();
    setupSearch();
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>
</html>
