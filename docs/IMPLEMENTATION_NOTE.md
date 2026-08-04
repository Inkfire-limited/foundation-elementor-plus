# Sender Newsletter Redesign

## Files Changed
- `includes/widgets/class-sender-newsletter-widget.php`
- `assets/css/sender-newsletter.css`
- `foundation-elementor-plus.php`

## JS Compatibility Preserved
- Kept the existing wrapper/root hooks:
  - `foundation-sender-newsletter-wrap`
  - `foundation-sender-newsletter`
  - `foundation-sender-newsletter__field`
  - `foundation-sender-newsletter__action`
  - `foundation-sender-newsletter__interests`
  - `foundation-sender-newsletter__message`
  - `foundation-sender-newsletter__honeypot`
- Preserved the existing root data attributes used by JS:
  - `data-foundation-sender-newsletter`
  - `data-ajax-url`
  - `data-nonce`
  - `data-group-id`
  - `data-button-text`
  - `data-success-message`
- Preserved field names and IDs:
  - `first_name`
  - `email`
  - `company`
  - `interests[]`
- Preserved `aria-live="polite"` on the message area.
- Preserved the honeypot field and visually hidden labels.

## JS Adjustments Made
- None. The existing `assets/js/sender-newsletter.js` selectors already tolerate the new inner layout structure.

## Accessibility Notes
- Maintained screen-reader-only labels for all text inputs.
- Kept the live message region.
- Kept the form keyboard-operable with visible focus states on inputs, chips, and the submit button.
- Kept touch target sizes at or above WCAG 2.2 AA-friendly sizing in the default stylesheet.
- Added `prefers-reduced-motion: reduce` handling for interactive transitions.
- Added `forced-colors: active` support for higher-contrast system modes.

## Verification
- `php -l includes/widgets/class-sender-newsletter-widget.php`
- `php -l foundation-elementor-plus.php`
- `node --check assets/js/sender-newsletter.js`

## Still Needs Visual QA
- Final desktop/tablet/mobile visual confirmation in Elementor editor and frontend over the real gradient background.
- Real submission smoke test against the live AJAX endpoint after deployment.
- Check that any widget-level style controls still feel sensible against the new glass layout, especially background and button color overrides.
