# Staging QA Checklist

1. Open `/wp-admin/admin.php?page=foundation-elementor-plus`, search for a widget in the admin app, toggle a few widgets while filtered, save, refresh, and confirm the full enabled-widget state persists correctly.
2. Disable one widget, confirm it disappears from the Elementor panel, then verify its frontend CSS/JS no longer loads on a page that does not use it.
3. Re-enable that widget and confirm it returns without breaking existing saved Elementor content.
4. In Elementor editor, test `Dark Animated Hero`, `Portfolio Mosaic`, and `Awards Wall` by changing controls and confirming preview JS still initializes after rerender.
5. Check homepage and hero-heavy templates at desktop, tablet, and mobile widths for overlap, clipping, and unexpected height changes.
6. Test `Mobile Header` open/close, nested accordions, search panel, and focus movement with keyboard only on mobile width.
7. Test `Process Carousel` with keyboard arrows and tabbing; confirm the active step and card stay in sync.
8. Test `Live Roles` and `Team Loop` filters/toggles with keyboard and touch.
9. Test `Portfolio Mosaic` filters, load-more behavior, and card interactions on both desktop and mobile.
10. Submit the `Sender Newsletter` form on staging and confirm success, validation, and failure states behave correctly.
11. Run one final pass for console errors and visible layout regressions on the live-used pages before promoting to production.
