(function () {
  'use strict';
  if (window.FoundationPostNavigationLoaded) return;
  window.FoundationPostNavigationLoaded = true;
  let sequence = 0;
  const ROOT = '.foundation-post-carousel';
  const EVENTS = 'init update slideChange reachBeginning reachEnd fromEdge lock unlock enable disable breakpoint observerUpdate resize transitionEnd destroy';
  function register() {
    window.FoundationWidgets.register('post-carousel-navigation', ROOT, function (root, life) {
      const own = (selector) => Array.from(root.querySelectorAll(selector)).filter((node) => node.closest(ROOT) === root);
      const previous = own('.foundation-post-prev .elementor-icon')[0];
      const next = own('.foundation-post-next .elementor-icon')[0];
      const widgets = own('.foundation-post-slider');
      // Ambiguous sections fail closed rather than controlling the wrong carousel.
      if (!previous || !next || widgets.length !== 1) return false;
      const widget = widgets[0];
      const controls = [previous, next];
      let swiper = null;
      let failedSwiper = null;
      let frame = 0;
      let managedId = null;
      let managedTarget = null;
      let pausedByPolicy = false;
      let userStopped = false;
      const savedSlides = new Map();
      const savedControls = controls.map((node) => ({ node, attrs: ['role','tabindex','aria-label','aria-controls','aria-disabled'].map((key) => [key,node.getAttribute(key)]) }));
      own('[data-foundation-post-status]').forEach((node) => node.remove());
      const status = document.createElement('span');
      status.setAttribute('data-foundation-post-status','');
      status.className = 'elementor-screen-only';
      status.setAttribute('role', 'status');
      status.setAttribute('aria-live', 'polite');
      status.setAttribute('aria-atomic', 'true');
      root.appendChild(status);
      const motion = life.media('(prefers-reduced-motion: reduce)', schedule);
      const attr = (node, key, value) => { if (node.getAttribute(key) !== value) node.setAttribute(key,value); };
      controls.forEach((node, index) => {
        // Compatibility for editor-side HTML templates or a cached pre-button fragment.
        if (node.tagName !== 'BUTTON') { attr(node,'role','button'); attr(node,'tabindex','0'); }
        attr(node,'aria-label', index ? 'Next posts' : 'Previous posts');
      });
      function findSlider() {
        return Array.from(widget.querySelectorAll('.swiper, .swiper-container')).find((node) => node.closest('.foundation-post-slider') === widget && (!node.closest('.swiper-slide') || !widget.contains(node.closest('.swiper-slide')))) || null;
      }
      function disconnectSwiper() {
        if (swiper && typeof swiper.off === 'function') swiper.off(EVENTS, schedule);
        swiper = null;
      }
      function disabled(index) {
        if (!swiper || swiper.destroyed || swiper.enabled === false || swiper.isLocked) return true;
        if (index ? swiper.allowSlideNext === false : swiper.allowSlidePrev === false) return true;
        return !(swiper.params && (swiper.params.loop || swiper.params.rewind)) && (index ? swiper.isEnd : swiper.isBeginning);
      }
      function releaseId() {
        if (managedTarget && managedTarget.id === managedId) managedTarget.removeAttribute('id');
        managedTarget = null; managedId = null;
      }
      function restoreSlides() {
        savedSlides.forEach((attrs, slide) => {
          attrs.forEach(([key,value]) => {
            if (value === null) slide.removeAttribute(key); else slide.setAttribute(key,value);
          });
        });
        savedSlides.clear();
      }
      function syncSlides(element) {
        if (!element || !swiper) { restoreSlides(); return; }
        const bounds = element.getBoundingClientRect();
        Array.from(element.querySelectorAll('.swiper-slide')).filter(slide => slide.closest('.swiper') === element).forEach(slide => {
          if (!savedSlides.has(slide)) savedSlides.set(slide, ['inert','aria-hidden'].map(key => [key,slide.getAttribute(key)]));
          const rect = slide.getBoundingClientRect();
          const visible = rect.width > 0 && Math.min(rect.right,bounds.right) - Math.max(rect.left,bounds.left) > 1;
          slide.toggleAttribute('inert', !visible);
          attr(slide,'aria-hidden',visible ? 'false' : 'true');
        });
      }
      function syncAutoplay() {
        const autoplay = swiper && swiper.autoplay;
        if (!autoplay || typeof autoplay.stop !== 'function' || typeof autoplay.start !== 'function') return;
        const rect = root.getBoundingClientRect();
        const pause = motion.matches || document.hidden || root.contains(document.activeElement) || root.matches(':hover') || rect.bottom <= 0 || rect.top >= window.innerHeight;
        if (pause && autoplay.running) { autoplay.stop(); pausedByPolicy = true; }
        else if (!pause && pausedByPolicy && !userStopped) { pausedByPolicy = false; autoplay.start(); }
      }
      function sync() {
        if (!life.active) return;
        const element = findSlider();
        const candidate = element && element.swiper;
        const live = candidate && candidate !== failedSwiper && !candidate.destroyed && candidate.initialized !== false && typeof candidate.slideNext === 'function' && typeof candidate.slidePrev === 'function' ? candidate : null;
        if (live !== swiper) {
          restoreSlides();
          disconnectSwiper();
          pausedByPolicy = false;
          swiper = live;
          if (swiper && typeof swiper.on === 'function') swiper.on(EVENTS, schedule);
        }
        if (element) {
          if (managedTarget && managedTarget !== element) releaseId();
          // Swiper's own wrapper ID remains untouched. Cloned section IDs are not reused.
          if (!element.id || document.getElementById(element.id) !== element) {
            let id;
            do { id = 'foundation-post-carousel-' + (++sequence); } while (document.getElementById(id));
            element.id = id; managedTarget = element; managedId = id;
          }
          controls.forEach((node) => attr(node,'aria-controls',element.id));
        } else {
          releaseId(); controls.forEach((node) => node.removeAttribute('aria-controls'));
        }
        attr(root,'data-foundation-post-nav',swiper ? 'ready' : 'waiting');
        controls.forEach((node,index) => attr(node,'aria-disabled',disabled(index) ? 'true' : 'false'));
        syncSlides(element);
        syncAutoplay();
        if (swiper && status.textContent) status.textContent = '';
      }
      function schedule() {
        if (!life.active || frame) return;
        frame = life.frame(() => { frame = 0; sync(); });
      }
      function navigate(event, index) {
        event.preventDefault();
        failedSwiper = null;
        sync(); // Resolve the current instance, never a stale hidden-arrow reference.
        if (!swiper) {
          status.textContent = 'The post carousel is still loading. You can also use the All Posts link.';
          return;
        }
        if (disabled(index)) return;
        try {
          userStopped = true;
          pausedByPolicy = false;
          const speed = motion.matches ? 0 : undefined;
          if (index) swiper.slideNext(speed); else swiper.slidePrev(speed);
          sync();
        } catch (_) {
          failedSwiper = swiper;
          disconnectSwiper();
          attr(root,'data-foundation-post-nav','waiting');
          controls.forEach((node) => attr(node,'aria-disabled','true'));
          status.textContent = 'The post carousel could not move. Please use the All Posts link.';
        }
      }
      controls.forEach((node,index) => {
        life.listen(node,'click',(event) => navigate(event,index));
        if (node.tagName !== 'BUTTON') {
          life.listen(node,'keydown',(event) => {
            if ((event.key === 'Enter' || event.key === ' ') && !event.repeat) navigate(event,index);
          });
        }
        life.listen(node,'focus',sync);
        life.listen(node,'pointerenter',sync);
      });
      const observer = new MutationObserver(schedule);
      observer.observe(widget,{childList:true,subtree:true,attributes:true,attributeFilter:['class']});
      life.cleanup(() => observer.disconnect());
      life.listen(widget,'load',schedule,true);
      life.listen(widget,'pointerdown',() => { userStopped = true; pausedByPolicy = false; });
      life.listen(root,'focusin',schedule);
      life.listen(root,'focusout',schedule);
      life.listen(root,'mouseenter',schedule);
      life.listen(root,'mouseleave',schedule);
      life.listen(document,'visibilitychange',schedule);
      life.listen(window,'scroll',schedule,{passive:true});
      life.listen(window,'resize',schedule);
      life.listen(window,'pageshow',schedule);
      life.listen(window,'load',schedule);
      // No timer guesses. Swiper initialisation changes its DOM, while these events
      // cover Elementor's synchronous and asynchronously loaded handlers.
      const hooks = window.elementorFrontend && window.elementorFrontend.hooks;
      if (hooks) {
        const ready = ($scope) => {
          const scope = $scope && ($scope[0] || $scope);
          if (scope && (scope === widget || widget.contains(scope))) schedule();
        };
        ['frontend/element_ready/loop-carousel.post','frontend/element_handler_ready/loop-carousel.post','frontend/element_ready/loop-carousel.default','frontend/element_handler_ready/loop-carousel.default'].forEach((name) => {
          hooks.addAction(name,ready);
          life.cleanup(() => { if (hooks.removeAction) hooks.removeAction(name,ready); });
        });
      }
      life.cleanup(() => {
        restoreSlides(); disconnectSwiper(); releaseId(); status.remove(); root.removeAttribute('data-foundation-post-nav');
        savedControls.forEach(({node,attrs}) => attrs.forEach(([key,value]) => {
          if (value === null) node.removeAttribute(key); else node.setAttribute(key,value);
        }));
      });
      sync();
    });
  }
  if (window.FoundationWidgets) register();
  else document.addEventListener('foundation:widgets-ready', register, {once:true});
})();
