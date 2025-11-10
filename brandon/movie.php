<?php require_once __DIR__ ."/../includes/init.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Movies — Big Premiere Point</title>
  <base href ="<?= rtrim(BASE_URL, '/') ?>/" />
  <meta name="description" content="Browse the movie catalog — filter by search, genre, rating, and status." />
  <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
  <style>
    :root{
      --bg:#0b0b0f; --panel:#12121a; --muted:#8b8ba1; --text:#f3f3f8;
      --accent:#e50914; --accent-2:#f5c518; --card:#1a1a24; --card-hover:#20202c;
      --ring:0 0 0 2px rgba(229,9,20,.45); --radius:16px; --shadow:0 10px 30px rgba(0,0,0,.45);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,"Apple Color Emoji","Segoe UI Emoji";
      color:var(--text);
      background:
        radial-gradient(1200px 600px at 20% -10%, #1b1b28 0%, transparent 60%),
        radial-gradient(800px 500px at 100% 0%, #231c24 0%, transparent 60%),
        var(--bg);
      overflow-x:hidden;
    }

    /* ===== Header (unchanged) ===== */
    .nav{position:sticky; top:0; z-index:50; backdrop-filter:saturate(1.1) blur(6px);
      background:linear-gradient(180deg, rgba(10,10,14,.85), rgba(10,10,14,.55) 40%, transparent);
      border-bottom:1px solid rgba(255,255,255,.06);
    }
    .nav-inner{max-width:1300px; margin:0 auto; display:flex; gap:16px; align-items:center; padding:12px 20px;}
    .brand{display:flex; align-items:center; gap:10px; text-decoration:none;}
    .logo{width:34px; height:34px; border-radius:8px; background:
      conic-gradient(from 200deg at 70% 40%, #ff4d4d, #ff1f5a 40%, #a20025 60%, #5e0015 80%, #ff4d4d);
      box-shadow:inset 0 0 12px rgba(0,0,0,.35), 0 4px 16px rgba(229,9,20,.35);
    }
    .brand-title{font-weight:800; letter-spacing:.4px; font-size:1.1rem; color:white}
    .nav-links{display:flex; gap:14px; align-items:center; flex:1;}
    .nav-links a, .more-trigger{
      color:var(--muted); text-decoration:none; font-weight:700; font-size:.95rem;
      padding:6px 12px; border-radius:10px;
      display:inline-flex; align-items:center; justify-content:center; text-align:center;
      min-height:36px; line-height:1.1;
    }
    .nav-links a.active, .nav-links a:hover, .more-trigger:hover, .has-dropdown:focus-within .more-trigger{color:#fff; background:rgba(255,255,255,.06);}
    .search-wrap{display:flex; gap:10px; align-items:center; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.09); border-radius:999px; padding:6px 10px; width:clamp(200px,35vw,380px);}
    .search-wrap input{background:transparent; border:0; outline:none; color:var(--text); flex:1; padding:6px 6px; font-size:.95rem;}
    .btn{appearance:none; border:0; background:var(--accent); color:white; font-weight:700; padding:10px 14px; border-radius:999px; cursor:pointer; box-shadow:0 10px 20px rgba(229,9,20,.25);}
    .btn:hover{filter:brightness(1.05)}
    .btn.small{font-size:.9rem; padding:8px 10px;}
    .btn.ghost{background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15);}
    @media (max-width:800px){ .nav-links{display:none} }

    .has-dropdown{position:relative; display:flex; align-items:center;}
    .more-trigger{appearance:none; background:transparent; border:0; font:inherit; color:var(--muted); gap:6px; cursor:pointer;}
    .dropdown{position:absolute; top:calc(100% + 8px); right:0; min-width:220px; z-index:60; background:var(--panel); border:1px solid rgba(255,255,255,.12); border-radius:12px; box-shadow:var(--shadow); padding:8px; display:none;}
    .dropdown a{display:block; padding:10px 12px; border-radius:8px; text-decoration:none; color:var(--text); font-weight:600; font-size:.95rem;}
    .dropdown a:hover{background:rgba(255,255,255,.06)}
    .chev{opacity:.7; transform:translateY(1px); transition:transform .15s ease;}
    .has-dropdown:hover .chev, .has-dropdown:focus-within .chev{transform:translateY(1px) rotate(180deg)}
    .has-dropdown:hover .dropdown, .has-dropdown:focus-within .dropdown{display:block}

    .section{max-width:1300px; margin:0 auto; padding:8px 20px 24px;}
    .section h1{font-size:1.8rem; font-weight:800; letter-spacing:.4px; margin:18px 2px;}

    /* ===== Filters + Tabs ===== */
    .filterbar{max-width:1300px; margin:0 auto 16px; padding:0 20px;}
    .filter-inner{
      display:grid; grid-template-columns: 1fr auto auto auto auto;
      gap:10px; align-items:center; background:rgba(255,255,255,.05);
      border:1px solid rgba(255,255,255,.09); padding:10px; border-radius:14px;
    }
    .input.grow{height:44px; border-radius:999px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); padding:0 14px; font-weight:600; color:var(--text);}
    .input.grow::placeholder{color:#a8a8bf}
    .select, .btn.ghost{height:44px; display:inline-flex; align-items:center; justify-content:center; padding:0 12px; border-radius:12px; font-weight:700;}
    .tabs{display:inline-flex; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:12px; overflow:hidden; height:44px;}
    .tab{appearance:none; background:transparent; border:0; color:var(--muted); padding:0 14px; font-weight:800; cursor:pointer; display:inline-flex; align-items:center; justify-content:center;}
    .tab.active{background:rgba(255,255,255,.14); color:#fff;}
    @media (max-width:980px){ .filter-inner{ grid-template-columns: 1fr 1fr 1fr; } .tabs{order:2} #filterGenre{order:3} #filterRating{order:4} #clearFilters{order:5} }

    /* ===== Grid + Cards (5 across) ===== */
    .grid{max-width:1300px; margin:0 auto 40px; padding:0 20px; display:grid; grid-template-columns:repeat(5, 1fr); gap:18px;}
    @media (max-width:1200px){ .grid{grid-template-columns:repeat(4, 1fr);} }
    @media (max-width:900px){  .grid{grid-template-columns:repeat(3, 1fr);} }
    @media (max-width:600px){  .grid{grid-template-columns:repeat(2, 1fr);} }

    .card{background:linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
      border:1px solid rgba(255,255,255,.08); border-radius:16px; overflow:hidden; position:relative;
      box-shadow:0 10px 20px rgba(0,0,0,.35); transition:transform .25s ease, box-shadow .25s ease, background .25s ease;}
    .card:hover{transform:translateY(-4px); box-shadow:0 16px 34px rgba(0,0,0,.55); background:var(--card-hover);}
    .poster{width:100%; aspect-ratio:2/3; object-fit:cover; display:block;}
    .card-body{padding:14px 14px 16px;}
    .title{font-weight:800; font-size:1rem; margin:6px 0 4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}
    .meta{display:flex; justify-content:space-between; align-items:center; gap:8px; color:var(--muted); font-size:.9rem;}
    .rating{display:inline-flex; align-items:center; gap:6px; background:rgba(245,197,24,.08); color:var(--accent-2); font-weight:800; padding:4px 8px; border-radius:999px; border:1px solid rgba(245,197,24,.25);}

    .card-actions{display:flex; gap:8px; margin-top:10px; flex-wrap:wrap;}
    .btn-pill{display:inline-flex; align-items:center; gap:6px; height:32px; padding:0 10px; border-radius:999px; font-weight:800; letter-spacing:.1px; box-shadow:none; font-size:.82rem;}
    .btn-trailer{background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15); color:var(--text);}
    .btn-trailer:hover{background:rgba(255,255,255,.12)}
    .btn-tix{background:var(--accent); color:#fff; border:0; box-shadow:0 10px 20px rgba(229,9,20,.22);}
    .btn-tix:hover{filter:brightness(1.06)}
    .btn-pill .dot{width:6px; height:6px; border-radius:50%; background:currentColor; opacity:.85;}

    footer{max-width:1300px; margin:14px auto 40px; padding:0 20px; color:var(--muted); display:flex; justify-content:space-between; align-items:center;}

    /* ===== Trailer dialog — match homepage look ===== */
    dialog{border:none; border-radius:16px; width:min(920px,92vw); background:#0e0e14; color:var(--text); box-shadow:var(--shadow); padding:0;}
    dialog::backdrop{background:rgba(0,0,0,.6)}
    .modal-header{display:flex; justify-content:space-between; align-items:center; padding:12px 16px; border-bottom:1px solid rgba(255,255,255,.08)}
    .modal-title{font-weight:800}
    .modal-body{padding:14px 16px 18px}
    .video{width:100%; aspect-ratio:16/9; border-radius:12px; border:1px solid rgba(255,255,255,.08)}
    .btn-close{background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15); color:var(--text); border-radius:12px; height:34px; padding:0 10px; font-weight:800;}
    .btn-close:hover{background:rgba(255,255,255,.14)}
  </style>
</head>
<body>
  <?php include __DIR__ ."/../includes/header.php"; ?>

  <div class="section">
    <h1>All Movies</h1>
  </div>

  <!-- Filters -->
  <div class="filterbar">
    <div class="filter-inner">
      <input id="catalogSearch" class="input grow" placeholder="Search the catalog (title or genre)..." />

      <!-- Status tabs -->
      <div class="tabs" role="tablist" aria-label="Showtime status">
        <button id="tabNow"  class="tab active" role="tab" aria-selected="true">Now&nbsp;Showing</button>
        <button id="tabSoon" class="tab"        role="tab" aria-selected="false">Coming&nbsp;Soon</button>
      </div>

      <select id="filterGenre" class="select">
        <option value="">All genres</option>
        <option>Action</option><option>Adventure</option><option>Drama</option>
        <option>Thriller</option><option>Romance</option><option>Family</option>
        <option>Sci-Fi</option><option>Horror</option>
      </select>

      <select id="filterRating" class="select">
        <option value="">Any rating</option>
        <option value="9">9.0+</option>
        <option value="8">8.0+</option>
        <option value="7">7.0+</option>
      </select>

      <button id="clearFilters" class="btn ghost">Clear</button>
    </div>
  </div>

  <!-- Grid -->
  <div class="grid" id="gridAll"></div>

  <?php include __DIR__ ."/../includes/footer.php"; ?>

  <!-- Trailer Modal (homepage style) -->
  <dialog id="trailerModal" aria-labelledby="modalTitle">
    <div class="modal-header">
      <strong id="modalTitle" class="modal-title">Trailer</strong>
      <button id="closeModal" class="btn-close" aria-label="Close trailer">✕</button>
    </div>
    <div class="modal-body">
      <iframe id="ytPlayer" class="video" src="" title="YouTube trailer" frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowfullscreen></iframe>
    </div>
  </dialog>

  <script>
    // ===== Demo Data (now supports per-movie buyUrl) =====
    const sample = (seed, w=400, h=600) => `https://picsum.photos/seed/${seed}/${w}/${h}`;
    const MOVIES = [
      // Provide buyUrl for movies that should open a specific page
      { id: 1,  title:'The Conjuring: Last Rites', year:2025, runtime:135, rating:8.3, genre:'Thriller', poster:"assets/images/theconjuring.jpg", trailer:'bMgfsdYoEEo', buyUrl: "brandon/conjuring.php" },
      { id: 2,  title:'Tron: Ares',                year:2025, runtime:119, rating:6.6, genre:'Action', poster: "assets/images/tronares.jpg", trailer:'YShVEXb7-ic', buyUrl: "pages/tronares.php" },
      { id: 3, title: 'Parasite',                  year: 2019, runtime: 131, rating: 8.5, genre: 'Drama', poster: "assets/images/parasite.jpg", trailer: 'isOGD_7hNIY' },
      { id: 4, title: 'Interstellar',              year: 2014, runtime: 169, rating: 8.7, genre: 'Sci-Fi', poster: "assets/images/interstellar.jpg", trailer: 'zSWdZVtXT7E' },
      { id: 5, title: 'The Fanastic Four',         year: 2025, runtime: 115, rating: 7.0, genre: 'Action', poster: "assets/images/f4.jpg", trailer: 'pAsmrKyMqaA' },
      { id: 6, title: 'Anyone but You',            year: 2025, runtime: 103, rating: 6.1, genre: 'Romance', poster: "assets/images/anyonebutyou.jpg", trailer: 'UtjH6Sk7Gxs' },
      { id: 7, title: 'How to Train your Dragon',  year: 2010, runtime: 98, rating: 8.1, genre: 'Adventure', poster: "assets/images/httyd.jpg", trailer: '2AKsAxrhqgM' },
      { id: 8, title: 'Inception',                 year: 2010, runtime: 162, rating: 8.8, genre: 'Thriller', poster: "assets/images/inception.jpg", trailer: 'YoHD9XEInc0' },
      { id: 9, title: 'Avatar',                    year: 2022, runtime: 192, rating: 7.5, genre: 'Sci-Fi', poster: "assets/images/avatar.jpg", trailer: 'd9MyW72ELq0' },
      { id:10,  title:'F1: The Movie',             year:2025, runtime:155, rating:7.7, genre:'Action', poster:"assets/images/f1movie.jpg", trailer:'8yh9BPUBbbQ', buyUrl: "brandon/f1movie.php" },
      { id: 11, title: 'Toy Story',                year: 1995, runtime: 81, rating: 8.3, genre: 'Family', poster: "assets/images/toystory.jpg", trailer: 'v-PjgYDrg70' },
      { id: 12, title: '300',                      year: 2006, runtime: 117, rating: 7.6, genre: 'Action', poster: "assets/images/300.jpg", trailer: 'ZJ6yq-oVKPc' },
      { id: 13, title: 'Gran Turismo',             year: 2023, runtime: 134, rating: 7.1, genre: 'Adventure', poster: "assets/images/gt.jpg", trailer: 'GVPzGBvPrzw' },
      { id: 14, title: 'Skyfall',                  year: 2012, runtime: 143, rating: 7.8, genre: 'Action', poster: "assets/images/skyfall.jpg", trailer: '6kw1UVovByw' },
      { id: 15, title: 'IT',                       year: 2017, runtime: 135, rating: 7.3, genre: 'Horror', poster: "assets/images/it.jpg", trailer: 'hAUTdjf9rko' }
    ];

    const $ = (sel, root=document) => root.querySelector(sel);
    const mins = m => `${Math.floor(m/60)}h ${m%60}m`;
    const CURRENT_YEAR = new Date().getFullYear();

    // Build the link per movie:
    const getBuyLink = (m) => m.buyUrl || `pages/movie.php?id=${encodeURIComponent(m.id)}`;

    // Card template
    function cardTemplate(m){
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
              <button class="btn btn-pill btn-trailer" data-trailer="${m.trailer}" aria-label="Play trailer for ${m.title}">
                <span class="dot" aria-hidden="true"></span> Trailer
              </button>
              <a class="btn btn-pill btn-tix" href="${getBuyLink(m)}" aria-label="Buy tickets for ${m.title}">
                <span class="dot" aria-hidden="true"></span> Buy&nbsp;Tickets
              </a>
            </div>
            <div class="pill" style="margin-top:8px">${m.genre}</div>
          </div>
        </article>`;
    }

    // Filters
    const $grid = $('#gridAll');
    const $genre = $('#filterGenre'), $rating = $('#filterRating'), $search = $('#catalogSearch');
    const $tabNow = $('#tabNow'), $tabSoon = $('#tabSoon');
    let statusFilter = 'now'; // 'now' | 'soon'

    const isNowShowing = m => m.year <= CURRENT_YEAR;
    const isComingSoon = m => m.year > CURRENT_YEAR;

    function filterMovies(){
      const g = $genre.value, r = Number($rating.value || 0);
      const q = $search.value.trim().toLowerCase();

      return MOVIES.filter(m=>{
        if (statusFilter==='now' && !isNowShowing(m)) return false;
        if (statusFilter==='soon' && !isComingSoon(m)) return false;
        if (g && m.genre !== g) return false;
        if (r && m.rating < r) return false;
        if (q && !(`${m.title} ${m.genre}`.toLowerCase().includes(q))) return false;
        return true;
      }).sort((a,b)=> b.year-a.year || b.rating-a.rating);
    }

    function renderGrid(){ $grid.innerHTML = filterMovies().map(cardTemplate).join(''); }

    function setTab(which){
      statusFilter = which;
      $tabNow.classList.toggle('active', which==='now');
      $tabSoon.classList.toggle('active', which==='soon');
      $tabNow.setAttribute('aria-selected', which==='now' ? 'true' : 'false');
      $tabSoon.setAttribute('aria-selected', which==='soon'? 'true' : 'false');
      renderGrid();
    }
    $tabNow.addEventListener('click', ()=> setTab('now'));
    $tabSoon.addEventListener('click', ()=> setTab('soon'));

    [$genre,$rating,$search].forEach(el=> el.addEventListener('input', renderGrid));
    $('#clearFilters').addEventListener('click', ()=>{ $genre.value=''; $rating.value=''; $search.value=''; setTab('now'); });

    // Trailer modal (homepage look)
    const modal = document.getElementById('trailerModal');
    const player= document.getElementById('ytPlayer');
    document.getElementById('closeModal').addEventListener('click', ()=>{ player.src=''; modal.close(); });
    function openTrailer(videoId){
      if (typeof modal.showModal === 'function'){
        player.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
        modal.showModal();
      }else{
        window.open(`https://www.youtube.com/watch?v=${videoId}`, '_blank');
      }
    }
    modal.addEventListener('close', ()=>{ player.src=''; });

    // Delegate trailer clicks
    $grid.addEventListener('click', (e)=>{
      const t = e.target;
      if (t.matches('[data-trailer]')) openTrailer(t.getAttribute('data-trailer'));
    });

    // Keep "More" menu accessible
    (function(){
      const container = document.querySelector('.has-dropdown'); if(!container) return;
      const trigger = container.querySelector('.more-trigger'); const dropdown = container.querySelector('.dropdown');
      const openMenu = ()=>{ trigger.setAttribute('aria-expanded','true'); dropdown.style.display='block'; };
      const closeMenu= ()=>{ trigger.setAttribute('aria-expanded','false'); dropdown.style.display='none'; };
      container.addEventListener('mouseenter', openMenu); container.addEventListener('mouseleave', closeMenu);
      trigger?.addEventListener('click', (e)=>{ e.preventDefault(); (trigger.getAttribute('aria-expanded')==='true'?closeMenu:openMenu)(); });
      document.addEventListener('click', (e)=>{ if(!container.contains(e.target)) closeMenu(); });
      container.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeMenu(); });
    })();

    // Init
    setTab('now');
  </script>
</body>
</html>
