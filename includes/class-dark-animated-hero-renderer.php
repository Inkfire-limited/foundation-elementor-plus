<?php

namespace FoundationElementorPlus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Dark_Animated_Hero_Renderer {
	const OPTION_KEY = 'foundation_dark_animated_hero_settings';

	public static function allow_hero_inline_style_properties( $styles ) {
		$allowed = array(
			'background-clip',
			'-webkit-background-clip',
			'-webkit-text-fill-color',
			'text-fill-color',
		);

		foreach ( $allowed as $property ) {
			if ( ! in_array( $property, $styles, true ) ) {
				$styles[] = $property;
			}
		}

		return $styles;
	}

	private static function kses_hero_html( $html ) {
		add_filter( 'safe_style_css', array( __CLASS__, 'allow_hero_inline_style_properties' ) );
		$filtered = wp_kses_post( $html );
		remove_filter( 'safe_style_css', array( __CLASS__, 'allow_hero_inline_style_properties' ) );

		return $filtered;
	}

	private static function get_palette_presets() {
		return array(
			'inkfire_gradient' => '#08352F,#0E6055,#138170,#07A079,#32B190,#CE3D27,#D85814,#E27200,#EC853D,#F18E5C',
			'inkfire_green'    => '#08352F,#0E6055,#138170,#07A079,#32B190',
			'inkfire_orange'   => '#CE3D27,#D85814,#E27200,#EC853D,#F18E5C',
		);
	}

	public static function get_palette_preset_options() {
		return array(
			''                 => 'Use preset',
			'inkfire_gradient' => 'Inkfire Gradient',
			'inkfire_green'    => 'Inkfire Green',
			'inkfire_orange'   => 'Inkfire Orange',
		);
	}

	private static function resolve_palette_string( $palette = '', $palette_key = '', $preset_palette_key = 'inkfire_gradient' ) {
		if ( '' !== trim( (string) $palette ) ) {
			return (string) $palette;
		}

		$palettes = self::get_palette_presets();
		$key      = '' !== trim( (string) $palette_key ) ? (string) $palette_key : (string) $preset_palette_key;

		if ( isset( $palettes[ $key ] ) ) {
			return $palettes[ $key ];
		}

		return $palettes['inkfire_gradient'];
	}

	public static function get_default_settings() {
		return array(
			'palette'        => self::get_palette_presets()['inkfire_gradient'],
			'team_images'    => implode(
				"\n",
				array(
					self::build_upload_url( '/2025/11/IMG_0054.jpeg' ),
					self::build_upload_url( '/2025/11/IMG_0053.jpeg' ),
					self::build_upload_url( '/2025/11/IMG_0049.png' ),
					self::build_upload_url( '/2025/11/IMG_0050.png' ),
				)
			),
			'team_link_text' => 'Meet the team',
			'team_link_url'  => '/about-us/meet-the-team/',
			'trust_label'    => 'Trusted Inkfire partners',
			'trust_logos'    => implode(
				"\n",
				array(
					self::build_upload_url( '/2026/03/logo-03.png' ),
					self::build_upload_url( '/2026/03/logo-04.png' ),
					self::build_upload_url( '/2026/03/logo-01.png' ),
					self::build_upload_url( '/2026/03/logo-02.png' ),
				)
			),
			'presets'        => array(
				'home'               => array(
					'label'            => 'Home',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'full',
					'align'            => 'center',
					'content_vertical_align' => 'center',
					'min_height'       => '100vh',
					'eyebrow'          => '🏆 Multi-award winning. Disabled-led team.',
					'title'            => '<span class="foundation-inkfire-brand"><img src="' . self::build_upload_url( '/2025/11/IMG_1089.png' ) . '" alt="Inkfire logo" style="height:1.5em; width:auto; display:inline-block; vertical-align:middle; margin-right:-4px; transform:translateY(-3px);"><span class="foundation-inkfire-brand-name">Inkfire</span></span> Building <br><span class="foundation-inkfire-gradient-wrap"><span class="foundation-inkfire-gradient-text">inclusive systems</span></span> that just work.',
					'subhead'          => 'We design, build and co-create inclusive digital systems, and support them long-term, when you need things done properly, at scale. Trusted by charities, public sector teams and growing organisations across the UK.',
					'primary_text'     => 'View our portfolio',
					'primary_url'      => '/portfolio/',
					'secondary_text'   => '',
					'secondary_url'    => '',
					'features'         => '',
					'feature_position' => 'after_subhead',
					'show_team'        => 1,
					'show_trust'       => 1,
				),
				'portfolio_archive'  => array(
					'label'            => 'Portfolio Archive',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'height_strategy'  => 'viewport',
					'min_height'       => '72vh',
					'eyebrow'          => '<span class="foundation-inkfire-pill foundation-inkfire-pill--crumb">{current_breadcrumb}</span><span class="foundation-inkfire-pill foundation-inkfire-pill--brand"><img src="' . self::build_upload_url( '/2025/11/IMG_1089.png' ) . '" alt="Inkfire logo"><span>Award winning</span></span><span class="foundation-inkfire-pill foundation-inkfire-pill--rating" aria-label="5 star rated"><span class="foundation-inkfire-rating-number">5</span><span class="foundation-inkfire-stars" aria-hidden="true">★</span><span class="foundation-inkfire-rating-label">rated</span></span>',
					'kicker'           => '{current_title_plain}',
					'title'            => 'Seen something you like? Partner with our disabled-led team ready to support your next project.',
					'subhead'          => 'Whether you need help getting started, a bit of support applying for Access To Work, or just a friendly chat, we’d love to hear from you.',
					'primary_text'     => 'Start a project',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Our services',
					'secondary_url'    => '/services/',
					'features'         => "Accessible by design\nBuilt for performance\nMade to scale",
					'feature_position' => 'after_subhead',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
				'portfolio_post'     => array(
					'label'            => 'Portfolio Post',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'height_strategy'  => 'viewport',
					'min_height'       => '76vh',
					'eyebrow'          => '<span class="foundation-inkfire-pill foundation-inkfire-pill--crumb">{current_breadcrumb}</span><span class="foundation-inkfire-pill foundation-inkfire-pill--brand"><img src="' . self::build_upload_url( '/2025/11/IMG_1089.png' ) . '" alt="Inkfire logo"><span>Award winning</span></span><span class="foundation-inkfire-pill foundation-inkfire-pill--rating" aria-label="5 star rated"><span class="foundation-inkfire-rating-number">5</span><span class="foundation-inkfire-stars" aria-hidden="true">★</span><span class="foundation-inkfire-rating-label">rated</span></span>',
					'kicker'           => '{current_title_plain}',
					'title'            => '{current_title}',
					'subhead'          => '{current_excerpt}',
					'primary_text'     => 'Start a project',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'View all work',
					'secondary_url'    => '/portfolio/',
					'features'         => '',
					'feature_position' => 'hidden',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
				'blog_archive'      => array(
					'label'            => 'Blog Archive / Search',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'height_strategy'  => 'viewport',
					'min_height'       => '72vh',
					'eyebrow'          => '<span class="foundation-inkfire-pill foundation-inkfire-pill--brand"><img src="' . self::build_upload_url( '/2025/11/IMG_1089.png' ) . '" alt="Inkfire logo"><span>Resource Hub</span></span><span class="foundation-inkfire-pill foundation-inkfire-pill--rating">Accessible insights</span>',
					'kicker'           => '{current_title_plain}',
					'title'            => 'Ideas, guidance, and behind-the-work updates from the Inkfire team.',
					'subhead'          => '{current_archive_description}',
					'primary_text'     => 'Start a project',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Explore services',
					'secondary_url'    => '/services/',
					'features'         => '',
					'feature_position' => 'hidden',
					'breadcrumb_position' => 'top',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
				'blog_post'         => array(
					'label'            => 'Blog Post',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'height_strategy'  => 'viewport',
					'min_height'       => '74vh',
					'eyebrow'          => '',
					'kicker'           => '{current_breadcrumb_links}',
					'title'            => '{current_title}',
					'subhead'          => '{current_excerpt}',
					'primary_text'     => 'Start a project',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Browse all articles',
					'secondary_url'    => '/blog/',
					'features'         => '',
					'feature_position' => 'hidden',
					'breadcrumb_position' => 'top',
					'show_team'        => 0,
					'show_trust'       => 0,
					'blog_post_meta_visibility' => 'show',
					'blog_post_meta_label' => '{current_category}',
					'blog_post_meta_pill_primary' => '{current_read_time}',
					'blog_post_meta_pill_secondary' => '{current_word_count}',
					'blog_post_author_visibility' => 'show',
					'blog_post_author_prefix' => 'Written by',
					'blog_post_author_name' => '{current_author_name}',
					'blog_post_author_avatar_url' => '{current_author_avatar_url}',
					'blog_post_author_link_url' => '{current_author_url}',
				),
				'seo'                => array(
					'label'            => 'SEO Service',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'height_strategy'  => 'viewport',
					'min_height'       => '72vh',
					'eyebrow'          => 'SEO services',
					'title'            => 'SEO systems that compound over time',
					'subhead'          => 'Technical SEO, content strategy, and accessible search experiences designed to bring the right people to you and keep momentum building.',
					'primary_text'     => 'Talk to us',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Creative marketing',
					'secondary_url'    => '/services/creative-marketing/',
					'features'         => "Technical SEO audits\nContent strategy\nInclusive search journeys",
					'feature_position' => 'after_subhead',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
				'it_support'         => array(
					'label'            => 'IT Support',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'height_strategy'  => 'viewport',
					'min_height'       => '72vh',
					'eyebrow'          => 'IT support',
					'title'            => 'Reliable tech support without the usual friction',
					'subhead'          => 'Managed IT, Microsoft 365, cybersecurity, and day-to-day support for organisations that need things handled properly.',
					'primary_text'     => 'Speak to our team',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Tech and support',
					'secondary_url'    => '/services/tech-support/',
					'features'         => "Managed support\nMicrosoft 365\nSecurity and device care",
					'feature_position' => 'after_subhead',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
				'web_development'    => array(
					'label'            => 'Web Development',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'height_strategy'  => 'viewport',
					'min_height'       => '72vh',
					'eyebrow'          => 'Web development',
					'title'            => 'Websites that are inclusive, fast, and built to grow',
					'subhead'          => 'Custom web design and development for ambitious organisations that need performance, accessibility, and a strong technical foundation.',
					'primary_text'     => 'Plan your website',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Web development',
					'secondary_url'    => '/services/web-accessibility/web-development/',
					'features'         => "Custom builds\nAccessibility first\nLong-term support",
					'feature_position' => 'after_subhead',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
				'branding_marketing' => array(
					'label'            => 'Branding / Marketing',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'height_strategy'  => 'viewport',
					'min_height'       => '72vh',
					'eyebrow'          => 'Branding and marketing',
					'title'            => 'Brands and campaigns people remember for the right reasons',
					'subhead'          => 'Strategy, storytelling, and inclusive creative marketing that helps people find you, trust you, and choose you.',
					'primary_text'     => 'Start a campaign',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Creative marketing',
					'secondary_url'    => '/services/creative-marketing/',
					'features'         => "Brand strategy\nContent and campaigns\nAccessible messaging",
					'feature_position' => 'after_subhead',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
				'other_service'      => array(
					'label'            => 'Other Service',
					'palette_key'      => 'inkfire_gradient',
					'size'             => 'compact',
					'align'            => 'left',
					'content_vertical_align' => 'center',
					'height_strategy'  => 'viewport',
					'min_height'       => '72vh',
					'eyebrow'          => 'Specialist support',
					'title'            => 'Complex projects, shaped around what you actually need',
					'subhead'          => 'For work that spans strategy, delivery, accessibility, and support, we shape a service around the outcome you need.',
					'primary_text'     => 'Tell us what you need',
					'primary_url'      => '/contact-us/',
					'secondary_text'   => 'Explore services',
					'secondary_url'    => '/services/',
					'features'         => "Accessibility consultancy\nBespoke builds\nFlexible support models",
					'feature_position' => 'after_subhead',
					'show_team'        => 0,
					'show_trust'       => 0,
				),
			),
		);
	}

	public static function get_settings() {
		$defaults = self::get_default_settings();
		$stored   = get_option( self::OPTION_KEY, array() );
		$stored   = is_array( $stored ) ? $stored : array();
		$settings = wp_parse_args( $stored, $defaults );

		$settings['presets'] = isset( $stored['presets'] ) && is_array( $stored['presets'] ) ? $stored['presets'] : array();

		foreach ( $defaults['presets'] as $preset_key => $preset_defaults ) {
			$settings['presets'][ $preset_key ] = wp_parse_args(
				isset( $settings['presets'][ $preset_key ] ) && is_array( $settings['presets'][ $preset_key ] ) ? $settings['presets'][ $preset_key ] : array(),
				$preset_defaults
			);
		}

		return $settings;
	}

	public static function get_preset_options() {
		$options  = array();
		$settings = self::get_settings();

		foreach ( $settings['presets'] as $preset_key => $preset ) {
			$options[ $preset_key ] = isset( $preset['label'] ) ? $preset['label'] : $preset_key;
		}

		return $options;
	}

	public static function enqueue_assets( $palette = '', $needs_icon_fonts = false ) {
		wp_enqueue_style( 'foundation-elementor-plus-dark-animated-hero' );
		wp_enqueue_script( 'foundation-elementor-plus-fluid-core' );
		wp_enqueue_script( 'foundation-elementor-plus-dark-animated-hero' );

		if ( $needs_icon_fonts ) {
			self::enqueue_icon_fonts();
		}

		$config = array(
			'palette'             => self::get_palette_values( $palette ),
			'ditheringTextureUrl' => FOUNDATION_ELEMENTOR_PLUS_URL . 'assets/vendor/LDR_LLL1_0.png',
		);

		wp_add_inline_script(
			'foundation-elementor-plus-dark-animated-hero',
			'window.FOUNDATION_INKFIRE_SPLASH_CONFIG = ' . wp_json_encode( $config ) . ';',
			'before'
		);
	}

	public static function render( $atts = array() ) {
		$settings       = self::get_settings();
		$preset_key     = isset( $atts['preset'] ) ? sanitize_key( (string) $atts['preset'] ) : 'home';
		$has_kicker_override = isset( $atts['kicker_html'] ) && '' !== trim( (string) $atts['kicker_html'] );
		$default_preset = self::get_preset_config( $preset_key );

		$defaults = array(
			'preset'           => 'home',
			'palette_key'      => isset( $default_preset['palette_key'] ) ? $default_preset['palette_key'] : 'inkfire_green',
			'palette'          => '',
			'size'             => $default_preset['size'],
			'align'            => $default_preset['align'],
			'content_vertical_align' => isset( $default_preset['content_vertical_align'] ) ? $default_preset['content_vertical_align'] : 'center',
			'min_height'       => $default_preset['min_height'],
			'height_strategy'  => isset( $default_preset['height_strategy'] ) ? $default_preset['height_strategy'] : 'preset',
			'eyebrow'          => $default_preset['eyebrow'],
			'eyebrow_style'    => 'green_glass',
			'kicker_html'      => isset( $default_preset['kicker'] ) ? $default_preset['kicker'] : '',
			'h1_html'          => $default_preset['title'],
			'subhead'          => $default_preset['subhead'],
			'btn_text'         => $default_preset['primary_text'],
			'btn_url'          => $default_preset['primary_url'],
			'show_primary_button' => '',
			'secondary_text'   => $default_preset['secondary_text'],
			'secondary_url'    => $default_preset['secondary_url'],
			'show_secondary_button' => '',
			'trust_html'       => '',
			'trust_label'      => '',
			'trust_logos'      => array(),
			'features'         => $default_preset['features'],
			'feature_position' => $default_preset['feature_position'],
			'breadcrumb_position' => isset( $default_preset['breadcrumb_position'] ) ? (string) $default_preset['breadcrumb_position'] : '',
			'show_team'        => (string) $default_preset['show_team'],
			'show_trust'       => (string) $default_preset['show_trust'],
			'blog_post_meta_visibility' => isset( $default_preset['blog_post_meta_visibility'] ) ? (string) $default_preset['blog_post_meta_visibility'] : '',
			'blog_post_meta_label' => isset( $default_preset['blog_post_meta_label'] ) ? (string) $default_preset['blog_post_meta_label'] : '',
			'blog_post_meta_pill_primary' => isset( $default_preset['blog_post_meta_pill_primary'] ) ? (string) $default_preset['blog_post_meta_pill_primary'] : '',
			'blog_post_meta_pill_secondary' => isset( $default_preset['blog_post_meta_pill_secondary'] ) ? (string) $default_preset['blog_post_meta_pill_secondary'] : '',
			'blog_post_author_visibility' => isset( $default_preset['blog_post_author_visibility'] ) ? (string) $default_preset['blog_post_author_visibility'] : '',
			'blog_post_author_prefix' => isset( $default_preset['blog_post_author_prefix'] ) ? (string) $default_preset['blog_post_author_prefix'] : '',
			'blog_post_author_name' => isset( $default_preset['blog_post_author_name'] ) ? (string) $default_preset['blog_post_author_name'] : '',
			'blog_post_author_avatar_url' => isset( $default_preset['blog_post_author_avatar_url'] ) ? (string) $default_preset['blog_post_author_avatar_url'] : '',
			'blog_post_author_link_url' => isset( $default_preset['blog_post_author_link_url'] ) ? (string) $default_preset['blog_post_author_link_url'] : '',
			'class'            => '',
		);

		$a = wp_parse_args( $atts, $defaults );

		$preset_key       = sanitize_key( (string) $a['preset'] );
		$context_post_id  = self::get_render_context_post_id( $preset_key );
		$token_context    = array( 'post_id' => $context_post_id );
		$preset_slug      = str_replace( '_', '-', $preset_key );
		$size             = in_array( (string) $a['size'], array( 'full', 'compact' ), true ) ? (string) $a['size'] : $default_preset['size'];
		$align            = in_array( (string) $a['align'], array( 'center', 'left' ), true ) ? (string) $a['align'] : $default_preset['align'];
		$content_vertical_align = in_array( (string) $a['content_vertical_align'], array( 'flex-start', 'center', 'flex-end' ), true ) ? (string) $a['content_vertical_align'] : ( isset( $default_preset['content_vertical_align'] ) ? (string) $default_preset['content_vertical_align'] : 'center' );
		$height_strategy  = in_array( (string) $a['height_strategy'], array( 'preset', 'viewport', 'viewport_offset', 'content' ), true ) ? (string) $a['height_strategy'] : ( isset( $default_preset['height_strategy'] ) ? (string) $default_preset['height_strategy'] : 'preset' );
		$headline_html    = self::normalize_icon_markup( self::replace_tokens( $a['h1_html'], $token_context ) );
		$kicker_html      = self::normalize_icon_markup( self::replace_tokens( $a['kicker_html'], $token_context ) );
		$subhead_html     = self::normalize_icon_markup( self::replace_tokens( $a['subhead'], $token_context ) );
		$eyebrow_html     = self::normalize_icon_markup( self::replace_tokens( $a['eyebrow'], $token_context ) );
		$raw_glass_breadcrumb_html = self::render_glass_breadcrumb( $preset_key );
		$breadcrumb_position = self::resolve_breadcrumb_position( $preset_key, isset( $a['breadcrumb_position'] ) ? (string) $a['breadcrumb_position'] : '', $default_preset );
		$glass_breadcrumb_html = 'hidden' === $breadcrumb_position ? '' : $raw_glass_breadcrumb_html;

		if ( 'blog_post' === $preset_key && ! $has_kicker_override && '' !== $glass_breadcrumb_html ) {
			$kicker_html = '';
		}

		$eyebrow_style    = in_array( (string) $a['eyebrow_style'], array( 'green_glass', 'orange_glass', 'white_glass' ), true ) ? (string) $a['eyebrow_style'] : 'green_glass';
		$feature_items    = self::parse_features( self::replace_tokens( $a['features'], $token_context ) );
		$feature_position = in_array( (string) $a['feature_position'], array( 'after_title', 'after_subhead', 'before_buttons', 'hidden' ), true ) ? (string) $a['feature_position'] : $default_preset['feature_position'];
		$show_team        = in_array( (string) $a['show_team'], array( '1', 'yes', 'true' ), true ) || true === $a['show_team'];
		$show_trust       = in_array( (string) $a['show_trust'], array( '1', 'yes', 'true' ), true ) || true === $a['show_trust'];
		$show_primary_button = '0' !== (string) $a['show_primary_button'] && '' !== trim( (string) $a['btn_text'] );
		$show_secondary_button = '0' !== (string) $a['show_secondary_button'] && '' !== trim( (string) $a['secondary_text'] );
		$preset_palette   = isset( $default_preset['palette_key'] ) ? (string) $default_preset['palette_key'] : 'inkfire_green';
		$palette_key      = isset( $a['palette_key'] ) ? sanitize_key( (string) $a['palette_key'] ) : '';
		$resolved_palette = self::resolve_palette_string( $a['palette'], $palette_key, $preset_palette );
		$blog_post_meta_markup = self::render_blog_post_meta_row( $preset_key, $a, $token_context );
		$blog_post_author_markup = self::render_blog_post_author_strip( $preset_key, $a, $token_context );
		$project_team_markup = self::render_project_team_hero(
			$preset_key,
			array(
				'visibility' => isset( $a['project_team_visibility'] ) ? (string) $a['project_team_visibility'] : '',
				'department' => isset( $a['project_team_department'] ) ? (string) $a['project_team_department'] : '',
			)
		);
		$is_portfolio_editorial = self::is_portfolio_editorial_context( $preset_key, $context_post_id );
		$portfolio_intro_label  = $is_portfolio_editorial ? self::get_portfolio_editorial_meta_value( $context_post_id, 'hero_intro_label' ) : '';
		$portfolio_display_title = $is_portfolio_editorial ? self::get_portfolio_editorial_meta_value( $context_post_id, 'hero_display_title' ) : '';
		$portfolio_details_markup = $is_portfolio_editorial ? self::render_portfolio_editorial_details_card( $context_post_id ) : '';
		$portfolio_stats_markup   = $is_portfolio_editorial ? self::render_portfolio_editorial_stats_strip( $context_post_id ) : '';

		$class_names = array(
			'foundation-inkfire-splash',
			'foundation-inkfire-splash--' . $size,
			'foundation-inkfire-splash--height-' . sanitize_html_class( str_replace( '_', '-', $height_strategy ) ),
			'foundation-inkfire-splash--align-' . $align,
			'foundation-inkfire-splash--preset-' . sanitize_html_class( $preset_slug ),
			'foundation-inkfire-splash--features-' . sanitize_html_class( $feature_position ),
			'foundation-inkfire-splash--palette-' . sanitize_html_class( str_replace( '_', '-', '' !== $palette_key ? $palette_key : $preset_palette ) ),
		);

		if ( $show_team ) {
			$class_names[] = 'foundation-inkfire-splash--has-team';
		}

		if ( $show_trust ) {
			$class_names[] = 'foundation-inkfire-splash--has-trust';
		}

		if ( ! empty( $feature_items ) && 'hidden' !== $feature_position ) {
			$class_names[] = 'foundation-inkfire-splash--has-features';
		}

		if ( '' !== $glass_breadcrumb_html ) {
			$class_names[] = 'foundation-inkfire-splash--breadcrumb-' . sanitize_html_class( $breadcrumb_position );
		}

		if ( '' !== trim( (string) $a['class'] ) ) {
			$class_names[] = sanitize_html_class( (string) $a['class'] );
		}

		$section_id    = 'foundation-inkfire-hero-' . wp_unique_id();
		$vertical_align_slug = str_replace( '-', '_', $content_vertical_align );
		$hero_classes  = 'foundation-inkfire-hero foundation-inkfire-hero--' . $align . ' foundation-inkfire-hero--' . $size . ' foundation-inkfire-hero--v-' . sanitize_html_class( $vertical_align_slug );
		$palette_attr  = implode( ',', self::get_palette_values( $resolved_palette ) );
		$inline_styles = '--foundation-inkfire-section-min-height:' . esc_attr( (string) $a['min_height'] ) . ';';

		$has_eyebrow      = '' !== trim( wp_strip_all_tags( $eyebrow_html ) );
		$has_kicker       = '' !== trim( wp_strip_all_tags( $kicker_html ) );
		$has_glass_breadcrumb = '' !== trim( $glass_breadcrumb_html );
		$has_blog_post_meta = '' !== trim( wp_strip_all_tags( $blog_post_meta_markup ) );
		$has_blog_post_author = '' !== trim( wp_strip_all_tags( $blog_post_author_markup ) );
		$needs_icon_fonts = self::markup_uses_fontawesome( $eyebrow_html . ' ' . $kicker_html . ' ' . $headline_html . ' ' . $subhead_html );
		self::enqueue_assets( $resolved_palette, $needs_icon_fonts );

		ob_start();
		?>
		<section
			id="<?php echo esc_attr( $section_id ); ?>"
			class="<?php echo esc_attr( trim( implode( ' ', array_filter( $class_names ) ) ) ); ?>"
			data-foundation-inkfire-splash
			data-palette="<?php echo esc_attr( $palette_attr ); ?>"
			data-palette-key="<?php echo esc_attr( '' !== $palette_key ? $palette_key : $preset_palette ); ?>"
			role="region"
			aria-labelledby="<?php echo esc_attr( $section_id . '-heading' ); ?>"
			aria-describedby="<?php echo esc_attr( $section_id . '-subhead' ); ?>"
			style="<?php echo esc_attr( $inline_styles ); ?>"
		>
			<canvas class="foundation-inkfire-splash__canvas" aria-hidden="true" role="presentation"></canvas>
			<div class="foundation-inkfire-splash__mask" aria-hidden="true"></div>

			<div class="<?php echo esc_attr( $hero_classes ); ?>">
				<div class="foundation-inkfire-hero__inner">
					<?php if ( $has_glass_breadcrumb && 'top' === $breadcrumb_position ) : ?>
						<div class="foundation-inkfire-shortcode-breadcrumb foundation-inkfire-shortcode-breadcrumb--top">
							<?php echo $glass_breadcrumb_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>

					<?php if ( $is_portfolio_editorial ) : ?>
						<div class="foundation-inkfire-portfolio-hero-shell">
							<div class="foundation-inkfire-portfolio-hero-main">
								<?php if ( $has_blog_post_meta ) : ?>
									<?php echo wp_kses_post( $blog_post_meta_markup ); ?>
								<?php endif; ?>

								<?php if ( $has_eyebrow || $has_kicker ) : ?>
									<div class="foundation-inkfire-meta-row">
										<?php if ( $has_kicker ) : ?>
											<p class="foundation-inkfire-kicker"><?php echo self::kses_hero_html( $kicker_html ); ?></p>
										<?php endif; ?>

										<?php if ( $has_eyebrow ) : ?>
											<p class="foundation-inkfire-eyebrow foundation-inkfire-eyebrow--<?php echo esc_attr( sanitize_html_class( str_replace( '_', '-', $eyebrow_style ) ) ); ?>"><?php echo self::kses_hero_html( $eyebrow_html ); ?></p>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<?php if ( '' !== $portfolio_intro_label ) : ?>
									<p class="foundation-inkfire-portfolio-intro-label"><?php echo esc_html( $portfolio_intro_label ); ?></p>
								<?php endif; ?>

								<?php if ( '' !== $portfolio_display_title ) : ?>
									<p class="foundation-inkfire-portfolio-display-title"><?php echo esc_html( $portfolio_display_title ); ?></p>
								<?php endif; ?>

								<h1 id="<?php echo esc_attr( $section_id . '-heading' ); ?>" class="foundation-inkfire-headline">
									<?php echo self::kses_hero_html( $headline_html ); ?>
								</h1>

								<?php if ( 'after_title' === $feature_position ) : ?>
									<?php echo self::render_feature_list( $feature_items, $feature_position ); ?>
								<?php endif; ?>

								<?php if ( '' !== trim( wp_strip_all_tags( $subhead_html ) ) ) : ?>
									<div id="<?php echo esc_attr( $section_id . '-subhead' ); ?>" class="foundation-inkfire-subhead">
										<?php echo self::kses_hero_html( $subhead_html ); ?>
									</div>
								<?php endif; ?>

								<?php if ( $has_blog_post_author ) : ?>
									<?php echo wp_kses_post( $blog_post_author_markup ); ?>
								<?php endif; ?>

								<?php if ( 'after_subhead' === $feature_position ) : ?>
									<?php echo self::render_feature_list( $feature_items, $feature_position ); ?>
								<?php endif; ?>

								<?php if ( 'before_buttons' === $feature_position ) : ?>
									<?php echo self::render_feature_list( $feature_items, $feature_position ); ?>
								<?php endif; ?>

								<div class="foundation-inkfire-action-row">
									<div class="foundation-inkfire-button-row">
										<?php if ( $show_primary_button ) : ?>
											<a href="<?php echo esc_url( (string) $a['btn_url'] ); ?>" class="foundation-inkfire-main-btn">
												<span><?php echo esc_html( (string) $a['btn_text'] ); ?></span>
												<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
													<path d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"></path>
												</svg>
											</a>
										<?php endif; ?>

										<?php if ( $show_secondary_button ) : ?>
											<a href="<?php echo esc_url( (string) $a['secondary_url'] ); ?>" class="foundation-inkfire-secondary-btn">
												<span><?php echo esc_html( (string) $a['secondary_text'] ); ?></span>
												<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
													<path d="M5 12h14M12 5l7 7-7 7"></path>
												</svg>
											</a>
										<?php endif; ?>
									</div>

									<?php if ( $show_team ) : ?>
										<?php echo self::render_team_hero(); ?>
									<?php endif; ?>

									<?php if ( '' !== $project_team_markup ) : ?>
										<?php echo wp_kses_post( $project_team_markup ); ?>
									<?php endif; ?>
								</div>
							</div>

							<?php if ( '' !== $portfolio_details_markup ) : ?>
								<aside class="foundation-inkfire-portfolio-hero-side">
									<?php echo wp_kses_post( $portfolio_details_markup ); ?>
								</aside>
							<?php endif; ?>
						</div>

						<?php if ( '' !== $portfolio_stats_markup ) : ?>
							<?php echo wp_kses_post( $portfolio_stats_markup ); ?>
						<?php endif; ?>
					<?php else : ?>
						<?php if ( $has_blog_post_meta ) : ?>
							<?php echo wp_kses_post( $blog_post_meta_markup ); ?>
						<?php endif; ?>

						<?php if ( $has_eyebrow || $has_kicker ) : ?>
							<div class="foundation-inkfire-meta-row">
								<?php if ( $has_kicker ) : ?>
									<p class="foundation-inkfire-kicker"><?php echo self::kses_hero_html( $kicker_html ); ?></p>
								<?php endif; ?>

								<?php if ( $has_eyebrow ) : ?>
									<p class="foundation-inkfire-eyebrow foundation-inkfire-eyebrow--<?php echo esc_attr( sanitize_html_class( str_replace( '_', '-', $eyebrow_style ) ) ); ?>"><?php echo self::kses_hero_html( $eyebrow_html ); ?></p>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<h1 id="<?php echo esc_attr( $section_id . '-heading' ); ?>" class="foundation-inkfire-headline">
							<?php echo self::kses_hero_html( $headline_html ); ?>
						</h1>

						<?php if ( 'after_title' === $feature_position ) : ?>
							<?php echo self::render_feature_list( $feature_items, $feature_position ); ?>
						<?php endif; ?>

						<?php if ( '' !== trim( wp_strip_all_tags( $subhead_html ) ) ) : ?>
							<div id="<?php echo esc_attr( $section_id . '-subhead' ); ?>" class="foundation-inkfire-subhead">
								<?php echo self::kses_hero_html( $subhead_html ); ?>
							</div>
						<?php endif; ?>

						<?php if ( $has_blog_post_author ) : ?>
							<?php echo wp_kses_post( $blog_post_author_markup ); ?>
						<?php endif; ?>

						<?php if ( 'after_subhead' === $feature_position ) : ?>
							<?php echo self::render_feature_list( $feature_items, $feature_position ); ?>
						<?php endif; ?>

						<?php if ( 'before_buttons' === $feature_position ) : ?>
							<?php echo self::render_feature_list( $feature_items, $feature_position ); ?>
						<?php endif; ?>

						<div class="foundation-inkfire-action-row">
							<div class="foundation-inkfire-button-row">
								<?php if ( $show_primary_button ) : ?>
									<a href="<?php echo esc_url( (string) $a['btn_url'] ); ?>" class="foundation-inkfire-main-btn">
										<span><?php echo esc_html( (string) $a['btn_text'] ); ?></span>
										<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
											<path d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"></path>
										</svg>
									</a>
								<?php endif; ?>

								<?php if ( $show_secondary_button ) : ?>
									<a href="<?php echo esc_url( (string) $a['secondary_url'] ); ?>" class="foundation-inkfire-secondary-btn">
										<span><?php echo esc_html( (string) $a['secondary_text'] ); ?></span>
										<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
											<path d="M5 12h14M12 5l7 7-7 7"></path>
										</svg>
									</a>
								<?php endif; ?>
							</div>

							<?php if ( $show_team ) : ?>
								<?php echo self::render_team_hero(); ?>
							<?php endif; ?>

							<?php if ( '' !== $project_team_markup ) : ?>
								<?php echo wp_kses_post( $project_team_markup ); ?>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $has_glass_breadcrumb && 'bottom' === $breadcrumb_position ) : ?>
						<div class="foundation-inkfire-shortcode-breadcrumb foundation-inkfire-shortcode-breadcrumb--bottom">
							<?php echo $glass_breadcrumb_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>

					<?php if ( $show_trust ) : ?>
						<?php echo wp_kses_post( self::render_trust_bar( $a['trust_html'], $a['trust_label'], $a['trust_logos'] ) ); ?>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php

		return ob_get_clean();
	}

	private static function get_preset_config( $preset_key ) {
		$settings = self::get_settings();

		if ( empty( $preset_key ) || ! isset( $settings['presets'][ $preset_key ] ) ) {
			return $settings['presets']['home'];
		}

		$preset = $settings['presets'][ $preset_key ];

		$context_post_id = self::get_render_context_post_id( $preset_key );

		if ( 'portfolio_post' === $preset_key && $context_post_id && 'ink_portfolio' === get_post_type( $context_post_id ) ) {
			$portfolio_live_url = self::get_portfolio_editorial_live_site_url( $context_post_id );
			$portfolio_permalink = (string) get_permalink( $context_post_id );
			$portfolio_headline = self::get_portfolio_editorial_meta_value( $context_post_id, 'hero_headline' );
			$portfolio_body     = self::get_portfolio_editorial_meta_value( $context_post_id, 'hero_body' );
			$preset['kicker']           = '{current_breadcrumb_links}';
			$preset['eyebrow']          = '{current_portfolio_meta_pills}';
			$preset['title']            = '' !== $portfolio_headline ? $portfolio_headline : '{current_title}';
			$preset['subhead']          = '' !== $portfolio_body ? $portfolio_body : '{current_excerpt}';
			$preset['features']         = '';
			$preset['feature_position'] = 'hidden';
			$preset['primary_text']     = $portfolio_live_url !== $portfolio_permalink ? 'Visit live site' : 'View project';
			$preset['primary_url']      = $portfolio_live_url;
			$preset['secondary_text']   = 'View all work';
			$preset['secondary_url']    = home_url( '/portfolio/' );
		} elseif ( 'blog_post' === $preset_key && $context_post_id && 'post' === get_post_type( $context_post_id ) ) {
			$preset['kicker']           = '{current_breadcrumb_links}';
			$preset['eyebrow']          = '';
			$preset['title']            = '{current_title}';
			$preset['subhead']          = '{current_excerpt}';
			$preset['features']         = '';
			$preset['feature_position'] = 'hidden';
			$preset['blog_post_meta_visibility'] = 'show';
			$preset['blog_post_meta_label'] = '{current_category}';
			$preset['blog_post_meta_pill_primary'] = '{current_read_time}';
			$preset['blog_post_meta_pill_secondary'] = '{current_word_count}';
			$preset['blog_post_author_visibility'] = 'show';
			$preset['blog_post_author_prefix'] = 'Written by';
			$preset['blog_post_author_name'] = '{current_author_name}';
			$preset['blog_post_author_avatar_url'] = '{current_author_avatar_url}';
			$preset['blog_post_author_link_url'] = '{current_author_url}';
		} elseif ( 'blog_archive' === $preset_key && is_search() ) {
			$preset['kicker']         = 'Search results';
			$preset['title']          = 'Results for "{current_search_query}"';
			$preset['subhead']        = '{current_results_summary}';
			$preset['secondary_text'] = 'Browse all articles';
			$preset['secondary_url']  = self::get_posts_archive_url();
		}

		return $preset;
	}

	private static function get_palette_values( $palette = '' ) {
		$palette = '' !== trim( (string) $palette )
			? (string) $palette
			: self::get_settings()['palette'];

		return array_values(
			array_filter(
				array_map(
					'trim',
					explode( ',', $palette )
				)
			)
		);
	}

	private static function resolve_breadcrumb_position( $preset_key, $position, array $default_preset = array() ) {
		if ( ! self::should_render_glass_breadcrumb( $preset_key ) ) {
			return 'hidden';
		}

		$allowed  = array( 'top', 'bottom', 'hidden' );
		$position = sanitize_key( (string) $position );

		if ( in_array( $position, $allowed, true ) ) {
			return $position;
		}

		$default_position = isset( $default_preset['breadcrumb_position'] ) ? sanitize_key( (string) $default_preset['breadcrumb_position'] ) : 'top';

		return in_array( $default_position, $allowed, true ) ? $default_position : 'top';
	}

	private static function should_render_glass_breadcrumb( $preset_key ) {
		return in_array( (string) $preset_key, array( 'blog_archive', 'blog_post' ), true );
	}

	private static function render_glass_breadcrumb( $preset_key ) {
		if ( ! self::should_render_glass_breadcrumb( $preset_key ) || ! shortcode_exists( 'ink_glass_breadcrumb' ) ) {
			return '';
		}

		$markup = do_shortcode( '[ink_glass_breadcrumb]' );

		return is_string( $markup ) ? trim( $markup ) : '';
	}

	private static function get_lines( $value ) {
		$lines = preg_split( '/\r\n|\r|\n/', (string) $value );

		return array_values(
			array_filter(
				array_map(
					static function ( $line ) {
						return trim( (string) $line );
					},
					$lines
				)
			)
		);
	}

	private static function replace_tokens( $value, array $context = array() ) {
		$current_title      = '';
		$current_title_plain = '';
		$current_excerpt    = '';
		$current_archive_description = '';
		$current_breadcrumb = '';
		$current_breadcrumb_links = '';
		$current_category   = '';
		$current_date       = '';
		$current_meta_pills = '';
		$current_blog_meta_pills = '';
		$current_read_time = '';
		$current_word_count = '';
		$current_author_name = '';
		$current_author_avatar_url = '';
		$current_author_url = '';
		$current_search_query = '';
		$current_results_summary = '';
		$context_post_id    = isset( $context['post_id'] ) ? (int) $context['post_id'] : 0;

		if ( $context_post_id > 0 ) {
			$current_title       = get_the_title( $context_post_id );
			$current_title_plain = $current_title;
			$current_excerpt     = self::get_current_context_excerpt( $context_post_id );
			$current_breadcrumb  = self::get_current_breadcrumb_label( $context_post_id );
			$current_breadcrumb_links = self::get_current_breadcrumb_markup( $context_post_id );
			$current_category    = self::get_current_primary_term_label( $context_post_id );
			$current_date        = get_the_date( 'j M Y', $context_post_id );
			$current_meta_pills  = self::get_current_portfolio_meta_pills( $context_post_id );
			$current_blog_meta_pills = self::get_current_blog_meta_pills( $context_post_id );
			$current_read_time   = self::get_current_read_time_label( $context_post_id );
			$current_word_count  = self::get_current_word_count_label( $context_post_id );
			$current_author_name = self::get_current_author_name( $context_post_id );
			$current_author_avatar_url = self::get_current_author_avatar_url( $context_post_id );
			$current_author_url  = self::get_current_author_url( $context_post_id );
		} elseif ( is_search() ) {
			$current_search_query    = get_search_query();
			$current_results_summary = self::get_current_search_results_summary();
			$current_title           = 'Search results';
			$current_title_plain     = 'Search';
			$current_excerpt         = $current_results_summary;
			$current_archive_description = $current_results_summary;
		} elseif ( is_home() ) {
			$current_title           = self::get_posts_archive_label();
			$current_title_plain     = $current_title;
			$current_archive_description = self::get_current_archive_description();
			$current_excerpt         = $current_archive_description;
		} elseif ( is_archive() || is_post_type_archive() ) {
			$current_title = wp_strip_all_tags( get_the_archive_title() );
			if ( is_post_type_archive() ) {
				$current_title_plain = is_post_type_archive( 'post' )
					? self::get_posts_archive_label()
					: wp_strip_all_tags( post_type_archive_title( '', false ) );
			} elseif ( is_tax() || is_category() || is_tag() ) {
				$current_title_plain = single_term_title( '', false );
			} elseif ( is_author() ) {
				$author = get_queried_object();
				$current_title_plain = ( $author && isset( $author->display_name ) ) ? wp_strip_all_tags( (string) $author->display_name ) : '';
			}
			$current_archive_description = self::get_current_archive_description();
			$current_excerpt             = $current_archive_description;
		} elseif ( is_singular() ) {
			$post_id            = get_the_ID();
			$current_title      = get_the_title();
			$current_title_plain = $current_title;
			$current_excerpt    = self::get_current_context_excerpt( $post_id );
			$current_breadcrumb = self::get_current_breadcrumb_label( $post_id );
			$current_breadcrumb_links = self::get_current_breadcrumb_markup( $post_id );
			$current_category   = self::get_current_primary_term_label( $post_id );
			$current_date       = $post_id ? get_the_date( 'j M Y', $post_id ) : '';
			$current_meta_pills = self::get_current_portfolio_meta_pills( $post_id );
			$current_blog_meta_pills = self::get_current_blog_meta_pills( $post_id );
			$current_read_time  = self::get_current_read_time_label( $post_id );
			$current_word_count = self::get_current_word_count_label( $post_id );
			$current_author_name = self::get_current_author_name( $post_id );
			$current_author_avatar_url = self::get_current_author_avatar_url( $post_id );
			$current_author_url = self::get_current_author_url( $post_id );
		} elseif ( is_front_page() ) {
			$current_title = get_bloginfo( 'name' );
			$current_title_plain = $current_title;
		} else {
			$current_title = wp_get_document_title();
			$current_title_plain = $current_title;
		}

		if ( '' === $current_title_plain ) {
			$current_title_plain = preg_replace( '/^[^:]+:\s*/', '', $current_title );
		}

		if ( '' === $current_excerpt ) {
			if ( '' !== $current_archive_description ) {
				$current_excerpt = $current_archive_description;
			} elseif ( is_post_type_archive( 'ink_portfolio' ) || 'ink_portfolio' === get_post_type( $context_post_id ) ) {
				$current_excerpt = 'Selected project work from Inkfire, designed to perform, scale, and stand out for the right reasons.';
			} elseif ( is_home() || is_search() || is_category() || is_tag() || is_author() || is_post_type_archive( 'post' ) || 'post' === get_post_type( $context_post_id ) ) {
				$current_excerpt = 'Practical guides, honest updates, and accessible thinking from the Inkfire team.';
			}
		}

		if ( '' === $current_results_summary ) {
			$current_results_summary = self::get_current_search_results_summary();
		}

		if ( '' === $current_search_query ) {
			$current_search_query = get_search_query();
		}

		$replaced = strtr(
			(string) $value,
			array(
				'{current_title}'   => $current_title,
				'{current_title_plain}' => $current_title_plain,
				'{current_excerpt}' => $current_excerpt,
				'{current_archive_description}' => $current_archive_description,
				'{current_breadcrumb}' => $current_breadcrumb,
				'{current_breadcrumb_links}' => $current_breadcrumb_links,
				'{current_category}' => $current_category,
				'{current_date}'    => $current_date,
				'{current_portfolio_meta_pills}' => $current_meta_pills,
				'{current_blog_meta_pills}' => $current_blog_meta_pills,
				'{current_read_time}' => $current_read_time,
				'{current_word_count}' => $current_word_count,
				'{current_author_name}' => $current_author_name,
				'{current_author_avatar_url}' => $current_author_avatar_url,
				'{current_author_url}' => $current_author_url,
				'{current_search_query}' => $current_search_query,
				'{current_results_summary}' => $current_results_summary,
				'{service_name}'    => $current_title,
				'{site_name}'       => get_bloginfo( 'name' ),
				'{year}'            => gmdate( 'Y' ),
			)
		);

		$replaced = preg_replace(
			'/<span class="foundation-inkfire-pill foundation-inkfire-pill--crumb">\s*<\/span>/',
			'',
			$replaced
		);

		return is_string( $replaced ) ? $replaced : (string) $value;
	}

	private static function get_current_breadcrumb_label( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id ) {
			return '';
		}

		$post_type = get_post_type( $post_id );

		if ( ! $post_type ) {
			return '';
		}

		$post_type_object = get_post_type_object( $post_type );
		$archive_label    = 'post' === $post_type
			? self::get_posts_archive_label()
			: ( ( $post_type_object && ! empty( $post_type_object->labels->name ) ) ? $post_type_object->labels->name : ucfirst( $post_type ) );
		$archive_label    = wp_strip_all_tags( (string) $archive_label );

		$term = self::get_current_primary_term( $post_id );
		if ( $term && ! empty( $term->name ) ) {
			return trim( $archive_label . ' / ' . wp_strip_all_tags( (string) $term->name ) );
		}

		return $archive_label;
	}

	private static function get_current_breadcrumb_markup( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id ) {
			return '';
		}

		$post_type = get_post_type( $post_id );
		if ( ! $post_type ) {
			return '';
		}

		$post_type_object = get_post_type_object( $post_type );
		$archive_label    = 'post' === $post_type
			? self::get_posts_archive_label()
			: ( ( $post_type_object && ! empty( $post_type_object->labels->name ) ) ? $post_type_object->labels->name : ucfirst( $post_type ) );
		$archive_label    = wp_strip_all_tags( (string) $archive_label );
		$archive_url      = 'post' === $post_type ? self::get_posts_archive_url() : get_post_type_archive_link( $post_type );

		if ( ! $archive_url && 'ink_portfolio' === $post_type ) {
			$archive_url = home_url( '/portfolio/' );
		}

		$term             = self::get_current_primary_term( $post_id );
		$parts            = array();

		if ( $archive_url ) {
			$parts[] = sprintf(
				'<a class="foundation-inkfire-breadcrumb-link foundation-inkfire-breadcrumb-link--archive" href="%s">%s</a>',
				esc_url( $archive_url ),
				esc_html( $archive_label )
			);
		} else {
			$parts[] = sprintf(
				'<span class="foundation-inkfire-breadcrumb-link foundation-inkfire-breadcrumb-link--archive">%s</span>',
				esc_html( $archive_label )
			);
		}

		if ( $term && ! empty( $term->name ) ) {
			$term_link = get_term_link( $term );

			$parts[] = '<span class="foundation-inkfire-breadcrumb-separator" aria-hidden="true">/</span>';

			if ( ! is_wp_error( $term_link ) ) {
				$parts[] = sprintf(
					'<a class="foundation-inkfire-breadcrumb-link foundation-inkfire-breadcrumb-link--term" href="%s">%s</a>',
					esc_url( $term_link ),
					esc_html( $term->name )
				);
			} else {
				$parts[] = sprintf(
					'<span class="foundation-inkfire-breadcrumb-link foundation-inkfire-breadcrumb-link--term">%s</span>',
					esc_html( $term->name )
				);
			}
		}

		return implode( '', $parts );
	}

	private static function get_current_context_excerpt( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id ) {
			return '';
		}

		if ( has_excerpt( $post_id ) ) {
			return get_the_excerpt( $post_id );
		}

		$content = get_post_field( 'post_content', $post_id );

		if ( '' !== trim( (string) $content ) ) {
			return wp_trim_words( wp_strip_all_tags( (string) $content ), 28 );
		}

		return '';
	}

	private static function get_current_archive_description() {
		if ( is_search() ) {
			return self::get_current_search_results_summary();
		}

		if ( is_home() ) {
			$posts_page_id = (int) get_option( 'page_for_posts' );

			if ( $posts_page_id > 0 ) {
				$excerpt = self::get_current_context_excerpt( $posts_page_id );
				if ( '' !== trim( $excerpt ) ) {
					return $excerpt;
				}
			}

			return 'Practical guides, case studies, and honest updates from the Inkfire team.';
		}

		if ( is_archive() || is_post_type_archive() ) {
			$description = (string) get_the_archive_description();
			if ( '' !== trim( wp_strip_all_tags( $description ) ) ) {
				return $description;
			}

			if ( is_post_type_archive( 'ink_portfolio' ) ) {
				return 'Selected project work from Inkfire, designed to perform, scale, and stand out for the right reasons.';
			}

			if ( is_post_type_archive( 'post' ) || is_category() || is_tag() || is_author() ) {
				return 'Practical guides, case studies, and honest updates from the Inkfire team.';
			}
		}

		return '';
	}

	private static function get_current_search_results_summary() {
		if ( ! is_search() ) {
			return '';
		}

		global $wp_query;

		$search_query = trim( (string) get_search_query() );
		$result_count = ( $wp_query instanceof \WP_Query ) ? (int) $wp_query->found_posts : 0;
		$result_label = 1 === $result_count ? '1 result' : sprintf( '%d results', $result_count );

		if ( '' === $search_query ) {
			return 'Browse the latest articles, case studies, and practical resources from Inkfire.';
		}

		return sprintf(
			'We found %1$s for "%2$s". Browse the matches below or explore the latest insights from Inkfire.',
			$result_label,
			$search_query
		);
	}

	private static function get_current_primary_term_label( $post_id = 0 ) {
		$term = self::get_current_primary_term( $post_id );

		if ( $term && ! empty( $term->name ) ) {
			return wp_strip_all_tags( (string) $term->name );
		}

		return '';
	}

	private static function get_current_primary_term( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id ) {
			return null;
		}

		$post_type = get_post_type( $post_id );
		if ( ! $post_type ) {
			return null;
		}

		$taxonomies = get_object_taxonomies( $post_type, 'names' );
		$preferred  = 'ink_portfolio' === $post_type
			? array( 'ink_portfolio_type', 'portfolio_category', 'category', 'portfolio_tag', 'post_tag' )
			: array( 'portfolio_category', 'category', 'portfolio_tag', 'post_tag' );
		$ordered    = array_values( array_unique( array_merge( $preferred, $taxonomies ) ) );

		foreach ( $ordered as $taxonomy ) {
			if ( ! in_array( $taxonomy, $taxonomies, true ) ) {
				continue;
			}

			$terms = get_the_terms( $post_id, $taxonomy );
			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				continue;
			}

			$term = reset( $terms );
			if ( $term && ! empty( $term->name ) ) {
				return $term;
			}
		}

		return null;
	}

	private static function get_current_portfolio_meta_pills( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id || 'ink_portfolio' !== get_post_type( $post_id ) ) {
			return '';
		}

		$pills    = array();
		$category = self::get_current_primary_term_label( $post_id );
		$date     = get_the_date( 'j M Y', $post_id );

		if ( '' !== trim( $category ) ) {
			$pills[] = sprintf(
				'<span class="foundation-inkfire-pill foundation-inkfire-pill--crumb">%s</span>',
				esc_html( $category )
			);
		}

		if ( '' !== trim( (string) $date ) ) {
			$pills[] = sprintf(
				'<span class="foundation-inkfire-pill foundation-inkfire-pill--rating">%s</span>',
				esc_html( (string) $date )
			);
		}

		return implode( '', $pills );
	}

	private static function get_current_blog_meta_pills( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
			return '';
		}

		$pills    = array();
		$category = self::get_current_primary_term_label( $post_id );
		$date     = get_the_date( 'j M Y', $post_id );

		if ( '' !== trim( $category ) ) {
			$pills[] = sprintf(
				'<span class="foundation-inkfire-pill foundation-inkfire-pill--crumb">%s</span>',
				esc_html( $category )
			);
		}

		if ( '' !== trim( (string) $date ) ) {
			$pills[] = sprintf(
				'<span class="foundation-inkfire-pill foundation-inkfire-pill--rating">%s</span>',
				esc_html( (string) $date )
			);
		}

		return implode( '', $pills );
	}

	private static function get_post_word_count( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id ) {
			return 0;
		}

		$content = get_post_field( 'post_content', $post_id );

		if ( ! is_string( $content ) || '' === trim( $content ) ) {
			return 0;
		}

		$clean_content = strip_shortcodes( $content );
		return max( 0, str_word_count( wp_strip_all_tags( $clean_content ) ) );
	}

	private static function get_current_read_time_label( $post_id = 0 ) {
		$words = self::get_post_word_count( $post_id );

		if ( $words < 1 ) {
			return '';
		}

		$minutes = max( 1, (int) ceil( $words / 200 ) );
		/* translators: %d: reading time in minutes */
		return sprintf( _n( '%d min read', '%d mins read', $minutes, 'foundation-elementor-plus' ), $minutes );
	}

	private static function get_current_word_count_label( $post_id = 0 ) {
		$words = self::get_post_word_count( $post_id );

		if ( $words < 1 ) {
			return '';
		}

		/* translators: %s: word count */
		return sprintf( __( '%s words', 'foundation-elementor-plus' ), number_format_i18n( $words ) );
	}

	private static function get_current_author_name( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id ) {
			return '';
		}

		$author_id = (int) get_post_field( 'post_author', $post_id );

		if ( $author_id < 1 ) {
			return '';
		}

		return wp_strip_all_tags( get_the_author_meta( 'display_name', $author_id ) );
	}

	private static function get_current_author_avatar_url( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id ) {
			return '';
		}

		$author_id = (int) get_post_field( 'post_author', $post_id );

		if ( $author_id < 1 ) {
			return '';
		}

		return (string) get_avatar_url(
			$author_id,
			array(
				'size' => 96,
			)
		);
	}

	private static function get_current_author_url( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id ) {
			return '';
		}

		$author_id = (int) get_post_field( 'post_author', $post_id );

		if ( $author_id < 1 ) {
			return '';
		}

		return (string) get_author_posts_url( $author_id );
	}

	private static function render_blog_post_meta_row( $preset_key, array $settings, array $token_context = array() ) {
		if ( 'blog_post' !== $preset_key ) {
			return '';
		}

		$visibility = isset( $settings['blog_post_meta_visibility'] ) ? (string) $settings['blog_post_meta_visibility'] : 'show';

		if ( 'hide' === $visibility || '0' === $visibility ) {
			return '';
		}

		$label = trim( wp_strip_all_tags( self::replace_tokens( (string) ( $settings['blog_post_meta_label'] ?? '{current_category}' ), $token_context ) ) );
		$primary_pill = trim( wp_strip_all_tags( self::replace_tokens( (string) ( $settings['blog_post_meta_pill_primary'] ?? '{current_read_time}' ), $token_context ) ) );
		$secondary_pill = trim( wp_strip_all_tags( self::replace_tokens( (string) ( $settings['blog_post_meta_pill_secondary'] ?? '{current_word_count}' ), $token_context ) ) );

		if ( '' === $label && '' === $primary_pill && '' === $secondary_pill ) {
			return '';
		}

		$output  = '<div class="foundation-inkfire-post-meta-strip">';

		if ( '' !== $label ) {
			$output .= '<p class="foundation-inkfire-post-meta-label">' . esc_html( $label ) . '</p>';
		}

		if ( '' !== $primary_pill || '' !== $secondary_pill ) {
			$output .= '<div class="foundation-inkfire-post-meta-pills">';

			if ( '' !== $primary_pill ) {
				$output .= '<span class="foundation-inkfire-pill foundation-inkfire-pill--blog-stat">' . esc_html( $primary_pill ) . '</span>';
			}

			if ( '' !== $secondary_pill ) {
				$output .= '<span class="foundation-inkfire-pill foundation-inkfire-pill--blog-stat">' . esc_html( $secondary_pill ) . '</span>';
			}

			$output .= '</div>';
		}

		$output .= '</div>';

		return $output;
	}

	private static function render_blog_post_author_strip( $preset_key, array $settings, array $token_context = array() ) {
		if ( 'blog_post' !== $preset_key ) {
			return '';
		}

		$visibility = isset( $settings['blog_post_author_visibility'] ) ? (string) $settings['blog_post_author_visibility'] : 'show';

		if ( 'hide' === $visibility || '0' === $visibility ) {
			return '';
		}

		$prefix = trim( wp_strip_all_tags( self::replace_tokens( (string) ( $settings['blog_post_author_prefix'] ?? 'Written by' ), $token_context ) ) );
		$name = trim( wp_strip_all_tags( self::replace_tokens( (string) ( $settings['blog_post_author_name'] ?? '{current_author_name}' ), $token_context ) ) );
		$avatar_url = trim( self::replace_tokens( (string) ( $settings['blog_post_author_avatar_url'] ?? '{current_author_avatar_url}' ), $token_context ) );
		$link_url = trim( self::replace_tokens( (string) ( $settings['blog_post_author_link_url'] ?? '{current_author_url}' ), $token_context ) );

		if ( '' === $name ) {
			return '';
		}

		$avatar_markup = '';

		if ( '' !== $avatar_url ) {
			$avatar_markup = sprintf(
				'<img class="foundation-inkfire-author-avatar" src="%1$s" alt="%2$s" loading="lazy">',
				esc_url( $avatar_url ),
				esc_attr( $name )
			);
		}

		$copy_markup  = '<div class="foundation-inkfire-author-copy">';
		if ( '' !== $prefix ) {
			$copy_markup .= '<span class="foundation-inkfire-author-prefix">' . esc_html( $prefix ) . '</span>';
		}
		$copy_markup .= '<span class="foundation-inkfire-author-name">' . esc_html( $name ) . '</span>';
		$copy_markup .= '</div>';

		$inner_markup = $avatar_markup . $copy_markup;

		if ( '' !== $link_url && '#' !== $link_url ) {
			return sprintf(
				'<div class="foundation-inkfire-author-strip"><a class="foundation-inkfire-author-link" href="%1$s">%2$s</a></div>',
				esc_url( $link_url ),
				$inner_markup
			);
		}

		return '<div class="foundation-inkfire-author-strip"><div class="foundation-inkfire-author-link foundation-inkfire-author-link--static">' . $inner_markup . '</div></div>';
	}

	private static function normalize_icon_markup( $html ) {
		return strtr(
			(string) $html,
			array(
				'class="fa-solid '   => 'class="fa-solid fas ',
				"class='fa-solid "   => "class='fa-solid fas ",
				'class="fa-regular ' => 'class="fa-regular far ',
				"class='fa-regular " => "class='fa-regular far ",
				'class="fa-brands '  => 'class="fa-brands fab ',
				"class='fa-brands "  => "class='fa-brands fab ",
			)
		);
	}

	private static function markup_uses_fontawesome( $html ) {
		return is_string( $html ) && ( false !== strpos( $html, 'fa-' ) || false !== strpos( $html, 'fas ' ) || false !== strpos( $html, 'far ' ) || false !== strpos( $html, 'fab ' ) );
	}

	private static function build_upload_url( $relative_path ) {
		return home_url( '/wp-content/uploads' . $relative_path );
	}

	private static function render_team_hero() {
		$settings      = self::get_settings();
		$team_images   = self::get_lines( $settings['team_images'] );
		$team_link_url = ! empty( $settings['team_link_url'] ) ? $settings['team_link_url'] : '/about-us/meet-the-team/';

		if ( empty( $team_images ) ) {
			return '';
		}

		$output  = '<div class="foundation-inkfire-avatar-group" role="group" aria-label="Inkfire team: disabled-led creative and tech experts">';
		$output .= '<div class="foundation-inkfire-avatars" aria-hidden="true">';

		foreach ( $team_images as $index => $url ) {
			$margin  = 0 === $index ? '0' : '-15px';
			$output .= '<img src="' . esc_url( $url ) . '" alt="" aria-hidden="true" loading="lazy" style="width:45px;height:45px;border-radius:50%;object-fit:cover;border:3px solid #11121b;margin-left:' . esc_attr( $margin ) . ';position:relative;z-index:' . ( 10 - (int) $index ) . ';box-sizing:content-box;display:block;">';
		}

		$output .= '</div>';
		$output .= '<a href="' . esc_url( $team_link_url ) . '" class="foundation-inkfire-meet-team-link" aria-label="Meet the Inkfire team">';
		$output .= '<span class="foundation-inkfire-meet-text">' . esc_html( $settings['team_link_text'] ) . '</span>';
		$output .= '<span class="foundation-inkfire-meet-arrow"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>';
		$output .= '</a>';
		$output .= '</div>';

		return $output;
	}

	private static function render_project_team_hero( $preset_key, array $overrides = array() ) {
		$context_post_id      = self::get_render_context_post_id( $preset_key );
		$is_portfolio_context = $context_post_id && 'ink_portfolio' === get_post_type( $context_post_id );
		$is_editor_preview    = self::is_elementor_editor_preview();

		if ( 'portfolio_post' !== $preset_key || ( ! $is_portfolio_context && ! $is_editor_preview ) ) {
			return '';
		}

		$post_id = $context_post_id ? $context_post_id : (int) get_the_ID();
		$team_enabled = (string) get_post_meta( $post_id, '_ink_portfolio_hero_team_enabled', true );
		$visibility   = isset( $overrides['visibility'] ) ? trim( (string) $overrides['visibility'] ) : '';

		if ( ! $is_portfolio_context && ! $is_editor_preview ) {
			return '';
		}

		if ( 'hide' === $visibility || '0' === $visibility ) {
			return '';
		}

		if ( $is_portfolio_context && ( '' === $visibility || 'post' === $visibility ) ) {
			if ( '0' === $team_enabled ) {
				return '';
			}
		}

		$departments = class_exists( __NAMESPACE__ . '\\Team_Inline_Images' )
			? Team_Inline_Images::get_department_options()
			: array(
				'all'        => __( 'All Team', 'foundation-elementor-plus' ),
				'management' => __( 'Management', 'foundation-elementor-plus' ),
				'it'         => __( 'IT', 'foundation-elementor-plus' ),
				'web'        => __( 'Web', 'foundation-elementor-plus' ),
				'marketing'  => __( 'Marketing', 'foundation-elementor-plus' ),
				'branding'   => __( 'Branding', 'foundation-elementor-plus' ),
			);
		$department_setting = isset( $overrides['department'] ) ? trim( (string) $overrides['department'] ) : '';
		$department         = 'all';

		if ( $is_portfolio_context ) {
			$department = ( '' === $department_setting || 'post' === $department_setting )
				? sanitize_key( (string) get_post_meta( $post_id, '_ink_portfolio_hero_team_department', true ) )
				: sanitize_key( $department_setting );
		} elseif ( $is_editor_preview ) {
			$department = ( '' === $department_setting || 'post' === $department_setting )
				? 'all'
				: sanitize_key( $department_setting );
		}

		if ( ! isset( $departments[ $department ] ) ) {
			$department = 'all';
		}

		if ( ! shortcode_exists( 'ink_team' ) ) {
			return '';
		}

		$settings       = self::get_settings();
		$team_link_url  = ! empty( $settings['team_link_url'] ) ? $settings['team_link_url'] : '/about-us/meet-the-team/';
		$avatars_markup = trim( do_shortcode( '[ink_team department="' . $department . '" class="foundation-team-inline--hero"]' ) );

		if ( '' === $avatars_markup ) {
			return '';
		}

		$output  = '<div class="foundation-inkfire-avatar-group foundation-inkfire-avatar-group--project">';
		$output .= '<span class="foundation-inkfire-meet-text">' . esc_html__( 'Worked on this', 'foundation-elementor-plus' ) . '</span>';
		$output .= '<a href="' . esc_url( $team_link_url ) . '" class="foundation-inkfire-team-strip-link" aria-label="' . esc_attr__( 'Meet the team', 'foundation-elementor-plus' ) . '">';
		$output .= $avatars_markup;
		$output .= '</a>';
		$output .= '</div>';

		return $output;
	}

	private static function is_portfolio_editorial_context( $preset_key, $context_post_id = 0 ) {
		return 'portfolio_post' === $preset_key && $context_post_id > 0 && 'ink_portfolio' === get_post_type( $context_post_id );
	}

	private static function get_portfolio_editorial_meta_value( $post_id, $field_key ) {
		return trim( (string) get_post_meta( $post_id, '_ink_portfolio_' . $field_key, true ) );
	}

	private static function get_portfolio_editorial_live_site_url( $post_id ) {
		$post_id  = (int) $post_id;
		$live_url = self::get_portfolio_editorial_meta_value( $post_id, 'hero_live_site_url' );

		if ( '' !== $live_url ) {
			return $live_url;
		}

		$card_link_url = trim( (string) get_post_meta( $post_id, '_ink_portfolio_card_link_url', true ) );
		$permalink     = (string) get_permalink( $post_id );

		if ( '' !== $card_link_url && $card_link_url !== $permalink ) {
			return $card_link_url;
		}

		return $permalink;
	}

	private static function get_portfolio_editorial_link_label( $url ) {
		$parts = wp_parse_url( (string) $url );

		if ( empty( $parts['host'] ) ) {
			return wp_strip_all_tags( (string) $url );
		}

		$host = preg_replace( '/^www\./i', '', (string) $parts['host'] );
		$path = isset( $parts['path'] ) ? trim( (string) $parts['path'], '/' ) : '';

		if ( '' !== $path ) {
			return $host . '/' . $path;
		}

		return $host;
	}

	private static function render_portfolio_editorial_detail_value( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		$lines = preg_split( '/\r\n|\r|\n/', $value );
		$lines = array_values(
			array_filter(
				array_map(
					static function( $line ) {
						return trim( (string) $line );
					},
					(array) $lines
				)
			)
		);

		if ( empty( $lines ) ) {
			return esc_html( $value );
		}

		return implode(
			'<br>',
			array_map(
				'esc_html',
				$lines
			)
		);
	}

	private static function render_portfolio_editorial_service_value( $value ) {
		$lines = preg_split( '/\r\n|\r|\n/', trim( (string) $value ) );
		$lines = array_values(
			array_filter(
				array_map(
					static function( $line ) {
						return trim( (string) $line );
					},
					(array) $lines
				)
			)
		);

		if ( empty( $lines ) ) {
			return '';
		}

		return esc_html( implode( ' · ', $lines ) );
	}

	private static function render_portfolio_editorial_details_card( $post_id ) {
		if ( $post_id < 1 || 'ink_portfolio' !== get_post_type( $post_id ) ) {
			return '';
		}

		$client     = self::get_portfolio_editorial_meta_value( $post_id, 'hero_client' );
		$industry   = self::get_portfolio_editorial_meta_value( $post_id, 'hero_industry' );
		$location   = self::get_portfolio_editorial_meta_value( $post_id, 'hero_location' );
		$services   = self::get_portfolio_editorial_meta_value( $post_id, 'hero_services' );
		$live_url   = self::get_portfolio_editorial_meta_value( $post_id, 'hero_live_site_url' );
		$live_label = self::get_portfolio_editorial_meta_value( $post_id, 'hero_live_site_label' );
		$permalink  = (string) get_permalink( $post_id );

		if ( '' === $client ) {
			$client = get_the_title( $post_id );
		}

		if ( '' === $live_url ) {
			$candidate_url = trim( (string) get_post_meta( $post_id, '_ink_portfolio_card_link_url', true ) );

			if ( '' !== $candidate_url && $candidate_url !== $permalink ) {
				$live_url = $candidate_url;
			}
		}

		if ( '' === $live_label && '' !== $live_url ) {
			$live_label = self::get_portfolio_editorial_link_label( $live_url );
		}

		$rows = array(
			array(
				'key'   => 'client',
				'label' => __( 'Client', 'foundation-elementor-plus' ),
				'value' => $client,
			),
			array(
				'key'   => 'industry',
				'label' => __( 'Industry', 'foundation-elementor-plus' ),
				'value' => $industry,
			),
			array(
				'key'   => 'location',
				'label' => __( 'Location', 'foundation-elementor-plus' ),
				'value' => $location,
			),
			array(
				'key'   => 'services',
				'label' => __( 'Services', 'foundation-elementor-plus' ),
				'value' => $services,
			),
		);

		$output = '<div class="foundation-inkfire-portfolio-details-card">';
		$output .= '<div class="foundation-inkfire-portfolio-details-card__head">';
		$output .= '<p class="foundation-inkfire-portfolio-details-card__eyebrow">' . esc_html__( 'Project details', 'foundation-elementor-plus' ) . '</p>';
		$output .= '</div>';
		$output .= '<div class="foundation-inkfire-portfolio-details-card__body">';

		foreach ( $rows as $row ) {
			if ( '' === trim( (string) $row['value'] ) ) {
				continue;
			}

			$output .= '<div class="foundation-inkfire-portfolio-details-card__row">';
			$output .= '<span class="foundation-inkfire-portfolio-details-card__label">' . esc_html( (string) $row['label'] ) . '</span>';
			if ( 'services' === ( $row['key'] ?? '' ) ) {
				$output .= '<span class="foundation-inkfire-portfolio-details-card__value">' . self::render_portfolio_editorial_service_value( (string) $row['value'] ) . '</span>';
			} else {
				$output .= '<span class="foundation-inkfire-portfolio-details-card__value">' . self::render_portfolio_editorial_detail_value( (string) $row['value'] ) . '</span>';
			}
			$output .= '</div>';
		}

		if ( '' !== $live_url ) {
			$output .= '<div class="foundation-inkfire-portfolio-details-card__row">';
			$output .= '<span class="foundation-inkfire-portfolio-details-card__label">' . esc_html__( 'Live site', 'foundation-elementor-plus' ) . '</span>';
			$output .= '<a class="foundation-inkfire-portfolio-details-card__value foundation-inkfire-portfolio-details-card__value--link" href="' . esc_url( $live_url ) . '" target="_blank" rel="noopener">';
			$output .= esc_html( $live_label );
			$output .= '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"></path></svg>';
			$output .= '</a>';
			$output .= '</div>';
		}

		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	private static function render_portfolio_editorial_stats_strip( $post_id ) {
		if ( $post_id < 1 || 'ink_portfolio' !== get_post_type( $post_id ) ) {
			return '';
		}

		$items = array();

		for ( $index = 1; $index <= 5; $index++ ) {
			$value = self::get_portfolio_editorial_meta_value( $post_id, 'hero_stat_' . $index . '_value' );
			$label = self::get_portfolio_editorial_meta_value( $post_id, 'hero_stat_' . $index . '_label' );

			if ( '' === $value || '' === $label ) {
				continue;
			}

			$items[] = array(
				'value' => $value,
				'label' => $label,
			);
		}

		if ( empty( $items ) ) {
			return '';
		}

		$output = '<div class="foundation-inkfire-portfolio-stats" aria-label="' . esc_attr__( 'Project highlights', 'foundation-elementor-plus' ) . '">';

		foreach ( $items as $item ) {
			$output .= '<div class="foundation-inkfire-portfolio-stat">';
			$output .= '<span class="foundation-inkfire-portfolio-stat__value">' . esc_html( (string) $item['value'] ) . '</span>';
			$output .= '<span class="foundation-inkfire-portfolio-stat__label">' . esc_html( (string) $item['label'] ) . '</span>';
			$output .= '</div>';
		}

		$output .= '</div>';

		return $output;
	}

	private static function is_elementor_editor_preview() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return false;
		}

		$plugin = \Elementor\Plugin::$instance;

		if ( isset( $plugin->editor ) && method_exists( $plugin->editor, 'is_edit_mode' ) && $plugin->editor->is_edit_mode() ) {
			return true;
		}

		if ( isset( $plugin->preview ) && method_exists( $plugin->preview, 'is_preview_mode' ) && $plugin->preview->is_preview_mode() ) {
			return true;
		}

		return false;
	}

	private static function get_render_context_post_id( $preset_key = '' ) {
		if ( is_singular() ) {
			return (int) get_the_ID();
		}

		$preview_post_type = '';

		if ( 'portfolio_post' === $preset_key ) {
			$preview_post_type = 'ink_portfolio';
		} elseif ( 'blog_post' === $preset_key ) {
			$preview_post_type = 'post';
		}

		if ( '' === $preview_post_type || ! self::is_elementor_editor_preview() ) {
			return 0;
		}

		$preview_post_id = self::get_elementor_preview_post_id( $preview_post_type );

		if ( $preview_post_id > 0 ) {
			return $preview_post_id;
		}

		return self::get_fallback_preview_post_id( $preview_post_type );
	}

	private static function get_posts_archive_label() {
		$posts_page_id = (int) get_option( 'page_for_posts' );

		if ( $posts_page_id > 0 ) {
			$title = get_the_title( $posts_page_id );
			if ( '' !== trim( (string) $title ) ) {
				return wp_strip_all_tags( (string) $title );
			}
		}

		return 'Blog';
	}

	private static function get_posts_archive_url() {
		$posts_page_id = (int) get_option( 'page_for_posts' );

		if ( $posts_page_id > 0 ) {
			$permalink = get_permalink( $posts_page_id );
			if ( $permalink ) {
				return $permalink;
			}
		}

		return home_url( '/blog/' );
	}

	private static function get_elementor_preview_post_id( $required_post_type = '' ) {
		if ( ! self::is_elementor_editor_preview() || ! class_exists( '\Elementor\Plugin' ) ) {
			return 0;
		}

		$plugin = \Elementor\Plugin::$instance;

		if ( ! isset( $plugin->documents ) || ! method_exists( $plugin->documents, 'get_current' ) ) {
			return 0;
		}

		$document = $plugin->documents->get_current();

		if ( ! $document || ! method_exists( $document, 'get_settings' ) ) {
			return 0;
		}

		$preview_id   = (int) $document->get_settings( 'preview_id' );
		$preview_type = (string) $document->get_settings( 'preview_type' );

		if ( $preview_id <= 0 ) {
			return 0;
		}

		if ( '' !== $required_post_type ) {
			if ( 'single/' . $required_post_type === $preview_type ) {
				return $preview_id;
			}

			if ( $required_post_type !== get_post_type( $preview_id ) ) {
				return 0;
			}
		}

		return $preview_id;
	}

	private static function get_fallback_preview_post_id( $post_type ) {
		$posts = get_posts(
			array(
				'post_type'      => $post_type,
				'post_status'    => array( 'publish', 'draft', 'private' ),
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
			)
		);

		if ( empty( $posts ) ) {
			return 0;
		}

		return (int) $posts[0];
	}

	private static function normalize_logo_urls( $logos ) {
		if ( is_array( $logos ) ) {
			return array_values(
				array_filter(
					array_map(
						static function ( $logo ) {
							if ( is_array( $logo ) && ! empty( $logo['url'] ) ) {
								return trim( (string) $logo['url'] );
							}

							return trim( (string) $logo );
						},
						$logos
					)
				)
			);
		}

		return self::get_lines( $logos );
	}

	private static function render_trust_bar( $trust_html = '', $trust_label = '', $trust_logos = array() ) {
		$settings = self::get_settings();

		if ( '' !== trim( (string) $trust_html ) ) {
			return $trust_html;
		}

		$logos = self::normalize_logo_urls( $trust_logos );
		if ( empty( $logos ) ) {
			$logos = self::get_lines( $settings['trust_logos'] );
		}
		if ( empty( $logos ) ) {
			return '';
		}

		$label   = '' !== trim( (string) $trust_label ) ? (string) $trust_label : (string) $settings['trust_label'];
		$output  = '<div class="foundation-inkfire-trust-bar" aria-label="' . esc_attr( $label ) . '">';
		$output .= '<span class="foundation-inkfire-trust-label">' . esc_html( $label ) . '</span>';
		$output .= '<ul class="foundation-inkfire-logos" style="list-style:none; margin:0; padding:0;">';

		foreach ( $logos as $logo_url ) {
			$output .= '<li class="foundation-inkfire-logo"><img src="' . esc_url( $logo_url ) . '" alt="" loading="lazy" decoding="async"></li>';
		}

		$output .= '</ul></div>';

		return $output;
	}

	private static function parse_features( $features ) {
		$raw_items = preg_split( '/\r\n|\r|\n|\|/', (string) $features );
		$items     = array();

		foreach ( $raw_items as $item ) {
			$item = trim( (string) $item );
			if ( '' === $item ) {
				continue;
			}

			$items[] = $item;
		}

		return $items;
	}

	private static function render_feature_list( $feature_items, $position ) {
		if ( empty( $feature_items ) || 'hidden' === $position ) {
			return '';
		}

		ob_start();
		?>
		<ul class="foundation-inkfire-feature-list foundation-inkfire-feature-list--<?php echo esc_attr( $position ); ?>" aria-label="<?php esc_attr_e( 'Key service highlights', 'foundation-elementor-plus' ); ?>">
			<?php foreach ( $feature_items as $feature_item ) : ?>
				<li class="foundation-inkfire-feature-item"><?php echo esc_html( $feature_item ); ?></li>
			<?php endforeach; ?>
		</ul>
		<?php
		return ob_get_clean();
	}

	private static function enqueue_icon_fonts() {
		$elementor_icon_handles = array(
			'elementor-icons',
			'elementor-icons-fa-solid',
			'elementor-icons-fa-regular',
			'elementor-icons-fa-brands',
			'elementor-icons-shared-0',
			'elementor-icons-shared-1',
			'elementor-icons-shared-2',
			'elementor-icons-shared-3',
		);

		$enqueued = false;

		foreach ( $elementor_icon_handles as $handle ) {
			if ( wp_style_is( $handle, 'registered' ) ) {
				wp_enqueue_style( $handle );
				$enqueued = true;
			}
		}

		unset( $enqueued );
	}
}
