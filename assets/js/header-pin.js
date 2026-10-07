/**
 * Keep the Elementor header containers in view while the page scrolls.
 *
 * Both header containers are positioned absolutely by their Elementor settings,
 * so they scroll away with the document. This preserves their authored resting
 * placement exactly, follows the scroll upward as the build banner leaves, then
 * holds at a snug gap.
 *
 * The desktop container's `top` is owned by an !important rule in
 * Header_Banner (calc(var(--foundation-header-banner-offset) + 20px)), so the
 * inline value is written with priority to stay in charge of both containers.
 * Elementor's `position` rules are not !important, so plain inline wins there.
 *
 * If this script never runs the header simply behaves as it did before.
 * No jQuery, no build step.
 */
(function () {
	'use strict';

	// Optimizers can replay ready events or execute an asset twice.
	if (window.FoundationHeaderPinLoaded) return;
	window.FoundationHeaderPinLoaded = true;
	var started = false;
	var layoutFrame = 0;
	var bannerObserver = null;
	var GAP = 20;
	var items = [];
	var frame = 0;

	var heroTarget = null;
	var menuPanels = new Map();
	var menuResizeObserver = null;
	var headerMutationObserver = null;
	var headerSizeObserver = null;
	var CONTENT_GAP = 40;
	var MENU_EDGE_GAP = 16;

	function setStyleIfChanged(el, name, value) {
		if (el.style.getPropertyValue(name) !== value) el.style.setProperty(name, value);
	}

	/** Reserve clearance at rest, never from the current scroll position. */
	function syncHeroClearance() {
		if (heroTarget) {
			heroTarget.removeAttribute('data-foundation-header-clearance');
			heroTarget.style.removeProperty('--foundation-header-base-padding');
			heroTarget.style.removeProperty('--foundation-header-required-padding');
			heroTarget = null;
		}
		if (!items.length) return;
		var heading = document.querySelector('main h1, [data-elementor-type="wp-page"] h1, body.elementor-page h1');
		if (!heading || heading.closest('.elementor-location-header, .elementor-location-footer')) return;
		var surface = heading.closest('.foundation-inkfire-splash');
		var target = surface && surface.querySelector('.foundation-inkfire-hero');
		if (!surface) {
			// Native Elementor heroes have padding on the direct inner box when boxed.
			surface = heading.closest('.e-con.e-parent');
			if (!surface) return;
			target = Array.prototype.find.call(surface.children, function (child) {
				return child.classList.contains('e-con-inner') && child.contains(heading);
			}) || surface;
		}
		if (!target) return;
		var bottom = Math.max.apply(null, items.map(function (item) { return item.restBottom; }));
		var required = Math.max(0, Math.ceil(bottom + CONTENT_GAP - (surface.getBoundingClientRect().top + scrollY())));
		var authored = parseFloat(window.getComputedStyle(target).paddingTop) || 0;
		// Preserve deliberate larger padding; only raise the minimum when necessary.
		target.style.setProperty('--foundation-header-base-padding', authored + 'px');
		target.style.setProperty('--foundation-header-required-padding', required + 'px');
		target.setAttribute('data-foundation-header-clearance', '');
		heroTarget = target;
	}

	function releaseMenuPanel(panel, state) {
		panel.classList.remove('foundation-mega-viewport');
		panel.style.removeProperty('--foundation-mega-available-height');
		if (state.addedTabindex && panel.getAttribute('tabindex') === '0') panel.removeAttribute('tabindex');
		if (menuResizeObserver) state.observed.forEach(function (el) { menuResizeObserver.unobserve(el); });
		panel.scrollTop = 0;
		menuPanels.delete(panel);
	}

	/** Let the browser scroll dropdown content, not the page behind a fixed header. */
	function syncMenuViewports() {
		var vv = window.visualViewport;
		var viewportTop = vv ? vv.offsetTop : 0;
		var viewportBottom = vv ? vv.offsetTop + vv.height : window.innerHeight;
		var toolbar = document.getElementById('wpadminbar');
		if (toolbar && window.getComputedStyle(toolbar).position === 'fixed') {
			viewportTop = Math.max(viewportTop, toolbar.getBoundingClientRect().bottom);
		}
		var active = new Set();
		document.querySelectorAll('.elementor-location-header.elementor-3844 .elementor-widget-n-menu .e-n-menu-content > .e-con.e-active').forEach(function (panel) {
			var menu = panel.closest('.e-n-menu');
			// The separate hamburger/dropdown layout already owns its scrolling.
			if (!menu || menu.getAttribute('data-layout') === 'dropdown' || !panel.getClientRects().length) return;
			var rect = panel.getBoundingClientRect();
			if (rect.width <= 0 || window.getComputedStyle(panel).visibility === 'hidden') return;
			active.add(panel);
			var state = menuPanels.get(panel);
			if (!state) {
				state = { addedTabindex: false, observed: [panel] };
				if (panel.firstElementChild) state.observed.push(panel.firstElementChild);
				menuPanels.set(panel, state);
				panel.classList.add('foundation-mega-viewport');
				if (menuResizeObserver) state.observed.forEach(function (el) { menuResizeObserver.observe(el); });
			}
			var available = menu.classList.contains('content-above')
				? rect.bottom - viewportTop - MENU_EDGE_GAP
				: viewportBottom - rect.top - MENU_EDGE_GAP;
			setStyleIfChanged(panel, '--foundation-mega-available-height', Math.max(1, Math.floor(available)) + 'px');
			var scrollable = panel.scrollHeight > panel.clientHeight + 1;
			if (scrollable && !panel.hasAttribute('tabindex')) {
				panel.setAttribute('tabindex', '0');
				state.addedTabindex = true;
			} else if (!scrollable && state.addedTabindex) {
				if (panel.getAttribute('tabindex') === '0') panel.removeAttribute('tabindex');
				state.addedTabindex = false;
			}
		});
		menuPanels.forEach(function (state, panel) {
			if (!active.has(panel)) releaseMenuPanel(panel, state);
		});
	}

	function observeHeaderLayout() {
		var header = document.querySelector('.elementor-location-header.elementor-3844');
		if (!header) return;
		if (typeof ResizeObserver !== 'undefined') {
			menuResizeObserver = new ResizeObserver(onScroll);
			headerSizeObserver = new ResizeObserver(scheduleMeasure);
			Array.prototype.forEach.call(header.children, function (el) {
				if (el.matches('.e-con.e-parent')) headerSizeObserver.observe(el);
			});
		}
		if (typeof MutationObserver !== 'undefined') {
			headerMutationObserver = new MutationObserver(onScroll);
			headerMutationObserver.observe(header, { subtree: true, childList: true, attributes: true, attributeFilter: ['class', 'aria-expanded', 'data-layout', 'hidden'] });
		}
		// Handles slide-in animation completion without per-frame polling.
		header.addEventListener('animationend', onScroll);
		header.addEventListener('transitionend', onScroll);
		if (window.visualViewport) {
			window.visualViewport.addEventListener('resize', onScroll, { passive: true });
			window.visualViewport.addEventListener('scroll', onScroll, { passive: true });
		}
	}


	function scrollY() {
		return window.pageYOffset || document.documentElement.scrollTop || 0;
	}

	/** The admin bar is itself fixed, so pinned headers must clear it. */
	function adminBarHeight() {
		var bar = document.getElementById( 'wpadminbar' );

		if ( ! bar || 'fixed' !== window.getComputedStyle( bar ).position ) {
			return 0;
		}

		return bar.getBoundingClientRect().height || 0;
	}

	function release( el ) {
		el.style.removeProperty( 'position' );
		el.style.removeProperty( 'top' );
		el.style.removeProperty( 'left' );
		el.style.removeProperty( 'width' );
	}

	/** Measure each container in its authored state, then take ownership of it. */
	function measure() {
		items = [];

		// Refresh the shared offset before reading authored CSS. Banner wrapping,
		// late fonts and content changes can all change its height without resize.
		var banner = document.querySelector( '.foundation-header-build-banner-wrap:not(.foundation-header-build-banner-wrap--preview)' );
		if (banner) {
			document.documentElement.style.setProperty( '--foundation-header-banner-offset', banner.offsetHeight + 'px' );
		}
		var containers = document.querySelectorAll( '.elementor-location-header.elementor-3844 > .e-con.e-parent:not(.elementor-sticky__spacer)' );

		Array.prototype.forEach.call( containers, function ( el ) {
			release( el );

			var rect = el.getBoundingClientRect();

			if ( 'absolute' !== window.getComputedStyle( el ).position || null === el.offsetParent || rect.height <= 0 ) {
				return;
			}

			items.push( {
				el: el,
				restTop: rect.top + scrollY(),
				restBottom: rect.bottom + scrollY(),
				left: rect.left,
				width: rect.width
			} );
		} );

		syncHeroClearance();
		apply();
	}

	function apply() {
		var y = scrollY();
		var min = GAP + adminBarHeight();

		for ( var i = 0; i < items.length; i++ ) {
			var item = items[ i ];
			var top = item.restTop - y;

			if ( top < min ) {
				top = min;
			}

			item.el.style.setProperty( 'position', 'fixed' );
			item.el.style.setProperty( 'top', top + 'px', 'important' );
			item.el.style.setProperty( 'left', item.left + 'px' );
			item.el.style.setProperty( 'width', item.width + 'px' );
		}
		syncMenuViewports();
	}

	function onScroll() {
		if ( frame ) {
			return;
		}

		frame = window.requestAnimationFrame( function () {
			frame = 0;
			apply();
		} );
	}

	function scheduleMeasure() {
		if (layoutFrame) return;
		layoutFrame = window.requestAnimationFrame(function () {
			layoutFrame = 0;
			measure();
		});
	}

	function start() {
		if (started) return;
		started = true;
		// Leave the builder canvas in charge of its own positioning.
		if (new URLSearchParams(window.location.search).has('elementor-preview') || document.body.classList.contains('elementor-editor-active')) return;
		observeHeaderLayout();
		measure();
		var banner = document.querySelector('.foundation-header-build-banner-wrap:not(.foundation-header-build-banner-wrap--preview)');
		if (banner && typeof ResizeObserver !== 'undefined') {
			bannerObserver = new ResizeObserver(scheduleMeasure);
			bannerObserver.observe(banner);
		}
		if (document.fonts && document.fonts.ready) document.fonts.ready.then(scheduleMeasure);

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', scheduleMeasure );
		window.addEventListener( 'orientationchange', scheduleMeasure );
		window.addEventListener( 'pageshow', scheduleMeasure );

		// Header_Banner re-measures the banner on load; re-read after it settles.
		window.addEventListener( 'load', scheduleMeasure );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', start, { once: true } );
	} else {
		start();
	}
} )();
