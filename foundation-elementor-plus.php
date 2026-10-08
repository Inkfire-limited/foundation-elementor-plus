<?php
/**
 * Plugin Name: Foundation Elementor Plus
 * Plugin URI: https://github.com/Inkfire-limited/foundation-elementor-plus
 * Description: Modular custom Elementor widgets for Foundation sites.
 * Version: 1.3.47
 * Author: Sonny x Inkfire
 * Text Domain: foundation-elementor-plus
 * Update URI: https://github.com/Inkfire-limited/foundation-elementor-plus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FOUNDATION_ELEMENTOR_PLUS_VERSION', '1.3.47' );
define( 'FOUNDATION_ELEMENTOR_PLUS_FILE', __FILE__ );
define( 'FOUNDATION_ELEMENTOR_PLUS_PATH', plugin_dir_path( __FILE__ ) );
define( 'FOUNDATION_ELEMENTOR_PLUS_URL', plugin_dir_url( __FILE__ ) );

require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-header-banner.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-hero-autoplay-toggle.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-github-updater.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-plugin.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-frontend-reliability.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-post-carousel-navigation.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-sender-newsletter.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/class-team-inline-images.php';
require_once FOUNDATION_ELEMENTOR_PLUS_PATH . 'includes/shortcodes/inkfire-linktree-shortcode.php';

\FoundationElementorPlus\Github_Updater::instance();
\FoundationElementorPlus\Plugin::instance();
( new \FoundationElementorPlus\Frontend_Reliability() )->hooks();
( new \FoundationElementorPlus\Post_Carousel_Navigation() )->hooks();
( new \FoundationElementorPlus\Header_Banner() )->hooks();
( new \FoundationElementorPlus\Hero_Autoplay_Toggle() )->hooks();
( new \FoundationElementorPlus\Sender_Newsletter() )->hooks();
( new \FoundationElementorPlus\Team_Inline_Images() )->hooks();
