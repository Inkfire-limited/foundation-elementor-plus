# Control Surface Audit

## Scope

This pass focused on Elementor widget settings quality, especially:

- whether each widget exposes a usable style surface
- whether responsive layout controls are available where they matter
- whether thin widgets have enough controls to be production-usable without custom CSS
- whether changes can be made without risking saved Elementor data

## What Was Already Strong

These widgets already had substantial style and responsive coverage before this pass:

- `foundation-y-hero`
- `foundation-selector-stack`
- `foundation-process-carousel`
- `foundation-bounce-rail`
- `foundation-portfolio-mosaic`
- `foundation-team-loop`
- `foundation-live-roles`
- `foundation-awards-recognition-wall`

## Clear Gaps Found

### Linktree

- No Elementor controls at all
- No layout controls
- No style tab
- No responsive spacing surface

### Mobile Header

- Content controls existed
- A few layout controls existed in the General tab
- No real style-tab surface for shell, brand, or action buttons

### Sender Newsletter

- Basic layout and typography controls existed
- Missing practical styling controls for:
  - surface background / border / shadow
  - input fields
  - option labels / checkbox accent
  - CTA button colors, radius, sizing, and shadow

## Fixes Applied

### `class-inkfire-linktree-widget.php`

Added:

- content-source note so the widget no longer appears blank/opaque in the panel
- responsive outer padding
- responsive content width
- responsive main panel gap
- responsive main panel padding
- responsive panel radius
- text / muted text / accent text color controls
- main panel background control
- secondary panel background control
- primary link background control

Also changed render output to wrap the shortcode in a Foundation widget shell so Elementor controls can target it safely.

### `class-mobile-header-widget.php`

Added a dedicated style surface:

- Shell Style
  - top bar padding
  - top bar gap
  - top bar radius
  - top bar text color
  - top bar background color
  - top bar border color
- Brand Style
  - brand typography
  - brand color
  - brand gap
- Action Buttons
  - button size
  - button radius
  - button icon color
  - button background color
  - button border color
  - active icon color
  - active background color

### `class-sender-newsletter-widget.php`

Expanded styling coverage significantly:

- Surface
  - background
  - border
  - box shadow
- Copy
  - title color
  - body / privacy / message / option text color
- Fields
  - field height
  - field padding
  - field radius
  - field background
  - field text color
  - placeholder color
  - border color
  - focus background
  - focus border color
  - option label color
  - checkbox accent color
- Button
  - typography
  - height
  - padding
  - radius
  - text color
  - background
  - hover text color
  - hover background
  - border
  - box shadow

## Safety Notes

- No existing widget slugs were changed.
- No saved control IDs were renamed or removed.
- Changes are additive only.
- Existing live widget instances should continue to load with their current settings untouched.

## Validation

- `php -l includes/widgets/class-inkfire-linktree-widget.php`
- `php -l includes/widgets/class-mobile-header-widget.php`
- `php -l includes/widgets/class-sender-newsletter-widget.php`

All passed.

## Remaining Manual QA

- Confirm new controls appear cleanly inside Elementor for:
  - Linktree
  - Mobile Header
  - Newsletter Form
- Confirm Linktree style overrides feel strong enough against the shortcode’s embedded styles
- Confirm Mobile Header active-state color controls are visually strong enough in the editor and frontend
- Confirm Newsletter field/button controls style the live form and the Sender-not-configured notice context acceptably
