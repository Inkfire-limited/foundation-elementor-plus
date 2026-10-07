(function () {
  'use strict';
  window.FoundationWidgets.register('y-hero', '[data-foundation-y-hero]', function (root, life) {

    const stack = root.querySelector('.foundation-y-hero__stack');
    const cards = stack ? Array.from(stack.children) : [];
    const up = root.querySelector('[data-yhero-prev]');
    const down = root.querySelector('[data-yhero-next]');
    if (!stack || !cards.length) return false;
    const originalInert = cards.map((card) => card.inert);
    let index = 0;
    const mobile = () => window.innerWidth <= (parseFloat(root.getAttribute('data-mobile-breakpoint')) || 920);
    function update() {
      const stacked = mobile();
      root.classList.toggle('foundation-y-hero--stacked', stacked);
      index = Math.max(0, Math.min(index, cards.length - 1));
      const style = window.getComputedStyle(stack);
      const step = cards[0].offsetHeight + (parseFloat(style.rowGap || style.gap) || 0);
      stack.style.transform = stacked ? 'none' : 'translateY(-' + (index * step) + 'px)';
      if (up) up.disabled = stacked || index === 0;
      if (down) down.disabled = stacked || index === cards.length - 1;
      // The desktop slider clips inactive cards. Do not leave their links in Tab order.
      cards.forEach((card, i) => { card.inert = !stacked && i !== index; });
    }
    life.listen(up, 'click', () => { if (!mobile()) { index--; update(); } });
    life.listen(down, 'click', () => { if (!mobile()) { index++; update(); } });
    life.listen(window, 'resize', update);
    life.listen(window, 'load', update);
    life.observe(window.ResizeObserver, cards[0], update);
    life.cleanup(() => cards.forEach((card, i) => { card.inert = originalInert[i]; }));
    update();

  });
})();
