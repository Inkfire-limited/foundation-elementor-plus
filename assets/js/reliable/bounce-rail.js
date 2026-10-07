(function () {
  'use strict';
  window.FoundationWidgets.register('bounce-rail', '[data-foundation-bounce-rail]', function (root, life) {

    const track = root.querySelector('[data-bounce-track]');
    if (!track) return false;
    const step = parseFloat(root.getAttribute('data-scroll-step')) || 302;
    const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const scroll = (direction) => track.scrollBy({ left: direction * step, behavior: motion.matches ? 'auto' : 'smooth' });
    life.listen(root.querySelector('[data-bounce-prev]'), 'click', () => scroll(-1));
    life.listen(root.querySelector('[data-bounce-next]'), 'click', () => scroll(1));

  });
})();
