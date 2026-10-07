(function () {
  'use strict';
  window.FoundationWidgets.register('process-carousel', '[data-foundation-process-carousel]', function (root, life) {

    const track = root.querySelector('[data-process-track]');
    const steps = Array.from(root.querySelectorAll('[data-process-step]'));
    const cards = Array.from(root.querySelectorAll('[data-process-card]'));
    const prev = root.querySelector('[data-process-prev]');
    const next = root.querySelector('[data-process-next]');
    if (!track || !steps.length || !cards.length) return false;
    let index = 0;
    const originalInert = cards.map((card) => card.inert);
    function update() {
      const style = window.getComputedStyle(track);
      const gap = parseFloat(style.columnGap || style.gap) || 0;
      track.style.transform = 'translateX(-' + (index * (cards[0].offsetWidth + gap)) + 'px)';
      if (prev) prev.disabled = index === 0;
      if (next) next.disabled = index === cards.length - 1;
      steps.forEach((step, i) => {
        step.classList.toggle('is-active', i === index);
        step.setAttribute('aria-selected', String(i === index));
        step.tabIndex = i === index ? 0 : -1;
      });
      cards.forEach((card, i) => {
        card.classList.toggle('is-active', i === index);
        card.setAttribute('aria-hidden', String(i !== index));
        card.inert = i !== index;
      });
    }
    function select(value, focus) {
      index = Math.max(0, Math.min(value, cards.length - 1)); update();
      if (focus && steps[index]) steps[index].focus();
    }
    steps.forEach((step, i) => {
      life.listen(step, 'click', () => select(parseInt(step.getAttribute('data-process-step'), 10) || i));
      life.listen(step, 'keydown', (event) => {
        const values = { ArrowRight: index + 1, ArrowLeft: index - 1, Home: 0, End: cards.length - 1 };
        if (!(event.key in values)) return;
        event.preventDefault(); select(values[event.key], true);
      });
    });
    life.listen(next, 'click', () => select(index + 1));
    life.listen(prev, 'click', () => select(index - 1));
    life.listen(window, 'resize', update);
    life.listen(window, 'load', update);
    life.observe(window.ResizeObserver, cards[0], update);
    life.cleanup(() => cards.forEach((card, i) => { card.inert = originalInert[i]; }));
    update();

  });
})();
