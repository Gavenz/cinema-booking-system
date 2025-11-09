<?php 
require_once __DIR__ ."/../includes/init.php"; 
require_once __DIR__ . '/../includes/flash.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us — Big Premiere Point</title>
  <meta name="description" content="Learn about Big Premiere Point — our story, mission, vision, values, and team." />
  <link rel="preconnect" href="https://images.unsplash.com" crossorigin>

  <!-- ===== Combined Styles (includes your provided snippet) ===== -->
  <style>
    :root{
      --bg:#0b0b0f;--panel:#12121a;--muted:#8b8ba1;--text:#f3f3f8;
      --accent:#e50914;--accent-2:#f5c518;--ring:0 0 0 2px rgba(229,9,20,.45);
      --card:#1a1a24;--card-hover:#20202c;--radius:16px;--shadow:0 10px 30px rgba(0,0,0,.45);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,"Apple Color Emoji","Segoe UI Emoji";
      color:var(--text);
      background:radial-gradient(1200px 600px at 20% -10%,#1b1b28 0%,transparent 60%),
                 radial-gradient(800px 500px at 100% 0%,#231c24 0%,transparent 60%),
                 var(--bg);
      overflow-x:hidden;
    }

    /* ===== Top Nav (header) ===== */
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
    .search-wrap input{background:transparent;border:0;outline:none;color:var(--text);flex:1;padding:6px 6px;font-size:.95rem}
    .icon{opacity:.8}
    .btn{
      appearance:none;border:0;background:var(--accent);color:#fff;font-weight:700;
      padding:10px 14px;border-radius:999px;cursor:pointer;box-shadow:0 10px 20px rgba(229,9,20,.25)
    }
    .btn:hover{filter:brightness(1.05)}
    .btn.small{font-size:.9rem;padding:8px 10px}
    .btn.ghost{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15)}
    .input,.textarea{
      width:100%;padding:10px 12px;border-radius:10px;
      background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);color:var(--text);outline:none;resize:vertical
    }
    .input:focus,.textarea:focus{box-shadow:var(--ring);border-color:rgba(255,255,255,.35)}
    .has-dropdown{position:relative;display:flex;align-items:center}
    .more-trigger{display:inline-flex;align-items:center;gap:6px;background:transparent;border:0;cursor:pointer}
    .dropdown{
      position:absolute;top:calc(100% + 8px);right:0;min-width:220px;z-index:60;
      background:#12121a;border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:8px;display:none
    }
    .dropdown a{display:block;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text);font-weight:600;font-size:.95rem}
    .dropdown a:hover{background:rgba(255,255,255,.06)}
    .has-dropdown:hover .dropdown,.has-dropdown:focus-within .dropdown{display:block}

    /* ===== Hero (About) ===== */
    .hero{
      position:relative;isolation:isolate;max-width:1300px;margin:24px auto;padding:20px;border-radius:var(--radius);
      background:linear-gradient(180deg,rgba(0,0,0,.55),rgba(0,0,0,.7)),url("https://picsum.photos/seed/about-hero/1400/700");
      background-size:cover;background-position:center;min-height:42vh;display:grid;align-content:end;box-shadow:var(--shadow)
    }
    .hero-content{max-width:820px}
    .badge{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);padding:6px 10px;border-radius:999px;font-size:.85rem}
    .hero h1{margin:10px 0 6px;font-size:clamp(1.75rem,3vw + .5rem,3rem)}
    .hero p{margin:0 0 16px;color:#d9d9e7;line-height:1.5}

    /* ===== Sections, Cards, Grid ===== */
    .section{max-width:1300px;margin:0 auto;padding:8px 20px 24px}
    .section h2{font-size:1.2rem;font-weight:800;letter-spacing:.4px;margin:18px 2px}
    .lead{color:#d9d9e7;font-size:1.05rem;line-height:1.6}

    .grid{display:grid;grid-template-columns:repeat(12,1fr);gap:16px}
    .card{
      background:linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,.02));
      border:1px solid rgba(255,255,255,.08);border-radius:14px;overflow:hidden;position:relative;box-shadow:0 10px 20px rgba(0,0,0,.35);
      transition:transform .25s ease,box-shadow .25s ease,background .25s ease
    }
    .card:hover{transform:translateY(-4px);box-shadow:0 16px 34px rgba(0,0,0,.55);background:var(--card-hover)}
    .card-body{padding:14px}
    .pill{display:inline-flex;gap:6px;align-items:center;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);padding:4px 10px;border-radius:999px;font-size:.8rem}
    .avatar{width:100%;aspect-ratio:1/1;object-fit:cover;display:block;border-bottom:1px solid rgba(255,255,255,.08)}
    .muted{color:var(--muted)}

    /* FAQ */
    details.faq{
      background:linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,.02));
      border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:10px 12px
    }
    details.faq + details.faq{margin-top:10px}
    details.faq > summary{cursor:pointer;font-weight:700;list-style:none}
    details.faq > summary::-webkit-details-marker{display:none}

    /* Contact */
    .contact-wrap{display:grid;grid-template-columns:repeat(12,1fr);gap:16px}
    .contact-card{grid-column:span 6}
    .contact-form{grid-column:span 6}
    .contact-list{list-style:none;padding:0;margin:0;display:grid;gap:10px}
    /* Updated link colors for readability */
    .contact-list a{color:var(--text);text-decoration:underline}
    .contact-list a:hover{color:var(--accent-2)}

    /* Footer */
    footer{max-width:1300px;margin:14px auto 40px;padding:0 20px;color:var(--muted);display:flex;justify-content:space-between;align-items:center}

    /* Responsive */
    @media (max-width:1000px){.contact-card,.contact-form{grid-column:span 12}}
    @media (max-width:800px){
      .nav-links{display:none}
      .dropdown{position:fixed;top:60px;right:16px;left:16px}
      .grid{grid-template-columns:repeat(6,1fr)}
    }
  </style>
</head>
<body>

  <?php include __DIR__ . '/../includes/header.php'; ?>  
  
  <!-- ===== HERO ===== -->
  <section class="hero" aria-label="About Big Premiere Point">
    <div class="hero-content">
      <span class="badge"><span aria-hidden>🎬</span> About Us</span>
      <h1>Movies, made magical for students</h1>
      <p class="lead">Big Premiere Point is a student-built cinema brand delivering blockbuster vibes on a student budget—crafted with care, community, and a whole lot of popcorn.</p>
      <div class="hero-cta" style="display:flex; gap:12px; align-items:center;">
        <span class="pill">Founded 2025 • Singapore</span>
      </div>
    </div>
  </section>

  <!-- ===== SNAP EXPLAINER ===== -->
  <section class="section" id="snapshot">
    <h2>What we do (the 10-second version)</h2>
    <p class="lead">
      We run student-friendly screenings with premium presentation—curated schedules, comfy seats, fair pricing, and a super-simple booking flow.
      Think <strong>big-premiere energy</strong> without the big-premiere cost.
    </p>
  </section>

  <!-- ===== TEAM (2 people) ===== -->
  <section class="section" id="team">
    <h2>Meet the team</h2>
    <div class="grid" role="list">
      <article class="card" role="listitem" style="grid-column: span 6;">
        <img class="avatar" src="https://picsum.photos/seed/team-alex/800/800" alt="Brandon Lam — Co-founder & Frontend Developer">
        <div class="card-body">
          <strong>Brandon Lam</strong>
          <div class="muted">Co-founder • Frontend Developer</div>
          <p class="muted" style="margin-top:8px">Curates lineups, hunts cult gems, and sweats the UI details.</p>
        </div>
      </article>

      <article class="card" role="listitem" style="grid-column: span 6;">
        <img class="avatar" src="https://picsum.photos/seed/team-maya/800/800" alt="Gaven Peh — Co-founder & Backend Developer">
        <div class="card-body">
          <strong>Gaven Peh</strong>
          <div class="muted">Co-founder • Backend Developer</div>
          <p class="muted" style="margin-top:8px">Keeps payments snappy and the servers smiling.</p>
        </div>
      </article>
    </div>
  </section>

  <!-- ===== MISSION • VISION • VALUES ===== -->
  <section class="section" id="mvv">
    <h2>Mission, Vision & Values</h2>
    <div class="grid">
      <article class="card" style="grid-column: span 4;">
        <img class="avatar" src="https://picsum.photos/seed/mission-card/800/600" alt="Projector light symbolizing mission">
        <div class="card-body">
          <strong>Our Mission</strong>
          <p class="muted" style="margin-top:8px">
            Make premium cinema <em>accessible</em> to students—fair pricing, exceptional presentation, and a smooth booking experience
            so every night at the movies feels like opening night.
          </p>
        </div>
      </article>

      <article class="card" style="grid-column: span 4;">
        <img class="avatar" src="https://picsum.photos/seed/vision-card/800/600" alt="City skyline and marquee symbolizing vision">
        <div class="card-body">
          <strong>Our Vision</strong>
          <p class="muted" style="margin-top:8px">
            A vibrant, student-powered cinema network across campuses—where creators get discovered, communities connect,
            and film culture thrives.
          </p>
        </div>
      </article>

      <article class="card" style="grid-column: span 4;">
        <img class="avatar" src="https://picsum.photos/seed/values-card/800/600" alt="Hands together symbolizing values">
        <div class="card-body">
          <strong>Our Values</strong>
          <p class="muted" style="margin-top:8px">
            <strong>Fairness</strong> (clear pricing), <strong>Craft</strong> (we sweat details),
            <strong>Inclusivity</strong> (everyone’s welcome), <strong>Delight</strong> (movies should feel magical).
          </p>
        </div>
      </article>
    </div>
  </section>

  <!-- ===== WHY WE STARTED (long version only) ===== -->
  <section class="section" id="founders">
    <h2>Why we started Big Premiere Point</h2>
    <div class="grid" role="list">
      <article class="card" role="listitem" style="grid-column: span 12;">
        <img class="avatar" src="https://picsum.photos/seed/founders-2/1200/600" alt="Student screening night with a packed audience">
        <div class="card-body">
          <strong>The longer version</strong>
          <p class="muted" style="margin-top:8px">
            After running campus screenings and hearing the same pain points—price, clunky booking, inconsistent quality—we rebuilt the pipeline:
            smarter scheduling, cleaner UX, and partnerships that pass savings to students while lifting presentation standards. We’re here to
            connect people with films (and each other) in welcoming, affordable spaces.
          </p>
        </div>
      </article>
    </div>
  </section>

  <!-- ===== FUN SIDE ===== -->
  <section class="section" id="fun">
    <h2>Our fun side</h2>
    <div class="grid">
      <article class="card" style="grid-column: span 4;">
        <img class="avatar" src="https://picsum.photos/seed/popcorn-lab/800/600" alt="Experimental popcorn flavors">
        <div class="card-body">
          <strong>🍿 Popcorn Lab</strong>
          <p class="muted" style="margin-top:8px">Rotating flavors (mala? matcha?) voted by students every month.</p>
        </div>
      </article>
      <article class="card" style="grid-column: span 4;">
        <img class="avatar" src="https://picsum.photos/seed/midnight-premieres/800/600" alt="Midnight premiere crowd">
        <div class="card-body">
          <strong>🎞️ Midnight Premieres</strong>
          <p class="muted" style="margin-top:8px">Fridays after 11pm, cult classics + costumes = prizes.</p>
        </div>
      </article>
      <article class="card" style="grid-column: span 4;">
        <img class="avatar" src="https://picsum.photos/seed/student-spotlight/800/600" alt="Student filmmaker Q&A">
        <div class="card-body">
          <strong>🎬 Student Spotlight</strong>
          <p class="muted" style="margin-top:8px">Show your short film before a feature. The crowd goes wild.</p>
        </div>
      </article>
    </div>
  </section>

  <!-- ===== WHY CHOOSE US ===== -->
  <section class="section" id="why">
    <h2>Why choose Big Premiere Point?</h2>
    <div class="grid">
      <article class="card" style="grid-column: span 4;">
        <img class="avatar" src="https://picsum.photos/seed/booking-fast/800/600" alt="Fast mobile booking">
        <div class="card-body">
          <strong>Lightning-fast booking</strong>
          <p class="muted" style="margin-top:8px">From pick to pay in under a minute, right on mobile.</p>
        </div>
      </article>
      <article class="card" style="grid-column: span 4;">
        <img class="avatar" src="https://picsum.photos/seed/student-pricing/800/600" alt="Wallet and ticket showing honest pricing">
        <div class="card-body">
          <strong>Student-first pricing</strong>
          <p class="muted" style="margin-top:8px">Fair base prices + honest bundles. Zero junk fees.</p>
        </div>
      </article>
      <article class="card" style="grid-column: span 4;">
        <img class="avatar" src="https://picsum.photos/seed/premium-av/800/600" alt="Projector and tuned audio">
        <div class="card-body">
          <strong>Premium presentation</strong>
          <p class="muted" style="margin-top:8px">Crisp projection, tuned audio, comfy seating. No compromises.</p>
        </div>
      </article>
    </div>

    <div style="display:flex; gap:12px; margin-top:16px;">
      <a class="btn" href="./index.html#movies">Get Tickets</a>
      <a class="btn ghost" href="./index.html#merch">Shop Merch</a>
    </div>
  </section>

  <!-- ===== FAQ ===== -->
  <section class="section" id="faq">
    <h2>Frequently Asked Questions</h2>
    <div class="grid">
      <div style="grid-column: span 8;">
        <details class="faq">
          <summary>Do you offer student discounts?</summary>
          <p class="muted" style="margin-top:8px">Yes—our base pricing is student-friendly and we run weekly bundles. Bring your student ID at entry.</p>
        </details>
        <details class="faq">
          <summary>How do I book seats?</summary>
          <p class="muted" style="margin-top:8px">Head to the Movies section on our homepage, pick a showtime, and checkout. Your e-ticket will be emailed instantly.</p>
        </details>
        <details class="faq">
          <summary>Can I host a club screening or event?</summary>
          <p class="muted" style="margin-top:8px">Absolutely. Reach out via the contact form with your date, film, and headcount—we’ll help you set it up.</p>
        </details>
        <details class="faq">
          <summary>Are your venues accessible?</summary>
          <p class="muted" style="margin-top:8px">We prioritize accessibility with step-free access, reserved seating, and captioned screenings where available.</p>
        </details>
      </div>
      <div style="grid-column: span 4;">
        <img class="avatar" src="https://picsum.photos/seed/faq-illustration/800/800" alt="Help and information illustration">
      </div>
    </div>
  </section>

  <!-- ===== CONTACT ===== -->
  <section class="section" id="contact">
    <h2>Contact us</h2>
    <div class="contact-wrap">
      <div class="card contact-card">
        <div class="card-body">
          <strong>Reach out</strong>
          <ul class="contact-list">
            <li><span class="muted">Email:</span> <a href="mailto:hello@bigpremierepoint.com">hello@bigpremierepoint.com</a></li>
            <li><span class="muted">Phone:</span> <a href="tel:+6560001234">+65 6000 1234</a></li>
            <li><span class="muted">Location:</span> 21 Cinema Walk, Singapore 018000</li>
            <li><span class="muted">Hours:</span> Mon–Sun, 10:00–22:00</li>
          </ul>
        </div>
      </div>

      <div class="card contact-form">
        <div class="card-body">
          <form id="contactForm">
            <div style="display:grid; gap:10px;">
              <label>
                <span class="muted" style="display:block; margin-bottom:6px;">Name</span>
                <input class="input" id="cName" required placeholder="Your name" />
              </label>
              <label>
                <span class="muted" style="display:block; margin-bottom:6px;">Email</span>
                <input class="input" id="cEmail" type="email" required placeholder="you@example.com" />
              </label>
              <label>
                <span class="muted" style="display:block; margin-bottom:6px;">Message</span>
                <textarea class="textarea" id="cMsg" rows="4" required placeholder="How can we help?"></textarea>
              </label>
              <button class="btn" type="submit" style="width:fit-content;">Send message</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <footer>
    <small>© <span id="year"></span> Big Premiere Point — Student Cinema Project</small>
    <small>Built with plain HTML, CSS &amp; JS</small>
  </footer>

  <!-- ===== Page Scripts (no login modal; Login goes to login.php) ===== -->
  <script>
    // Dropdown aria-expanded sync
    (function setupMoreMenu(){
      const container = document.querySelector('.has-dropdown');
      if (!container) return;
      const trigger = container.querySelector('.more-trigger');
      const openMenu = () => trigger.setAttribute('aria-expanded','true');
      const closeMenu = () => trigger.setAttribute('aria-expanded','false');
      container.addEventListener('mouseenter', openMenu);
      container.addEventListener('mouseleave', closeMenu);
      trigger.addEventListener('focus', openMenu);
      container.addEventListener('keydown', (e)=>{ if(e.key==='Escape'){ closeMenu(); trigger.blur(); }});
      document.addEventListener('click', (e)=>{ if(!container.contains(e.target)) closeMenu(); });
      trigger.addEventListener('keydown', (e)=>{
        if(e.key==='Enter' || e.key===' '){
          e.preventDefault();
          const expanded = trigger.getAttribute('aria-expanded')==='true';
          expanded ? closeMenu() : openMenu();
        }
      });
    })();

    // Contact form (demo only)
    (function setupContact(){
      const form = document.getElementById('contactForm');
      if (!form) return;
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        const name = document.getElementById('cName').value.trim();
        alert(`Thanks, ${name || 'friend'} — your message is on its way!`);
        form.reset();
      });
    })();

    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>
</html>
