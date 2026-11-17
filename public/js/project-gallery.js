(() => {
  const dialog = document.querySelector('[data-project-lightbox]');
  if (!dialog) return;

  const triggers = [...document.querySelectorAll('[data-gallery-open]')];
  if (!triggers.length) return;

  const image = dialog.querySelector('[data-lightbox-image]');
  const caption = dialog.querySelector('[data-lightbox-caption]');
  const count = dialog.querySelector('[data-lightbox-count]');
  const zoomButton = dialog.querySelector('[data-lightbox-zoom]');
  const stage = dialog.querySelector('[data-lightbox-stage]');
  let current = 0;
  let touchStartX = null;

  const render = index => {
    current = (index + triggers.length) % triggers.length;
    const trigger = triggers[current];
    image.src = trigger.dataset.gallerySrc;
    image.alt = trigger.dataset.galleryAlt || '';
    caption.textContent = image.alt;
    count.textContent = `${String(current + 1).padStart(2, '0')} / ${String(triggers.length).padStart(2, '0')}`;
    stage.classList.remove('is-zoomed');
    zoomButton.textContent = 'Zoom in +';
    zoomButton.setAttribute('aria-pressed', 'false');
  };

  triggers.forEach((trigger, index) => trigger.addEventListener('click', () => {
    render(index);
    dialog.showModal();
  }));

  dialog.querySelector('[data-lightbox-close]').addEventListener('click', () => dialog.close());
  dialog.querySelector('[data-lightbox-previous]').addEventListener('click', () => render(current - 1));
  dialog.querySelector('[data-lightbox-next]').addEventListener('click', () => render(current + 1));

  const toggleZoom = () => {
    const zoomed = stage.classList.toggle('is-zoomed');
    zoomButton.textContent = zoomed ? 'Zoom out −' : 'Zoom in +';
    zoomButton.setAttribute('aria-pressed', String(zoomed));
  };
  zoomButton.addEventListener('click', toggleZoom);
  image.addEventListener('click', toggleZoom);

  dialog.addEventListener('click', event => {
    if (event.target === dialog) dialog.close();
  });
  dialog.addEventListener('keydown', event => {
    if (event.key === 'ArrowLeft') render(current - 1);
    if (event.key === 'ArrowRight') render(current + 1);
  });
  stage.addEventListener('touchstart', event => {
    touchStartX = event.changedTouches[0].clientX;
  }, { passive: true });
  stage.addEventListener('touchend', event => {
    if (touchStartX === null) return;
    const distance = event.changedTouches[0].clientX - touchStartX;
    if (Math.abs(distance) > 55) render(current + (distance < 0 ? 1 : -1));
    touchStartX = null;
  }, { passive: true });
})();
