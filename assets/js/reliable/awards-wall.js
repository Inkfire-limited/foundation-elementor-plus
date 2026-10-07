(function () {
  'use strict';
  window.FoundationWidgets.register('awards-wall', '[data-foundation-awards-hover]', function (root, life) {

    const clear = () => {
      root.classList.remove('is-hovering');
      root.style.removeProperty('--foundation-awards-x'); root.style.removeProperty('--foundation-awards-y');
    };
    const motion = life.media('(prefers-reduced-motion: reduce)', clear);
    const pointer = life.media('(hover: hover) and (pointer: fine)', clear);
    life.listen(root, 'mousemove', (event) => {
      if (motion.matches || !pointer.matches) return;
      const rect = root.getBoundingClientRect(); if (!rect.width || !rect.height) return;
      root.style.setProperty('--foundation-awards-x', ((event.clientX - rect.left) / rect.width * 100) + '%');
      root.style.setProperty('--foundation-awards-y', ((event.clientY - rect.top) / rect.height * 100) + '%');
      root.classList.add('is-hovering');
    });
    life.listen(root, 'mouseleave', clear); life.listen(root, 'blur', clear, true); life.cleanup(clear);

  });
})();
