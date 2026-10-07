(function () {
  'use strict';
  window.FoundationWidgets.register('team-loop', '[data-foundation-team-loop]', function (root, life) {

    const department = root.querySelector('[data-team-loop-filter="department"]');
    const group = root.querySelector('[data-team-loop-filter="group"]');
    const items = Array.from(root.querySelectorAll('[data-team-loop-item]'));
    const empty = root.querySelector('[data-team-loop-empty]');
    function normalize(value) { return value === 'management' ? 'founders' : ['all', 'staff', 'founders'].includes(value) ? value : 'all'; }
    function set(item, open) {
      const toggle = item.querySelector('[data-team-loop-toggle]');
      const panel = item.querySelector('[data-team-loop-panel]');
      const text = item.querySelector('.foundation-team-loop__toggle-text');
      if (!toggle || !panel) return;
      toggle.setAttribute('aria-expanded', String(open)); panel.hidden = !open;
      if (text) text.textContent = toggle.getAttribute(open ? 'data-open-label' : 'data-closed-label') || text.textContent;
    }
    function filter() {
      const dep = department ? department.value : root.getAttribute('data-default-department') || 'all';
      const grp = normalize(group ? group.value : root.getAttribute('data-default-group') || 'all');
      let visible = 0;
      items.forEach((item) => {
        const deps = (item.getAttribute('data-team-loop-departments') || '').split(/\s+/);
        const matches = (dep === 'all' || deps.includes(dep)) && (grp === 'all' || normalize(item.getAttribute('data-team-loop-group') || 'staff') === grp);
        item.hidden = !matches; item.setAttribute('aria-hidden', String(!matches));
        if (matches) { item.style.removeProperty('display'); visible++; }
        else { item.style.setProperty('display', 'none', 'important'); set(item, false); }
      });
      if (empty) empty.hidden = visible > 0;
    }
    items.forEach((item) => {
      const toggle = item.querySelector('[data-team-loop-toggle]');
      life.listen(toggle, 'click', () => set(item, toggle.getAttribute('aria-expanded') !== 'true'));
    });
    life.listen(department, 'change', filter); life.listen(group, 'change', filter);
    filter();

  });
})();
