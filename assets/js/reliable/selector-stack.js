(function () {
  'use strict';
  window.FoundationWidgets.register('selector-stack', '[data-inkfire-selector]', function (root, life) {
    const cards = Array.from(root.querySelectorAll('.inkfire-selector__card'));
    const links = Array.from(root.querySelectorAll('.inkfire-selector__nav a'));
    const layout = root.querySelector('.inkfire-selector__layout');
    const stage = root.querySelector('.inkfire-selector__stage');
    const nav = root.querySelector('.inkfire-selector__nav');
    const rail = root.querySelector('.inkfire-selector__nav-rail');
    if (!cards.length || !layout || !stage || !nav) return false;
    const number = (value, fallback) => Number.isFinite(parseFloat(value)) ? parseFloat(value) : fallback;
    const clamp = (value, min, max) => Math.min(Math.max(value, min), max);
    const motion = life.media('(prefers-reduced-motion: reduce)', requestUpdate);
    let mobileIndex = 0;
    let frame = null;
    let constrained = false;
    const originalInert = cards.map((card) => card.inert);
    // Never rewrite ancestor overflow. An incompatible containing block receives a readable in-flow layout.
    function inspectParents() {
      constrained = false;
      for (let node = root.parentElement; node && node !== document.body; node = node.parentElement) {
        const style = getComputedStyle(node);
        if (style.transform !== 'none' || style.perspective !== 'none' || style.filter !== 'none' || /paint|layout|strict|content/.test(style.contain)) {
          constrained = true; break;
        }
      }
      requestUpdate();
    }
    function metrics() {
      const s = getComputedStyle(root);
      const height = cards[0].offsetHeight || number(s.getPropertyValue('--inkfire-selector-card-height'), 620);
      const segment = Math.max(height + number(s.getPropertyValue('--inkfire-selector-stack-gap'), 20), 1);
      const distance = segment * Math.max(cards.length - 1, 0);
      const stageHeight = height + number(s.getPropertyValue('--inkfire-selector-peek'), 132);
      return { top: number(s.getPropertyValue('--inkfire-selector-sticky-top'), 120), height, segment, distance, stageHeight,
        total: stageHeight + distance + number(s.getPropertyValue('--inkfire-selector-exit-space'), 260) };
    }
    function active(index, focusableAll) {
      links.forEach((link, i) => {
        link.classList.toggle('is-active', i === index);
        if (i === index) link.setAttribute('aria-current', 'true'); else link.removeAttribute('aria-current');
      });
      cards.forEach((card, i) => { card.inert = !focusableAll && i !== index; });
    }
    function reset() {
      layout.classList.remove('is-pinned', 'is-finished');
      layout.style.left = ''; layout.style.width = ''; root.style.minHeight = ''; stage.style.height = '';
      ['--inkfire-selector-pin-distance', '--inkfire-selector-nav-height', '--inkfire-selector-nav-top-auto'].forEach((p) => root.style.removeProperty(p));
      cards.forEach((card) => { card.style.removeProperty('--inkfire-selector-card-y'); card.style.removeProperty('--inkfire-selector-card-scale'); });
    }
    function render() {
      frame = null;
      const staticMode = motion.matches || constrained;
      root.classList.toggle('inkfire-selector--static', staticMode);
      if (staticMode) { reset(); active(mobileIndex, true); return; }
      if (window.innerWidth <= 1024) {
        reset();
        cards.forEach((card, i) => { card.classList.toggle('is-active', i === mobileIndex); card.classList.toggle('is-before', i < mobileIndex); });
        stage.style.height = cards[mobileIndex].offsetHeight + 'px'; active(mobileIndex, false); return;
      }
      const m = metrics(); const rect = root.getBoundingClientRect();
      const start = window.scrollY + rect.top - m.top;
      const progress = clamp(window.scrollY - start, 0, m.distance);
      const index = Math.min(cards.length - 1, Math.floor(progress / m.segment));
      root.style.minHeight = m.total + 'px'; root.style.setProperty('--inkfire-selector-pin-distance', m.distance + 'px');
      root.style.setProperty('--inkfire-selector-nav-height', m.height + 'px');
      root.style.setProperty('--inkfire-selector-nav-top-auto', (m.top + Math.max((m.height - nav.offsetHeight) / 2, 0)) + 'px');
      stage.style.height = m.stageHeight + 'px';
      const pinned = window.scrollY >= start && window.scrollY <= start + m.distance;
      layout.classList.toggle('is-pinned', pinned); layout.classList.toggle('is-finished', window.scrollY > start + m.distance);
      layout.style.left = pinned ? rect.left + 'px' : ''; layout.style.width = pinned ? rect.width + 'px' : '';
      cards.forEach((card, i) => {
        card.style.setProperty('--inkfire-selector-card-y', Math.max(0, i * m.segment - progress) + 'px');
        card.style.setProperty('--inkfire-selector-card-scale', (1 - Math.min(Math.max(0, index - i), 4) * 0.03).toFixed(3));
      });
      active(index, false);
    }
    function requestUpdate() { if (frame === null) frame = life.frame(render); }
    links.forEach((link, index) => life.listen(link, 'click', (event) => {
      if (event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || !cards[index]) return;
      event.preventDefault(); mobileIndex = index;
      if (motion.matches || constrained) { active(index, true); cards[index].scrollIntoView({ behavior: 'auto', block: 'start' }); }
      else if (window.innerWidth <= 1024) {
        render();
        // Only scroll the horizontal rail, never the document during normal page scrolling.
        if (rail) { const a = link.getBoundingClientRect(), b = rail.getBoundingClientRect(); rail.scrollBy({ left: a.left - b.left - (b.width - a.width) / 2, behavior: 'auto' }); }
      } else { const m = metrics(); window.scrollTo({ top: window.scrollY + root.getBoundingClientRect().top - m.top + index * m.segment, behavior: 'smooth' }); }
    }));
    life.listen(window, 'scroll', () => { if (window.innerWidth > 1024 && !motion.matches && !constrained) requestUpdate(); }, { passive: true });
    life.listen(window, 'resize', inspectParents); life.listen(window, 'load', inspectParents);
    life.observe(window.ResizeObserver, cards[0], requestUpdate);
    life.cleanup(() => { reset(); root.classList.remove('inkfire-selector--static'); cards.forEach((card, i) => { card.inert = originalInert[i]; }); });
    inspectParents();
  });
})();
