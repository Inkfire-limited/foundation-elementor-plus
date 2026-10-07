(function () {
  'use strict';
  if (window.FoundationWidgets) return;
  const definitions = new Map();
  let observer = null;
  let queued = false;
  let hookOwner = null;
  const pendingScopes = new Set();

  function lifecycle() {
    const disposers = new Set();
    const timers = new Set();
    const frames = new Set();
    const boundElements = new Set();
    let active = true;
    const api = {
      get active() { return active; },
      get stale() { return Array.from(boundElements).some((node) => !node.isConnected); },
      cleanup(fn) { disposers.add(fn); return () => disposers.delete(fn); },
      listen(target, name, fn, options) {
        if (!target || !target.addEventListener) return;
        if (target instanceof Element) boundElements.add(target);
        target.addEventListener(name, fn, options);
        api.cleanup(() => target.removeEventListener(name, fn, options));
      },
      media(query, fn) {
        const media = window.matchMedia(query);
        if (media.addEventListener) api.listen(media, 'change', fn);
        else { media.addListener(fn); api.cleanup(() => media.removeListener(fn)); }
        return media;
      },
      timeout(fn, delay) {
        const id = window.setTimeout(() => { timers.delete(id); if (active) fn(); }, delay);
        timers.add(id); return id;
      },
      clearTimeout(id) { window.clearTimeout(id); timers.delete(id); },
      frame(fn) {
        const id = window.requestAnimationFrame((time) => { frames.delete(id); if (active) fn(time); });
        frames.add(id); return id;
      },
      cancelFrame(id) { window.cancelAnimationFrame(id); frames.delete(id); },
      observe(Type, target, callback, options) {
        if (!Type || !target) return null;
        const instance = new Type((...args) => { if (active) callback(...args); }, options);
        if (target instanceof Element) boundElements.add(target);
        instance.observe(target); api.cleanup(() => instance.disconnect()); return instance;
      },
      async json(url, options, timeout) {
        const controller = new AbortController();
        const removeCleanup = api.cleanup(() => controller.abort());
        const timer = window.setTimeout(() => controller.abort(), timeout || 25000);
        try {
          const response = await fetch(url, Object.assign({}, options, { signal: controller.signal }));
          const json = await response.json();
          return { response, json };
        } finally { window.clearTimeout(timer); removeCleanup(); }
      },
      destroy() {
        if (!active) return;
        active = false;
        timers.forEach(window.clearTimeout); frames.forEach(window.cancelAnimationFrame);
        timers.clear(); frames.clear();
        Array.from(disposers).reverse().forEach((fn) => { try { fn(); } catch (_) {} });
        disposers.clear(); boundElements.clear();
      }
    };
    return api;
  }

  function unmount(definition, root) {
    const instance = definition.instances.get(root);
    if (!instance) return;
    definition.instances.delete(root);
    instance.life.destroy();
  }
  function mount(definition, root) {
    if (!root.isConnected || definition.instances.has(root)) return;
    const life = lifecycle();
    const instance = { life, anchor: root.firstElementChild };
    definition.instances.set(root, instance);
    try {
      const cleanup = definition.init(root, life);
      if (cleanup === false) { unmount(definition, root); return; }
      if (typeof cleanup === 'function') life.cleanup(cleanup);
      instance.anchor = root.firstElementChild;
    } catch (error) {
      unmount(definition, root);
      console.warn('[Foundation widgets] ' + definition.name + ' could not initialise.', error);
    }
  }
  function scan(scope) {
    const node = scope && scope.querySelectorAll ? scope : document;
    definitions.forEach((definition) => {
      if (node.matches && node.matches(definition.selector)) mount(definition, node);
      node.querySelectorAll(definition.selector).forEach((root) => mount(definition, root));
    });
  }
  function sweep() {
    definitions.forEach((definition) => {
      definition.instances.forEach((instance, root) => {
        if (!root.isConnected) unmount(definition, root);
        else if (instance.anchor !== root.firstElementChild || instance.life.stale) {
          unmount(definition, root); mount(definition, root);
        }
      });
    });
  }
  function schedule(scope) {
    pendingScopes.add(scope || document);
    if (queued) return;
    queued = true;
    queueMicrotask(() => {
      queued = false; sweep();
      const scopes = Array.from(pendingScopes); pendingScopes.clear();
      scopes.forEach((node) => { if (node === document || node.isConnected) scan(node); });
      connectElementor();
    });
  }
  function connectElementor() {
    const hooks = window.elementorFrontend && window.elementorFrontend.hooks;
    if (!hooks || hooks === hookOwner) return;
    hookOwner = hooks;
    hooks.addAction('frontend/element_ready/global', ($scope) => {
      const scope = $scope && ($scope[0] || $scope);
      sweep(); scan(scope);
    });
  }
  function start() {
    if (!observer && document.body) {
      observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          if (mutation.removedNodes.length) schedule(mutation.target);
          mutation.addedNodes.forEach((node) => { if (node.nodeType === 1) schedule(node); });
        });
      });
      observer.observe(document.body, { childList: true, subtree: true });
    }
    connectElementor(); scan(document);
  }
  window.FoundationWidgets = {
    register(name, selector, init) {
      if (definitions.has(name)) return;
      definitions.set(name, { name, selector, init, instances: new Map() });
      if (document.readyState !== 'loading') start();
    },
    scan,
    destroy(scope) {
      definitions.forEach((definition) => definition.instances.forEach((_, root) => {
        if (!scope || scope === root || scope.contains(root)) unmount(definition, root);
      }));
    },
    status() {
      return Array.from(definitions.values(), (definition) => ({
        name: definition.name, mounted: definition.instances.size
      }));
    }
  };
  document.dispatchEvent(new CustomEvent('foundation:widgets-ready'));
  if (window.jQuery) window.jQuery(window).on('elementor/frontend/init.foundationWidgets', start);
  window.addEventListener('elementor/frontend/init', start);
  document.addEventListener('DOMContentLoaded', start, { once: true });
  window.addEventListener('load', start, { once: true });
  window.addEventListener('pageshow', start);
  window.addEventListener('pagehide', (event) => {
    if (!event.persisted) window.FoundationWidgets.destroy();
  });
  if (document.readyState !== 'loading') start();
})();
