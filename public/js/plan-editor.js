/* Visual editor for the click-through floor plan: draw a rectangle on the plan,
   name it, and the line is written into the textarea (the textarea stays the source of truth). */
(() => {
  const editor = document.querySelector('[data-plan-editor]');
  if (!editor) return;
  const $ = s => editor.querySelector(s);
  const text = document.querySelector('[data-plan-text]');
  const stage = $('[data-pe-stage]'), img = $('[data-pe-img]'), empty = $('[data-pe-empty]');
  const form = $('[data-pe-form]'), title = $('[data-pe-title]');
  const f = { name: $('[data-pe-name]'), size: $('[data-pe-size]'), note: $('[data-pe-note]'), image: $('[data-pe-image]') };
  const pathInput = document.getElementById(editor.dataset.pathInput);
  const uploadInput = document.getElementById(editor.dataset.uploadInput);
  const clean = v => String(v || '').replace(/[|\r\n]+/g, ' ').trim();   // "|" separates fields
  const round = v => Math.round(v * 10) / 10;
  let rooms = [], selected = -1, draft = null, objectUrl = '';

  /* ---- text <-> rooms ---- */
  const parse = () => text.value.split(/\r?\n/).map(line => {
    const p = line.split('|').map(x => x.trim());
    const box = (p[0] || '').split(',').map(x => parseFloat(x));
    if (p.length < 2 || !p[1] || box.length !== 4 || box.some(n => !isFinite(n))) return null;
    return { x: box[0], y: box[1], w: box[2], h: box[3], name: p[1], size: p[2] || '', note: p[3] || '', image: p[4] || '' };
  }).filter(Boolean);
  const write = () => {
    text.value = rooms.map(r => {
      const parts = [`${round(r.x)}, ${round(r.y)}, ${round(r.w)}, ${round(r.h)}`, r.name, r.size, r.note, r.image];
      while (parts.length > 2 && !parts[parts.length - 1]) parts.pop();
      return parts.join(' | ');
    }).join('\n');
  };

  /* ---- drawing the overlays ---- */
  const paint = () => {
    stage.querySelectorAll('.pe-room,.pe-draft').forEach(n => n.remove());
    rooms.forEach((r, i) => {
      const d = document.createElement('div');
      d.className = 'pe-room' + (i === selected ? ' is-selected' : '');
      d.style.cssText = `left:${r.x}%;top:${r.y}%;width:${r.w}%;height:${r.h}%`;
      d.dataset.index = i; d.title = r.name;
      const b = document.createElement('b'); b.textContent = i + 1; d.append(b);
      stage.append(d);
    });
    if (draft) {
      const d = document.createElement('div'); d.className = 'pe-draft';
      d.style.cssText = `left:${draft.x}%;top:${draft.y}%;width:${draft.w}%;height:${draft.h}%`;
      stage.append(d);
    }
  };
  const openForm = (i, isNew) => {
    selected = i;
    const r = isNew ? { name: '', size: '', note: '', image: '' } : rooms[i];
    f.name.value = r.name; f.size.value = r.size; f.note.value = r.note; f.image.value = r.image;
    title.textContent = isNew ? 'New room' : `Room ${i + 1}`;
    $('[data-pe-delete]').hidden = isNew;
    form.hidden = false; paint(); f.name.focus();
  };
  const closeForm = () => { form.hidden = true; selected = -1; draft = null; paint(); };

  /* ---- pointer interaction ---- */
  const pos = e => {
    const r = stage.getBoundingClientRect();
    return { x: Math.min(100, Math.max(0, (e.clientX - r.left) / r.width * 100)), y: Math.min(100, Math.max(0, (e.clientY - r.top) / r.height * 100)) };
  };
  let start = null;
  stage.addEventListener('pointerdown', e => {
    if (e.button > 0) return;
    const hit = e.target.closest('.pe-room');
    if (hit) { draft = null; openForm(+hit.dataset.index, false); return; }
    start = pos(e); draft = { x: start.x, y: start.y, w: 0, h: 0 };
    form.hidden = true; selected = -1;
    stage.setPointerCapture(e.pointerId); paint();
  });
  stage.addEventListener('pointermove', e => {
    if (!start) return;
    const p = pos(e);
    draft = { x: Math.min(start.x, p.x), y: Math.min(start.y, p.y), w: Math.abs(p.x - start.x), h: Math.abs(p.y - start.y) };
    paint();
  });
  const finish = () => {
    if (!start) return;
    start = null;
    if (draft && draft.w > 2 && draft.h > 2) openForm(-1, true); else { draft = null; paint(); }
  };
  stage.addEventListener('pointerup', finish);
  stage.addEventListener('pointercancel', finish);

  /* ---- form actions ---- */
  const save = () => {
    const name = clean(f.name.value);
    if (!name) { f.name.focus(); return; }
    const data = { name, size: clean(f.size.value), note: clean(f.note.value), image: clean(f.image.value) };
    if (selected === -1 && draft) { rooms.push({ x: draft.x, y: draft.y, w: draft.w, h: draft.h, ...data }); }
    else if (rooms[selected]) Object.assign(rooms[selected], data);
    write(); closeForm();
  };
  $('[data-pe-save]').addEventListener('click', save);
  $('[data-pe-cancel]').addEventListener('click', closeForm);
  $('[data-pe-delete]').addEventListener('click', () => { if (rooms[selected]) { rooms.splice(selected, 1); write(); } closeForm(); });
  form.addEventListener('keydown', e => {
    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') { e.preventDefault(); save(); }
    if (e.key === 'Escape') closeForm();
  });
  text.addEventListener('input', () => { rooms = parse(); selected = -1; form.hidden = true; draft = null; paint(); });

  /* ---- which image to draw on: a freshly chosen upload wins over the saved path ---- */
  const setImage = () => {
    const src = objectUrl || (pathInput && pathInput.value.trim()) || '';
    if (!src) { stage.hidden = true; empty.hidden = false; return; }
    img.src = src; stage.hidden = false; empty.hidden = true;
  };
  if (pathInput) pathInput.addEventListener('input', () => { objectUrl = ''; setImage(); });
  if (uploadInput) uploadInput.addEventListener('change', () => {
    if (objectUrl) URL.revokeObjectURL(objectUrl);
    objectUrl = uploadInput.files[0] ? URL.createObjectURL(uploadInput.files[0]) : '';
    setImage();
  });

  rooms = parse(); setImage(); paint();
})();
