# Foundation Elementor Plus 1.3.0

## What changed
- Rebuilt the plugin settings screen as a React-powered admin app using WordPress' bundled `wp.element` package.
- Added a SaaS-style visual refresh with a custom admin stylesheet.
- Kept the underlying WordPress settings API and `options.php` save flow intact for compatibility and safety.
- Preserved widget toggles, defer settings, legacy upload normalization, category settings, and feed integrations.
- Added a JavaScript-free fallback settings form inside `<noscript>`.
- Added a hidden widget submission sentinel so disabling every widget at once saves correctly.

## Why plain React instead of TypeScript tonight
TypeScript is the better long-term choice for a growing admin app, but plain React keeps the shipping build lighter and avoids adding a heavier compile chain inside the plugin right now.

## Files added
- `assets/admin/foundation-admin.css`
- `assets/admin/foundation-admin-app.js`

## Files updated
- `foundation-elementor-plus.php`
- `includes/class-plugin.php`

## Safe assumptions
- Existing widget slugs and option keys are unchanged.
- The admin reskin does not alter frontend widget rendering.
- The save pipeline still runs through the registered WordPress option sanitizer.
