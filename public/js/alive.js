/* ==========================================================================
   ALIVE LAYER — no dependencies. Reads the markup Laravel already renders
   (and the content Dia edits in the admin) and adds life on top.
   Each feature is isolated: if one fails, the rest keep working.
   ========================================================================== */
(() => {
  'use strict';

  const root = document.documentElement;
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fine = matchMedia('(hover: hover) and (pointer: fine)').matches;
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const mk = (tag, cls, text) => { const n = document.createElement(tag); if (cls) n.className = cls; if (text) n.textContent = text; return n; };
  const pad = (n, l = 2) => String(n).padStart(l, '0');
  const onScroll = fn => { let t = false; addEventListener('scroll', () => { if (t) return; t = true; requestAnimationFrame(() => { fn(); t = false; }); }, { passive: true }); };
  const hasIO = 'IntersectionObserver' in window;

  root.classList.add('alive');

  /* ---- 1. Scroll progress bar -------------------------------------------- */
  function progress() {
    const bar = mk('div', 'alive-progress');
    bar.setAttribute('aria-hidden', 'true');
    document.body.prepend(bar);
    const update = () => {
      const h = root.scrollHeight - innerHeight;
      bar.style.transform = `scaleX(${h > 0 ? Math.min(1, scrollY / h) : 0})`;
    };
    onScroll(update); addEventListener('resize', update); update();
  }

  /* ---- 2. Header: hides on scroll down, returns on scroll up, tracks section */
  function header() {
    const h = $('.site-header'); if (!h) return;
    let last = scrollY;
    onScroll(() => {
      const y = scrollY, down = y > last + 6, up = y < last - 6;
      h.classList.toggle('is-scrolled', y > 10);
      if (down && y > 320) h.classList.add('is-hidden');
      else if (up || y < 320) h.classList.remove('is-hidden');
      if (down || up) last = y;
    });
    h.addEventListener('focusin', () => h.classList.remove('is-hidden'));

    const links = $$('nav a[href*="#"]', h), map = new Map();
    links.forEach(a => { const s = a.hash && document.getElementById(a.hash.slice(1)); if (s) map.set(s, a); });
    if (!map.size || !hasIO) return;
    const io = new IntersectionObserver(entries => entries.forEach(e => {
      const a = map.get(e.target);
      if (a && e.isIntersecting) { links.forEach(l => l.removeAttribute('aria-current')); a.setAttribute('aria-current', 'true'); }
    }), { rootMargin: '-40% 0px -55% 0px' });
    map.forEach((_, s) => io.observe(s));
  }

  /* ---- 3. Headline: words rise from a mask ------------------------------- */
  function headline() {
    const h = $('.section-hero h1'); if (!h || reduce) return;
    const text = h.textContent.trim();
    h.setAttribute('aria-label', text);
    h.textContent = '';
    text.split(/\s+/).forEach((word, i) => {
      const outer = mk('span', 'w'), inner = mk('span', '', word);
      outer.setAttribute('aria-hidden', 'true');
      outer.style.setProperty('--i', i);
      outer.append(inner); h.append(outer, ' ');
    });
    requestAnimationFrame(() => requestAnimationFrame(() => h.classList.add('is-in')));
  }

  /* ---- 4. Hero lab: blueprint spotlight, coordinates, parallax, local time */
  function heroLab() {
    const hero = $('.section-hero'); if (!hero) return;
    const art = $('.hero-art', hero);

    // live local time (Lebanon) in the hero footer
    const bottom = $('.hero-bottom', hero);
    if (bottom) {
      const place = ($('.contact-links > span') || {}).textContent || 'Lebanon';
      const stamp = mk('span', 'hero-clock');
      stamp.setAttribute('aria-hidden', 'true');
      bottom.insertBefore(stamp, bottom.lastElementChild);
      const tick = () => {
        let t = '';
        try { t = new Intl.DateTimeFormat('en-GB', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'Asia/Beirut' }).format(new Date()); } catch (e) { return; }
        stamp.textContent = `${place.trim()} · ${t}`;
      };
      tick(); setInterval(tick, 20000);
    }

    if (fine && !reduce) {
      const glow = mk('div', 'hero-glow'); glow.setAttribute('aria-hidden', 'true'); hero.prepend(glow);
      const coords = mk('span', 'hero-coords'); coords.setAttribute('aria-hidden', 'true'); hero.append(coords);
      hero.addEventListener('pointermove', e => {
        const r = hero.getBoundingClientRect(), x = e.clientX - r.left, y = e.clientY - r.top;
        hero.style.setProperty('--mx', x + 'px'); hero.style.setProperty('--my', y + 'px');
        hero.classList.add('is-hot');
        coords.style.transform = `translate(${x + 18}px,${y + 18}px)`;
        coords.textContent = `X ${pad(Math.round(x), 4)}\nY ${pad(Math.round(y), 4)}`;
        if (art) {
          art.style.setProperty('--ry', ((e.clientX / innerWidth - .5) * 8).toFixed(2) + 'deg');
          art.style.setProperty('--rx', ((.5 - e.clientY / innerHeight) * 6).toFixed(2) + 'deg');
        }
      });
      hero.addEventListener('pointerleave', () => {
        hero.classList.remove('is-hot');
        if (art) { art.style.setProperty('--rx', '0deg'); art.style.setProperty('--ry', '0deg'); }
      });
    }
    if (art && !reduce) onScroll(() => { if (scrollY < innerHeight * 1.5) art.style.setProperty('--py', (scrollY * -.08).toFixed(1) + 'px'); });
  }

  /* ---- 5. Sun studio: drag the sun, watch light and shadow change -------- */
  function sun() {
    const img = $('.hero-art img'), col = $('.section-hero .hero-grid > div');
    if (!img || !col) return;
    const stage = mk('div', 'sun-stage');
    img.replaceWith(stage); stage.append(img);
    stage.append(mk('span', 'sun-glow'), mk('span', 'sun-shade'), mk('span', 'sun-disc'));
    stage.querySelectorAll('span').forEach(s => s.setAttribute('aria-hidden', 'true'));

    const ctl = mk('div', 'sun-ctl');
    ctl.innerHTML = '<label for="sun-range"><span class="sun-ico" aria-hidden="true"></span>Move the sun</label>' +
      '<input id="sun-range" type="range" min="0" max="100" value="22" step="1" aria-describedby="sun-out">' +
      '<output id="sun-out" class="sun-out" for="sun-range"></output>';
    col.append(ctl);
    const range = $('input', ctl), out = $('output', ctl);

    const set = t => {
      const s = Math.sin(Math.PI * t);                 // 0 at dawn/dusk, 1 at noon
      stage.style.setProperty('--sx', (6 + 88 * t).toFixed(1) + '%');
      stage.style.setProperty('--sy', (84 - s * 64).toFixed(1) + '%');
      stage.style.setProperty('--shade', (.08 + (1 - s) * .42).toFixed(3));
      stage.style.setProperty('--shade-angle', (t < .5 ? 270 : 90) + 'deg');
      stage.style.setProperty('--warm', ((1 - s) * .55).toFixed(3));
      const mins = Math.round((6 + 12 * t) * 60);
      const elev = 5 + s * 62;                         // sun height in degrees
      const len = Math.min(60, 10 / Math.tan(elev * Math.PI / 180));
      out.textContent = `${pad(Math.floor(mins / 60))}:${pad(mins % 60)} · a 10 m facade casts a ${len < 10 ? len.toFixed(1) : Math.round(len)} m shadow`;
    };
    set(range.value / 100);
    range.addEventListener('input', () => { auto = false; set(range.value / 100); });

    // gentle idle drift until the visitor takes over
    let auto = !reduce, visible = true;
    if (hasIO) new IntersectionObserver(([e]) => { visible = e.isIntersecting; }).observe(stage);
    const loop = now => {
      if (auto && visible) {
        const t = .5 + .5 * Math.sin(now / 4800 - 1.9);
        range.value = Math.round(t * 100); set(t);
      }
      if (auto) requestAnimationFrame(loop);
    };
    if (auto) requestAnimationFrame(loop);
  }

  /* ---- 6. Reveal on scroll ------------------------------------------------ */
  function reveal() {
    if (reduce || !hasIO) return;
    root.classList.add('reveal-on');
    const sel = '.section-heading,.project-card,.timeline article,.service-grid article,.skill-groups>div,.about-grid>*,.contact-grid>*,.hero-art,.alive-stats,.filters,.cut-compare,.plan-explorer,.maquette-detail';
    const io = new IntersectionObserver(entries => entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
    }), { threshold: .12, rootMargin: '0px 0px -6% 0px' });
    $$(sel).forEach(el => {
      el.classList.add('rv');
      const idx = Array.prototype.indexOf.call(el.parentElement.children, el);
      el.style.setProperty('--d', (idx % 4) * 110 + 'ms');
      io.observe(el);
    });
    // replay the entrance when a filter is chosen
    $$('.filters button').forEach(b => b.addEventListener('click', () => {
      $$('.project-card:not([hidden])').forEach((c, i) => {
        c.classList.remove('in'); c.style.setProperty('--d', i * 80 + 'ms'); void c.offsetWidth;
        requestAnimationFrame(() => c.classList.add('in'));
      });
    }));
  }

  /* ---- 8. Project cards: 3D tilt + glare + whole-image click ------------- */
  function cards() {
    $$('.project-card').forEach(card => {
      const vis = $('.architecture-visual', card), link = $('h3 a', card);
      if (!vis) return;
      if (link) {
        vis.style.cursor = 'pointer';
        vis.addEventListener('click', e => { if (!e.target.closest('a,button,model-viewer')) location.href = link.href; });
      }
      if (!fine || reduce || $('model-viewer', vis)) return;   // keep orbit-drag on 3D models
      const glare = mk('span', 'glare'); glare.setAttribute('aria-hidden', 'true'); vis.append(glare);
      let raf = 0;
      vis.addEventListener('pointermove', e => {
        const r = vis.getBoundingClientRect(), x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => {
          vis.style.transform = `perspective(900px) rotateX(${((.5 - y) * 7).toFixed(2)}deg) rotateY(${((x - .5) * 9).toFixed(2)}deg) scale(1.012)`;
          glare.style.setProperty('--gx', (x * 100).toFixed(0) + '%'); glare.style.setProperty('--gy', (y * 100).toFixed(0) + '%');
        });
      });
      vis.addEventListener('pointerleave', () => { cancelAnimationFrame(raf); vis.style.transform = ''; });
    });
  }

  /* ---- 9. Numbers that count up (derived from the page, never invented) -- */
  function stats() {
    const prose = $('.section-about .prose'); if (!prose) return;
    const groups = $$('.section-skills .skill-groups > div');
    const countIn = re => { const g = groups.find(x => re.test(($('h3', x) || {}).textContent || '')); return g ? $$('.skill', g).length : 0; };
    const items = [
      [$$('.section-experience .timeline article').length, 'Internships'],
      [countIn(/software|tool/i), 'Design tools'],
      [countIn(/language/i), 'Languages']
    ].filter(([n]) => n > 0);
    if (!items.length) return;
    const box = mk('div', 'alive-stats');
    items.forEach(([n, label]) => { const d = mk('div'), b = mk('b', '', reduce ? String(n) : '0'); b.dataset.n = n; d.append(b, mk('span', '', label)); box.append(d); });
    prose.after(box);
    if (reduce || !hasIO) { $$('b', box).forEach(b => { b.textContent = b.dataset.n; }); return; }
    const io = new IntersectionObserver(([e]) => {
      if (!e.isIntersecting) return; io.disconnect();
      $$('b', box).forEach(b => {
        const to = +b.dataset.n, t0 = performance.now(), dur = 900 + to * 120;
        const step = now => { const p = Math.min(1, (now - t0) / dur); b.textContent = Math.round(to * (1 - Math.pow(1 - p, 3))); if (p < 1) requestAnimationFrame(step); };
        requestAnimationFrame(step);
      });
    }, { threshold: .6 });
    io.observe(box);
  }

  /* ---- 10. Blueprint mode (button, or press B) ---------------------------- */
  function blueprint() {
    const btn = mk('button', 'bp-toggle');
    btn.type = 'button'; btn.setAttribute('aria-pressed', 'false'); btn.title = 'Toggle blueprint mode (B)';
    btn.innerHTML = '<span aria-hidden="true">◐</span> Blueprint';
    document.body.append(btn);
    const apply = on => {
      document.body.classList.toggle('blueprint', on);
      btn.setAttribute('aria-pressed', String(on));
      try { localStorage.setItem('alive-bp', on ? '1' : '0'); } catch (e) { /* storage may be blocked */ }
    };
    let saved = false; try { saved = localStorage.getItem('alive-bp') === '1'; } catch (e) { /* ignore */ }
    apply(saved);
    btn.addEventListener('click', () => apply(!document.body.classList.contains('blueprint')));
    addEventListener('keydown', e => {
      if (e.key.toLowerCase() !== 'b' || e.metaKey || e.ctrlKey || e.altKey) return;
      if (/input|textarea|select/i.test(e.target.tagName) || e.target.isContentEditable) return;
      btn.click();
    });
  }

  /* ---- 11. Magnetic buttons ----------------------------------------------- */
  function magnetic() {
    if (!fine || reduce) return;
    $$('.button,.nav-download,.project-arrow,.bp-toggle').forEach(el => {
      el.addEventListener('pointermove', e => {
        const r = el.getBoundingClientRect();
        el.style.translate = `${((e.clientX - r.left - r.width / 2) * .28).toFixed(1)}px ${((e.clientY - r.top - r.height / 2) * .35).toFixed(1)}px`;
      });
      el.addEventListener('pointerleave', () => { el.style.translate = ''; });
    });
  }

  /* ---- 12. Cursor ring ("View" over projects) ----------------------------- */
  function cursor() {
    if (!fine || reduce) return;
    const ring = mk('div', 'alive-cursor'), label = mk('span');
    ring.setAttribute('aria-hidden', 'true'); ring.append(label); document.body.append(ring);
    let x = -100, y = -100, rx = x, ry = y, shown = false;
    addEventListener('pointermove', e => {
      if (e.pointerType === 'touch') return;
      x = e.clientX; y = e.clientY;
      if (!shown) { shown = true; rx = x; ry = y; ring.classList.add('on'); }
      const interactive = e.target.closest('a,button,input,textarea,label,[data-cursor]');
      const view = !interactive && e.target.closest('.architecture-visual') && !e.target.closest('model-viewer');
      ring.classList.toggle('is-link', Boolean(interactive));
      ring.classList.toggle('has-label', Boolean(view));
      if (view) label.textContent = 'View';
    }, { passive: true });
    root.addEventListener('mouseleave', () => { ring.classList.remove('on'); shown = false; });
    const loop = () => {
      rx += (x - rx) * .2; ry += (y - ry) * .2;
      ring.style.transform = `translate3d(${rx.toFixed(1)}px,${ry.toFixed(1)}px,0)`;
      requestAnimationFrame(loop);
    };
    loop();
  }

  /* ---- 13. Copy e-mail with a toast, and a friendlier send button --------- */
  function contact() {
    const mail = $('.contact-links a[href^="mailto:"]');
    if (mail && navigator.clipboard) {
      const row = mk('span', 'copy-row'), btn = mk('button', 'copy-btn', 'Copy');
      btn.type = 'button'; btn.setAttribute('aria-label', 'Copy email address');
      mail.before(row); row.append(mail, btn);
      const toast = mk('div', 'alive-toast', 'Email copied'); toast.setAttribute('role', 'status'); document.body.append(toast);
      btn.addEventListener('click', async () => {
        try { await navigator.clipboard.writeText(mail.getAttribute('href').replace(/^mailto:/i, '')); } catch (e) { return; }
        toast.classList.add('show'); setTimeout(() => toast.classList.remove('show'), 1800);
      });
    }
    const form = $('.contact-form');
    if (form) form.addEventListener('submit', () => { const b = $('button[type=submit]', form); if (b) b.firstChild.textContent = 'Sending… '; });
  }

  [progress, header, headline, heroLab, sun, reveal, cards, stats, blueprint, magnetic, cursor, contact]
    .forEach(fn => { try { fn(); } catch (err) { console.error('[alive]', fn.name, err); } });
})();
