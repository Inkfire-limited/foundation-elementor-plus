(function () {
  'use strict';
  window.FoundationWidgets.register('rubiks-gallery', '[data-foundation-rubiks-gallery]', function (root, life) {
    const stage = root.querySelector('.foundation-rubiks-gallery__stage');
    if (!stage) return false;
    const originals = Array.from(stage.querySelectorAll('.foundation-rubiks-gallery__card'));
    let items;
    try { items = JSON.parse(root.querySelector('.foundation-rubiks-gallery__data')?.textContent || '[]'); } catch (_) { return false; }
    if (!Array.isArray(items) || !items.length || originals.length < 4) return false;
    items = items.filter((item) => item && typeof item === 'object');
    if (!items.length) return false;
    const animations = new Map();
    const videos = new Set();
    const retired = new Set();
    let slots = originals.slice(0, 4);
    let index = slots.length % items.length;
    let sequence = 0, timer = null, inViewport = !window.IntersectionObserver, hovering = false, focused = false, userPaused = false;
    const interval = Math.max(900, parseInt(root.getAttribute('data-interval'), 10) || 1200);
    const duration = Math.max(520, Math.min(Math.round(interval * 0.58), 920));
    const delay = Math.max(interval, duration + 280);
    const gap = 'calc(50% + (var(--foundation-rubiks-gap) / 2))';
    const positions = [{ top:'0',left:'0' },{ top:'0',left:gap },{ top:gap,left:'0' },{ top:gap,left:gap }];
    const motion = life.media('(prefers-reduced-motion: reduce)', sync);
    const small = life.media('(max-width: 767px)', sync);
    root.style.setProperty('--foundation-rubiks-move-duration', duration + 'ms');
    const pause = document.createElement('button'); pause.type = 'button'; pause.className = 'foundation-rubiks-gallery__pause';
    pause.textContent = 'Pause gallery animation'; pause.setAttribute('aria-pressed', 'false'); root.appendChild(pause);
    const allowed = () => life.active && inViewport && !document.hidden && !motion.matches && !small.matches && !hovering && !focused && !userPaused;
    function position(card, point) { if (point.top != null) card.style.top = point.top; if (point.left != null) card.style.left = point.left; }
    function url(value) { try { const parsed = new URL(value, document.baseURI); return ['https:', 'http:'].includes(parsed.protocol) ? parsed.href : ''; } catch (_) { return ''; } }
    function initMedia(card) {
      card.querySelectorAll('video').forEach((video) => { video.autoplay = false; video.muted = true; videos.add(video); video.pause(); });
      card.querySelectorAll('[data-foundation-rubiks-lottie]').forEach((node) => {
        if (animations.has(node) || !window.lottie) return;
        const path = url(node.getAttribute('data-lottie-url')); if (!path) return;
        const animation = window.lottie.loadAnimation({ container: node, renderer: 'svg', loop: true, autoplay: false, path, rendererSettings: { preserveAspectRatio: 'xMidYMid slice' } });
        animations.set(node, animation);
      });
    }
    function dispose(card) {
      card.querySelectorAll('video').forEach((video) => { video.pause(); videos.delete(video); });
      card.querySelectorAll('[data-foundation-rubiks-lottie]').forEach((node) => { const a = animations.get(node); if (a) a.destroy(); animations.delete(node); });
    }
    function media() {
      const play = allowed();
      videos.forEach((video) => {
        if (play && video.paused) { const promise = video.play(); if (promise?.catch) promise.catch(() => {}); }
        else if (!play) video.pause();
      });
      animations.forEach((animation) => { if (play) animation.play(); else animation.pause(); });
    }
    function create(point) {
      const item = items[index]; index = (index + 1) % items.length;
      const card = document.createElement('div'); card.className = 'foundation-rubiks-gallery__card'; card.tabIndex = 0;
      card.setAttribute('aria-label', item.alt || 'Portfolio media item');
      const surface = document.createElement('div'); surface.className = 'foundation-rubiks-gallery__surface'; card.appendChild(surface);
      let element;
      if (item.media_type === 'video') {
        element = document.createElement('video'); element.className = 'foundation-rubiks-gallery__video'; element.src = url(item.video_url);
        element.muted = true; element.loop = true; element.playsInline = true; element.preload = 'metadata'; if (item.video_poster) element.poster = url(item.video_poster);
      } else if (item.media_type === 'lottie') {
        element = document.createElement('div'); element.className = 'foundation-rubiks-gallery__lottie'; element.setAttribute('data-foundation-rubiks-lottie', ''); element.setAttribute('data-lottie-url', url(item.lottie_url)); element.setAttribute('aria-hidden', 'true');
      } else {
        element = document.createElement('img'); element.className = 'foundation-rubiks-gallery__image'; element.src = url(item.image_render_url || item.image_url); element.alt = ''; element.decoding = 'async';
      }
      surface.appendChild(element); stage.appendChild(card); position(card, point); initMedia(card); return card;
    }
    function removeLater(card) {
      card.inert = true; retired.add(card); dispose(card);
      life.timeout(() => { card.remove(); retired.delete(card); }, duration + 40);
    }
    function tick() {
      timer = null; if (!allowed()) return;
      if (sequence < 2) {
        const out1 = slots[1], out2 = slots[2], move1 = slots[0], move2 = slots[3];
        position(out1, { left:'120%' }); position(out2, { left:'-60%' }); position(move1, positions[1]); position(move2, positions[2]);
        const a = create({ top:'0', left:'-60%' }), b = create({ top:gap, left:'120%' });
        a.offsetHeight; position(a, positions[0]); b.offsetHeight; position(b, positions[3]);
        slots = [a, move1, move2, b]; removeLater(out1); removeLater(out2); sequence++;
      } else {
        const out = slots[3], move = slots[1]; position(out, { top:'120%' }); position(move, positions[3]);
        const a = create({ top:'-60%', left:gap }); a.offsetHeight; position(a, positions[1]); slots[1] = a; slots[3] = move; removeLater(out); sequence = 0;
      }
      media(); timer = life.timeout(tick, delay);
    }
    function sync() {
      if (timer !== null) { life.clearTimeout(timer); timer = null; }
      root.setAttribute('data-foundation-rubiks-static', String(motion.matches || small.matches));
      pause.hidden = motion.matches || small.matches;
      media(); if (allowed()) timer = life.timeout(tick, delay);
    }
    originals.forEach((card, i) => { if (i < 4) position(card, positions[i]); initMedia(card); });
    life.listen(pause, 'click', () => { userPaused = !userPaused; pause.setAttribute('aria-pressed', String(userPaused)); pause.textContent = userPaused ? 'Resume gallery animation' : 'Pause gallery animation'; sync(); });
    if (root.getAttribute('data-pause-on-hover') !== 'false') {
      life.listen(root, 'mouseenter', () => { hovering = true; sync(); }); life.listen(root, 'mouseleave', () => { hovering = false; sync(); });
    }
    life.listen(stage, 'focusin', () => { focused = true; sync(); });
    life.listen(stage, 'focusout', (event) => { focused = !!event.relatedTarget && stage.contains(event.relatedTarget); sync(); });
    life.listen(document, 'visibilitychange', sync);
    life.observe(window.IntersectionObserver, root, (entries) => { inViewport = entries[0].isIntersecting; sync(); }, { threshold: 0.3, rootMargin: '120px 0px' });
    life.cleanup(() => {
      videos.forEach((video) => video.pause()); animations.forEach((animation) => animation.destroy()); animations.clear(); videos.clear(); pause.remove();
      stage.querySelectorAll('.foundation-rubiks-gallery__card').forEach((card) => card.remove());
      originals.forEach((card, i) => { card.inert = false; if (i < 4) position(card, positions[i]); stage.appendChild(card); });
    });
    sync();
  });
})();
