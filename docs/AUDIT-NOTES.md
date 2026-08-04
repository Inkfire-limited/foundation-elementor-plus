# Foundation Elementor Plus 1.1.0 audit notes

## What changed
- Bumped plugin version to `1.1.0`.
- Added a shared base widget class for safer future maintenance.
- Added a shared accessibility control section to interactive widgets:
  - Screen Reader Label
  - Reduce Motion In This Widget
- Added shared frontend accessibility CSS:
  - consistent `:focus-visible` ring
  - reduced motion helpers
  - hidden content utility
  - 44x44 minimum target size for primary controls
- Added a lightweight upgrade routine that stores plugin version and clears Elementor asset cache on update.
- Added a shared core frontend stylesheet registration and enqueue path.
- Simplified widget display titles in the Elementor panel.
- Added shared widget shell attributes for cleaner frontend hooks and safer a11y extension.
- Removed unused duplicate Rubik's Gallery assets in the plugin root.
- Removed 47 backup files from the production package.

## Preservation choices
- Widget `get_name()` values were kept intact to avoid breaking existing Elementor data.
- Existing widget CSS class names were kept intact to avoid nuking live layouts.
- Existing render structures were only touched at root wrapper level.

## Static checks completed
- PHP lint passed on all active PHP files.
- Asset registry updated for the new shared core stylesheet.

## Recommended next pass
- Live visual QA on staging across the homepage, portfolio pages, careers page, and mobile header.
- Targeted panel cleanup for the heaviest widgets:
  - Portfolio Grid
  - Social Wall
  - Y Hero
  - Mobile Header
- Optional second pass to split huge widgets into per-section traits or dedicated config classes.
