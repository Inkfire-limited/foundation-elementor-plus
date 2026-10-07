<?php
/**
 * Inkfire Linktree shortcode.
 *
 * Plugin-owned implementation for:
 * [inkfire_linktree]
 * [amh_social_quicklinks]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'amh_social_quicklinks_svg' ) ) {
	function amh_social_quicklinks_svg( $icon ) {
		$icons = array(
			'home'      => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 2 11h2v9a1 1 0 0 0 1 1h5v-6h4v6h5a1 1 0 0 0 1-1v-9h2L12 3Z"/></svg>',
			'blog'      => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm0 2v12h16V6H4Zm3 2h6v2H7V8Zm0 4h10v2H7v-2Zm0 4h7v2H7v-2Z"/></svg>',
			'star'      => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 2.5 2.93 5.93 6.55.95-4.74 4.62 1.12 6.53L12 17.45 6.14 20.53l1.12-6.53L2.5 9.38l6.55-.95L12 2.5Z"/></svg>',
			'mail'      => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 6c0-1.1-.9-2-2-2H4C2.9 4 2 4.9 2 6v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6Zm-2 0-8 5-8-5h16Zm0 12H4V8l8 5 8-5v10Z"/></svg>',
			'facebook'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 21v-8.2h2.77l.41-3.2H13.5V7.56c0-.93.26-1.56 1.59-1.56h1.69V3.14c-.82-.09-1.65-.13-2.48-.12-2.45 0-4.13 1.49-4.13 4.24V9.6H7.4v3.2h2.77V21h3.33Z"/></svg>',
			'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7Zm5 3.5A4.5 4.5 0 1 1 7.5 12 4.5 4.5 0 0 1 12 7.5Zm0 2A2.5 2.5 0 1 0 14.5 12 2.5 2.5 0 0 0 12 9.5Zm5.25-3a1.25 1.25 0 1 1-1.25 1.25 1.25 1.25 0 0 1 1.25-1.25Z"/></svg>',
			'linkedin'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.94 8.5A1.56 1.56 0 1 1 6.9 5.37a1.56 1.56 0 0 1 .04 3.13ZM5.5 10h2.88v8.5H5.5V10Zm4.7 0h2.76v1.16h.04c.38-.73 1.32-1.5 2.72-1.5 2.91 0 3.45 1.92 3.45 4.42v4.42h-2.88v-3.92c0-.93-.02-2.13-1.3-2.13s-1.5 1.01-1.5 2.06v3.99H10.2V10Z"/></svg>',
			'tiktok'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3c.28 1.88 1.43 3.46 3 4.32A7.2 7.2 0 0 0 21 8.2v3.07a10.3 10.3 0 0 1-4-.8v5.73a6.2 6.2 0 1 1-6.2-6.2c.28 0 .55.02.82.05v3.15a3.05 3.05 0 1 0 2.38 2.98V3H14Z"/></svg>',
			'x'         => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2H21l-6.02 6.88L22 22h-5.48l-4.29-5.61L7.32 22H4.56l6.44-7.36L2 2h5.62l3.87 5.11L18.244 2Zm-.97 18h1.52L6.8 3.89H5.17L17.274 20Z"/></svg>',
			'youtube'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 7.2a3 3 0 0 0-2.1-2.12C19.55 4.5 12 4.5 12 4.5s-7.55 0-9.4.58A3 3 0 0 0 .5 7.2 31.3 31.3 0 0 0 0 12a31.3 31.3 0 0 0 .5 4.8 3 3 0 0 0 2.1 2.12C4.45 19.5 12 19.5 12 19.5s7.55 0 9.4-.58a3 3 0 0 0 2.1-2.12A31.3 31.3 0 0 0 24 12a31.3 31.3 0 0 0-.5-4.8ZM9.6 15.1V8.9l5.4 3.1-5.4 3.1Z"/></svg>',
			'arrow'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m13.17 5 1.41 1.41L10.99 10H20v2h-9.01l3.59 3.59L13.17 17l-6-6 6-6Z" transform="translate(24 0) scale(-1 1)"/></svg>',
			'chevron'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9.5 12 15.5 18 9.5"></path></svg>',
		);

		return $icons[ $icon ] ?? $icons['arrow'];
	}
}

if ( ! function_exists( 'amh_social_quicklinks_default_blog_url' ) ) {
	function amh_social_quicklinks_default_blog_url( $slug = 'news' ) {
		$term = get_term_by( 'slug', sanitize_title( $slug ), 'category' );

		if ( $term && ! is_wp_error( $term ) ) {
			$link = get_term_link( $term, 'category' );
			if ( ! is_wp_error( $link ) ) {
				return $link;
			}
		}

		return home_url( '/category/' . sanitize_title( $slug ) . '/' );
	}
}

if ( ! function_exists( 'amh_social_quicklinks_awards' ) ) {
	function amh_social_quicklinks_awards() {
		$awards = array(
			array(
				'src'   => home_url( '/wp-content/uploads/2026/02/WINNER-01.png' ),
				'alt'   => 'Scope Awards 2025 winner mark',
				'theme' => 'is-ink',
			),
			array(
				'src'   => home_url( '/wp-content/uploads/2026/02/WINNER-02.png' ),
				'alt'   => 'Scope Awards 2025 recognition artwork',
				'theme' => 'is-ink-soft',
			),
			array(
				'src'   => home_url( '/wp-content/uploads/2025/11/dc_badge1.png' ),
				'alt'   => 'Disability Confident Employer badge',
				'theme' => 'is-paper',
			),
			array(
				'src'   => home_url( '/wp-content/uploads/2025/11/8ea3eae8-6680-4be4-9082-752dd23a2a68-Photoroom-e1764035925813.png' ),
				'alt'   => 'ICO registered logo',
				'theme' => 'is-white-mark',
			),
		);

		return apply_filters( 'inkfire_linktree_awards', $awards );
	}
}

if ( ! function_exists( 'amh_social_quicklinks_defaults' ) ) {
	function amh_social_quicklinks_defaults() {
		$custom_logo_id  = (int) get_theme_mod( 'custom_logo' );
		$custom_logo_url = $custom_logo_id ? wp_get_attachment_image_url( $custom_logo_id, 'full' ) : '';

		return array(
			'tagline'            => 'Inkfire quicklinks',
			'title'              => 'A quick route into Inkfire',
			'bio'                => 'Disabled-led, inclusive, and built around real people. Our team brings together diverse expertise to create digital work that’s accessible, practical, and considered.',
			'logo_url'           => $custom_logo_url ? $custom_logo_url : home_url( '/wp-content/uploads/2024/01/Primary-Logo-White.svg' ),
			'icon_url'           => home_url( '/wp-content/uploads/2025/11/IMG_1089.png' ),
			'footer_logo_url'    => home_url( '/wp-content/uploads/2025/11/8ea3eae8-6680-4be4-9082-752dd23a2a68-Photoroom-e1764035925813.png' ),
			'home_url'           => home_url( '/' ),
			'blog_category_slug' => 'news',
			'blog_url'           => amh_social_quicklinks_default_blog_url( 'news' ),
			'review_url'         => 'https://www.google.com/search?q=inkfire+review&num=10&sca_esv=a34d0b4f4b0c4824&biw=1720&bih=966&sxsrf=ANbL-n4gyMjmUBLyZ9WroS5apG18mMx-FQ%3A1774371921137&ei=UcTCaaSBCIfqhbIP3_LU8Qk&ved=0ahUKEwjk7bvAgrmTAxUHdUEAHV85NZ4Q4dUDCBE&uact=5&oq=inkfire+review&gs_lp=Egxnd3Mtd2l6LXNlcnAiDmlua2ZpcmUgcmV2aWV3MgUQABjvBTIIEAAYgAQYogQyCBAAGIAEGKIEMggQABiABBiiBEj3BlDCBVjCBXACeACQAQCYAVugAVuqAQExuAEDyAEA-AEBmAIDoAJmwgIIEAAYsAMY7wXCAgsQABiABBiwAxiiBMICCxAAGLADGKIEGIkFmAMAiAYBkAYFkgcBM6AHrAKyBwExuAdhwgcFMC4yLjHIBweACAA&sclient=gws-wiz-serp&lqi=Cg5pbmtmaXJlIHJldmlldyICOAFIx_OEy523gIAIWg0QABgAIgdpbmtmaXJlkgENZGVzaWduX2FnZW5jeQ#rlimm=13572927679599070356',
			'facebook_url'       => 'https://facebook.com/inkfirelimited',
			'instagram_url'      => 'https://www.instagram.com/inkfirelimited/',
			'linkedin_url'       => 'https://uk.linkedin.com/company/inkfire',
			'x_url'              => 'https://twitter.com/Inkfirelimited',
			'tiktok_url'         => 'https://www.tiktok.com/@inkfirelimited',
			'youtube_url'        => 'https://www.youtube.com/@mali.and.m.e',
			'youtube_id'         => 'A_WiCvOV72g',
			'newsletter_form_id' => 'lo07zevpeekjkb1p38j',
			'newsletter_title'   => 'Join the Inkfire newsletter',
			'newsletter_copy'    => 'Get our latest updates, opportunities, and behind-the-scenes thoughts without having to go looking for them.',
			'newsletter_note'    => 'We keep it human, occasional, and worth opening.',
			'contact_phone'      => '+44 (0)333 613 4653',
			'contact_email'      => 'hello@mediumblue-seal-942128.hostingersite.com',
			'contact_address'    => '9 Kingswell Road, Ensbury Park, Bournemouth, BH10 5DF',
			'contact_hours'      => 'Mon – Fri, 9am – 5pm (UK time)',
			'company_number'     => '15153305',
			'vat_number'         => 'GB483189752',
		);
	}
}

if ( ! function_exists( 'amh_quicklinks_is_target_page' ) ) {
	function amh_quicklinks_is_target_page() {
		if ( is_admin() || wp_doing_ajax() ) {
			return false;
		}

		if ( is_page( 5308 ) || is_page( 10777 ) || is_page( 'social-quicklinks' ) || is_page( 'linktree' ) ) {
			return true;
		}

		global $post;
		if ( ! ( $post instanceof WP_Post ) ) {
			return false;
		}

		$content = (string) $post->post_content;

		return has_shortcode( $content, 'amh_social_quicklinks' ) || has_shortcode( $content, 'inkfire_linktree' );
	}
}

if ( ! function_exists( 'amh_quicklinks_schema_payload' ) ) {
	function amh_quicklinks_schema_payload() {
		$defaults  = amh_social_quicklinks_defaults();
		$page_url  = get_permalink() ? get_permalink() : home_url( '/linktree/' );
		$home_url  = home_url( '/' );
		$site_name = get_bloginfo( 'name', 'display' );
		$in_lang   = str_replace( '_', '-', (string) get_locale() );
		$modified  = is_singular() ? get_post_modified_time( 'c', true ) : gmdate( 'c' );
		$title     = 'Inkfire Quicklinks | Inkfire Limited';
		$desc      = wp_strip_all_tags( (string) $defaults['bio'] );
		$same_as   = array_values(
			array_filter(
				array(
					$defaults['facebook_url'],
					$defaults['instagram_url'],
					$defaults['linkedin_url'],
					$defaults['x_url'],
					$defaults['tiktok_url'],
					$defaults['youtube_url'],
				)
			)
		);

		$quicklinks = array(
			array( 'name' => 'Visit homepage', 'url' => $defaults['home_url'] ),
			array( 'name' => 'Read What\'s New at Inkfire', 'url' => $defaults['blog_url'] ),
			array( 'name' => 'Leave a Google review', 'url' => $defaults['review_url'] ),
			array( 'name' => 'Facebook', 'url' => $defaults['facebook_url'] ),
			array( 'name' => 'Instagram', 'url' => $defaults['instagram_url'] ),
			array( 'name' => 'LinkedIn', 'url' => $defaults['linkedin_url'] ),
			array( 'name' => 'X', 'url' => $defaults['x_url'] ),
			array( 'name' => 'TikTok', 'url' => $defaults['tiktok_url'] ),
			array( 'name' => 'YouTube', 'url' => $defaults['youtube_url'] ),
		);

		$item_list = array();
		$position  = 1;
		foreach ( $quicklinks as $item ) {
			if ( empty( $item['url'] ) ) {
				continue;
			}

			$item_list[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => $item['name'],
				'url'      => $item['url'],
			);
		}

		return array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type'      => 'WebSite',
					'@id'        => trailingslashit( $home_url ) . '#website',
					'url'        => $home_url,
					'name'       => $site_name,
					'publisher'  => array( '@id' => trailingslashit( $home_url ) . '#organization' ),
					'inLanguage' => $in_lang,
				),
				array(
					'@type'  => 'Organization',
					'@id'    => trailingslashit( $home_url ) . '#organization',
					'name'   => 'Inkfire Limited',
					'url'    => $home_url,
					'logo'   => array(
						'@type' => 'ImageObject',
						'url'   => $defaults['logo_url'],
					),
					'sameAs' => $same_as,
				),
				array(
					'@type'        => 'WebPage',
					'@id'          => trailingslashit( $page_url ) . '#webpage',
					'url'          => $page_url,
					'name'         => $title,
					'description'  => $desc,
					'dateModified' => $modified,
					'isPartOf'     => array( '@id' => trailingslashit( $home_url ) . '#website' ),
					'about'        => array( '@id' => trailingslashit( $home_url ) . '#organization' ),
					'inLanguage'   => $in_lang,
				),
				array(
					'@type'           => 'ItemList',
					'@id'             => trailingslashit( $page_url ) . '#quicklinks',
					'name'            => 'Inkfire quicklinks',
					'itemListElement' => $item_list,
				),
			),
		);
	}
}

if ( ! function_exists( 'amh_quicklinks_output_schema' ) ) {
	function amh_quicklinks_output_schema() {
		if ( ! amh_quicklinks_is_target_page() ) {
			return;
		}

		echo "\n" . '<script type="application/ld+json" id="amh-quicklinks-schema">' . wp_json_encode( amh_quicklinks_schema_payload(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}

	add_action( 'wp_head', 'amh_quicklinks_output_schema', 35 );
}

if ( ! function_exists( 'amh_quicklinks_wp_robots' ) ) {
	function amh_quicklinks_wp_robots( $robots ) {
		if ( ! amh_quicklinks_is_target_page() ) {
			return $robots;
		}

		unset( $robots['noindex'], $robots['nofollow'] );
		$robots['index']             = true;
		$robots['follow']            = true;
		$robots['max-image-preview'] = 'large';
		$robots['max-snippet']       = -1;
		$robots['max-video-preview'] = -1;

		return $robots;
	}

	add_filter( 'wp_robots', 'amh_quicklinks_wp_robots', 20 );
}

if ( ! function_exists( 'amh_quicklinks_output_meta_fallback' ) ) {
	function amh_quicklinks_output_meta_fallback() {
		if ( ! amh_quicklinks_is_target_page() ) {
			return;
		}

		if ( defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) ) {
			return;
		}

		$defaults = amh_social_quicklinks_defaults();
		$title    = 'Inkfire Quicklinks | Inkfire Limited';
		$desc     = wp_strip_all_tags( (string) $defaults['bio'] );
		$url      = get_permalink() ? get_permalink() : home_url( '/linktree/' );
		$image    = $defaults['logo_url'];

		echo "\n" . '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
		echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
		echo '<meta property="og:type" content="website">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}

	add_action( 'wp_head', 'amh_quicklinks_output_meta_fallback', 6 );
}

if ( ! function_exists( 'amh_quicklinks_rankmath_title' ) ) {
	function amh_quicklinks_rankmath_title( $title ) {
		if ( ! amh_quicklinks_is_target_page() ) {
			return $title;
		}

		return 'Inkfire Quicklinks | Inkfire Limited';
	}

	add_filter( 'rank_math/frontend/title', 'amh_quicklinks_rankmath_title', 20 );
}

if ( ! function_exists( 'amh_quicklinks_rankmath_description' ) ) {
	function amh_quicklinks_rankmath_description( $description ) {
		if ( ! amh_quicklinks_is_target_page() ) {
			return $description;
		}

		$defaults = amh_social_quicklinks_defaults();
		return wp_strip_all_tags( (string) $defaults['bio'] );
	}

	add_filter( 'rank_math/frontend/description', 'amh_quicklinks_rankmath_description', 20 );
}

if ( ! function_exists( 'amh_quicklinks_rankmath_canonical' ) ) {
	function amh_quicklinks_rankmath_canonical( $canonical ) {
		if ( ! amh_quicklinks_is_target_page() ) {
			return $canonical;
		}

		return get_permalink() ? get_permalink() : home_url( '/linktree/' );
	}

	add_filter( 'rank_math/frontend/canonical', 'amh_quicklinks_rankmath_canonical', 20 );
}

if ( ! function_exists( 'amh_quicklinks_get_stats' ) ) {
	function amh_quicklinks_get_stats() {
		$stats = get_option( 'amh_quicklinks_stats_v1', array() );

		if ( ! is_array( $stats ) ) {
			$stats = array();
		}

		$stats = wp_parse_args(
			$stats,
			array(
				'total_views' => 0,
				'events'      => array(),
				'links'       => array(),
				'last_seen'   => '',
			)
		);

		if ( ! is_array( $stats['events'] ) ) {
			$stats['events'] = array();
		}

		if ( ! is_array( $stats['links'] ) ) {
			$stats['links'] = array();
		}

		return $stats;
	}
}

if ( ! function_exists( 'amh_quicklinks_increment_stat' ) ) {
	function amh_quicklinks_increment_stat( $event, $label = '' ) {
		$event = sanitize_key( $event );
		$label = sanitize_key( $label );

		if ( '' === $event ) {
			return;
		}

		$stats = amh_quicklinks_get_stats();

		if ( 'page_view' === $event ) {
			$stats['total_views'] = (int) $stats['total_views'] + 1;
		}

		$stats['events'][ $event ] = isset( $stats['events'][ $event ] ) ? (int) $stats['events'][ $event ] + 1 : 1;

		if ( '' !== $label ) {
			$stats['links'][ $label ] = isset( $stats['links'][ $label ] ) ? (int) $stats['links'][ $label ] + 1 : 1;
		}

		$stats['last_seen'] = current_time( 'mysql' );
		update_option( 'amh_quicklinks_stats_v1', $stats, false );
	}
}

if ( ! function_exists( 'amh_quicklinks_track_event_ajax' ) ) {
	function amh_quicklinks_track_event_ajax() {
		check_ajax_referer( 'amh_quicklinks_track', 'nonce' );

		$event = isset( $_POST['event'] ) ? sanitize_key( wp_unslash( $_POST['event'] ) ) : '';
		$label = isset( $_POST['label'] ) ? sanitize_key( wp_unslash( $_POST['label'] ) ) : '';

		$allowed_events = array(
			'page_view',
			'link_click',
			'social_click',
			'post_click',
			'newsletter_submit',
			'video_play',
		);

		if ( ! in_array( $event, $allowed_events, true ) ) {
			wp_send_json_error( array( 'message' => 'Invalid event.' ), 400 );
		}

		amh_quicklinks_increment_stat( $event, $label );
		wp_send_json_success();
	}

	add_action( 'wp_ajax_amh_quicklinks_track', 'amh_quicklinks_track_event_ajax' );
	add_action( 'wp_ajax_nopriv_amh_quicklinks_track', 'amh_quicklinks_track_event_ajax' );
}

if ( ! function_exists( 'amh_quicklinks_render_dashboard_widget' ) ) {
	function amh_quicklinks_render_dashboard_widget() {
		$stats = amh_quicklinks_get_stats();
		arsort( $stats['links'] );

		echo '<div style="font-size:13px;line-height:1.6;">';
		echo '<div style="margin:0 0 12px;padding:10px 12px;border-left:6px solid #11b3a1;border-radius:10px;background:linear-gradient(135deg,#151622,#233249);color:#fff;">';
		echo '<strong style="display:block;font-size:14px;">Inkfire Linktree results</strong>';
		echo '<span>This shows how many people viewed the page and what they clicked most.</span>';
		echo '</div>';
		echo '<p><strong>Linktree page:</strong> /linktree/</p>';
		echo '<p><strong>Total views:</strong> ' . esc_html( number_format_i18n( (int) $stats['total_views'] ) ) . '</p>';

		if ( ! empty( $stats['last_seen'] ) ) {
			echo '<p><strong>Last event:</strong> ' . esc_html( mysql2date( 'j M Y H:i', $stats['last_seen'] ) ) . '</p>';
		}

		if ( ! empty( $stats['links'] ) ) {
			echo '<table class="widefat striped" style="margin-top:10px;">';
			echo '<thead><tr><th>What people clicked</th><th style="width:120px;">Clicks</th></tr></thead><tbody>';
			foreach ( $stats['links'] as $label => $count ) {
				echo '<tr><td>' . esc_html( ucwords( str_replace( '_', ' ', (string) $label ) ) ) . '</td><td>' . esc_html( number_format_i18n( (int) $count ) ) . '</td></tr>';
			}
			echo '</tbody></table>';
		} else {
			echo '<p>No click data yet.</p>';
		}

		echo '</div>';
	}
}

if ( ! function_exists( 'amh_quicklinks_register_dashboard_widget' ) ) {
	function amh_quicklinks_register_dashboard_widget() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_add_dashboard_widget(
			'amh_quicklinks_stats_widget',
			'Inkfire Linktree Results',
			'amh_quicklinks_render_dashboard_widget'
		);
	}

	add_action( 'wp_dashboard_setup', 'amh_quicklinks_register_dashboard_widget' );
}

if ( ! function_exists( 'amh_social_quicklinks_shortcode' ) ) {
	function amh_social_quicklinks_shortcode( $atts = array(), $content = null, $tag = 'amh_social_quicklinks' ) {
		$defaults = amh_social_quicklinks_defaults();
		$atts     = shortcode_atts( $defaults, $atts, $tag );
		$uid      = sanitize_html_class( wp_unique_id( 'inkfire-linktree-' ) );
		$nonce    = wp_create_nonce( 'amh_quicklinks_track' );
		$ajax_url = admin_url( 'admin-ajax.php' );

		$primary_links = array(
			array(
				'icon'  => 'home',
				'label' => 'Visit the homepage',
				'meta'  => 'Start with the latest Inkfire pages and services',
				'url'   => esc_url( $atts['home_url'] ),
				'key'   => 'homepage',
				'tone'  => 'is-green',
			),
			array(
				'icon'  => 'blog',
				'label' => 'What’s new at Inkfire',
				'meta'  => 'Fresh updates, news, and recent thinking',
				'url'   => esc_url( $atts['blog_url'] ),
				'key'   => 'blog',
				'tone'  => 'is-orange',
			),
			array(
				'icon'  => 'star',
				'label' => 'Read our Google reviews',
				'meta'  => 'See why clients keep giving us five stars',
				'url'   => esc_url( $atts['review_url'] ),
				'key'   => 'reviews',
				'tone'  => 'is-peach',
			),
		);

		$social_links = array();
		foreach (
			array(
				'instagram' => $atts['instagram_url'],
				'linkedin'  => $atts['linkedin_url'],
				'facebook'  => $atts['facebook_url'],
				'x'         => $atts['x_url'],
				'tiktok'    => $atts['tiktok_url'],
				'youtube'   => $atts['youtube_url'],
			) as $label => $url
		) {
			if ( empty( $url ) ) {
				continue;
			}

			$social_links[] = array(
				'icon'  => $label,
				'label' => 'linkedin' === $label ? 'LinkedIn' : ( 'x' === $label ? 'X' : ucfirst( $label ) ),
				'url'   => esc_url( $url ),
				'key'   => $label,
			);
		}

		$latest_posts = get_posts(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => 3,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'category_name'       => sanitize_title( (string) $atts['blog_category_slug'] ),
			)
		);

		$awards = amh_social_quicklinks_awards();

		$newsletter_markup = '';
		if ( ! empty( $atts['newsletter_form_id'] ) && shortcode_exists( 'sender-form' ) ) {
			$newsletter_markup = trim( do_shortcode( '[sender-form id="' . sanitize_text_field( (string) $atts['newsletter_form_id'] ) . '"]' ) );
		}

		ob_start();
		?>
		<section id="<?php echo esc_attr( $uid ); ?>" class="amh-ql-shell" aria-label="Inkfire quicklinks">
			<div class="amh-ql-backdrop" aria-hidden="true"></div>

			<div class="amh-ql-stage">
				<div class="amh-ql-main amh-ql-glass">
					<div class="amh-ql-head">
						<div class="amh-ql-brand" aria-hidden="true">
							<?php if ( ! empty( $atts['icon_url'] ) ) : ?>
								<img src="<?php echo esc_url( $atts['icon_url'] ); ?>" alt="" class="amh-ql-brand-icon">
							<?php endif; ?>
							<img src="<?php echo esc_url( $atts['logo_url'] ); ?>" alt="Inkfire logo" class="amh-ql-logo">
						</div>
						<p class="amh-ql-tagline"><?php echo esc_html( strtoupper( (string) $atts['tagline'] ) ); ?></p>

						<div class="amh-ql-copy">
							<h1><?php echo esc_html( $atts['title'] ); ?></h1>
							<p class="amh-ql-bio"><?php echo esc_html( $atts['bio'] ); ?></p>
						</div>
					</div>

					<div class="amh-ql-social-panel" aria-label="Inkfire social links">
						<div class="amh-ql-social-row">
							<?php foreach ( $social_links as $social ) : ?>
								<a
									href="<?php echo esc_url( $social['url'] ); ?>"
									class="amh-ql-social-pill"
									target="_blank"
									rel="noopener noreferrer"
									data-amh-track="social_<?php echo esc_attr( $social['key'] ); ?>"
									data-amh-event="social_click"
								>
									<span class="amh-ql-icon" aria-hidden="true"><?php echo amh_social_quicklinks_svg( $social['icon'] ); ?></span>
									<span><?php echo esc_html( $social['label'] ); ?></span>
								</a>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="amh-ql-link-stack">
						<?php foreach ( $primary_links as $link ) : ?>
							<a
								class="amh-ql-link <?php echo esc_attr( $link['tone'] ); ?>"
								href="<?php echo esc_url( $link['url'] ); ?>"
								data-amh-track="primary_<?php echo esc_attr( $link['key'] ); ?>"
								data-amh-event="link_click"
								target="_blank"
								rel="noopener noreferrer"
							>
								<span class="amh-ql-link-icon amh-ql-icon" aria-hidden="true"><?php echo amh_social_quicklinks_svg( $link['icon'] ); ?></span>
								<span class="amh-ql-link-copy">
									<strong><?php echo esc_html( $link['label'] ); ?></strong>
									<span><?php echo esc_html( $link['meta'] ); ?></span>
								</span>
								<span class="amh-ql-link-arrow amh-ql-icon" aria-hidden="true"><?php echo amh_social_quicklinks_svg( 'arrow' ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>

					<div class="amh-ql-middle">
						<div class="amh-ql-video amh-ql-glass-soft">
							<div class="amh-ql-section-head">
								<div>
									<p class="amh-ql-eyebrow">Portfolio reel</p>
								</div>
							</div>
							<div class="amh-ql-video-frame">
								<iframe
									src="https://www.youtube-nocookie.com/embed/<?php echo esc_attr( $atts['youtube_id'] ); ?>?rel=0&modestbranding=1"
									title="Inkfire video"
									loading="lazy"
									referrerpolicy="strict-origin-when-cross-origin"
									allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
									allowfullscreen
									data-amh-track="youtube_video"
									data-amh-event="video_play"
								></iframe>
							</div>
						</div>

						<?php if ( ! empty( $newsletter_markup ) ) : ?>
							<details class="amh-ql-newsletter amh-ql-glass-soft" open>
								<summary class="amh-ql-newsletter-summary">
									<div class="amh-ql-section-head">
										<div>
											<p class="amh-ql-eyebrow">Newsletter</p>
											<h2><?php echo esc_html( $atts['newsletter_title'] ); ?></h2>
										</div>
										<span class="amh-ql-newsletter-chevron amh-ql-icon" aria-hidden="true"><?php echo amh_social_quicklinks_svg( 'chevron' ); ?></span>
									</div>
									<p class="amh-ql-newsletter-copy"><?php echo esc_html( $atts['newsletter_copy'] ); ?></p>
								</summary>
								<div class="amh-ql-newsletter-panel">
									<div class="amh-ql-newsletter-form" data-amh-track="newsletter_form" data-amh-event="newsletter_submit">
										<?php echo $newsletter_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
									<p class="amh-ql-newsletter-note"><?php echo esc_html( $atts['newsletter_note'] ); ?></p>
								</div>
							</details>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $latest_posts ) ) : ?>
						<div class="amh-ql-feed amh-ql-glass-soft">
							<div class="amh-ql-section-head">
								<div>
									<p class="amh-ql-eyebrow">What’s new at Inkfire</p>
									<h2>Latest updates</h2>
								</div>
								<a href="<?php echo esc_url( $atts['blog_url'] ); ?>" class="amh-ql-inline-link" data-amh-track="open_blog_archive" data-amh-event="link_click">Open category</a>
							</div>

							<div class="amh-ql-posts">
								<?php foreach ( $latest_posts as $post_item ) : ?>
									<a href="<?php echo esc_url( get_permalink( $post_item ) ); ?>" class="amh-ql-post-card" data-amh-track="latest_post_<?php echo esc_attr( (string) $post_item->ID ); ?>" data-amh-event="post_click">
										<?php if ( has_post_thumbnail( $post_item ) ) : ?>
											<img
												src="<?php echo esc_url( get_the_post_thumbnail_url( $post_item, 'medium_large' ) ); ?>"
												alt=""
												class="amh-ql-post-thumb"
												loading="lazy"
											>
										<?php endif; ?>
										<div class="amh-ql-post-copy">
											<strong><?php echo esc_html( get_the_title( $post_item ) ); ?></strong>
											<span><?php echo esc_html( get_the_date( 'j M Y', $post_item ) ); ?></span>
										</div>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $awards ) ) : ?>
						<div class="amh-ql-trust amh-ql-glass-soft">
							<div class="amh-ql-section-head">
								<div>
									<p class="amh-ql-eyebrow">Awards & recognition</p>
									<h2>Recognition we’re proud of</h2>
								</div>
							</div>

							<div class="amh-ql-trust-grid">
								<?php foreach ( $awards as $logo ) : ?>
									<div class="amh-ql-trust-item <?php echo esc_attr( $logo['theme'] ); ?>">
										<img src="<?php echo esc_url( $logo['src'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" loading="lazy">
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<div class="amh-ql-contact amh-ql-glass-soft">
						<div class="amh-ql-section-head">
							<div>
								<p class="amh-ql-eyebrow">Contact & company details</p>
								<h2>Need a quick route to us?</h2>
							</div>
						</div>

						<div class="amh-ql-contact-grid">
							<div class="amh-ql-contact-item">
								<strong>Call</strong>
								<a href="tel:+443336134653"><?php echo esc_html( $atts['contact_phone'] ); ?></a>
							</div>
							<div class="amh-ql-contact-item">
								<strong>Email</strong>
								<a href="mailto:<?php echo esc_attr( antispambot( $atts['contact_email'] ) ); ?>"><?php echo esc_html( antispambot( $atts['contact_email'] ) ); ?></a>
							</div>
							<div class="amh-ql-contact-item">
								<strong>Office hours</strong>
								<span><?php echo esc_html( $atts['contact_hours'] ); ?></span>
							</div>
							<div class="amh-ql-contact-item">
								<strong>Address</strong>
								<address><?php echo esc_html( $atts['contact_address'] ); ?></address>
							</div>
						</div>

						<div class="amh-ql-company-strip">
							<div class="amh-ql-company-meta">
								<span><strong>Company No:</strong> <?php echo esc_html( $atts['company_number'] ); ?></span>
								<span><strong>VAT:</strong> <?php echo esc_html( $atts['vat_number'] ); ?></span>
							</div>
						</div>
					</div>

					<span class="amh-ql-sr" data-amh-live aria-live="polite"></span>
				</div>
			</div>

			<style>
				#<?php echo esc_attr( $uid ); ?> {
					--amh-ql-bg-a: #15454b;
					--amh-ql-bg-b: #1e6167;
					--amh-ql-bg-c: #0e8c78;
					--amh-ql-bg-d: #01ae93;
					--amh-ql-shell: rgba(18, 23, 40, 0.82);
					--amh-ql-shell-soft: rgba(24, 30, 50, 0.72);
					--amh-ql-border: rgba(255, 255, 255, 0.12);
					--amh-ql-border-strong: rgba(255, 255, 255, 0.18);
					--amh-ql-shadow: 0 24px 56px rgba(0, 0, 0, 0.34);
					--amh-ql-text: #f8f5f2;
					--amh-ql-muted: rgba(248, 245, 242, 0.78);
					--amh-ql-green: #11b3a1;
					--amh-ql-green-deep: #0c7c71;
					--amh-ql-orange: #f2874a;
					--amh-ql-orange-deep: #ce6a35;
					--amh-ql-peach: #fbccbf;
					--amh-ql-focus: #11b3a1;
					position: relative;
					overflow: hidden;
					padding: 22px 16px;
					background-color: var(--amh-ql-bg-b);
					background-image: linear-gradient(
						115deg,
						var(--amh-ql-bg-a) 0%,
						var(--amh-ql-bg-b) 35%,
						var(--amh-ql-bg-c) 70%,
						var(--amh-ql-bg-d) 100%
					);
					color: var(--amh-ql-text);
					font-family: "Atkinson Hyperlegible Next", "Atkinson Hyperlegible", system-ui, sans-serif;
					isolation: isolate;
					border-top: 1px solid rgba(255, 255, 255, 0.2);
					border-bottom: 1px solid rgba(0, 0, 0, 0.1);
					box-shadow: 0 10px 30px rgba(1, 174, 147, 0.15);
				}
				#<?php echo esc_attr( $uid ); ?> * {
					box-sizing: border-box;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-backdrop {
					position: absolute;
					inset: 0;
					pointer-events: none;
					background: linear-gradient(
						120deg,
						transparent 30%,
						rgba(1, 174, 147, 0.25) 50%,
						transparent 70%
					);
					mix-blend-mode: overlay;
					opacity: 0;
					transition: opacity 0.5s ease;
					z-index: -1;
				}
				#<?php echo esc_attr( $uid ); ?>:hover .amh-ql-backdrop {
					opacity: 1;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-stage {
					max-width: 480px;
					margin: 0 auto;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-glass,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-glass-soft {
					border: 1px solid var(--amh-ql-border);
					border-top-color: var(--amh-ql-border-strong);
					backdrop-filter: blur(18px);
					-webkit-backdrop-filter: blur(18px);
					box-shadow: var(--amh-ql-shadow);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-main {
					display: grid;
					gap: 24px;
					padding: 24px 18px;
					border-radius: 32px;
					position: relative;
					overflow: hidden;
					border: 1px solid rgba(255, 255, 255, 0.06);
					box-shadow:
						inset 0 0 18px rgba(0, 0, 0, 0.55),
						inset 0 1px 0 rgba(255, 255, 255, 0.12),
						0 0 10px 5px rgba(0, 0, 0, 0.5);
					background:
						radial-gradient(circle at 10% 40%, rgba(223, 21, 124, 0.06), transparent 55%),
						radial-gradient(circle at 90% 15%, rgba(23, 154, 214, 0.08), transparent 55%),
						linear-gradient(180deg, #1a1c29 0%, #151622 100%);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-main::before {
					content: "";
					position: absolute;
					inset: 0;
					pointer-events: none;
					border-radius: inherit;
					backdrop-filter: blur(6px);
					-webkit-backdrop-filter: blur(6px);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-main::after {
					content: "";
					position: absolute;
					inset: 0;
					pointer-events: none;
					border-radius: inherit;
					background: linear-gradient(180deg, rgba(255, 255, 255, 0.06), transparent 20%);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-main > * {
					position: relative;
					z-index: 1;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-head {
					display: grid;
					gap: 14px;
					justify-items: center;
					text-align: center;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-brand {
					display: inline-grid;
					gap: 12px;
					justify-items: center;
					align-items: center;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-brand-icon {
					width: 82px;
					height: 82px;
					object-fit: cover;
					border-radius: 26px;
					box-shadow: 0 14px 28px rgba(0, 0, 0, 0.18);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-logo {
					width: 100%;
					max-width: 100%;
					height: auto;
					object-fit: contain;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-tagline,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-eyebrow {
					margin: 0;
					font-size: 0.82rem;
					font-weight: 700;
					line-height: 1.2;
					letter-spacing: 0.18em;
					text-transform: uppercase;
					color: var(--amh-ql-peach);
					font-family: "Atkinson Hyperlegible Next", "Atkinson Hyperlegible", system-ui, sans-serif;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-video .amh-ql-eyebrow {
					padding-bottom: 10px;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-copy h1,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-section-head h2,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link-copy strong,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-post-copy strong {
					font-family: "Montserrat", sans-serif;
					letter-spacing: -0.05em;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-copy h1 {
					margin: 0;
					font-size: clamp(1.95rem, 9vw, 3rem);
					line-height: 0.93;
					font-weight: 700;
					color: #fff5f1;
					padding-bottom: 15px;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-bio {
					margin: 0;
					font-size: 1rem;
					line-height: 1.65;
					color: var(--amh-ql-muted);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-social-row {
					display: flex;
					flex-wrap: wrap;
					gap: 10px;
					justify-content: center;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-social-pill {
					display: inline-flex;
					align-items: center;
					gap: 10px;
					min-height: 46px;
					padding: 0 16px;
					border-radius: 999px;
					background: rgba(255, 255, 255, 0.06);
					border: 1px solid rgba(255, 255, 255, 0.12);
					color: #ffffff;
					text-decoration: none;
					font-weight: 600;
					transition: transform 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-social-pill:hover {
					transform: translateY(-1px);
					background: rgba(255, 255, 255, 0.10);
					border-color: rgba(255, 255, 255, 0.18);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link-stack {
					display: grid;
					gap: 14px;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link {
					display: flex;
					align-items: center;
					gap: 14px;
					padding: 16px 18px;
					border-radius: 24px;
					text-decoration: none;
					color: #ffffff;
					border: 1px solid rgba(255, 255, 255, 0.12);
					box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
					backdrop-filter: blur(12px);
					-webkit-backdrop-filter: blur(12px);
					transition: transform 0.2s ease, filter 0.2s ease;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link:hover {
					transform: translateY(-2px);
					filter: brightness(1.03);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link.is-green {
					background-color: rgba(14, 91, 78, 0.25);
					background-image: radial-gradient(
						circle at top left,
						rgba(7, 160, 121, 0.4) 0%,
						rgba(14, 91, 78, 0) 70%
					);
					border: 1px solid rgba(7, 160, 121, 0.2);
					border-top: 1px solid rgba(7, 160, 121, 0.6);
					box-shadow:
						0 15px 35px rgba(0, 0, 0, 0.25),
						0 0 30px rgba(7, 160, 121, 0.2),
						inset 0 0 20px rgba(7, 160, 121, 0.1);
					backdrop-filter: blur(20px);
					-webkit-backdrop-filter: blur(20px);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link.is-orange {
					background-color: #f5977a;
					background-image: linear-gradient(
						135deg,
						rgba(226, 114, 0, 0.55) 0%,
						rgba(219, 88, 68, 0.60) 100%
					);
					border: 1px solid rgba(226, 114, 0, 0.35);
					border-top: 1px solid rgba(255, 255, 255, 0.25);
					box-shadow:
						0 8px 20px rgba(0, 0, 0, 0.25),
						inset 0 0 20px rgba(226, 114, 0, 0.1);
					backdrop-filter: blur(12px);
					-webkit-backdrop-filter: blur(12px);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link.is-peach {
					background: linear-gradient(135deg, rgba(251, 204, 191, 0.98), rgba(242, 135, 74, 0.88));
					color: #151622;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link.is-peach .amh-ql-link-copy span {
					color: rgba(21, 22, 34, 0.78);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link-icon {
					flex: 0 0 46px;
					width: 46px;
					height: 46px;
					display: inline-flex;
					align-items: center;
					justify-content: center;
					border-radius: 16px;
					background: rgba(255, 255, 255, 0.15);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link-copy {
					flex: 1 1 auto;
					min-width: 0;
					display: grid;
					gap: 0;
					align-content: start;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link-copy strong,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-post-copy strong {
					display: block;
					margin: 0 0 2px;
					font-size: 1.05rem;
					line-height: 1.18;
					font-weight: 700;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link-copy span,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-post-copy span,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter-copy,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter-note {
					font-size: 0.85rem;
					line-height: 1.1;
					color: var(--amh-ql-muted);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link.is-peach .amh-ql-link-copy strong {
					color: #151622;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-middle,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-feed,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-trust,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-contact {
					display: grid;
					gap: 16px;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-video,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-feed,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-trust,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-contact {
					padding: 20px;
					border-radius: 28px;
					background: linear-gradient(180deg, rgba(27, 33, 54, 0.84), rgba(20, 25, 42, 0.74));
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-section-head {
					display: flex;
					flex-wrap: wrap;
					align-items: flex-start;
					justify-content: space-between;
					gap: 10px 16px;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-section-head h2 {
					margin: 4px 0 10px;
					font-size: clamp(1.4rem, 3vw, 2rem);
					line-height: 1.02;
					font-weight: 700;
					color: #fff5f1;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-inline-link {
					color: var(--amh-ql-peach);
					font-weight: 700;
					text-decoration: none;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-inline-link:hover {
					color: #ffffff;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-video-frame {
					position: relative;
					overflow: hidden;
					border-radius: 22px;
					background: rgba(0, 0, 0, 0.3);
					padding-top: 56.25%;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-video-frame iframe {
					position: absolute;
					inset: 0;
					width: 100%;
					height: 100%;
					border: 0;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter {
					padding: 0;
					overflow: hidden;
					background: linear-gradient(180deg, rgba(27, 33, 54, 0.84), rgba(20, 25, 42, 0.74));
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter-summary {
					list-style: none;
					padding: 18px;
					cursor: pointer;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter-summary::-webkit-details-marker {
					display: none;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter[open] {
					background: linear-gradient(180deg, rgba(255, 247, 243, 0.96), rgba(251, 236, 228, 0.94));
					border-color: rgba(255, 255, 255, 0.22);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter[open] .amh-ql-eyebrow,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter[open] .amh-ql-section-head h2,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter[open] .amh-ql-newsletter-copy,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter[open] .amh-ql-newsletter-note {
					color: #151622;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter-panel {
					padding: 0 18px 18px;
					display: grid;
					gap: 12px;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter-chevron {
					display: inline-flex;
					align-items: center;
					justify-content: center;
					width: 38px;
					height: 38px;
					border-radius: 999px;
					background: rgba(255, 255, 255, 0.08);
					border: 1px solid rgba(255, 255, 255, 0.12);
					transform: rotate(0deg);
					transition: transform 0.2s ease;
					flex-shrink: 0;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter[open] .amh-ql-newsletter-chevron {
					transform: rotate(180deg);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter[open] .amh-ql-newsletter-chevron {
					background: rgba(21, 22, 34, 0.08);
					border-color: rgba(21, 22, 34, 0.12);
					color: #151622;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter-form .sender-form-field {
					width: 100%;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter-form input[type="email"],
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter-form input[type="text"],
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter-form input[type="name"] {
					width: 100%;
					min-height: 50px;
					padding: 0 14px;
					border-radius: 16px;
					border: 1px solid rgba(255, 255, 255, 0.14);
					background: rgba(255, 255, 255, 0.08);
					color: #ffffff;
					font-family: "Atkinson Hyperlegible Next", "Atkinson Hyperlegible", system-ui, sans-serif;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter-form button,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter-form input[type="submit"] {
					min-height: 50px;
					padding: 0 20px;
					border: 0;
					border-radius: 999px;
					background: linear-gradient(135deg, rgba(12, 124, 113, 0.98), rgba(17, 179, 161, 0.92));
					color: #ffffff;
					font-weight: 700;
					font-family: "Atkinson Hyperlegible Next", "Atkinson Hyperlegible", system-ui, sans-serif;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-posts {
					display: grid;
					gap: 12px;
					grid-template-columns: repeat(1, minmax(0, 1fr));
					align-items: stretch;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-post-card {
					display: grid;
					align-content: start;
					gap: 14px;
					padding: 14px;
					border-radius: 22px;
					background: rgba(255, 255, 255, 0.06);
					border: 1px solid rgba(255, 255, 255, 0.08);
					color: #ffffff;
					text-decoration: none;
					transition: transform 0.2s ease, background-color 0.2s ease;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-post-card:hover {
					transform: translateY(-2px);
					background: rgba(255, 255, 255, 0.10);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-post-thumb {
					width: 100%;
					height: 132px;
					border-radius: 18px;
					object-fit: cover;
					background: rgba(255, 255, 255, 0.08);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-post-copy {
					display: grid;
					gap: 8px;
					align-content: start;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-post-copy strong {
					font-size: 0.96rem;
					line-height: 1.22;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-post-copy span {
					font-size: 0.86rem;
					line-height: 1.5;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-trust-grid {
					display: grid;
					gap: 12px;
					grid-template-columns: repeat(2, minmax(0, 1fr));
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-trust-item {
					min-height: 92px;
					display: grid;
					place-items: center;
					padding: 12px;
					border-radius: 22px;
					border: 1px solid rgba(255, 255, 255, 0.08);
					overflow: hidden;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-trust-item.is-ink {
					background: linear-gradient(145deg, rgba(14, 17, 31, 0.92), rgba(35, 50, 73, 0.88));
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-trust-item.is-ink-soft {
					background: linear-gradient(145deg, rgba(22, 26, 44, 0.90), rgba(15, 46, 52, 0.82));
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-trust-item.is-paper {
					background: rgba(248, 245, 242, 0.96);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-trust-item.is-paper-warm {
					background: rgba(251, 204, 191, 0.94);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-trust-item.is-white-mark {
					background:
						radial-gradient(circle at 100% 0%, rgba(17, 179, 161, 0.18), transparent 42%),
						linear-gradient(145deg, rgba(28, 32, 50, 0.96), rgba(13, 17, 31, 0.94));
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-trust-item img {
					width: 100%;
					max-width: 136px;
					max-height: 52px;
					object-fit: contain;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-contact-grid {
					display: grid;
					gap: 12px;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-contact-item {
					display: grid;
					gap: 6px;
					padding: 14px 16px;
					border-radius: 20px;
					background: rgba(255, 255, 255, 0.05);
					border: 1px solid rgba(255, 255, 255, 0.08);
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-contact-item strong {
					font-family: "Montserrat", sans-serif;
					font-size: 0.95rem;
					line-height: 1.1;
					letter-spacing: -0.03em;
					color: #fff5f1;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-contact-item a,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-contact-item span,
				#<?php echo esc_attr( $uid ); ?> .amh-ql-contact-item address {
					margin: 0;
					color: var(--amh-ql-muted);
					font-style: normal;
					text-decoration: none;
					line-height: 1.6;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-company-strip {
					display: grid;
					gap: 8px;
					justify-items: center;
					text-align: center;
					padding-top: 4px;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-company-meta {
					display: grid;
					gap: 6px;
					color: var(--amh-ql-muted);
					font-size: 0.95rem;
					line-height: 1.55;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-icon svg {
					width: 18px;
					height: 18px;
					fill: currentColor;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-link-arrow svg {
					width: 22px;
					height: 22px;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-newsletter-chevron svg {
					width: 18px;
					height: 18px;
					fill: none;
					stroke: currentColor;
					stroke-width: 2;
					stroke-linecap: round;
					stroke-linejoin: round;
				}
				#<?php echo esc_attr( $uid ); ?> .amh-ql-sr {
					position: absolute;
					width: 1px;
					height: 1px;
					margin: -1px;
					padding: 0;
					overflow: hidden;
					clip: rect(0, 0, 0, 0);
					border: 0;
				}
				#<?php echo esc_attr( $uid ); ?> a:focus-visible,
				#<?php echo esc_attr( $uid ); ?> button:focus-visible,
				#<?php echo esc_attr( $uid ); ?> input:focus-visible,
				#<?php echo esc_attr( $uid ); ?> textarea:focus-visible,
				#<?php echo esc_attr( $uid ); ?> select:focus-visible {
					outline: 3px solid var(--amh-ql-focus);
					outline-offset: 3px;
				}
				@media (min-width: 640px) {
					#<?php echo esc_attr( $uid ); ?> {
						padding: 28px;
					}
					#<?php echo esc_attr( $uid ); ?> .amh-ql-main {
						padding: 30px 26px;
					}
					#<?php echo esc_attr( $uid ); ?> .amh-ql-trust-grid {
						grid-template-columns: repeat(2, minmax(0, 1fr));
					}
					#<?php echo esc_attr( $uid ); ?> .amh-ql-contact-grid {
						grid-template-columns: repeat(1, minmax(0, 1fr));
					}
				}
				@media (max-width: 639px) {
					#<?php echo esc_attr( $uid ); ?> .amh-ql-link {
						align-items: flex-start;
					}
					#<?php echo esc_attr( $uid ); ?> .amh-ql-link-arrow {
						padding-top: 12px;
					}
					#<?php echo esc_attr( $uid ); ?> .amh-ql-trust-grid {
						grid-template-columns: repeat(2, minmax(0, 1fr));
					}
				}
				@media (max-width: 380px) {
					#<?php echo esc_attr( $uid ); ?> .amh-ql-posts {
						grid-template-columns: minmax(0, 1fr);
					}
				}
				@media (prefers-reduced-motion: reduce) {
					#<?php echo esc_attr( $uid ); ?> * {
						transition: none !important;
						scroll-behavior: auto !important;
					}
				}
			</style>

			<script>
				(function() {
					var root = document.getElementById('<?php echo esc_js( $uid ); ?>');
					if (!root || root.dataset.amhQuicklinksInit === '1') {
						return;
					}
					root.dataset.amhQuicklinksInit = '1';

					var nonce = '<?php echo esc_js( $nonce ); ?>';
					var ajaxUrl = '<?php echo esc_js( $ajax_url ); ?>';
					var liveEl = root.querySelector('[data-amh-live]');

					function setLive(message) {
						if (liveEl) {
							liveEl.textContent = message || '';
						}
					}

					function track(eventName, label) {
						var body = new URLSearchParams();
						body.append('action', 'amh_quicklinks_track');
						body.append('nonce', nonce);
						body.append('event', eventName || '');
						body.append('label', label || '');

						if (navigator.sendBeacon) {
							var blob = new Blob([body.toString()], { type: 'application/x-www-form-urlencoded; charset=UTF-8' });
							if (navigator.sendBeacon(ajaxUrl, blob)) {
								return;
							}
						}

						fetch(ajaxUrl, {
							method: 'POST',
							headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
							body: body.toString(),
							credentials: 'same-origin',
							keepalive: true
						}).catch(function(){});
					}

					track('page_view', 'linktree');

					root.querySelectorAll('[data-amh-track]').forEach(function(el) {
						var eventName = String(el.getAttribute('data-amh-event') || 'link_click');
						var label = String(el.getAttribute('data-amh-track') || 'unknown');
						var trigger = el.tagName === 'IFRAME' ? 'load' : 'click';

						el.addEventListener(trigger, function() {
							track(eventName, label);
							if (eventName === 'newsletter_submit') {
								setLive('Newsletter form interacted with');
							}
						}, { passive: true });
					});
				})();
			</script>
		</section>
		<?php

		return ob_get_clean();
	}

	add_shortcode( 'amh_social_quicklinks', 'amh_social_quicklinks_shortcode' );
	add_shortcode( 'inkfire_linktree', 'amh_social_quicklinks_shortcode' );
}
