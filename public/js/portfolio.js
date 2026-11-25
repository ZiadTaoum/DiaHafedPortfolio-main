document.querySelectorAll('.filters').forEach(filters => {
  filters.querySelectorAll('button').forEach(button => button.addEventListener('click', () => {
    filters.querySelectorAll('button').forEach(b => { b.classList.toggle('active', b === button); b.setAttribute('aria-pressed', String(b === button)); });
    filters.parentElement.querySelectorAll('[data-category]').forEach(card => { card.hidden = button.dataset.filter !== 'all' && card.dataset.category !== button.dataset.filter; });
  }));
});
document.querySelectorAll('form[data-confirm]').forEach(form => form.addEventListener('submit', event => { if (!confirm(form.dataset.confirm)) event.preventDefault(); }));

const projectCategory = document.querySelector('[data-project-category]');
const magazineFields = document.querySelector('[data-magazine-fields]');
if (projectCategory && magazineFields) {
  const updateMagazineFields = () => {
    magazineFields.hidden = !['magazine', 'editorial'].includes(projectCategory.value.trim().toLowerCase());
  };
  projectCategory.addEventListener('input', updateMagazineFields);
  projectCategory.addEventListener('change', updateMagazineFields);
  updateMagazineFields();
}

document.querySelectorAll('[data-clear-media]').forEach(button => {
  const field = button.dataset.clearMedia;
  const path = document.getElementById(`data_${field}`);
  const upload = document.getElementById(`upload_${field}`);
  const preview = document.getElementById(path.dataset.previewId);
  let objectUrl;

  const update = () => {
    if (objectUrl) URL.revokeObjectURL(objectUrl);
    objectUrl = preview instanceof HTMLImageElement && upload.files[0]
      ? URL.createObjectURL(upload.files[0])
      : undefined;
    const source = objectUrl || path.value.trim();
    const localPath = /^\/(?!\/)[a-zA-Z0-9/_\-.]+$/.test(source);
    if (preview) {
      preview.hidden = !objectUrl && !localPath;
      const attribute = preview instanceof HTMLImageElement ? 'src' : 'href';
      if (preview.hidden) preview.removeAttribute(attribute);
      else preview.setAttribute(attribute, source);
    }
    button.disabled = !path.value && !upload.files.length;
  };

  path.addEventListener('input', update);
  upload.addEventListener('change', update);
  button.addEventListener('click', () => {
    path.value = '';
    upload.value = '';
    path.dispatchEvent(new Event('input', { bubbles: true }));
    upload.dispatchEvent(new Event('change', { bubbles: true }));
  });
  update();
});

document.querySelectorAll('[data-flipbook]').forEach(reader => {
  const sources = [...reader.querySelectorAll('[data-flipbook-src]')].map(image => ({ src: image.dataset.flipbookSrc, alt: image.alt, doubleSpread: image.dataset.doubleSpread === 'true' }));
  const leftPage = reader.querySelector('[data-flipbook-left]');
  const rightPage = reader.querySelector('[data-flipbook-right]');
  const turn = reader.querySelector('[data-flipbook-turn]');
  const turnSheet = reader.querySelector('[data-flipbook-sheet]');
  const frontImage = reader.querySelector('[data-flipbook-front]');
  const backImage = reader.querySelector('[data-flipbook-back]');
  const stage = reader.querySelector('[data-flipbook-stage]');
  const count = reader.querySelector('[data-flipbook-count]');
  const progress = reader.querySelector('[data-flipbook-progress]');
  const previous = reader.querySelector('[data-flipbook-prev]');
  const next = reader.querySelector('[data-flipbook-next]');
  const book = reader.querySelector('[data-flipbook-book]');
  const visibleSpread = reader.querySelector('[data-flipbook-visible-spread]');
  const spreadArtwork = reader.querySelector('[data-flipbook-spread-art]');
  if (!sources.length || !leftPage || !rightPage || !turn || !turnSheet || !stage || !count || !progress || !previous || !next || !book || !visibleSpread || !spreadArtwork) return;

  if (reader.dataset.autoSize === 'true') {
    const cover = new Image();
    cover.addEventListener('load', () => {
      if (cover.naturalWidth && cover.naturalHeight) {
        book.style.setProperty('--flipbook-spread-ratio', String(2 * cover.naturalWidth / cover.naturalHeight));
      }
    }, { once: true });
    cover.src = sources[0].src;
  }

  const hasCover = reader.dataset.hasCover === 'true';
  const spreads = [];
  let sourceIndex = 0;
  let pageNumber = 1;
  if (hasCover) {
    spreads.push({ kind: 'cover', left: null, right: sources[0], start: 1, end: 1 });
    sourceIndex = 1;
    pageNumber = 2;
  }
  let pendingPage = null;
  while (sourceIndex < sources.length) {
    const source = sources[sourceIndex++];
    if (source.doubleSpread) {
      if (pendingPage) {
        spreads.push({ kind: 'pages', left: pendingPage, right: null, start: pageNumber, end: pageNumber });
        pageNumber += 1;
        pendingPage = null;
      }
      spreads.push({ kind: 'double', artwork: source, left: null, right: null, start: pageNumber, end: pageNumber + 1 });
      pageNumber += 2;
    } else if (pendingPage) {
      spreads.push({ kind: 'pages', left: pendingPage, right: source, start: pageNumber, end: pageNumber + 1 });
      pageNumber += 2;
      pendingPage = null;
    } else {
      pendingPage = source;
    }
  }
  if (pendingPage) {
    spreads.push({ kind: 'pages', left: pendingPage, right: null, start: pageNumber, end: pageNumber });
  }

  let current = 0;
  let flipping = false;
  const paintLeaf = (element, page, number, side) => {
    element.classList.toggle('is-blank', !page);
    element.replaceChildren();
    if (!page) {
      const mark = document.createElement('span');
      mark.className = 'flipbook-blank-mark';
      mark.setAttribute('aria-hidden', 'true');
      mark.textContent = reader.dataset.title;
      element.append(mark);
      if (number) {
        const folio = document.createElement('span');
        folio.className = 'flipbook-folio';
        folio.textContent = String(number).padStart(2, '0');
        element.append(folio);
      }
      return;
    }
    const art = document.createElement('span');
    art.className = 'flipbook-art';
    const image = document.createElement('img');
    image.src = page.src;
    image.alt = page.alt;
    image.draggable = false;
    art.append(image);
    const folio = document.createElement('span');
    folio.className = 'flipbook-folio';
    folio.textContent = String(number).padStart(2, '0');
    element.append(art, folio);
  };
  const render = index => {
    const spread = spreads[index];
    visibleSpread.classList.toggle('is-ad-spread', spread.kind === 'double');
    spreadArtwork.hidden = spread.kind !== 'double';
    if (spread.kind === 'double') {
      spreadArtwork.src = spread.artwork.src;
      spreadArtwork.alt = spread.artwork.alt;
    } else {
      spreadArtwork.removeAttribute('src');
      spreadArtwork.alt = '';
    }
    paintLeaf(leftPage, spread.left, spread.kind === 'cover' ? null : spread.start, 'left');
    paintLeaf(rightPage, spread.right, spread.kind === 'double' ? spread.end : spread.kind === 'cover' ? spread.start : spread.end, 'right');
    updateReadingPosition(spread);
    previous.disabled = index === 0 || flipping;
    next.disabled = index === spreads.length - 1 || flipping;
    reader.dataset.currentSpread = String(index);
  };
  const updateReadingPosition = spread => {
    count.textContent = spread.kind === 'cover'
      ? `Cover · Page ${spread.start} of ${reader.dataset.totalPages}`
      : spread.start === spread.end
      ? `Page ${spread.start} of ${reader.dataset.totalPages}`
      : `Pages ${spread.start}–${spread.end} of ${reader.dataset.totalPages}`;
    progress.value = spread.end;
  };
  const flip = direction => {
    const targetIndex = current + direction;
    if (flipping || targetIndex < 0 || targetIndex >= spreads.length) return;

    const oldSpread = spreads[current];
    const newSpread = spreads[targetIndex];
    const forward = direction > 0;
    const turningImage = forward ? (oldSpread.right ?? oldSpread.artwork) : (oldSpread.left ?? oldSpread.artwork);
    const reverseImage = forward ? (newSpread.left ?? newSpread.artwork) : (newSpread.right ?? newSpread.artwork);
    // Keep the destination spread underneath the turning leaf. Hiding the
    // current spread before the turn completes makes every flip flash blank.
    visibleSpread.classList.toggle('is-ad-spread', newSpread.kind === 'double');
    spreadArtwork.hidden = newSpread.kind !== 'double';
    if (newSpread.kind === 'double') {
      spreadArtwork.src = newSpread.artwork.src;
      spreadArtwork.alt = newSpread.artwork.alt;
    } else {
      spreadArtwork.removeAttribute('src');
      spreadArtwork.alt = '';
    }
    paintLeaf(leftPage, newSpread.left, newSpread.kind === 'cover' ? null : newSpread.start, 'left');
    paintLeaf(rightPage, newSpread.right, newSpread.kind === 'cover' ? newSpread.start : newSpread.end, 'right');
    updateReadingPosition(newSpread);

    frontImage.src = turningImage?.src ?? '';
    frontImage.alt = turningImage?.alt ?? '';
    backImage.src = reverseImage?.src ?? '';
    backImage.alt = reverseImage?.alt ?? '';
    frontImage.classList.toggle('is-spread-right', forward && Boolean(oldSpread.artwork && turningImage === oldSpread.artwork));
    frontImage.classList.toggle('is-spread-left', !forward && Boolean(oldSpread.artwork && turningImage === oldSpread.artwork));
    backImage.classList.toggle('is-spread-left', forward && Boolean(newSpread.artwork && reverseImage === newSpread.artwork));
    backImage.classList.toggle('is-spread-right', !forward && Boolean(newSpread.artwork && reverseImage === newSpread.artwork));
    turn.classList.toggle('is-backward', !forward);
    turn.classList.remove('is-turning');
    turnSheet.classList.remove('is-turning');
    flipping = true;
    previous.disabled = true;
    next.disabled = true;

    window.setTimeout(() => {
      if (!flipping) return;
      const finish = () => {
        if (!flipping) return;
        turnSheet.removeEventListener('animationend', onAnimationEnd);
        current = targetIndex;
        flipping = false;
        turn.classList.remove('is-turning', 'is-backward');
        turnSheet.classList.remove('is-turning');
        render(current);
      };
      const onAnimationEnd = event => {
        if (event.target === turnSheet) finish();
      };
      turnSheet.addEventListener('animationend', onAnimationEnd);
      turn.classList.add('is-turning');
      turnSheet.classList.add('is-turning');
      window.setTimeout(finish, 1100);
    }, 16);
  };

  previous.addEventListener('click', () => flip(-1));
  next.addEventListener('click', () => flip(1));
  let suppressClickUntil = 0;
  stage.addEventListener('click', event => {
    if (Date.now() < suppressClickUntil) return;
    if (event.target.closest('button')) return;
    const bounds = stage.getBoundingClientRect();
    flip(event.clientX > bounds.left + bounds.width / 2 ? 1 : -1);
  });
  stage.addEventListener('keydown', event => {
    if (event.key === 'ArrowRight' || event.key === 'PageDown') { event.preventDefault(); flip(1); }
    if (event.key === 'ArrowLeft' || event.key === 'PageUp') { event.preventDefault(); flip(-1); }
  });

  let touchStart = null;
  stage.addEventListener('pointerdown', event => { touchStart = event.clientX; }, { passive: true });
  stage.addEventListener('pointerup', event => {
    if (touchStart === null) return;
    const movement = event.clientX - touchStart;
    if (Math.abs(movement) > 45) {
      flip(movement < 0 ? 1 : -1);
      suppressClickUntil = Date.now() + 500;
    }
    touchStart = null;
  }, { passive: true });
  render(current);
});

document.querySelectorAll('[data-theme-editor]').forEach(editor => {
  const preview = editor.querySelector('[data-theme-preview]');
  const rows = [...editor.querySelectorAll('[data-theme-color]')];
  const valid = value => /^#[0-9a-f]{6}$/i.test(value);
  const luminance = hex => {
    const rgb = hex.slice(1).match(/../g).map(v => parseInt(v, 16) / 255).map(v => v <= .04045 ? v / 12.92 : ((v + .055) / 1.055) ** 2.4);
    return rgb[0] * .2126 + rgb[1] * .7152 + rgb[2] * .0722;
  };
  const update = () => {
    const colors = {};
    rows.forEach(row => {
      const value = row.querySelector('[data-color-hex]').value;
      colors[row.dataset.variable] = valid(value) ? value : row.dataset.default;
      preview.style.setProperty('--' + row.dataset.variable, colors[row.dataset.variable]);
    });
    const pairs = [['Main text', 'ink', 'paper'], ['Secondary text', 'muted', 'paper'], ['Banner text', 'ink', 'orange'], ['About text', 'muted', 'surface'], ['Button text', 'light', 'ink'], ['Dark section text', 'light', 'dark'], ['Dark secondary text', 'dark-muted', 'dark']];
    const low = pairs.filter(([, a, b]) => {
      const x = luminance(colors[a]), y = luminance(colors[b]);
      return (Math.max(x, y) + .05) / (Math.min(x, y) + .05) < 4.5;
    }).map(([label]) => label);
    editor.querySelector('[data-theme-contrast]').textContent = low.length ? 'Readability check: low contrast for ' + low.join(', ').toLowerCase() + '. Consider adjusting these colors.' : 'Readability check: preview text combinations have good contrast.';
  };
  rows.forEach(row => {
    const picker = row.querySelector('[data-color-picker]'), hex = row.querySelector('[data-color-hex]');
    picker.addEventListener('input', () => { hex.value = picker.value; update(); });
    hex.addEventListener('input', () => { if (valid(hex.value)) picker.value = hex.value; update(); });
  });
  editor.querySelector('[data-theme-reset]').addEventListener('click', () => {
    rows.forEach(row => { row.querySelector('[data-color-picker]').value = row.dataset.default; row.querySelector('[data-color-hex]').value = row.dataset.default; });
    update();
  });
  update();
});
