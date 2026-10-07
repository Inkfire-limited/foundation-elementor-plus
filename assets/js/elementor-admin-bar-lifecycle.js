/** Scope ready-event deduplication to Elementor's native toolbar bootstrap. */
(function () {
  'use strict';
  var state = window.FoundationElementorAdminBarLifecycle;
  if (!state) {
    state = window.FoundationElementorAdminBarLifecycle = { registered: false, started: false, restore: null };
  }
  if (state.restore) state.restore();
  var original = document.addEventListener;
  var originalRemove = document.removeEventListener;
  var descriptor = Object.getOwnPropertyDescriptor(document, 'addEventListener');
  function scopedAddEventListener(type, listener, options) {
    var script = document.currentScript;
    if (this !== document || type !== 'DOMContentLoaded' || !script ||
        script.id !== 'elementor-admin-bar-js' || typeof listener !== 'function') {
      return original.apply(this, arguments);
    }
    if (state.registered) return;
    state.registered = true;
    var capture = typeof options === 'object' && options !== null ? !!options.capture : !!options;
    var initialising = false;
    function initialiseOnce(event) {
      if (state.started || initialising) return;
      // A ready-event replay must not initialise against an unfinished toolbar.
      if (!document.getElementById('wp-admin-bar-root-default')) return;
      initialising = true;
      try {
        if (!document.getElementById('wp-admin-bar-elementor_edit_page')) {
          listener.call(document, event);
        }
        state.started = !!document.getElementById('wp-admin-bar-elementor_edit_page');
      } finally {
        initialising = false;
        if (state.started) {
          // Remove only after the real callback ran, not a loader's suppressed event.
          originalRemove.call(document, type, initialiseOnce, capture);
        }
      }
    }
    if (document.readyState !== 'loading') {
      // Native bootstrap otherwise misses pages where its script arrives late.
      Promise.resolve().then(function () { initialiseOnce(new Event('DOMContentLoaded')); });
      return;
    }
    // Rocket Loader can consume the native ready event without calling us.
    // Native once:true would then remove its proxy before the replay arrives.
    var listenerOptions = typeof options === 'object' && options !== null
      ? Object.assign({}, options, { once: false }) : { capture: capture, once: false };
    return original.call(document, type, initialiseOnce, listenerOptions);
  }
  document.addEventListener = scopedAddEventListener;
  state.restore = function () {
    if (document.addEventListener === scopedAddEventListener) {
      if (descriptor) Object.defineProperty(document, 'addEventListener', descriptor);
      else delete document.addEventListener;
    }
    state.restore = null;
  };
})();
