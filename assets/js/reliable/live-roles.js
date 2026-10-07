(function () {
  'use strict';
  window.FoundationWidgets.register('live-roles', '[data-foundation-live-roles]', function (root, life) {

    const filters = Array.from(root.querySelectorAll('[data-live-roles-filter]'));
    const roles = Array.from(root.querySelectorAll('[data-live-roles-item]'));
    const empty = root.querySelector('[data-live-roles-empty]');
    const defaultOpen = root.getAttribute('data-default-open') === 'yes';
    if (!roles.length) return false;
    function set(role, open) {
      const toggle = role.querySelector('[data-live-roles-toggle]');
      const panel = role.querySelector('[data-live-roles-panel]');
      if (!toggle || !panel) return;
      toggle.setAttribute('aria-expanded', String(open)); panel.hidden = !open;
    }
    function filter(value) {
      filters.forEach((button) => {
        const active = button.getAttribute('data-live-roles-filter') === value;
        button.classList.toggle('is-active', active); button.setAttribute('aria-pressed', String(active));
      });
      roles.forEach((role) => {
        role.hidden = value !== 'all' && (role.getAttribute('data-role-status') || 'open') !== value;
        if (role.hidden) set(role, false);
      });
      const visible = roles.filter((role) => !role.hidden);
      if (empty) empty.hidden = visible.length > 0;
      if (defaultOpen && visible.length && !visible.some((role) => role.querySelector('[aria-expanded="true"]'))) set(visible[0], true);
    }
    roles.forEach((role) => {
      const toggle = role.querySelector('[data-live-roles-toggle]');
      life.listen(toggle, 'click', () => {
        const open = toggle.getAttribute('aria-expanded') !== 'true';
        roles.forEach((candidate) => set(candidate, candidate === role && open));
      });
      set(role, false);
    });
    filters.forEach((button) => life.listen(button, 'click', () => filter(button.getAttribute('data-live-roles-filter') || 'all')));
    filter(root.getAttribute('data-default-filter') || 'all');

  });
})();
