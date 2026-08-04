# Visual + Builder Audit

## Pages / Widgets Tested

- Live Blueprint source page used for payload extraction: homepage (`post_id=30`, title `Home`)
- Local QA page: `http://foundation.local/` using the imported Blueprint homepage `_elementor_data`
- Local editor route: `http://foundation.local/wp-admin/post.php?post=14&action=elementor`
- Local editor preview route: `http://foundation.local/?elementor-preview=14&ver=1`

## Evidence Capture

- Local plugin copy matches the audited Blueprint plugin exactly.
- Real Blueprint homepage Elementor data was imported into the local sandbox page.
- Frontend and editor preview were compared by rendered widget wrapper HTML, not just by asset handles.
- Browser screenshots and console capture were attempted but blocked by a local macOS Chrome crash in `HIServices` before render.

## Root-Cause List Before Edits

1. The original local sandbox page was not a valid test surface.
   Widgets had been inserted with blank settings, so several rendered nothing and only their assets loaded.

2. After importing the real Blueprint homepage data, frontend and editor preview matched for the widgets that actually rendered.
   This materially lowers the risk of a current builder-preview markup mismatch on the homepage widgets.

3. `Portfolio Mosaic` is the main local false negative.
   The widget is present in Elementor data, but it defaults to query-driven portfolio content. The Local site does not have the Blueprint `ink_portfolio` content source, so the widget does not render a frontend wrapper locally.

4. Browser automation is still environment-limited, not plugin-limited.
   The macOS Chrome crash happens during app registration, before page render, so this pass cannot claim screenshot-based visual signoff.

## Widget Audit Matrix

| Widget | Status | Frontend Status | Builder Status | Root Cause | Exact Files Changed | Risk | Evidence Summary |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Dark Animated Hero | Pass / still needs manual QA | Rendered locally from Blueprint data | Editor preview HTML matched frontend exactly | No proven frontend vs editor-preview divergence in this pass | None | Low | Widget wrapper hash matched between `/` and `?elementor-preview=14&ver=1` |
| Y Hero | Pass / still needs manual QA | Rendered locally from Blueprint data | Editor preview HTML matched frontend exactly | No new proven mismatch after prior timing fix | None | Low | Wrapper HTML matched exactly across frontend and preview |
| Bounce Rail | Pass / still needs manual QA | Rendered locally from Blueprint data | Editor preview HTML matched frontend exactly | No proven builder-only divergence | None | Low | Wrapper HTML matched exactly across frontend and preview |
| Portfolio Mosaic / Grid | Issue found / still needs manual QA | Not rendered locally despite imported widget data | Cannot verify visually in Local preview | Local environment lacks Blueprint portfolio content/CPT context; widget settings default to query mode | None | Medium | Widget exists in imported `_elementor_data`, but no rendered wrapper appears in local DOM |
| Mobile Header | Pass / still needs manual QA | Rendered locally from Blueprint data | Editor preview HTML matched frontend exactly | No proven preview mismatch; still needs true interaction QA | None | Medium-low | Large wrapper HTML matched exactly; off-canvas interaction still needs manual touch/keyboard check |
| Selector Stack | Pass / still needs manual QA | Rendered locally from Blueprint data | Editor preview HTML matched frontend exactly | No current preview markup mismatch detected after prior editor-safe fix | None | Low | Wrapper HTML matched exactly across frontend and preview |
| Process Carousel | Pass / still needs manual QA | Rendered locally from Blueprint data | Editor preview HTML matched frontend exactly | No current preview markup mismatch detected after prior timing fix | None | Low | Wrapper HTML matched exactly across frontend and preview |
| Awards Wall | Pass / still needs manual QA | Rendered locally from Blueprint data | Editor preview HTML matched frontend exactly | No proven mismatch in current pass | None | Low | Wrapper HTML matched exactly across frontend and preview |
| Linktree | Pass / still needs manual QA | Rendered locally from Blueprint data | Editor preview HTML matched frontend exactly | No proven mismatch in current pass | None | Low | Wrapper HTML matched exactly across frontend and preview |
| Team Grid | Pass / still needs manual QA | Rendered locally from Blueprint data | Editor preview HTML matched frontend exactly | No proven mismatch in current pass | None | Low | Wrapper HTML matched exactly across frontend and preview |
| Newsletter Form | Issue found / still needs manual QA | Rendered as a minimal setup/config state locally | Editor preview HTML matched frontend exactly | Output depends on Sender configuration state, not a proven builder rendering bug | None | Low | Very small rendered wrapper matches in both contexts; real form state still needs environment config |

## Render Comparison Summary

- Rendered Foundation widgets in local frontend DOM: `13`
- Rendered Foundation widgets in local editor preview DOM: `13`
- Wrapper HTML hash match result for rendered widgets: `13/13 matched`
- Foundation addon asset diff between frontend and preview: `no differences`

Rendered and matched:

- `foundation-dark-animated-hero.default`
- `foundation-mobile-header.default`
- `foundation-selector-stack.default`
- `foundation-y-hero.default`
- `foundation-bounce-rail.default`
- `foundation-process-carousel.default`
- `foundation-portfolio-mega-menu.default`
- `foundation-awards-recognition-wall.default`
- `foundation-live-roles.default`
- `foundation-team-loop.default`
- `foundation-rubiks-gallery.default`
- `foundation-sender-newsletter.default`
- `foundation-inkfire-linktree.default`

Present in imported data but not rendered locally:

- `foundation-portfolio-mosaic`

## Screenshots / Evidence Summary

- Screenshots taken: none
- Browser console capture: none
- Blocking reason: local Chrome aborts in macOS `HIServices` before rendering
- Evidence collected instead:
  - local frontend DOM parsing
  - local editor preview DOM parsing
  - widget wrapper HTML hash comparison
  - frontend vs preview Foundation asset comparison
  - live Blueprint homepage Elementor data import
  - source inspection for query-dependent widgets

## Remaining Manual QA

- Logged-in Elementor visual pass on the local page at desktop, tablet, and mobile breakpoints
- True interaction test for `Mobile Header`, `Selector Stack`, `Bounce Rail`, and `Process Carousel`
- Human visual confirmation for `Dark Animated Hero` canvas behavior after control changes
- Local or staging environment with real `ink_portfolio` content so `Portfolio Mosaic` can be verified properly
- Sender integration configured in the target environment if the newsletter should display a live form instead of a setup state
