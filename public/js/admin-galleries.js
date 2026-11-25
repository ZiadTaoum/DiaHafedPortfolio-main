const editor = document.querySelector('[data-galleries]');
const form = editor.closest('form');
const galleryList = editor.querySelector('[data-gallery-list]');
const galleryTemplate = editor.querySelector('[data-gallery-template]');
const galleries = [];

function imageAction(actions, text, label, action, disabled = false) {
  const button = document.createElement('button');
  button.type = 'button';
  button.textContent = text;
  button.setAttribute('aria-label', label);
  button.disabled = disabled;
  button.addEventListener('click', action);
  actions.append(button);
  return button;
}

function addGallery(saved = { title: '', visible: true, images: [] }) {
  const panel = galleryTemplate.content.firstElementChild.cloneNode(true);
  const title = panel.querySelector('[data-gallery-title]');
  const visible = panel.querySelector('[data-gallery-visible]');
  const list = panel.querySelector('[data-gallery-image-list]');
  const picker = panel.querySelector('[data-gallery-uploads]');
  const paths = panel.querySelector('[data-gallery-paths]');
  const gallery = { title, visible, images: Object.values(saved.images || {}).filter(image => image.path) };
  title.value = saved.title ?? '';
  visible.checked = Boolean(Number(saved.visible));
  galleries.push(gallery);
  galleryList.append(panel);

  const render = (focusedImage, direction) => {
    list.replaceChildren();
    paths.value = gallery.images.flatMap(image => image.path ? [image.path] : []).join('\n');
    gallery.images.forEach((entry, index) => {
      const label = entry.file?.name || entry.path.split('/').pop();
      const card = document.createElement('article');
      card.className = 'gallery-image-card';
      const image = document.createElement('img');
      if (entry.url || /^\/(?!\/)[a-zA-Z0-9/_\-.]+$/.test(entry.path)) {
        image.src = entry.url || entry.path;
      }
      image.alt = '';
      image.loading = 'lazy';
      const caption = document.createElement('span');
      caption.className = 'gallery-image-name';
      caption.textContent = (index + 1) + '. ' + label;
      const actions = document.createElement('div');
      actions.className = 'gallery-image-actions';
      const move = offset => {
        gallery.images.splice(index, 1);
        gallery.images.splice(index + offset, 0, entry);
        render(entry, offset);
      };
      const up = imageAction(actions, '↑', 'Move ' + label + ' up', () => move(-1), index === 0);
      const down = imageAction(actions, '↓', 'Move ' + label + ' down', () => move(1), index === gallery.images.length - 1);
      imageAction(actions, 'Remove', 'Remove ' + label, () => {
        if (entry.url) URL.revokeObjectURL(entry.url);
        gallery.images.splice(index, 1);
        render();
        (list.children[index] || list.lastElementChild)?.querySelector('button:last-child').focus();
        if (!gallery.images.length) picker.focus();
      });
      card.append(image, caption, actions);
      list.append(card);
      if (entry === focusedImage) {
        (direction < 0 && !up.disabled || down.disabled ? up : down).focus();
      }
    });
    if (!gallery.images.length) list.textContent = 'No gallery images yet.';
  };

  picker.addEventListener('change', () => {
    for (const file of picker.files) {
      gallery.images.push({ file, url: URL.createObjectURL(file) });
    }
    picker.value = '';
    render();
  });
  panel.querySelector('[data-apply-paths]').addEventListener('click', () => {
    const updated = paths.value.split(/\r?\n/).map(path => path.trim()).filter(Boolean);
    const images = gallery.images.flatMap(image => {
      if (image.file) return [image];
      return updated.length ? [{ path: updated.shift() }] : [];
    });
    gallery.images = images.concat(updated.map(path => ({ path })));
    render();
  });
  panel.querySelector('[data-remove-gallery]').addEventListener('click', () => {
    gallery.images.forEach(image => { if (image.url) URL.revokeObjectURL(image.url); });
    galleries.splice(galleries.indexOf(gallery), 1);
    panel.remove();
    editor.querySelector('[data-add-gallery]').focus();
  });
  render();
  return title;
}

Object.values(JSON.parse(editor.dataset.galleries)).forEach(addGallery);
editor.querySelector('[data-add-gallery]').addEventListener('click', () => addGallery().focus());
form.addEventListener('formdata', ({ formData }) => {
  formData.set('edit_galleries', '1');
  galleries.forEach((gallery, index) => {
    const field = 'galleries[' + index + ']';
    formData.set(field + '[title]', gallery.title.value);
    formData.set(field + '[visible]', gallery.visible.checked ? '1' : '0');
    gallery.images.forEach((image, imageIndex) => {
      const entry = field + '[images][' + imageIndex + ']';
      formData.set(entry + '[path]', image.path ?? '');
      if (image.file) formData.set(entry + '[upload]', image.file);
    });
  });
});
