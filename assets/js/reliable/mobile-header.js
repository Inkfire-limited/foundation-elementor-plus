(function () {
  'use strict';
  let closeActiveMenu = null;
  window.FoundationWidgets.register('mobile-header', '[data-inkfire-header]', function (root, life) {
    const overlay = root.querySelector('.imh-overlay');
    const menu = root.querySelector('.imh-menu');
    const toggles = Array.from(root.querySelectorAll('.imh-menu-toggle'));
    const searchToggles = Array.from(root.querySelectorAll('.imh-search-toggle'));
    const sheets = Array.from(root.querySelectorAll('.imh-search-sheet'));
    if (!overlay || !menu || !toggles.length) return false;
    let previousOverflow = null, returnFocus = null;
    const pillUpdates = [];
    const focusables = () => Array.from(menu.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])'))
      .filter((node) => !node.closest('[hidden],[inert]') && node.getClientRects().length && getComputedStyle(node).visibility !== 'hidden');
    function searchSheet(toggle) {
      const id = toggle.getAttribute('aria-controls');
      return (id && sheets.find((sheet) => sheet.id === id)) || sheets[0];
    }
    function search(open, owner) {
      sheets.forEach((sheet) => {
        const visible = open && (!owner || sheet === searchSheet(owner));
        sheet.hidden = !visible; sheet.inert = !visible;
      });
      searchToggles.forEach((toggle) => toggle.setAttribute('aria-expanded', String(!searchSheet(toggle)?.hidden)));
      if (open && owner) searchSheet(owner)?.querySelector('input')?.focus({ preventScroll: true });
    }
    function setMenu(open) {
      const wasOpen = menu.classList.contains('is-open');
      if (open && !wasOpen) {
        if (closeActiveMenu && closeActiveMenu !== close) closeActiveMenu();
        previousOverflow = document.body.style.overflow; returnFocus = document.activeElement; closeActiveMenu = close;
      }
      menu.classList.toggle('is-open', open); menu.hidden = !open; menu.setAttribute('aria-hidden', String(!open));
      overlay.classList.toggle('is-visible', open); overlay.hidden = !open;
      toggles.forEach((toggle) => { toggle.setAttribute('aria-expanded', String(open)); toggle.classList.toggle('is-open', open); });
      if (open) {
        document.body.style.overflow = 'hidden'; document.body.classList.add('foundation-mobile-header-open'); document.documentElement.classList.add('foundation-mobile-header-open');
        if (!wasOpen) focusables()[0]?.focus({ preventScroll: true });
        life.frame(() => pillUpdates.forEach((fn) => fn()));
      } else {
        search(false);
        if (previousOverflow !== null) { document.body.style.overflow = previousOverflow; previousOverflow = null; }
        if (closeActiveMenu === close) closeActiveMenu = null;
        if (!closeActiveMenu) { document.body.classList.remove('foundation-mobile-header-open'); document.documentElement.classList.remove('foundation-mobile-header-open'); }
        if (wasOpen && menu.contains(document.activeElement) && returnFocus?.isConnected) returnFocus.focus({ preventScroll: true });
      }
    }
    function close() { setMenu(false); }
    toggles.forEach((toggle) => life.listen(toggle, 'click', () => setMenu(!menu.classList.contains('is-open'))));
    searchToggles.forEach((toggle) => life.listen(toggle, 'click', () => search(toggle.getAttribute('aria-expanded') !== 'true', toggle)));
    root.querySelectorAll('.imh-search-submit').forEach((button) => life.listen(button, 'click', () => {
      const form = button.closest('.imh-search-inner')?.querySelector('.imh-search-form');
      if (form) { if (typeof form.requestSubmit === 'function') form.requestSubmit(); else form.submit(); }
    }));
    life.listen(overlay, 'click', close);
    life.listen(document, 'keydown', (event) => {
      const open = menu.classList.contains('is-open');
      if (event.key === 'Escape') {
        if (open) { event.preventDefault(); close(); }
        else {
          const owner = searchToggles.find((toggle) => toggle.getAttribute('aria-expanded') === 'true');
          if (owner) { search(false); owner.focus(); }
        }
        return;
      }
      if (event.key !== 'Tab' || !open) return;
      const nodes = focusables(), first = nodes[0], last = nodes[nodes.length - 1];
      if (!first) return;
      if (event.shiftKey && (document.activeElement === first || !menu.contains(document.activeElement))) { event.preventDefault(); last.focus(); }
      else if (!event.shiftKey && (document.activeElement === last || !menu.contains(document.activeElement))) { event.preventDefault(); first.focus(); }
    });
    life.listen(menu, 'click', (event) => {
      const link = event.target instanceof Element && event.target.closest('a[href]');
      if (link && event.button === 0 && !event.ctrlKey && !event.metaKey && !event.shiftKey && !event.altKey) close();
    });
    life.listen(window, 'resize', () => {
      if (menu.classList.contains('is-open') && (!root.getClientRects().length || getComputedStyle(root).visibility === 'hidden')) close();
      pillUpdates.forEach((fn) => fn());
    });
    life.listen(window, 'pageshow', (event) => { if (event.persisted) close(); });
    const accordions = Array.from(root.querySelectorAll('[data-accordion-item]'));
    accordions.forEach((item) => {
      const toggle = item.querySelector('.imh-accordion-toggle'), panel = item.querySelector('.imh-accordion-panel');
      if (!toggle || !panel) return;
      life.listen(toggle, 'click', () => {
        const willOpen = !item.classList.contains('is-active');
        accordions.forEach((other) => {
          const active = other === item && willOpen;
          other.classList.toggle('is-active', active);
          other.querySelector('.imh-accordion-toggle')?.setAttribute('aria-expanded', String(active));
          const target = other.querySelector('.imh-accordion-panel'); if (target) target.hidden = !active;
        });
      });
    });
    root.querySelectorAll('[data-card-item]').forEach((item) => {
      const toggle = item.querySelector('.imh-card-toggle'), panel = item.querySelector('.imh-card-panel');
      if (!toggle || !panel) return;
      life.listen(toggle, 'click', () => {
        const open = toggle.getAttribute('aria-expanded') !== 'true';
        item.classList.toggle('is-open', open); toggle.setAttribute('aria-expanded', String(open)); panel.hidden = !open;
      });
    });
    root.querySelectorAll('.imh-inline-cta').forEach((wrap) => {
      const buttons = Array.from(wrap.querySelectorAll('.imh-inline-cta-btn')), pill = wrap.querySelector('.imh-inline-cta-pill');
      if (!buttons.length || !pill) return;
      let active = buttons.find((button) => button.classList.contains('is-active')) || buttons[0];
      function move(target) {
        if (!wrap.getClientRects().length) return;
        const a = wrap.getBoundingClientRect(), b = target.getBoundingClientRect();
        pill.style.width = b.width + 'px'; pill.style.transform = 'translateX(' + (b.left - a.left) + 'px)';
        buttons.forEach((button) => { button.classList.toggle('is-active', button === target); button.removeAttribute('aria-current'); }); active = target;
      }
      buttons.forEach((button, index) => {
        life.listen(button, 'mouseenter', () => move(button)); life.listen(button, 'focus', () => move(button));
        life.listen(button, 'keydown', (event) => {
          if (!['ArrowRight','ArrowLeft'].includes(event.key)) return;
          event.preventDefault(); buttons[(index + (event.key === 'ArrowRight' ? 1 : buttons.length - 1)) % buttons.length].focus();
        });
      });
      pillUpdates.push(() => move(active)); life.frame(() => move(active));
    });
    search(false);
    root.dataset.inkfireHeaderInitialized = 'true';
    life.cleanup(() => { close(); delete root.dataset.inkfireHeaderInitialized; });
  });
})();
