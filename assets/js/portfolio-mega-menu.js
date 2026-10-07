(function () {
  'use strict';
  // One delegated listener also covers late-loaded and re-rendered widgets.
  if (window.foundationPortfolioMenuBound) return;
  window.foundationPortfolioMenuBound = true;
  document.addEventListener('click', function (event) {
    if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    const trigger = event.target instanceof Element
      ? event.target.closest('[data-foundation-portfolio-mega-menu] [data-foundation-portfolio-menu-popup]')
      : null;
    if (!trigger) return;
    const popupId = Number(trigger.getAttribute('data-foundation-portfolio-menu-popup'));
    if (!Number.isInteger(popupId) || popupId <= 0) return;
    const popup = window.elementorProFrontend && window.elementorProFrontend.modules && window.elementorProFrontend.modules.popup;
    if (!popup || typeof popup.showPopup !== 'function') return;
    try {
      popup.showPopup({ id: popupId });
      event.preventDefault();
    } catch (error) {
      // Keep the real href usable if the optional popup cannot be opened.
    }
  });
})();
