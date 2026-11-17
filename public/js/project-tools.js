/* Project page tools: section-cut slider + click-through floor plan. No dependencies. */
(() => {
  'use strict';
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const clamp = (v, a, b) => Math.min(b, Math.max(a, v));

  /* ======================= Section cut ======================= */
  document.querySelectorAll('[data-cut]').forEach(root => {
    const stage = root.querySelector('[data-cut-stage]');
    const range = root.querySelector('.cut-range');
    const under = root.querySelector('[data-cut-under]');
    if (!stage || !range) return;

    let value = 50, dragging = false, sweep = 0;
    const set = v => {
      value = clamp(v, 0, 100);
      stage.style.setProperty('--p', value + '%');
      range.value = String(Math.round(value));
      stage.classList.toggle('hide-left', value < 18);
      stage.classList.toggle('hide-right', value > 82);
    };
    const fromEvent = e => { const r = stage.getBoundingClientRect(); set((e.clientX - r.left) / r.width * 100); };
    const stopSweep = () => { if (sweep) { cancelAnimationFrame(sweep); sweep = 0; } };

    // match the frame to the artwork so nothing is cropped unexpectedly
    const fit = () => { if (under.naturalWidth && under.naturalHeight) stage.style.aspectRatio = under.naturalWidth + ' / ' + under.naturalHeight; };
    if (under.complete) fit(); else under.addEventListener('load', fit, { once: true });

    stage.addEventListener('pointerdown', e => {
      if (e.button > 0) return;
      stopSweep(); dragging = true; stage.classList.add('is-dragging');
      stage.setPointerCapture(e.pointerId); fromEvent(e);
    });
    stage.addEventListener('pointermove', e => { if (dragging) fromEvent(e); });
    const end = () => { dragging = false; stage.classList.remove('is-dragging'); };
    stage.addEventListener('pointerup', end);
    stage.addEventListener('pointercancel', end);
    range.addEventListener('input', () => { stopSweep(); set(+range.value); });
    range.addEventListener('focus', stopSweep);

    // one slow sweep the first time it scrolls into view, so people see it is draggable
    set(50);
    if (!reduce && 'IntersectionObserver' in window) {
      const io = new IntersectionObserver(([entry]) => {
        if (!entry.isIntersecting) return;
        io.disconnect();
        const keys = [50, 14, 86, 50], seg = 700, t0 = performance.now();
        const step = now => {
          const t = (now - t0) / seg, i = Math.min(Math.floor(t), keys.length - 2), k = t - i;
          const ease = k * k * (3 - 2 * k);
          set(keys[i] + (keys[i + 1] - keys[i]) * ease);
          sweep = t < keys.length - 1 ? requestAnimationFrame(step) : 0;
        };
        sweep = requestAnimationFrame(step);
      }, { threshold: .6 });
      io.observe(stage);
    }
  });

  /* ======================= Floor plan ======================= */
  document.querySelectorAll('[data-plan]').forEach(root => {
    const rooms = Array.from(root.querySelectorAll('[data-room]'));
    const items = Array.from(root.querySelectorAll('[data-room-item]'));
    const q = s => root.querySelector(s);
    const empty = q('[data-plan-empty]'), detail = q('[data-plan-detail]');
    const number = q('[data-plan-number]'), name = q('[data-plan-name]'), size = q('[data-plan-size]');
    const note = q('[data-plan-note]'), photo = q('[data-plan-photo]');
    const prev = q('[data-plan-prev]'), next = q('[data-plan-next]'), play = q('[data-plan-play]');
    if (!rooms.length || !detail) return;

    let current = -1, timer = 0;
    const small = () => matchMedia('(max-width: 850px)').matches;

    const select = (i, opts = {}) => {
      i = (i + rooms.length) % rooms.length;
      const room = rooms[i], d = room.dataset;
      current = i;
      rooms.forEach((r, n) => { r.classList.toggle('is-active', n === i); r.setAttribute('aria-pressed', String(n === i)); });
      items.forEach((b, n) => b.classList.toggle('is-active', n === i));
      empty.hidden = true; detail.hidden = false;
      // restart the entrance animation
      detail.style.animation = 'none'; void detail.offsetWidth; detail.style.animation = '';
      number.textContent = String(i + 1).padStart(2, '0');
      name.textContent = d.name;
      size.textContent = d.size; size.hidden = !d.size;
      note.textContent = d.note; note.hidden = !d.note;
      if (d.image) { photo.src = d.image; photo.alt = d.name; photo.hidden = false; } else { photo.hidden = true; photo.removeAttribute('src'); }
      if (opts.reveal && small()) detail.scrollIntoView({ block: 'nearest', behavior: reduce ? 'auto' : 'smooth' });
    };
    const stop = () => { clearInterval(timer); timer = 0; play.setAttribute('aria-pressed', 'false'); play.textContent = '▶ Play tour'; };
    const start = () => {
      play.setAttribute('aria-pressed', 'true'); play.textContent = '❚❚ Pause tour';
      select(current < 0 ? 0 : current + 1);
      timer = setInterval(() => select(current + 1), 3400);
    };

    rooms.forEach((r, i) => r.addEventListener('click', () => { stop(); select(i, { reveal: true }); }));
    items.forEach((b, i) => b.addEventListener('click', () => { stop(); select(i, { reveal: true }); }));
    prev.addEventListener('click', () => { stop(); select(current < 0 ? rooms.length - 1 : current - 1); });
    next.addEventListener('click', () => { stop(); select(current + 1); });
    play.addEventListener('click', () => (timer ? stop() : start()));
    root.addEventListener('keydown', e => {
      if (e.target.matches('input,textarea')) return;
      if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); stop(); select(current + 1); rooms[current].focus({ preventScroll: true }); }
      if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { e.preventDefault(); stop(); select(current < 0 ? rooms.length - 1 : current - 1); rooms[current].focus({ preventScroll: true }); }
      if (e.key === 'Escape') { stop(); current = -1; rooms.forEach(r => { r.classList.remove('is-active'); r.setAttribute('aria-pressed', 'false'); }); items.forEach(b => b.classList.remove('is-active')); detail.hidden = true; empty.hidden = false; }
    });
    document.addEventListener('visibilitychange', () => { if (document.hidden && timer) stop(); });
  });
})();
