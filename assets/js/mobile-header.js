(function () {
  let observerStarted = false;
  let healthCheckScheduled = false;
  let warnedAboutMissingInit = false;

  function setupHeader(root) {
    if (!root || root.dataset.inkfireHeaderInitialized === 'true') {
      return false;
    }

    const overlay = root.querySelector('.imh-overlay');
    const menu = root.querySelector('.imh-menu');
    const menuToggles = root.querySelectorAll('.imh-menu-toggle');
    const searchToggles = root.querySelectorAll('.imh-search-toggle');
    const searchSubmitButtons = root.querySelectorAll('.imh-search-submit');
    const searchSheets = root.querySelectorAll('.imh-search-sheet');
    const accordionItems = root.querySelectorAll('[data-accordion-item]');
    const cardItems = root.querySelectorAll('[data-card-item]');

    if (!overlay || !menu) {
      return false;
    }

    root.dataset.inkfireHeaderInitialized = 'true';

    function clearBodyScrollLock() {
      document.body.style.overflow = '';
      document.body.classList.remove('foundation-mobile-header-open');
      document.documentElement.classList.remove('foundation-mobile-header-open');
    }

    function setSearchState(isOpen) {
      searchSheets.forEach((sheet) => {
        sheet.hidden = !isOpen;
      });
      searchToggles.forEach((toggle) => {
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      });
    }

    function setMenuState(isOpen) {
      menu.classList.toggle('is-open', isOpen);
      menu.hidden = !isOpen;
      menu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
      overlay.classList.toggle('is-visible', isOpen);
      overlay.hidden = !isOpen;
      if (isOpen) {
        document.body.style.overflow = 'hidden';
        document.body.classList.add('foundation-mobile-header-open');
        document.documentElement.classList.add('foundation-mobile-header-open');
      } else {
        clearBodyScrollLock();
      }

      menuToggles.forEach((toggle) => {
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        toggle.classList.toggle('is-open', isOpen);
      });

      if (!isOpen) {
        setSearchState(false);
      }
    }

    menuToggles.forEach((toggle) => {
      toggle.addEventListener('click', function () {
        const willOpen = this.getAttribute('aria-expanded') !== 'true';
        setMenuState(willOpen);
      });
    });

    searchToggles.forEach((toggle) => {
      toggle.addEventListener('click', function () {
        const willOpen = this.getAttribute('aria-expanded') !== 'true';
        setSearchState(willOpen);
        if (willOpen && !menu.classList.contains('is-open') && this.closest('.imh-menu')) {
          setMenuState(true);
        }
      });
    });

    searchSubmitButtons.forEach((button) => {
      button.addEventListener('click', function () {
        const searchWrap = this.closest('.imh-search-inner');
        const form = searchWrap ? searchWrap.querySelector('.imh-search-form') : null;

        if (!form) {
          return;
        }

        if (typeof form.requestSubmit === 'function') {
          form.requestSubmit();
          return;
        }

        form.submit();
      });
    });

    overlay.addEventListener('click', function () {
      setMenuState(false);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        setMenuState(false);
      }
    });

    accordionItems.forEach((item) => {
      const toggle = item.querySelector('.imh-accordion-toggle');
      const panel = item.querySelector('.imh-accordion-panel');

      if (!toggle || !panel) {
        return;
      }

      toggle.addEventListener('click', function () {
        const isActive = item.classList.contains('is-active');

        accordionItems.forEach((otherItem) => {
          const otherToggle = otherItem.querySelector('.imh-accordion-toggle');
          const otherPanel = otherItem.querySelector('.imh-accordion-panel');
          otherItem.classList.remove('is-active');
          if (otherToggle) {
            otherToggle.setAttribute('aria-expanded', 'false');
          }
          if (otherPanel) {
            otherPanel.hidden = true;
          }
        });

        if (!isActive) {
          item.classList.add('is-active');
          toggle.setAttribute('aria-expanded', 'true');
          panel.hidden = false;
        }
      });
    });

    cardItems.forEach((item) => {
      const toggle = item.querySelector('.imh-card-toggle');
      const panel = item.querySelector('.imh-card-panel');

      if (!toggle || !panel) {
        return;
      }

      toggle.addEventListener('click', function () {
        const willOpen = toggle.getAttribute('aria-expanded') !== 'true';
        item.classList.toggle('is-open', willOpen);
        toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        panel.hidden = !willOpen;
      });
    });

    root.querySelectorAll('.imh-inline-cta').forEach((wrap) => {
      const buttons = wrap.querySelectorAll('.imh-inline-cta-btn');
      const pill = wrap.querySelector('.imh-inline-cta-pill');

      if (!buttons.length || !pill) {
        return;
      }

      let activeButton = wrap.querySelector('.imh-inline-cta-btn.is-active') || buttons[0];
      const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      function movePill(target) {
        const wrapRect = wrap.getBoundingClientRect();
        const rect = target.getBoundingClientRect();
        const offset = rect.left - wrapRect.left;

        pill.style.width = rect.width + 'px';

        if (prefersReducedMotion) {
          pill.style.transition = 'none';
          pill.style.transform = 'translateX(' + offset + 'px)';
          window.setTimeout(function () {
            pill.style.transition = '';
          }, 0);
        } else {
          pill.style.transform = 'translateX(' + offset + 'px)';
        }

        buttons.forEach((button) => {
          button.classList.remove('is-active');
          button.setAttribute('aria-current', 'false');
        });

        target.classList.add('is-active');
        target.setAttribute('aria-current', 'true');
        activeButton = target;
      }

      buttons.forEach((button) => {
        button.addEventListener('mouseenter', function () {
          movePill(button);
        });

        button.addEventListener('focus', function () {
          movePill(button);
        });

        button.addEventListener('keydown', function (event) {
          if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') {
            return;
          }

          event.preventDefault();
          const currentIndex = Array.prototype.indexOf.call(buttons, button);
          const nextIndex = event.key === 'ArrowRight'
            ? (currentIndex + 1) % buttons.length
            : (currentIndex - 1 + buttons.length) % buttons.length;

          buttons[nextIndex].focus();
        });
      });

      window.setTimeout(function () {
        movePill(activeButton);
      }, 80);

      window.addEventListener('resize', function () {
        movePill(activeButton);
      });
    });

    clearBodyScrollLock();
    return true;
  }

  function initAll(scope) {
    const root = scope || document;
    let initializedAny = false;

    if (root.matches && root.matches('[data-inkfire-header]')) {
      initializedAny = setupHeader(root) || initializedAny;
    }

    root.querySelectorAll('[data-inkfire-header]').forEach(function (headerRoot) {
      initializedAny = setupHeader(headerRoot) || initializedAny;
    });

    return initializedAny;
  }

  function emitInitWarning() {
    if (warnedAboutMissingInit) {
      return;
    }

    warnedAboutMissingInit = true;
    console.warn('[Inkfire mobile header] Header markup was detected without an initialized controller. Reattempting setup.');
    document.dispatchEvent(new CustomEvent('inkfire:mobile-header:warning'));
  }

  function scheduleHealthCheck() {
    if (healthCheckScheduled) {
      return;
    }

    healthCheckScheduled = true;

    window.setTimeout(function () {
      healthCheckScheduled = false;

      const headers = document.querySelectorAll('[data-inkfire-header]');
      if (!headers.length) {
        return;
      }

      const hasUninitializedHeader = Array.prototype.some.call(headers, function (headerRoot) {
        return headerRoot.dataset.inkfireHeaderInitialized !== 'true';
      });

      if (!hasUninitializedHeader) {
        warnedAboutMissingInit = false;
        return;
      }

      emitInitWarning();
      initAll(document);
    }, 1200);
  }

  function startHeaderObserver() {
    if (observerStarted || typeof MutationObserver === 'undefined' || !document.body) {
      return;
    }

    observerStarted = true;

    const observer = new MutationObserver(function (mutations) {
      let shouldInit = false;

      mutations.forEach(function (mutation) {
        if (shouldInit) {
          return;
        }

        mutation.addedNodes.forEach(function (node) {
          if (shouldInit || !node || node.nodeType !== 1) {
            return;
          }

          if ((node.matches && node.matches('[data-inkfire-header]')) || (node.querySelector && node.querySelector('[data-inkfire-header]'))) {
            shouldInit = true;
          }
        });
      });

      if (shouldInit) {
        initAll(document);
        scheduleHealthCheck();
      }
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      initAll(document);
      startHeaderObserver();
      scheduleHealthCheck();
    });
  } else {
    initAll(document);
    startHeaderObserver();
    scheduleHealthCheck();
  }

  window.addEventListener('load', function () {
    initAll(document);
    startHeaderObserver();
    scheduleHealthCheck();
  });

  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    window.elementorFrontend.hooks.addAction('frontend/element_ready/foundation-mobile-header.default', function ($scope) {
      initAll($scope[0] || $scope);
      scheduleHealthCheck();
    });
  } else {
    window.addEventListener('elementor/frontend/init', function () {
      if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction('frontend/element_ready/foundation-mobile-header.default', function ($scope) {
          initAll($scope[0] || $scope);
          scheduleHealthCheck();
        });
      }
    });
  }
})();
