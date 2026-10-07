=== Foundation Elementor Plus ===
Contributors: hawks010
Tags: elementor, widgets, agency, design
Requires at least: 6.4
Tested up to: 6.9.4
Requires PHP: 7.4
Stable tag: 1.3.46
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Foundation Elementor Plus is Inkfire's modular Elementor widget suite for Foundation-powered WordPress sites.

== Description ==

Foundation Elementor Plus packages the custom Elementor widgets, shared helpers, admin settings, and integrations used across Inkfire's Foundation builds.

Key points:

* Preserves live Elementor widget slugs and saved control IDs.
* Registers widget CSS and JavaScript only for enabled widgets.
* Includes a production-oriented admin settings screen for widget toggles and integrations.
* Supports GitHub release-based plugin updates without depending on WordPress.org.
* Keeps uninstall cleanup scoped to plugin-owned options and transient caches.

== Installation ==

1. Upload the `foundation-elementor-plus` folder to `/wp-content/plugins/`.
2. Activate `Foundation Elementor Plus` in WordPress.
3. Ensure Elementor is installed and active.
4. Open `Foundation > Elementor Plus` to review enabled widgets and integrations.

== GitHub Updates ==

This plugin includes a built-in GitHub updater aimed at the repository:

`hawks010/foundation-elementor-plus`

Recommended release flow:

1. Push plugin changes to the GitHub repository.
2. Create a GitHub release whose tag matches the plugin version, for example `1.3.4`.
3. WordPress will detect the newer release in the plugin updates screen.

If the repository is private, add a token in `wp-config.php`:

`define( 'FOUNDATION_ELEMENTOR_PLUS_GITHUB_TOKEN', 'your-github-token-here' );`

The token should have at least repository read access for the target repository.

== Frequently Asked Questions ==

= Will uninstall delete Elementor content? =

No. The uninstall routine removes plugin-owned options, counters, and transient caches only. Elementor content stored in posts is left intact.

= Can I preserve plugin settings on uninstall? =

Yes. Add this to `wp-config.php` before deleting the plugin:

`define( 'FOUNDATION_ELEMENTOR_PLUS_PRESERVE_DATA', true );`

== Changelog ==

= 1.3.46 =
* Removed the Social posts navigation pill from the Inkfire In Action mega menu.
* Added green-glass social icon links for Inkfire's LinkedIn, Instagram, Facebook, and X profiles.

= 1.3.45 =
* Routed the Inkfire In Action mega-menu social link to the existing Social Media Management page so every menu pill has a live destination.

= 1.3.44 =
* Retuned the former Portfolio Mega Menu for the Inkfire In Action hub.
* Added mixed activity links for portfolio, case studies, back-to-work stories, client stories, latest posts, and social posts.
* Updated the menu feed to use recent Inkfire activity from portfolio items and blog posts.

= 1.3.43 =
* Added a Blog posts by category source mode to Awards Wall for clearer dynamic category queries.
* Kept the existing Blog posts mode backward compatible while exposing the same query, ordering, excerpt, and layout controls for category-driven walls.

= 1.3.42 =
* Added left-aligned width controls to Dark Animated Hero for the title, subtitle line, and paragraph copy.
* Scoped the new width controls so they apply across all left-aligned hero presets without affecting centered variants.

= 1.3.41 =
* Added a header visibility toggle to the Live Events widget.
* Added width controls for the header title, header intro, card title, and card summary blocks.

= 1.3.40 =
* Added a shell-frame toggle to the Live Events widget so editors can switch to a fully transparent, padding-free wrapper when needed.
* Kept shell-specific layout and style controls scoped to the framed mode.

= 1.3.39 =
* Tightened the Live Events widget layout with better wrapping, more compact spacing, and cleaner expanded content flow.
* Added richer editor controls for image sizing, spacing, typography, pill styling, and button styling.
* Switched the default event accents to green and orange glass treatments for pills and CTAs.

= 1.3.38 =
* Added the Live Events Elementor widget with stacked glass accordion cards for upcoming and past events.
* Registered the event widget's dedicated CSS and JavaScript assets in the plugin manifest.
* Synced the production event widget into the GitHub-tracked plugin build.

= 1.3.37 =
* Rebuilt the plugin admin page onto the shared Foundation admin shell while preserving the existing Settings API option keys.

= 1.3.5 =
* Added a breadcrumb position control for Dark Animated Hero blog presets so the glass breadcrumb shortcode can sit above the content, below the buttons, or be hidden.
* Tightened Dark Animated Hero preset defaults and aligned plugin header metadata with the packaged readme version.

= 1.3.4 =
* Refined Elementor control panels for key widgets, including Portfolio Menu, Dark Animated Hero, Awards Wall, Rubik's Gallery, and Linktree.
* Switched Dark Animated Hero viewport sizing away from raw `vh` defaults and normalized legacy values to safer viewport units at render time.
* Preserved existing widget slugs, saved control IDs, and frontend markup while improving styling coverage and viewport behavior.

= 1.3.3 =

* Added built-in GitHub release update support for repository-based deployments.
* Added WordPress distribution metadata in `readme.txt`.
* Replaced the uninstall routine with a multisite-aware cleanup pass.
* Included the latest control-surface and builder-stability improvements from the production audit.

= 1.3.2 =

* Synced the audited production build to Blueprint.

= 1.3.1 =

* Improved asset loading, builder stability, and admin settings handling.
