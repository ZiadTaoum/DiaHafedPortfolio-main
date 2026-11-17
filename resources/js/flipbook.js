import { PageFlip } from 'page-flip';
import 'page-flip/src/Style/stPageFlip.css';

const readers = document.querySelectorAll('[data-offline-flipbook]');

readers.forEach(reader => {
  const stage = reader.querySelector('.offline-flipbook-stage');
  const bookElement = reader.querySelector('[data-pageflip-book]');
  const pages = [...reader.querySelectorAll('.offline-flipbook-page')];
  const previous = reader.querySelector('[data-pageflip-prev]');
  const next = reader.querySelector('[data-pageflip-next]');
  const count = reader.querySelector('[data-pageflip-count]');
  const progress = reader.querySelector('[data-pageflip-progress]');
  const width = Number(reader.dataset.pageWidth) || 320;
  const height = Number(reader.dataset.pageHeight) || 300;

  if (!stage || !bookElement || !pages.length || !previous || !next || !count || !progress) return;

  // Create an exact-aspect-ratio parent around bookElement because StPageFlip
  // measures bookElement.parentElement.clientWidth / clientHeight in 'stretch' mode!
  const sizer = document.createElement('div');
  sizer.className = 'offline-flipbook-sizer';
  stage.insertBefore(sizer, bookElement);
  sizer.appendChild(bookElement);

  const fitSizerToStage = () => {
    const style = window.getComputedStyle(stage);
    const availW = stage.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight);
    const availH = stage.clientHeight - parseFloat(style.paddingTop) - parseFloat(style.paddingBottom);

    const pageRatio = width / height;
    const isPortrait = availW < 520;
    const spreadRatio = isPortrait ? pageRatio : pageRatio * 2;

    let boxW = availW;
    let boxH = boxW / spreadRatio;

    if (boxH > availH) {
      boxH = availH;
      boxW = boxH * spreadRatio;
    }

    // Snap width to an even integer so (boxW / 2) never has a .5px remainder
    const evenW = Math.floor(boxW) - (Math.floor(boxW) % 2);
    const exactH = Math.round(evenW / spreadRatio);

    sizer.style.width = `${evenW}px`;
    sizer.style.height = `${exactH}px`;
  };

  fitSizerToStage();

  const pageFlip = new PageFlip(bookElement, {
    width,
    height,
    size: 'stretch',
    minWidth: 180,
    maxWidth: 1000,
    minHeight: 180,
    maxHeight: 1000,
    drawShadow: true,
    flippingTime: 850,
    usePortrait: true,
    startPage: 0,
    autoSize: true,
    maxShadowOpacity: 0.22,
    showCover: true,
    mobileScrollSupport: false,
    clickEventForward: true,
  });

  const updateControls = pageIndex => {
    const total = pageFlip.getPageCount();
    const currentPage = Math.max(0, Math.min(Number(pageIndex) || 0, total - 1));
    const landscape = pageFlip.getOrientation() === 'landscape';
    const firstVisible = currentPage + 1;
    const lastVisible = landscape && currentPage > 0
      ? Math.min(currentPage + 2, total)
      : firstVisible;

    count.textContent = currentPage === 0
      ? `Cover · Page 1 of ${total}`
      : firstVisible === lastVisible
        ? `Page ${firstVisible} of ${total}`
        : `Pages ${firstVisible}–${lastVisible} of ${total}`;
    progress.style.width = `${(lastVisible / total) * 100}%`;
    previous.disabled = currentPage === 0;
    next.disabled = currentPage >= total - 1;
  };

  window.addEventListener('resize', () => {
    fitSizerToStage();
    pageFlip.update();
  });

  previous.addEventListener('click', () => pageFlip.flipPrev());
  next.addEventListener('click', () => pageFlip.flipNext());
  pageFlip.on('flip', event => updateControls(event.data));
  pageFlip.on('changeOrientation', () => {
    fitSizerToStage();
    updateControls(pageFlip.getCurrentPageIndex());
  });
  pageFlip.loadFromHTML(pages);
  updateControls(pageFlip.getCurrentPageIndex());
});
