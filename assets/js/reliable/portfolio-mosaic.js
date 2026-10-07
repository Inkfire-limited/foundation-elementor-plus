(function () {
  'use strict';
  window.FoundationWidgets.register('portfolio-mosaic', '[data-foundation-portfolio-mosaic]', function (root, life) {
    const grid = root.querySelector('.foundation-portfolio-mosaic__grid');
    const cards = Array.from(root.querySelectorAll('[data-foundation-portfolio-mosaic-card]'));
    const filters = Array.from(root.querySelectorAll('[data-foundation-portfolio-filter]'));
    if (!grid) return false;
    const initial = Math.max(0, parseInt(root.dataset.foundationPortfolioInitialLimit, 10) || 0);
    const enabled = root.dataset.foundationPortfolioLoadMore === 'yes' || initial > 0;
    const step = Math.max(1, parseInt(root.dataset.foundationPortfolioLoadMoreStep, 10) || 1);
    let limit = initial;
    let activeFilter = 'all';
    let wrap = root.querySelector('[data-foundation-portfolio-load-more-wrap]');
    let more = root.querySelector('[data-foundation-portfolio-load-more-button]');
    if (enabled && !wrap) {
      wrap = document.createElement('div'); wrap.className = 'foundation-portfolio-mosaic__load-more-wrap';
      wrap.setAttribute('data-foundation-portfolio-load-more-wrap', '');
      (root.querySelector('.foundation-portfolio-mosaic__wrap') || root).appendChild(wrap);
    }
    if (enabled && !more) {
      more = document.createElement('button'); more.type = 'button'; more.className = 'foundation-portfolio-mosaic__load-more';
      more.setAttribute('data-foundation-portfolio-load-more-button', '');
      more.textContent = root.dataset.foundationPortfolioLoadMoreLabel || 'Load More Work'; wrap.appendChild(more);
    }
    function layout() {
      const style = getComputedStyle(root);
      const threshold = parseFloat(style.getPropertyValue('--foundation-portfolio-meta-stack-threshold')) || 360;
      const columns = getComputedStyle(grid).gridTemplateColumns;
      const count = columns && columns !== 'none' ? columns.split(' ').filter(Boolean).length : 1;
      const featured = !root.classList.contains('foundation-portfolio-mosaic--layout-editorial') && !root.classList.contains('foundation-portfolio-mosaic--compact') && count >= (parseInt(root.dataset.foundationPortfolioFeatureMinColumns, 10) || 4);
      root.classList.toggle('foundation-portfolio-mosaic--feature-active', featured);
      cards.forEach((card) => card.classList.toggle('foundation-portfolio-mosaic__card-shell--stacked-meta', card.clientWidth <= threshold));
    }
    function apply(filter, reset) {
      activeFilter = filter;
      if (reset) limit = initial;
      let matched = 0, shown = 0;
      cards.forEach((card) => {
        const fixed = card.dataset.foundationPortfolioStaticCard === 'yes';
        const matches = fixed || filter === 'all' || (card.dataset.foundationPortfolioFilters || '').split(' ').includes(filter);
        let visible = matches;
        if (matches && !fixed) { matched++; if (enabled && limit > 0 && shown >= limit) visible = false; else shown++; }
        card.hidden = !visible; card.setAttribute('aria-hidden', String(!visible));
      });
      filters.forEach((button) => {
        const active = (button.dataset.foundationPortfolioFilter || 'all') === filter;
        button.classList.toggle('is-active', active); button.setAttribute('aria-selected', String(active));
        if (button.getAttribute('role') === 'tab') button.tabIndex = active ? 0 : -1;
      });
      if (enabled && more && wrap) { wrap.hidden = more.hidden = shown >= matched; more.setAttribute('aria-hidden', String(more.hidden)); }
      root.dataset.foundationPortfolioActiveFilter = filter; root.dataset.foundationPortfolioCurrentLimit = String(limit);
      root.classList.toggle('foundation-portfolio-mosaic--filtered-view', filter !== 'all' && shown > 0);
      layout();
    }
    filters.forEach((button, index) => {
      life.listen(button, 'click', () => apply(button.dataset.foundationPortfolioFilter || 'all', true));
      life.listen(button, 'keydown', (event) => {
        const values = { ArrowRight: (index + 1) % filters.length, ArrowLeft: (index - 1 + filters.length) % filters.length, Home: 0, End: filters.length - 1 };
        if (!(event.key in values)) return;
        event.preventDefault(); const target = filters[values[event.key]];
        apply(target.dataset.foundationPortfolioFilter || 'all', true); target.focus();
      });
    });
    life.listen(more, 'click', () => { limit += step; apply(activeFilter, false); });
    const clear = () => cards.forEach((card) => {
      card.classList.remove('is-hovering');
      ['--foundation-portfolio-pointer-x', '--foundation-portfolio-pointer-y', '--foundation-portfolio-rotate-x', '--foundation-portfolio-rotate-y'].forEach((key) => card.style.removeProperty(key));
    });
    const motion = life.media('(prefers-reduced-motion: reduce)', clear);
    const pointer = life.media('(hover: hover) and (pointer: fine)', clear);
    cards.forEach((card) => {
      life.listen(card, 'mousemove', (event) => {
        if (motion.matches || !pointer.matches || root.dataset.foundationPortfolioTilt !== 'yes') return;
        const r = card.getBoundingClientRect(); if (!r.width || !r.height) return;
        const x = (event.clientX - r.left) / r.width * 100, y = (event.clientY - r.top) / r.height * 100;
        card.style.setProperty('--foundation-portfolio-pointer-x', x + '%'); card.style.setProperty('--foundation-portfolio-pointer-y', y + '%');
        card.style.setProperty('--foundation-portfolio-rotate-x', ((50 - y) / 50 * 3) + 'deg'); card.style.setProperty('--foundation-portfolio-rotate-y', ((x - 50) / 50 * 3) + 'deg');
        card.classList.add('is-hovering');
      });
      life.listen(card, 'mouseleave', clear); life.listen(card, 'blur', clear, true);
    });
    life.observe(window.ResizeObserver, root, layout); life.listen(window, 'resize', layout); life.cleanup(clear);
    apply('all', true);
  });
})();
