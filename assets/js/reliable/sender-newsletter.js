(function () {
  'use strict';
  window.FoundationWidgets.register('sender-newsletter', '[data-foundation-sender-newsletter]', function (root, life) {

    const form = root.querySelector('form.foundation-sender-newsletter');
    const button = form && form.querySelector('button[type="submit"]');
    if (!form || !button) return false;
    const message = form.querySelector('.foundation-sender-newsletter__message');
    const label = root.getAttribute('data-button-text') || button.textContent || 'Join the list';
    const ajax = root.getAttribute('data-ajax-url') || '/wp-admin/admin-ajax.php';
    let nonce = root.getAttribute('data-nonce') || '';
    let busy = false;
    function show(text, state) {
      if (!message) return;
      message.textContent = text;
      message.className = 'foundation-sender-newsletter__message ' + state;
    }
    life.cleanup(() => { button.disabled = false; button.textContent = label; form.removeAttribute('aria-busy'); });
    life.listen(form, 'submit', async (event) => {
      event.preventDefault();
      if (busy) return;
      const email = form.querySelector('[name="email"]');
      if (!email || !email.value.trim() || !email.checkValidity()) {
        show('Please enter a valid email address.', 'is-error'); if (email) email.focus(); return;
      }
      busy = true; button.disabled = true; button.textContent = 'Joining...'; form.setAttribute('aria-busy', 'true'); show('', '');
      const data = new URLSearchParams(new FormData(form));
      data.set('action', 'foundation_sender_subscribe'); data.set('nonce', nonce);
      data.set('group_id', root.getAttribute('data-group-id') || '');
      const send = (body) => life.json(ajax, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }, body: body.toString() }, 25000);
      try {
        let result = await send(data);
        // Only retry a rejected nonce. Never auto-repeat a potentially completed subscription.
        if (result.response.status === 403 && result.json.data && result.json.data.code === 'nonce_expired') {
          const refreshed = await life.json(ajax + (ajax.includes('?') ? '&' : '?') + 'action=foundation_sender_nonce', { credentials: 'same-origin', cache: 'no-store' }, 10000);
          if (!refreshed.response.ok || !refreshed.json.success || !refreshed.json.data.nonce) throw new Error('Please refresh the page and try again.');
          nonce = refreshed.json.data.nonce; data.set('nonce', nonce); result = await send(data);
        }
        if (!result.response.ok || !result.json.success) throw new Error((result.json.data && result.json.data.message) || 'Something went wrong. Please try again.');
        if (!life.active) return;
        show(root.getAttribute('data-success-message') || (result.json.data && result.json.data.message) || 'Thanks for signing up.', 'is-success'); form.reset();
      } catch (error) {
        if (life.active) show(error.name === 'AbortError' ? 'The request took too long. Please try again.' : error.message || 'Connection issue. Please try again.', 'is-error');
      } finally {
        busy = false;
        if (life.active) { button.disabled = false; button.textContent = label; form.removeAttribute('aria-busy'); }
      }
    });

  });
})();
