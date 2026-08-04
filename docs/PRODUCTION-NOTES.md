# Foundation Elementor Plus 1.2.0

## Production-ready pass

This build focuses on safer production use without changing widget slugs or saved Elementor control keys.

### What changed
- Version bumped to 1.2.0
- Shared Foundation core CSS now loads only with active Foundation widgets instead of being enqueued site-wide
- Frontend widget scripts can be deferred from the plugin settings page
- Added per-widget enable/disable switches in the Foundation admin screen
- Widget registration now respects enabled/disabled state, so unused widgets stay out of Elementor and do not load assets
- Added manual Elementor cache clear action in the plugin admin screen
- Kept widget names, slugs, and main frontend class names stable to avoid breaking existing content
- Uninstall now removes the stored plugin version option too

### Notes
- Disabling a widget hides it from Elementor and prevents it from rendering until re-enabled
- Existing pages should be regression checked after changing enabled widget states
- Dark Animated Hero still uses per-instance script bootstrapping for its fluid config, but now also pulls the shared core stylesheet only when used

### Recommended staging checks
- Home hero in Elementor editor and on the public page
- Mobile header open/close states
- Portfolio grid and portfolio menu on desktop and mobile
- Selector Stack and Process Carousel keyboard/touch interactions
- Team Grid and Live Roles filters
- Newsletter Form submission states
