(function () {
  'use strict';
  window.FoundationWidgets.register('live-events', '[data-foundation-live-events]', function (root, life) {

    const items = Array.from(root.querySelectorAll('[data-live-events-item]'));
    if (!items.length) return false;
    function set(item, open) {
      const toggle = item.querySelector('[data-live-events-toggle]');
      const panel = item.querySelector('[data-live-events-panel]');
      if (!toggle || !panel) return;
      toggle.setAttribute('aria-expanded', String(open)); panel.hidden = !open;
    }
    items.forEach((item) => {
      const toggle = item.querySelector('[data-live-events-toggle]');
      life.listen(toggle, 'click', () => {
        const open = toggle.getAttribute('aria-expanded') !== 'true';
        items.forEach((candidate) => set(candidate, candidate === item && open));
      });
      set(item, false);
    });
    if (root.getAttribute('data-default-open') === 'yes') {
      const upcoming = root.querySelector('[data-live-events-item][data-event-group="upcoming"]');
      if (upcoming) set(upcoming, true);
    }

  });
})();
