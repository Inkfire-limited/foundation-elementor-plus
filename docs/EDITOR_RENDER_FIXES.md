# Editor Render Fixes

## Status For This Local QA Pass

- No additional plugin code changes were applied in this pass.
- The previously shipped editor-stability fixes for `Selector Stack`, `Y Hero`, and `Process Carousel` remain in place.
- Current local evidence did not prove a new builder-only regression that justified another production code edit.

## Builder-Specific Findings Confirmed

### Frontend vs editor preview

- The imported Blueprint homepage widgets render the same Foundation addon asset payload on:
  - `http://foundation.local/`
  - `http://foundation.local/?elementor-preview=14&ver=1`
- For the 13 Foundation widgets that rendered locally, the outer widget wrapper HTML matched exactly between frontend and editor preview.

### Portfolio Mosaic

- `foundation-portfolio-mosaic` exists in the imported Blueprint Elementor data on the local page.
- It does not render locally because the Local install does not have Blueprint's portfolio content source and related context.
- This is an environment limitation for QA, not proof of a widget registration or builder-preview bug.

### Newsletter Form

- `foundation-sender-newsletter` renders a very small output state locally.
- The frontend and preview wrapper HTML match, so there is no proven preview mismatch here.
- Whether that should be a live form or a setup/config state depends on the target environment's Sender integration.

## Why No New Code Was Added

- No widget slug, control path, markup structure, or asset handle needed changing based on the evidence gathered here.
- The strongest current proof points are parity checks, not breakage signals:
  - same rendered widget set in frontend and preview
  - same Foundation addon asset set in frontend and preview
  - same wrapper HTML for all locally rendered priority widgets
- Without real screenshot capture or a proven DOM/runtime divergence, additional code edits would have been guesswork.

## Previously Applied Editor-Safe Fixes Still Relevant

### Selector Stack

- Editor mode avoids the fragile pinned desktop behavior.
- Resize-driven recalculation support remains in place.

### Y Hero

- Deferred measurement and `ResizeObserver` support remain in place.

### Process Carousel

- Deferred transform recalculation and `ResizeObserver` support remain in place.

## Remaining Risky Widgets

- `Dark Animated Hero`
  Visual canvas behavior still needs human verification in the Elementor canvas after control edits.

- `Mobile Header`
  Off-canvas, focus order, and touch behavior still need real browser interaction testing.

- `Portfolio Mosaic`
  Needs a local or staging environment with real `ink_portfolio` content before it can be signed off visually.

## Environment Blockers

- Browser screenshot and console capture were blocked by a macOS Chrome crash in `HIServices` before render.
- The Local site is now using real Blueprint homepage Elementor data, but it is still not a full Blueprint content clone.
- The Local site does not currently include the content layer needed to exercise `Portfolio Mosaic` query mode faithfully.
