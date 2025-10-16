<?php /* food.php — Cinema Food & Drinks */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Food & Drinks — Big Premiere Point</title>
  <meta name="description" content="Snacks, drinks, and combos you’ll find at the cinema." />
  <link rel="preconnect" href="https://images.unsplash.com" crossorigin>

  <style>
    :root{
      --bg:#0b0b0f;--panel:#12121a;--muted:#8b8ba1;--text:#f3f3f8;
      --accent:#e50914;--accent-2:#f5c518;--ring:0 0 0 2px rgba(229,9,20,.45);
      --card:#1a1a24;--card-hover:#20202c;--shadow:0 10px 30px rgba(0,0,0,.45);
      --radius:16px;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,"Apple Color Emoji","Segoe UI Emoji";
      color:var(--text);
      background:
        radial-gradient(1200px 600px at 20% -10%,#1b1b28 0%,transparent 60%),
        radial-gradient(800px 500px at 100% 0%,#231c24 0%,transparent 60%),
        var(--bg);
      overflow-x:hidden;
    }

    /* ===== Top Nav ===== */
    .nav{
      position:sticky;top:0;z-index:50;backdrop-filter:saturate(1.1) blur(6px);
      background:linear-gradient(180deg,rgba(10,10,14,.85),rgba(10,10,14,.55) 40%,transparent);
      border-bottom:1px solid rgba(255,255,255,.06);
    }
    .nav-inner{max-width:1300px;margin:0 auto;display:flex;gap:16px;align-items:center;padding:12px 20px}
    .brand{display:flex;align-items:center;gap:10px;text-decoration:none}
    .logo{
      width:34px;height:34px;border-radius:8px;
      background:conic-gradient(from 200deg at 70% 40%,#ff4d4d,#ff1f5a 40%,#a20025 60%,#5e0015 80%,#ff4d4d);
      box-shadow:inset 0 0 12px rgba(0,0,0,.35),0 4px 16px rgba(229,9,20,.35)
    }
    .brand-title{font-weight:800;letter-spacing:.4px;font-size:1.1rem;color:#fff}

    .nav-links{display:flex;gap:14px;align-items:center;flex:1}
    .nav-links a,.more-trigger{
      color:var(--muted);text-decoration:none;font-weight:700;font-size:.95rem;
      padding:6px 10px;border-radius:10px
    }
    .nav-links a.active,.nav-links a:hover,.more-trigger:hover,
    .has-dropdown:focus-within .more-trigger{color:#fff;background:rgba(255,255,255,.06)}

    .search-wrap{
      display:flex;gap:10px;align-items:center;
      background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.09);
      border-radius:999px;padding:6px 10px;width:clamp(200px,35vw,380px)
    }
    .search-wrap input{
      background:transparent;border:0;outline:none;color:var(--text);flex:1;padding:6px 6px;font-size:.95rem
    }
    .icon{opacity:.8}

    .btn{
      appearance:none;border:0;background:var(--accent);color:#fff;font-weight:700;
      padding:10px 14px;border-radius:999px;cursor:pointer;box-shadow:0 10px 20px rgba(229,9,20,.25)
    }
    .btn:hover{filter:brightness(1.05)}
    .btn.small{font-size:.9rem;padding:8px 10px}
    .btn.ghost{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15)}

    /* Dropdown (More) */
    .has-dropdown{position:relative;display:flex;align-items:center}
    .more-trigger{display:inline-flex;align-items:center;gap:6px;background:transparent;border:0;cursor:pointer}
    .dropdown{
      position:absolute;top:calc(100% + 8px);right:0;min-width:220px;z-index:60;
      background:var(--panel);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:8px;display:none
    }
    .dropdown a{display:block;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text);font-weight:600;font-size:.95rem}
    .dropdown a:hover{background:rgba(255,255,255,.06)}
    .has-dropdown:hover .dropdown,.has-dropdown:focus-within .dropdown{display:block}

    /* ===== Page Head ===== */
    .hero{
      position:relative;isolation:isolate;max-width:1300px;margin:24px auto;padding:20px;border-radius:var(--radius);
      background:linear-gradient(180deg,rgba(0,0,0,.55),rgba(0,0,0,.7)), url("https://picsum.photos/seed/concessions-hero/1400/700");
      background-size:cover;background-position:center;min-height:34vh;display:grid;align-content:end;box-shadow:var(--shadow);
    }
    .hero h1{margin:4px 0 8px;font-size:clamp(1.6rem,3vw + .5rem,2.6rem)}
    .hero p{margin:0 0 12px;color:#d9d9e7}

    .section{max-width:1300px;margin:0 auto;padding:8px 20px 24px}
    .section h2{font-size:1.2rem;font-weight:800;letter-spacing:.4px;margin:18px 2px}

    /* ===== Controls Bar ===== */
    .controls{
      display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;
      background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.12);
      border-radius:14px;padding:12px;align-items:center
    }
    .chip-row{display:flex;flex-wrap:wrap;gap:8px}
    .chip{
      background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);color:#fff;
      padding:6px 10px;border-radius:999px;font-size:.9rem;cursor:pointer;user-select:none
    }
    .chip.active,.chip:hover{background:rgba(255,255,255,.14)}
    .select{
      width:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(255,255,255,.2);
      background:#fff;color:#000; /* readable by default */
      outline:none
    }
    .select:focus{box-shadow:var(--ring);border-color:rgba(255,255,255,.35)}
    .select option{color:#000;background:#fff}

    /* ===== Grid ===== */
    .grid{display:grid;grid-template-columns:repeat(12,1fr);gap:16px}
    .card{
      grid-column:span 3;background:linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,.02));
      border:1px solid rgba(255,255,255,.08);border-radius:14px;overflow:hidden;box-shadow:0 10px 20px rgba(0,0,0,.35);
      transition:transform .25s ease, box-shadow .25s ease, background .25s ease
    }
    .card:hover{transform:translateY(-4px);box-shadow:0 16px 34px rgba(0,0,0,.55);background:var(--card-hover)}
    .thumb{width:100%;aspect-ratio:16/10;object-fit:cover;display:block;border-bottom:1px solid rgba(255,255,255,.08)}
    .card-body{padding:12px}
    .title{font-weight:800}
    .muted{color:var(--muted)}
    .meta{display:flex;gap:10px;align-items:center;justify-content:space-between;margin-top:6px}
    .price{font-weight:800}
    .tag{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);padding:4px 10px;border-radius:999px;font-size:.8rem}

    /* ===== Footer ===== */
    footer{max-width:1300px;margin:14px auto 40px;padding:0 20px;color:var(--muted);display:flex;justify-content:space-between;align-items:center}

    @media (max-width:1100px){ .card{grid-column:span 4} }
    @media (max-width:800px){
      .nav-links{display:none}
      .controls{grid-template-columns:1fr}
      .card{grid-column:span 6}
    }
    @media (max-width:520px){ .card{grid-column:span 12} }
  </style>
</head>
<body>
  <!-- ===== Header ===== -->
  <header class="nav" role="banner">
    <div class="nav-inner">
      <a class="brand" href="index.php" aria-label="Big Premiere Point Home">
        <div class="logo" aria-hidden="true"></div>
        <div class="brand-title">Big Premiere Point</div>
      </a>

      <nav class="nav-links" aria-label="Primary">
        <a href="index.php#movies">Movies</a>
        <a class="active" href="food.php">Food &amp; Drinks</a>
        <a href="index.php#theatres">Find a Theatre</a>

        <div class="has-dropdown" aria-haspopup="true">
          <button class="more-trigger" aria-expanded="false" aria-controls="more-menu">
            More
            <svg class="chev" width="14" height="14" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <div id="more-menu" class="dropdown" role="menu" aria-label="More">
            <a role="menuitem" href="merch.php">Merchandise</a>
            <a role="menuitem" href="gifts.php">Gift Cards</a>
            <a role="menuitem" href="about.php">About Us</a>
          </div>
        </div>
      </nav>

      <div class="search-wrap" role="search">
        <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <input id="searchInput" placeholder="Search snacks..." aria-label="Search snacks" />
      </div>

      <a href="login.php" class="btn" aria-label="Login">Login</a>
    </div>
  </header>

  <!-- ===== Hero ===== -->
  <section class="hero" aria-label="Cinema Snacks Hero">
    <div class="hero-content">
      <span class="tag"><span aria-hidden>🍿</span> Concessions</span>
      <h1>Food &amp; Drinks</h1>
      <p>Classic popcorn, fizzy drinks, chocolates, hot bites, and crowd-pleasing combos. Pick your favorites and roll film.</p>
    </div>
  </section>

  <!-- ===== Controls ===== -->
  <section class="section" aria-label="Filters and Sorting">
    <div class="controls">
      <div>
        <label for="category" class="muted" style="display:block;margin-bottom:6px;">Category</label>
        <select id="category" class="select" aria-label="Filter by category">
          <option value="all">All</option>
          <option value="Popcorn">Popcorn</option>
          <option value="Drinks">Drinks</option>
          <option value="Candy">Candy</option>
          <option value="Hot Food">Hot Food</option>
          <option value="Combos">Combos</option>
        </select>
      </div>

      <div>
        <label for="sort" class="muted" style="display:block;margin-bottom:6px;">Sort</label>
        <select id="sort" class="select" aria-label="Sort items">
          <option value="popular">Most Popular</option>
          <option value="price-asc">Price: Low to High</option>
          <option value="price-desc">Price: High to Low</option>
          <option value="calories-asc">Calories: Low to High</option>
          <option value="calories-desc">Calories: High to Low</option>
        </select>
      </div>

      <div>
        <label for="chips" class="muted" style="display:block;margin-bottom:6px;">Quick filters</label>
        <div class="chip-row" id="chips">
          <span class="chip" data-chip="vegan">Vegan</span>
          <span class="chip" data-chip="gluten-free">Gluten-Free</span>
          <span class="chip" data-chip="shareable">Shareable</span>
          <span class="chip" data-chip="low-cal">Low-Cal</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== Grid ===== -->
  <section class="section" aria-label="Menu Grid">
    <div class="grid" id="grid"></div>
  </section>

  <footer>
    <small>© <span id="year"></span> Big Premiere Point — Student Cinema Project</small>
    <small>Built with plain HTML, CSS &amp; JS</small>
  </footer>

  <script>
    // ===== Demo data =====
    const item = (id, name, cat, price, cal, tags=[], seed='snack') => ({
      id, name, category: cat, price, calories: cal, tags, img:`https://picsum.photos/seed/${seed}-${id}/800/500`
    });

    const ITEMS = [
      // item (no., name, sub-category, price, cals, tag, picture)
      item(1,'Classic Salted Popcorn','Popcorn',4.5,350,['shareable'],'popcorn'),
      item(2,'Caramel Popcorn','Popcorn',5.5,420,['shareable'],'caramel'),
      item(3,'Butter Popcorn','Popcorn',5.0,390,['shareable'],'butter'),
      item(4,'Nachos w/ Cheese','Hot Food',7.5,560,['shareable'],'nachos'),
      item(5,'Hot Dog','Hot Food',6.5,520,[],'hotdog'),
      item(6,'Chicken Bites','Hot Food',7.9,610,['shareable'],'chicken'),
      item(7,'Coca-Cola (L)','Drinks',4.0,210,[],'cola'),
      item(8,'Sprite (L)','Drinks',4.0,190,[],'sprite'),
      item(9,'Iced Lemon Tea','Drinks',4.2,160,['low-cal'],'tea'),
      item(10,'Mineral Water','Drinks',3.0,0,['low-cal','vegan','gluten-free'],'water'),
      item(11,'M&M’s','Candy',4.3,240,[],'mms'),
      item(12,'Skittles','Candy',4.0,230,['vegan','gluten-free'],'skittles'),
      item(13,'Gummy Bears','Candy',3.9,210,['gluten-free'],'gummy'),
      item(14,'Combo A: Popcorn + Drink','Combos',7.9,560,['shareable'],'comboA'),
      item(15,'Combo B: Nachos + Drink','Combos',9.5,750,['shareable'],'comboB'),
      item(16,'Combo C: Hot Dog + Drink','Combos',9.0,730,[],'comboC'),
    ];

    // ===== DOM helpers =====
    const $ = (s, r=document)=>r.querySelector(s);
    const $$ = (s, r=document)=>[...r.querySelectorAll(s)];

    // ===== State =====
    const state = {
      q: '',
      category: 'all',
      sort: 'popular',
      chips: new Set()
    };

    // ===== Rendering =====
    function cardTemplate(i){
      return `
        <article class="card" data-id="${i.id}">
          <img class="thumb" src="${i.img}" alt="${i.name}" loading="lazy" />
          <div class="card-body">
            <div class="title">${i.name}</div>
            <div class="muted" style="margin-top:4px">${i.category}</div>
            <div class="meta">
              <span class="price">$${i.price.toFixed(2)}</span>
              <span class="tag">${i.calories} kcal</span>
            </div>
            ${i.tags.length ? `<div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:8px;">${i.tags.map(t=>`<span class="tag">${t}</span>`).join('')}</div>` : ``}
          </div>
        </article>
      `;
    }

    function applyFilters(){
      let list = ITEMS.slice();

      // text search
      if(state.q){
        const q = state.q.toLowerCase();
        list = list.filter(i => `${i.name} ${i.category} ${i.tags.join(' ')}`.toLowerCase().includes(q));
      }

      // category
      if(state.category !== 'all'){
        list = list.filter(i => i.category === state.category);
      }

      // chips (all selected must match)
      if(state.chips.size){
        list = list.filter(i => [...state.chips].every(tag => i.tags.includes(tag)));
      }

      // sort
      switch(state.sort){
        case 'price-asc': list.sort((a,b)=>a.price-b.price); break;
        case 'price-desc': list.sort((a,b)=>b.price-a.price); break;
        case 'calories-asc': list.sort((a,b)=>a.calories-b.calories); break;
        case 'calories-desc': list.sort((a,b)=>b.calories-a.calories); break;
        default: /* popular */ list.sort((a,b)=>a.id-b.id); break;
      }

      // render
      $('#grid').innerHTML = list.map(cardTemplate).join('') || `<p class="muted">No items match your filters.</p>`;
    }

    // ===== Events =====
    function setup(){
      // More dropdown a11y
      (function moreMenu(){
        const container = document.querySelector('.has-dropdown');
        if(!container) return;
        const trigger = container.querySelector('.more-trigger');
        function set(open){ trigger.setAttribute('aria-expanded', open?'true':'false'); }
        container.addEventListener('mouseenter', ()=>set(true));
        container.addEventListener('mouseleave', ()=>set(false));
        trigger.addEventListener('focus', ()=>set(true));
        document.addEventListener('click', (e)=>{ if(!container.contains(e.target)) set(false); });
        trigger.addEventListener('keydown', (e)=>{ if(e.key==='Enter'||e.key===' '){ e.preventDefault(); set(trigger.getAttribute('aria-expanded')!=='true'); }});
      })();

      $('#year').textContent = new Date().getFullYear();

      // search
      $('#searchInput').addEventListener('input', e => { state.q = e.target.value.trim(); applyFilters(); });

      // selects (already readable via CSS)
      $('#category').addEventListener('change', e => { state.category = e.target.value; applyFilters(); });
      $('#sort').addEventListener('change', e => { state.sort = e.target.value; applyFilters(); });

      // chips
      $('#chips').addEventListener('click', (e)=>{
        const chip = e.target.closest('.chip'); if(!chip) return;
        const key = chip.dataset.chip;
        if(chip.classList.toggle('active')) state.chips.add(key); else state.chips.delete(key);
        applyFilters();
      });

      applyFilters();
    }

    document.addEventListener('DOMContentLoaded', setup);
  </script>
</body>
</html>
