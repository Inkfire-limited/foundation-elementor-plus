# Foundation Elementor Plus Production Audit

## Scope
- Audited the Blueprint build at `1.3.0`.
- Reviewed plugin bootstrap, widget registry, widget assets, admin settings flow, AJAX/security touchpoints, uninstall behavior, and the highest-risk widget JS/render paths.
- Preserved widget slugs, control IDs, and live-facing CSS hooks.

## Highest-risk files
- `includes/class-plugin.php`
- `includes/class-widget-registry.php`
- `includes/class-dark-animated-hero-renderer.php`
- `includes/widgets/class-dark-animated-hero-widget.php`
- `includes/widgets/class-y-hero-widget.php`
- `includes/widgets/class-bounce-rail-widget.php`
- `includes/widgets/class-portfolio-mosaic-widget.php`
- `assets/admin/foundation-admin-app.js`
- `assets/js/dark-animated-hero.js`

## Major issues found
- Admin reskin regression: saving widget toggles while widget search was active could silently disable unmatched widgets because only filtered checkboxes were submitted.
- Elementor editor boot gap: `dark-animated-hero`, `portfolio-mosaic`, and `awards-wall` scripts were not reliably reinitializing when Elementor re-rendered widgets in preview/editor contexts.
- Performance/config scope gap: asset registration was still broader than necessary for disabled widgets.
- Accessibility gap: `Process Carousel` used tab-like UI without explicit tab-to-panel ID wiring.
- Cleanup gap: uninstall only removed the main settings/version options, not the rest of the plugin-owned option data.

## What was fixed
- Added hidden enabled-widget inputs to the React admin app so saved widget state is complete even when the widget grid is filtered.
- Improved admin app tab semantics with `tablist`/`tab`/`tabpanel` relationships.
- Limited frontend asset registration to enabled widget handles only.
- Added Elementor lifecycle-safe reinit paths to:
- `assets/js/dark-animated-hero.js`
- `assets/js/portfolio-mosaic.js`
- `assets/js/awards-wall.js`
- Added `Process Carousel` tab/panel `id` / `aria-controls` / `aria-labelledby` wiring.
- Expanded uninstall cleanup to remove plugin-owned options for:
- hero presets
- header banner settings
- team inline images
- quicklinks stats

## Remaining risks
- `Dark Animated Hero`, `Bounce Rail`, `Y Hero`, and `Portfolio Mosaic` remain large, high-complexity widgets. They are safer now, but they still need real staging visual QA before production.
- `Inkfire Linktree` remains shortcode-driven with heavy inline CSS/markup. I did not refactor that automatically because the layout risk is too high without page-level verification.
- `Dark Animated Hero` still uses a custom renderer/bootstrap path rather than the shared root-attribute helper used by most other widgets. That is acceptable for now, but it is still a long-term maintenance hotspot.
- I did not change live render markup in the heavier widgets beyond additive accessibility wiring, so any DOM-weight reductions that require wrapper removal are intentionally deferred.

## Security and standards notes
- Capability checks, nonce use, and sanitization looked solid in the plugin settings flow, header banner, team inline image admin screen, and Sender AJAX endpoint.
- No REST routes were present in this build.
- PHP lint passed after the changes.

## Manual QA still needed
- Authenticated admin/browser QA on the Blueprint settings screen.
- Elementor editor QA for hero widgets, especially control changes that trigger preview rerenders.
- Frontend regression QA on live page templates that already use these widgets.
