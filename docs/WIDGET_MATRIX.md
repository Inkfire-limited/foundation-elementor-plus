# Foundation Elementor Plus Widget Matrix

| Widget | Status | Main Improvements Made | A11y Notes | Performance Notes | Unresolved Concerns |
| --- | --- | --- | --- | --- | --- |
| Dark Animated Hero | Improved | Editor-safe JS reinit for Elementor rerenders | Section already labels itself via heading; still a custom renderer path | Fluid effect now reboots more reliably only where needed | Heavy renderer and animation path still need staging QA |
| Mobile Header | Reviewed | No automatic markup changes | Good ARIA coverage on toggles/menus; keep keyboard QA | Loads per widget handle | Mobile interaction QA still required across breakpoints |
| Selector Stack | Reviewed | No automatic changes | Shared focus/reduced-motion shell applies | Already widget-scoped | Sticky/scroll behavior still needs live QA |
| Y Hero | Reviewed | No automatic changes | Shared shell a11y applies | Already editor-hooked and widget-scoped | Hero layout remains high-risk for visual regressions |
| Bounce Rail | Reviewed | No automatic changes | Shared shell a11y applies | Already widget-scoped | Large control surface and feed logic still need manual QA |
| Process Carousel | Improved | Added tab/panel ID wiring | Better screen reader relationship between step tabs and panels | Existing widget-scoped JS retained | Touch/keyboard QA still needed |
| Portfolio Mosaic | Improved | Added Elementor-safe JS reinit | Filters already expose `aria-selected`; shared shell applies | Widget boot now survives editor rerenders better | Heavy DOM/control surface still needs visual QA |
| Portfolio Mega Menu | Reviewed | No automatic changes | Shared shell a11y applies | Popup script remains conditional in render | Popup-enabled paths still need QA |
| Awards Wall | Improved | Added Elementor-safe JS reinit | Shared shell a11y applies | Hover effect now rebinds safely in editor | Hover/touch visual QA still needed |
| Live Roles | Reviewed | No automatic changes | Strong toggle/panel ARIA already present | Widget-scoped asset loading | Filter and expansion QA still needed |
| Team Loop | Reviewed | No automatic changes | Shared shell a11y applies | Widget-scoped asset loading | Filtering/grouping still needs live QA |
| Inkfire Linktree | Reviewed | No automatic changes | Shortcode output already includes labels in key places | Shortcode remains self-contained | Inline CSS/markup weight is still high; too risky to rewrite blindly |
| Rubiks Gallery | Reviewed | No automatic changes | Shared shell a11y applies | Lottie only needed with widget handle | Motion/media QA still required |
| Sender Newsletter | Reviewed | No automatic changes | Form labeling and live-region pattern present | Widget-scoped JS/CSS; AJAX protected by nonce | Live submission QA needed against real Sender config |
