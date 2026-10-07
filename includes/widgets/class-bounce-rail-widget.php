<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Bounce_Rail_Widget extends Widget_Base {
	public function get_name() {
		return 'foundation-bounce-rail';
	}

	public function get_title() {
		return esc_html__( 'Foundation Social Wall', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-posts-carousel';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'bounce', 'posts', 'youtube', 'rail', 'carousel' );
	}

	public function get_style_depends(): array {
		return array( 'foundation-elementor-plus-bounce-rail' );
	}

	public function get_script_depends(): array {
		return array( 'foundation-elementor-plus-bounce-rail' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_header_style_controls();
		$this->register_card_style_controls();
		$this->register_control_style_controls();
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$widget_id     = 'foundation-bounce-rail-' . $this->get_id();
		$columns       = ! empty( $settings['columns'] ) && is_array( $settings['columns'] ) ? array_values( $settings['columns'] ) : array();
		$show_controls = isset( $settings['show_controls'] ) && 'yes' === $settings['show_controls'];
		$all_posts     = ! empty( $settings['all_posts_text'] );
		$youtube_items = $this->get_youtube_feed_items( $settings );
		$instagram_items = $this->get_instagram_feed_items( $settings );

		if ( empty( $columns ) ) {
			return;
		}

		if ( $all_posts ) {
			$this->add_link_attributes( 'all_posts_link', $settings['all_posts_link'] );
		}
		?>
		<section id="<?php echo esc_attr( $widget_id ); ?>" class="foundation-bounce-rail" data-foundation-bounce-rail data-scroll-step="<?php echo esc_attr( ! empty( $settings['scroll_step'] ) ? (string) (int) $settings['scroll_step'] : '302' ); ?>">
			<div class="foundation-bounce-rail__header">
				<div class="foundation-bounce-rail__copy">
					<?php $this->render_header_field( $settings['eyebrow'] ?? '', 'foundation-bounce-rail__eyebrow', 'p' ); ?>
					<?php $this->render_header_field( $settings['title'] ?? '', 'foundation-bounce-rail__title', 'h2' ); ?>
					<?php $this->render_header_field( $settings['subtitle'] ?? '', 'foundation-bounce-rail__subtitle', 'div' ); ?>
				</div>

				<?php if ( $show_controls || $all_posts ) : ?>
					<div class="foundation-bounce-rail__controls">
						<?php if ( $show_controls ) : ?>
							<button type="button" class="foundation-bounce-rail__arrow foundation-bounce-rail__arrow--prev" data-bounce-prev aria-label="<?php echo esc_attr( ! empty( $settings['prev_aria_label'] ) ? $settings['prev_aria_label'] : 'Previous items' ); ?>">
								<?php
								$prev_icon = $this->get_icon_markup( $settings['prev_icon'] ?? array(), 'foundation-bounce-rail__arrow-icon' );
								echo $prev_icon ? $prev_icon : esc_html( ! empty( $settings['prev_label'] ) ? $settings['prev_label'] : '<-' );
								?>
							</button>
							<button type="button" class="foundation-bounce-rail__arrow foundation-bounce-rail__arrow--next" data-bounce-next aria-label="<?php echo esc_attr( ! empty( $settings['next_aria_label'] ) ? $settings['next_aria_label'] : 'Next items' ); ?>">
								<?php
								$next_icon = $this->get_icon_markup( $settings['next_icon'] ?? array(), 'foundation-bounce-rail__arrow-icon' );
								echo $next_icon ? $next_icon : esc_html( ! empty( $settings['next_label'] ) ? $settings['next_label'] : '->' );
								?>
							</button>
						<?php endif; ?>

						<?php if ( $all_posts ) : ?>
							<a class="foundation-bounce-rail__all" <?php echo $this->get_render_attribute_string( 'all_posts_link' ); ?>>
								<?php echo esc_html( $settings['all_posts_text'] ); ?>
								<?php echo $this->get_icon_markup( $settings['all_posts_icon'] ?? array(), 'foundation-bounce-rail__all-icon' ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="foundation-bounce-rail__track" data-bounce-track>
				<?php foreach ( $columns as $column ) : ?>
					<?php $this->render_column( $column, $youtube_items, $instagram_items ); ?>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}

	private function render_column( array $column, array $youtube_items = array(), array $instagram_items = array() ) {
		$type   = ! empty( $column['column_type'] ) ? $column['column_type'] : 'stack';
		$offset = isset( $column['column_offset'] ) ? (int) $column['column_offset'] : 0;
		?>
		<div class="foundation-bounce-rail__column foundation-bounce-rail__column--<?php echo esc_attr( $type ); ?>" style="<?php echo esc_attr( '--foundation-bounce-column-offset:' . $offset . 'px;' ); ?>">
			<?php
			if ( 'reel' === $type ) {
				$this->render_reel_column( $column, $youtube_items, $instagram_items );
			} else {
				$this->render_stack_column( $column );
			}
			?>
		</div>
		<?php
	}

	private function render_stack_column( array $column ) {
		$cards = 'dynamic' === ( $column['stack_source'] ?? 'manual' )
			? $this->get_dynamic_cards_for_column( $column )
			: $this->get_manual_cards_for_column( $column );

		if ( empty( $cards ) ) {
			return;
		}

		foreach ( $cards as $card ) {
			$this->render_post_card( $card );
		}
	}

	private function render_post_card( array $card ) {
		$title = $card['title'] ?? '';
		$image = $card['image'] ?? '';
		$link  = $card['link'] ?? '';
		$tag   = $card['tag'] ?? '';
		$time  = $card['time'] ?? '';
		?>
		<article class="foundation-bounce-rail__post-card">
			<?php if ( $link ) : ?>
				<a class="foundation-bounce-rail__post-link" href="<?php echo esc_url( $link ); ?>">
			<?php endif; ?>

			<?php if ( $image ) : ?>
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
			<?php endif; ?>

			<div class="foundation-bounce-rail__overlay">
				<div class="foundation-bounce-rail__meta">
					<?php if ( $tag ) : ?>
						<span class="foundation-bounce-rail__tag"><?php echo esc_html( $tag ); ?></span>
					<?php endif; ?>
					<?php if ( $time ) : ?>
						<span class="foundation-bounce-rail__time">
							<?php echo $this->get_icon_markup( $this->get_settings_for_display()['time_icon'] ?? array(), 'foundation-bounce-rail__time-icon' ); ?>
							<span><?php echo esc_html( $time ); ?></span>
						</span>
					<?php endif; ?>
				</div>
				<?php if ( $title ) : ?>
					<h3><?php echo esc_html( $title ); ?></h3>
				<?php endif; ?>
			</div>

			<?php if ( $link ) : ?>
				</a>
			<?php endif; ?>
		</article>
		<?php
	}

	private function render_reel_column( array $column, array $youtube_items = array(), array $instagram_items = array() ) {
		$reel_source = ! empty( $column['reel_source'] ) ? $column['reel_source'] : 'manual';
		$feed_index  = isset( $column['youtube_item_index'] ) ? max( 0, (int) $column['youtube_item_index'] ) : 0;
		$feed_item   = array();

		if ( 'youtube_auto' === $reel_source && isset( $youtube_items[ $feed_index ] ) ) {
			$feed_item = $youtube_items[ $feed_index ];
		} elseif ( 'instagram_auto' === $reel_source && isset( $instagram_items[ $feed_index ] ) ) {
			$feed_item = $instagram_items[ $feed_index ];
		}

		$source_url  = ! empty( $column['reel_youtube_url'] ) ? trim( (string) $column['reel_youtube_url'] ) : '';
		$url_item    = $source_url ? $this->get_remote_media_metadata( $source_url, $this->get_settings_for_display() ) : array();
		$resolved    = wp_parse_args( $url_item, $feed_item );
		$reel_link   = ! empty( $column['reel_link']['url'] ) ? $column['reel_link']['url'] : '';
		$image_url   = $this->get_media_url_from_repeater( $column, 'reel_image' );
		$fallback_id = ! empty( $resolved['video_id'] ) ? $resolved['video_id'] : ( $source_url ? $this->extract_youtube_id( $source_url ) : '' );
		$poster_url  = ! empty( $resolved['image'] ) ? $resolved['image'] : ( $image_url ? $image_url : ( $fallback_id ? 'https://i.ytimg.com/vi/' . rawurlencode( $fallback_id ) . '/hqdefault.jpg' : '' ) );
		$link_url    = $reel_link ? $reel_link : ( ! empty( $resolved['link'] ) ? $resolved['link'] : $source_url );
		$chip_mode   = ! empty( $column['reel_chip_mode'] ) ? $column['reel_chip_mode'] : 'pulled_metadata';
		$chip        = $this->resolve_reel_chip_text( $column, $resolved, $reel_source, $source_url );
		$chip_class  = 'foundation-bounce-rail__reel-chip';

		if ( 'source_label' === $chip_mode ) {
			$chip_class .= ' foundation-bounce-rail__reel-chip--pill';
		}
		?>
		<article class="foundation-bounce-rail__reel">
			<?php if ( $link_url ) : ?>
				<a class="foundation-bounce-rail__reel-link" href="<?php echo esc_url( $link_url ); ?>" target="_blank" rel="noopener">
			<?php endif; ?>

			<?php if ( $poster_url ) : ?>
				<img src="<?php echo esc_url( $poster_url ); ?>" alt="<?php echo esc_attr( $chip ); ?>" loading="lazy">
			<?php endif; ?>

			<?php if ( $chip ) : ?>
				<div class="<?php echo esc_attr( $chip_class ); ?>"><?php echo esc_html( $chip ); ?></div>
			<?php endif; ?>

			<?php if ( $link_url ) : ?>
				</a>
			<?php endif; ?>
		</article>
		<?php
	}

	private function resolve_reel_chip_text( array $column, array $resolved, $reel_source, $source_url ) {
		$chip_mode = ! empty( $column['reel_chip_mode'] ) ? $column['reel_chip_mode'] : 'pulled_metadata';

		if ( 'hidden' === $chip_mode ) {
			return '';
		}

		if ( 'custom_text' === $chip_mode ) {
			return ! empty( $column['reel_chip'] ) ? trim( (string) $column['reel_chip'] ) : '';
		}

		if ( 'source_label' === $chip_mode ) {
			if ( ! empty( $column['reel_source_label'] ) ) {
				return trim( (string) $column['reel_source_label'] );
			}

			return $this->get_reel_source_label( $reel_source, $source_url, $resolved );
		}

		$chip = ! empty( $resolved['title'] ) ? trim( (string) $resolved['title'] ) : '';

		if ( '' === $chip && ! empty( $resolved['description'] ) ) {
			$chip = wp_html_excerpt( wp_strip_all_tags( (string) $resolved['description'] ), 88, '...' );
		}

		if ( '' === $chip && ! empty( $column['reel_chip'] ) ) {
			$chip = trim( (string) $column['reel_chip'] );
		}

		return $chip;
	}

	private function get_reel_source_label( $reel_source, $source_url, array $resolved ) {
		$resolved_link = ! empty( $resolved['link'] ) ? (string) $resolved['link'] : '';
		$haystack      = strtolower( trim( $resolved_link ? $resolved_link : (string) $source_url ) );

		if ( 'instagram_auto' === $reel_source || false !== strpos( $haystack, 'instagram.com' ) ) {
			return esc_html__( 'Instagram Reel', 'foundation-elementor-plus' );
		}

		if ( false !== strpos( $haystack, '/shorts/' ) ) {
			return esc_html__( 'YouTube Short', 'foundation-elementor-plus' );
		}

		if ( 'youtube_auto' === $reel_source || false !== strpos( $haystack, 'youtube.com' ) || false !== strpos( $haystack, 'youtu.be' ) ) {
			return esc_html__( 'YouTube Video', 'foundation-elementor-plus' );
		}

		return esc_html__( 'Featured Reel', 'foundation-elementor-plus' );
	}

	private function get_dynamic_cards_for_column( array $column ) {
		$args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 2,
			'ignore_sticky_posts' => true,
			'offset'              => isset( $column['query_offset'] ) ? max( 0, (int) $column['query_offset'] ) : 0,
		);

		$query_mode = ! empty( $column['query_mode'] ) ? $column['query_mode'] : 'recent';

		if ( 'category' === $query_mode && ! empty( $column['query_category'] ) ) {
			$args['category_name'] = sanitize_title( $column['query_category'] );
		}

		if ( 'ids' === $query_mode && ! empty( $column['query_post_ids'] ) ) {
			$post_ids = array_values(
				array_filter(
					array_map(
						'intval',
						preg_split( '/[\s,]+/', (string) $column['query_post_ids'] )
					)
				)
			);

			if ( ! empty( $post_ids ) ) {
				$args['post__in'] = $post_ids;
				$args['orderby']  = 'post__in';
			}
		}

		$posts = get_posts( $args );

		if ( empty( $posts ) ) {
			return array();
		}

		return array_map(
			function ( $post ) {
				$categories = get_the_category( $post->ID );
				$image      = get_the_post_thumbnail_url( $post->ID, 'large' );

				return array(
					'title' => get_the_title( $post->ID ),
					'image' => $image ? $image : '',
					'link'  => get_permalink( $post->ID ),
					'tag'   => ! empty( $categories ) ? $categories[0]->name : '',
					'time'  => $this->estimate_read_time( $post->post_content ),
				);
			},
			$posts
		);
	}

	private function get_manual_cards_for_column( array $column ) {
		$cards = array();

		for ( $index = 1; $index <= 2; $index++ ) {
			$title = ! empty( $column[ 'manual_card_' . $index . '_title' ] ) ? $column[ 'manual_card_' . $index . '_title' ] : '';

			if ( '' === $title ) {
				continue;
			}

			$cards[] = array(
				'title' => $title,
				'image' => $this->get_media_url_from_repeater( $column, 'manual_card_' . $index . '_image' ),
				'link'  => ! empty( $column[ 'manual_card_' . $index . '_link' ]['url'] ) ? $column[ 'manual_card_' . $index . '_link' ]['url'] : '',
				'tag'   => ! empty( $column[ 'manual_card_' . $index . '_tag' ] ) ? $column[ 'manual_card_' . $index . '_tag' ] : '',
				'time'  => ! empty( $column[ 'manual_card_' . $index . '_time' ] ) ? $column[ 'manual_card_' . $index . '_time' ] : '',
			);
		}

		return $cards;
	}

	private function get_media_url_from_repeater( array $item, $key ) {
		if ( empty( $item[ $key ] ) || ! is_array( $item[ $key ] ) ) {
			return '';
		}

		if ( ! empty( $item[ $key ]['id'] ) ) {
			$image_url = wp_get_attachment_image_url( (int) $item[ $key ]['id'], 'large' );
			if ( $image_url ) {
				return $image_url;
			}
		}

		return ! empty( $item[ $key ]['url'] ) ? $item[ $key ]['url'] : '';
	}

	private function estimate_read_time( $content ) {
		$word_count = str_word_count( wp_strip_all_tags( (string) $content ) );
		$minutes    = max( 1, (int) ceil( $word_count / 200 ) );

		return sprintf( '%d Min Read', $minutes );
	}

	private function get_icon_markup( $icon, $class_name ) {
		if ( empty( $icon['value'] ) ) {
			return '';
		}

		ob_start();
		Icons_Manager::render_icon(
			$icon,
			array(
				'aria-hidden' => 'true',
				'class'       => $class_name,
			)
		);
		return (string) ob_get_clean();
	}

	private function extract_youtube_id( $url ) {
		$patterns = array(
			'~youtu\.be/([^?&/]+)~',
			'~youtube\.com/watch\?v=([^?&/]+)~',
			'~youtube\.com/shorts/([^?&/]+)~',
			'~youtube\.com/embed/([^?&/]+)~',
		);

		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, (string) $url, $matches ) ) {
				return $matches[1];
			}
		}

		return '';
	}

	private function get_remote_media_metadata( $url, array $settings = array() ) {
		$url = trim( (string) $url );

		if ( '' === $url || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return array();
		}

		$cache_key = 'foundation_remote_media_' . md5( $url );
		$cached    = get_transient( $cache_key );

		if ( is_array( $cached ) ) {
			return $cached;
		}

		$host = (string) wp_parse_url( $url, PHP_URL_HOST );
		$host = strtolower( preg_replace( '/^www\./', '', $host ) );

		if ( false !== strpos( $host, 'youtube.com' ) || false !== strpos( $host, 'youtu.be' ) ) {
			$item = $this->get_youtube_video_metadata_from_url( $url, $settings );
		} elseif ( false !== strpos( $host, 'instagram.com' ) ) {
			$item = $this->get_instagram_media_metadata_from_url( $url );
		} else {
			$item = $this->get_open_graph_metadata_from_url( $url );
		}

		set_transient( $cache_key, $item, HOUR_IN_SECONDS );
		return is_array( $item ) ? $item : array();
	}

	private function get_youtube_video_metadata_from_url( $url, array $settings = array() ) {
		$video_id = $this->extract_youtube_id( $url );
		$api_key  = $this->get_effective_setting( $settings, 'youtube_api_key', 'youtube_api_key' );

		if ( $video_id && $api_key ) {
			$response = wp_remote_get(
				'https://www.googleapis.com/youtube/v3/videos?part=snippet&id=' . rawurlencode( $video_id ) . '&key=' . rawurlencode( $api_key ),
				array(
					'timeout' => 12,
				)
			);

			if ( ! is_wp_error( $response ) ) {
				$payload = json_decode( wp_remote_retrieve_body( $response ), true );
				$item    = $payload['items'][0]['snippet'] ?? array();

				if ( ! empty( $item ) ) {
					$thumbnails = $item['thumbnails'] ?? array();
					$image      = $thumbnails['high']['url'] ?? ( $thumbnails['medium']['url'] ?? ( $thumbnails['default']['url'] ?? '' ) );

					return array(
						'title'       => isset( $item['title'] ) ? (string) $item['title'] : '',
						'description' => isset( $item['description'] ) ? (string) $item['description'] : '',
						'image'       => $image,
						'link'        => 'https://www.youtube.com/watch?v=' . rawurlencode( $video_id ),
						'video_id'    => $video_id,
					);
				}
			}
		}

		$oembed = wp_remote_get(
			'https://www.youtube.com/oembed?url=' . rawurlencode( $url ) . '&format=json',
			array(
				'timeout' => 12,
			)
		);

		$data = array();
		if ( ! is_wp_error( $oembed ) ) {
			$payload = json_decode( wp_remote_retrieve_body( $oembed ), true );
			if ( is_array( $payload ) ) {
				$data = array(
					'title'       => isset( $payload['title'] ) ? (string) $payload['title'] : '',
					'description' => '',
					'image'       => isset( $payload['thumbnail_url'] ) ? (string) $payload['thumbnail_url'] : '',
					'link'        => $url,
					'video_id'    => $video_id,
				);
			}
		}

		$page = $this->get_open_graph_metadata_from_url( $url );
		if ( ! empty( $page ) ) {
			$data = wp_parse_args( $page, $data );
			if ( empty( $data['video_id'] ) ) {
				$data['video_id'] = $video_id;
			}
		}

		return $data;
	}

	private function get_instagram_media_metadata_from_url( $url ) {
		return $this->get_open_graph_metadata_from_url( $url );
	}

	private function get_open_graph_metadata_from_url( $url ) {
		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 12,
				'headers' => array(
					'User-Agent' => 'Mozilla/5.0 (compatible; FoundationElementorPlus/1.0; +https://mediumblue-seal-942128.hostingersite.com)',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return array();
		}

		$body = wp_remote_retrieve_body( $response );

		if ( '' === $body ) {
			return array();
		}

		$title = $this->extract_meta_value( $body, array(
			'/<meta[^>]+property="og:title"[^>]+content="([^"]*)"/i',
			'/<meta[^>]+name="title"[^>]+content="([^"]*)"/i',
			'/<title>([^<]*)<\/title>/i',
		) );

		$description = $this->extract_meta_value( $body, array(
			'/<meta[^>]+property="og:description"[^>]+content="([^"]*)"/i',
			'/<meta[^>]+name="description"[^>]+content="([^"]*)"/i',
			'/"shortDescription":"((?:[^"\\\\]|\\\\.)*)"/',
		) );

		$image = $this->extract_meta_value( $body, array(
			'/<meta[^>]+property="og:image"[^>]+content="([^"]*)"/i',
			'/<meta[^>]+name="twitter:image"[^>]+content="([^"]*)"/i',
		) );

		return array_filter(
			array(
				'title'       => $this->decode_meta_value( $title ),
				'description' => $this->decode_meta_value( $description ),
				'image'       => $this->decode_meta_value( $image ),
				'link'        => $url,
			)
		);
	}

	private function extract_meta_value( $body, array $patterns ) {
		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, (string) $body, $matches ) && ! empty( $matches[1] ) ) {
				return (string) $matches[1];
			}
		}

		return '';
	}

	private function decode_meta_value( $value ) {
		$value = html_entity_decode( (string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		if ( str_contains( $value, '\\' ) ) {
			$decoded = json_decode( '"' . str_replace( '"', '\"', $value ) . '"', true );
			if ( is_string( $decoded ) ) {
				$value = $decoded;
			}
		}

		return trim( wp_strip_all_tags( $value ) );
	}

	private function get_effective_setting( array $settings, $widget_key, $global_key ) {
		$widget_value = isset( $settings[ $widget_key ] ) ? trim( (string) $settings[ $widget_key ] ) : '';

		if ( '' !== $widget_value ) {
			return $widget_value;
		}

		$plugin_settings = get_option( \FoundationElementorPlus\Plugin::OPTION_KEY, array() );

		return isset( $plugin_settings[ $global_key ] ) ? trim( (string) $plugin_settings[ $global_key ] ) : '';
	}

	private function get_youtube_feed_items( array $settings ) {
		$channel_id   = $this->resolve_youtube_channel_id( $settings );
		$cache_window = ! empty( $settings['youtube_cache_minutes'] ) ? max( 5, (int) $settings['youtube_cache_minutes'] ) : 60;
		$cache_key    = 'foundation_youtube_feed_' . md5( $channel_id );
		$api_key      = $this->get_effective_setting( $settings, 'youtube_api_key', 'youtube_api_key' );
		$cached       = get_transient( $cache_key );

		if ( empty( $channel_id ) ) {
			return array();
		}

		if ( is_array( $cached ) ) {
			return $cached;
		}

		if ( $channel_id && $api_key ) {
			$items = $this->get_youtube_api_feed_items( $channel_id, $api_key );
			if ( ! empty( $items ) ) {
				set_transient( $cache_key, $items, $cache_window * MINUTE_IN_SECONDS );
				return $items;
			}
		}

		$response = wp_remote_get(
			'https://www.youtube.com/feeds/videos.xml?channel_id=' . rawurlencode( $channel_id ),
			array(
				'timeout' => 12,
			)
		);

		if ( is_wp_error( $response ) ) {
			return array();
		}

		$body = wp_remote_retrieve_body( $response );

		if ( empty( $body ) ) {
			return array();
		}

		$xml = @simplexml_load_string( $body );

		if ( false === $xml || empty( $xml->entry ) ) {
			return array();
		}

		$items = array();

		foreach ( $xml->entry as $entry ) {
			$namespaces = $entry->getNameSpaces( true );
			$yt         = isset( $namespaces['yt'] ) ? $entry->children( $namespaces['yt'] ) : null;
			$video_id   = $yt && ! empty( $yt->videoId ) ? (string) $yt->videoId : '';
			$link       = '';

			if ( ! empty( $entry->link ) ) {
				foreach ( $entry->link as $link_node ) {
					$attributes = $link_node->attributes();
					if ( isset( $attributes['href'] ) ) {
						$link = (string) $attributes['href'];
						break;
					}
				}
			}

			$items[] = array(
				'title'    => ! empty( $entry->title ) ? (string) $entry->title : '',
				'video_id' => $video_id,
				'link'     => $link,
				'image'    => $video_id ? 'https://i.ytimg.com/vi/' . rawurlencode( $video_id ) . '/hqdefault.jpg' : '',
			);
		}

		set_transient( $cache_key, $items, $cache_window * MINUTE_IN_SECONDS );

		return $items;
	}

	private function get_youtube_api_feed_items( $channel_id, $api_key ) {
		$channel_response = wp_remote_get(
			'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=' . rawurlencode( $channel_id ) . '&key=' . rawurlencode( $api_key ),
			array(
				'timeout' => 12,
			)
		);

		if ( is_wp_error( $channel_response ) ) {
			return array();
		}

		$channel_payload = json_decode( wp_remote_retrieve_body( $channel_response ), true );
		$playlist_id     = $channel_payload['items'][0]['contentDetails']['relatedPlaylists']['uploads'] ?? '';

		if ( '' === $playlist_id ) {
			return array();
		}

		$playlist_response = wp_remote_get(
			'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=' . rawurlencode( $playlist_id ) . '&maxResults=12&key=' . rawurlencode( $api_key ),
			array(
				'timeout' => 12,
			)
		);

		if ( is_wp_error( $playlist_response ) ) {
			return array();
		}

		$playlist_payload = json_decode( wp_remote_retrieve_body( $playlist_response ), true );
		$items            = $playlist_payload['items'] ?? array();

		if ( empty( $items ) || ! is_array( $items ) ) {
			return array();
		}

		return array_map(
			function ( $item ) {
				$snippet    = $item['snippet'] ?? array();
				$video_id   = $snippet['resourceId']['videoId'] ?? '';
				$thumbnails = $snippet['thumbnails'] ?? array();
				$image      = $thumbnails['high']['url'] ?? ( $thumbnails['medium']['url'] ?? ( $thumbnails['default']['url'] ?? '' ) );

				return array(
					'title'       => isset( $snippet['title'] ) ? (string) $snippet['title'] : '',
					'description' => isset( $snippet['description'] ) ? (string) $snippet['description'] : '',
					'video_id'    => $video_id,
					'link'        => $video_id ? 'https://www.youtube.com/watch?v=' . rawurlencode( $video_id ) : '',
					'image'       => $image,
				);
			},
			$items
		);
	}

	private function resolve_youtube_channel_id( array $settings ) {
		$raw_value    = $this->get_effective_setting( $settings, 'youtube_channel_source', 'youtube_channel_source_default' );
		$cache_window = ! empty( $settings['youtube_cache_minutes'] ) ? max( 5, (int) $settings['youtube_cache_minutes'] ) : 60;

		if ( '' === $raw_value && ! empty( $settings['youtube_channel_id'] ) ) {
			$raw_value = trim( (string) $settings['youtube_channel_id'] );
		}

		if ( '' === $raw_value ) {
			return '';
		}

		if ( preg_match( '/\b(UC[a-zA-Z0-9_-]{20,})\b/', $raw_value, $matches ) ) {
			return $matches[1];
		}

		$cache_key = 'foundation_youtube_channel_' . md5( $raw_value );
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return is_string( $cached ) ? $cached : '';
		}

		foreach ( $this->get_youtube_lookup_urls( $raw_value ) as $lookup_url ) {
			$response = wp_remote_get(
				$lookup_url,
				array(
					'timeout' => 12,
				)
			);

			if ( is_wp_error( $response ) ) {
				continue;
			}

			$body       = wp_remote_retrieve_body( $response );
			$channel_id = $this->extract_youtube_channel_id_from_body( $body );

			if ( $channel_id ) {
				set_transient( $cache_key, $channel_id, $cache_window * MINUTE_IN_SECONDS );
				return $channel_id;
			}
		}

		set_transient( $cache_key, '', 15 * MINUTE_IN_SECONDS );
		return '';
	}

	private function get_instagram_feed_items( array $settings ) {
		$account_id   = $this->get_effective_setting( $settings, 'instagram_business_account_id', 'instagram_business_account_id' );
		$access_token = $this->get_effective_setting( $settings, 'instagram_access_token', 'instagram_access_token' );
		$cache_window = ! empty( $settings['instagram_cache_minutes'] ) ? max( 5, (int) $settings['instagram_cache_minutes'] ) : 60;

		if ( '' === $account_id || '' === $access_token ) {
			return array();
		}

		$cache_key = 'foundation_instagram_feed_' . md5( $account_id );
		$cached    = get_transient( $cache_key );

		if ( is_array( $cached ) ) {
			return $cached;
		}

		$response = wp_remote_get(
			'https://graph.facebook.com/v22.0/' . rawurlencode( $account_id ) . '/media?fields=id,caption,media_product_type,media_type,media_url,thumbnail_url,permalink&limit=12&access_token=' . rawurlencode( $access_token ),
			array(
				'timeout' => 12,
			)
		);

		if ( is_wp_error( $response ) ) {
			return array();
		}

		$payload = json_decode( wp_remote_retrieve_body( $response ), true );
		$data    = $payload['data'] ?? array();

		if ( empty( $data ) || ! is_array( $data ) ) {
			return array();
		}

		$items = array();
		foreach ( $data as $item ) {
			$media_product_type = isset( $item['media_product_type'] ) ? (string) $item['media_product_type'] : '';
			$media_type         = isset( $item['media_type'] ) ? (string) $item['media_type'] : '';

			if ( 'REELS' !== $media_product_type && 'VIDEO' !== $media_type ) {
				continue;
			}

			$caption = isset( $item['caption'] ) ? (string) $item['caption'] : '';
			$title   = wp_html_excerpt( trim( preg_split( '/\r\n|\r|\n/', $caption )[0] ?? '' ), 88, '...' );

			$items[] = array(
				'title'       => $title,
				'description' => $caption,
				'image'       => ! empty( $item['thumbnail_url'] ) ? (string) $item['thumbnail_url'] : ( ! empty( $item['media_url'] ) ? (string) $item['media_url'] : '' ),
				'link'        => ! empty( $item['permalink'] ) ? (string) $item['permalink'] : '',
			);
		}

		set_transient( $cache_key, $items, $cache_window * MINUTE_IN_SECONDS );

		return $items;
	}

	private function get_youtube_lookup_urls( $value ) {
		$value = trim( (string) $value );
		$urls  = array();

		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			$urls[] = $value;
			if ( preg_match( '~youtube\.com/(@[^/?#]+)~', $value, $matches ) ) {
				$urls[] = 'https://www.youtube.com/' . $matches[1];
			}
		} elseif ( str_starts_with( $value, '@' ) ) {
			$urls[] = 'https://www.youtube.com/' . ltrim( $value, '/' );
		} else {
			$urls[] = 'https://www.youtube.com/@' . ltrim( $value, '@' );
			$urls[] = 'https://www.youtube.com/c/' . rawurlencode( $value );
			$urls[] = 'https://www.youtube.com/user/' . rawurlencode( $value );
		}

		return array_values( array_unique( $urls ) );
	}

	private function extract_youtube_channel_id_from_body( $body ) {
		$patterns = array(
			'/"channelId":"(UC[a-zA-Z0-9_-]{20,})"/',
			'/"externalId":"(UC[a-zA-Z0-9_-]{20,})"/',
			'/<meta[^>]+itemprop="channelId"[^>]+content="(UC[a-zA-Z0-9_-]{20,})"/i',
			'/<meta[^>]+property="og:url"[^>]+content="https:\/\/www\.youtube\.com\/channel\/(UC[a-zA-Z0-9_-]{20,})"/i',
			'~youtube\.com/channel/(UC[a-zA-Z0-9_-]{20,})~',
		);

		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, (string) $body, $matches ) ) {
				return $matches[1];
			}
		}

		return '';
	}

	private function register_content_controls() {
		$this->start_controls_section(
			'section_header_content',
			array(
				'label' => esc_html__( 'Header', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'header_content_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Eyebrow, title, and subtitle support safe HTML plus shortcodes, so you can add inline spans or things like [ink_team].', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => esc_html__( 'Eyebrow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => esc_html__( 'JOIN US ON INSTAGRAM | @INKFIRELIMITED', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => "Top picks from\nThe Bounce",
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'Subtitle', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'show_controls',
			array(
				'label'        => esc_html__( 'Show Scroll Arrows', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'prev_icon',
			array(
				'label'     => esc_html__( 'Previous Icon', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-arrow-left',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_controls' => 'yes',
				),
			)
		);

		$this->add_control(
			'prev_label',
			array(
				'label'       => esc_html__( 'Previous Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '<-',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_controls' => 'yes',
				),
			)
		);

		$this->add_control(
			'next_icon',
			array(
				'label'     => esc_html__( 'Next Icon', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_controls' => 'yes',
				),
			)
		);

		$this->add_control(
			'next_label',
			array(
				'label'       => esc_html__( 'Next Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '->',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_controls' => 'yes',
				),
			)
		);

		$this->add_control(
			'prev_aria_label',
			array(
				'label'       => esc_html__( 'Previous Aria Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Previous items', 'foundation-elementor-plus' ),
				'label_block' => true,
				'condition'   => array(
					'show_controls' => 'yes',
				),
			)
		);

		$this->add_control(
			'next_aria_label',
			array(
				'label'       => esc_html__( 'Next Aria Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Next items', 'foundation-elementor-plus' ),
				'label_block' => true,
				'condition'   => array(
					'show_controls' => 'yes',
				),
			)
		);

		$this->add_control(
			'all_posts_icon',
			array(
				'label'   => esc_html__( 'All Posts Icon', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'all_posts_text',
			array(
				'label'       => esc_html__( 'All Posts Button Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'All posts ->', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'all_posts_link',
			array(
				'label'         => esc_html__( 'All Posts Link', 'foundation-elementor-plus' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://',
				'show_external' => true,
				'default'       => array(
					'url' => '#',
				),
				'dynamic'       => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'time_icon',
			array(
				'label'   => esc_html__( 'Read Time Icon', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'far fa-clock',
					'library' => 'fa-regular',
				),
			)
		);

		$this->add_control(
			'scroll_step',
			array(
				'label'   => esc_html__( 'Scroll Step', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 302,
				'min'     => 120,
				'max'     => 1200,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_youtube_feed',
			array(
				'label' => esc_html__( 'YouTube Feed', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'youtube_channel_source',
			array(
				'label'       => esc_html__( 'YouTube Channel', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => esc_html__( 'Paste a channel ID, handle like @inkfire, or a full YouTube channel URL. Leave blank to use the global Foundation setting.', 'foundation-elementor-plus' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'youtube_cache_minutes',
			array(
				'label'       => esc_html__( 'Feed Cache Minutes', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 60,
				'min'         => 5,
				'max'         => 1440,
				'description' => esc_html__( 'Keeps the feed fast and avoids repeated requests.', 'foundation-elementor-plus' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_instagram_feed',
			array(
				'label' => esc_html__( 'Instagram Feed', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'instagram_business_account_id',
			array(
				'label'       => esc_html__( 'Instagram Business Account ID', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => esc_html__( 'Optional widget-level override. Leave blank to use the global Foundation setting.', 'foundation-elementor-plus' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'instagram_access_token',
			array(
				'label'       => esc_html__( 'Instagram Access Token', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => esc_html__( 'Optional widget-level override. Needed only for Instagram feed mode.', 'foundation-elementor-plus' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'instagram_cache_minutes',
			array(
				'label'       => esc_html__( 'Instagram Feed Cache Minutes', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 60,
				'min'         => 5,
				'max'         => 1440,
				'description' => esc_html__( 'Keeps the Instagram feed fast and avoids repeated requests.', 'foundation-elementor-plus' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_columns',
			array(
				'label' => esc_html__( 'Columns', 'foundation-elementor-plus' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'column_type',
			array(
				'label'   => esc_html__( 'Column Type', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'stack',
				'options' => array(
					'stack' => esc_html__( 'Post Stack', 'foundation-elementor-plus' ),
					'reel'  => esc_html__( 'Reel', 'foundation-elementor-plus' ),
				),
			)
		);

		$repeater->add_control(
			'column_offset',
			array(
				'label'      => esc_html__( 'Vertical Offset (px)', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 0,
				'min'        => -200,
				'max'        => 400,
				'step'       => 1,
				'description' => esc_html__( 'Use this to stagger columns up or down.', 'foundation-elementor-plus' ),
				'dynamic'    => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'stack_source',
			array(
				'label'     => esc_html__( 'Stack Source', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'manual',
				'options'   => array(
					'manual'  => esc_html__( 'Manual Cards', 'foundation-elementor-plus' ),
					'dynamic' => esc_html__( 'Dynamic Posts', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'column_type' => 'stack',
				),
			)
		);

		$repeater->add_control(
			'query_mode',
			array(
				'label'     => esc_html__( 'Dynamic Query Mode', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'recent',
				'options'   => array(
					'recent'   => esc_html__( 'Recent Posts', 'foundation-elementor-plus' ),
					'category' => esc_html__( 'Category', 'foundation-elementor-plus' ),
					'ids'      => esc_html__( 'Specific Post IDs', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'column_type'  => 'stack',
					'stack_source' => 'dynamic',
				),
			)
		);

		$repeater->add_control(
			'query_category',
			array(
				'label'       => esc_html__( 'Category Slug', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => array(
					'column_type'  => 'stack',
					'stack_source' => 'dynamic',
					'query_mode'   => 'category',
				),
			)
		);

		$repeater->add_control(
			'query_post_ids',
			array(
				'label'       => esc_html__( 'Post IDs', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Comma-separated post IDs.', 'foundation-elementor-plus' ),
				'label_block' => true,
				'condition'   => array(
					'column_type'  => 'stack',
					'stack_source' => 'dynamic',
					'query_mode'   => 'ids',
				),
			)
		);

		$repeater->add_control(
			'query_offset',
			array(
				'label'     => esc_html__( 'Query Offset', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'min'       => 0,
				'max'       => 50,
				'condition' => array(
					'column_type'  => 'stack',
					'stack_source' => 'dynamic',
				),
			)
		);

		for ( $index = 1; $index <= 2; $index++ ) {
			$repeater->add_control(
				'manual_card_' . $index . '_title',
				array(
					'label'       => sprintf( esc_html__( 'Card %d Title', 'foundation-elementor-plus' ), $index ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'dynamic'     => array(
						'active' => true,
					),
					'condition'   => array(
						'column_type'  => 'stack',
						'stack_source' => 'manual',
					),
				)
			);

			$repeater->add_control(
				'manual_card_' . $index . '_image',
				array(
					'label'     => sprintf( esc_html__( 'Card %d Image', 'foundation-elementor-plus' ), $index ),
					'type'      => Controls_Manager::MEDIA,
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'column_type'  => 'stack',
						'stack_source' => 'manual',
					),
				)
			);

			$repeater->add_control(
				'manual_card_' . $index . '_tag',
				array(
					'label'       => sprintf( esc_html__( 'Card %d Tag', 'foundation-elementor-plus' ), $index ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'dynamic'     => array(
						'active' => true,
					),
					'condition'   => array(
						'column_type'  => 'stack',
						'stack_source' => 'manual',
					),
				)
			);

			$repeater->add_control(
				'manual_card_' . $index . '_time',
				array(
					'label'       => sprintf( esc_html__( 'Card %d Time', 'foundation-elementor-plus' ), $index ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'dynamic'     => array(
						'active' => true,
					),
					'condition'   => array(
						'column_type'  => 'stack',
						'stack_source' => 'manual',
					),
				)
			);

			$repeater->add_control(
				'manual_card_' . $index . '_link',
				array(
					'label'         => sprintf( esc_html__( 'Card %d Link', 'foundation-elementor-plus' ), $index ),
					'type'          => Controls_Manager::URL,
					'show_external' => true,
					'dynamic'       => array(
						'active' => true,
					),
					'condition'     => array(
						'column_type'  => 'stack',
						'stack_source' => 'manual',
					),
				)
			);
		}

		$repeater->add_control(
			'reel_youtube_url',
			array(
				'label'       => esc_html__( 'Reel Media URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => esc_html__( 'Paste a YouTube short/video or Instagram reel URL. When metadata is found it overrides the template chip and cover image.', 'foundation-elementor-plus' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'column_type' => 'reel',
				),
			)
		);

		$repeater->add_control(
			'reel_source',
			array(
				'label'     => esc_html__( 'Reel Source', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'manual',
				'options'   => array(
					'manual'         => esc_html__( 'Pasted URL / Manual', 'foundation-elementor-plus' ),
					'youtube_auto'   => esc_html__( 'YouTube Feed', 'foundation-elementor-plus' ),
					'instagram_auto' => esc_html__( 'Instagram Reels Feed', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'column_type' => 'reel',
				),
			)
		);

		$repeater->add_control(
			'youtube_item_index',
			array(
				'label'       => esc_html__( 'Feed Item Position', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 20,
				'description' => esc_html__( '0 = latest item, 1 = second latest, and so on.', 'foundation-elementor-plus' ),
				'condition'   => array(
					'column_type' => 'reel',
					'reel_source' => array( 'youtube_auto', 'instagram_auto' ),
				),
			)
		);

		$repeater->add_control(
			'reel_image',
			array(
				'label'     => esc_html__( 'Reel Cover Image', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'description' => esc_html__( 'Fallback only. Feed or pasted URL metadata will be used first when available.', 'foundation-elementor-plus' ),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'column_type' => 'reel',
				),
			)
		);

		$repeater->add_control(
			'reel_chip_mode',
			array(
				'label'     => esc_html__( 'Reel Chip Content', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'pulled_metadata',
				'options'   => array(
					'pulled_metadata' => esc_html__( 'Pulled Metadata', 'foundation-elementor-plus' ),
					'source_label'    => esc_html__( 'Source Label Pill', 'foundation-elementor-plus' ),
					'custom_text'     => esc_html__( 'Custom Text', 'foundation-elementor-plus' ),
					'hidden'          => esc_html__( 'Hide Chip', 'foundation-elementor-plus' ),
				),
				'description' => esc_html__( 'Choose whether reels show the pulled caption/title, a short source label like Instagram Reel, your own custom text, or no chip at all.', 'foundation-elementor-plus' ),
				'condition' => array(
					'column_type' => 'reel',
				),
			)
		);

		$repeater->add_control(
			'reel_source_label',
			array(
				'label'       => esc_html__( 'Source Label Override', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => esc_html__( 'Instagram Reel', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Optional short pill label. Leave blank to auto-use Instagram Reel, YouTube Short, or YouTube Video.', 'foundation-elementor-plus' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'column_type'    => 'reel',
					'reel_chip_mode' => 'source_label',
				),
			)
		);

		$repeater->add_control(
			'reel_chip',
			array(
				'label'       => esc_html__( 'Reel Chip Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'description' => esc_html__( 'Used as fallback text in Pulled Metadata mode, or as the visible chip text in Custom Text mode.', 'foundation-elementor-plus' ),
				'condition'   => array(
					'column_type' => 'reel',
				),
			)
		);

		$repeater->add_control(
			'reel_link',
			array(
				'label'         => esc_html__( 'Reel Link Override', 'foundation-elementor-plus' ),
				'type'          => Controls_Manager::URL,
				'show_external' => true,
				'dynamic'       => array(
					'active' => true,
				),
				'condition'     => array(
					'column_type' => 'reel',
				),
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'       => esc_html__( 'Rail Columns', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ column_type }}}',
				'default'     => $this->get_default_columns(),
			)
		);

		$this->end_controls_section();
	}

	private function register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'header_gap',
			array(
				'label'      => esc_html__( 'Header Bottom Space', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-header-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_copy_width',
			array(
				'label'      => esc_html__( 'Header Copy Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'default'    => array(
					'unit' => '%',
					'size' => 65,
				),
				'tablet_default' => array(
					'unit' => '%',
					'size' => 100,
				),
				'mobile_default' => array(
					'unit' => '%',
					'size' => 100,
				),
				'range'      => array(
					'%' => array(
						'min' => 35,
						'max' => 100,
					),
					'px' => array(
						'min' => 280,
						'max' => 1200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-copy-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'rail_gap',
			array(
				'label'      => esc_html__( 'Rail Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 22,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-rail-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'track_padding_left',
			array(
				'label'      => esc_html__( 'Rail Left Inset', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 240,
					),
					'%' => array(
						'min' => 0,
						'max' => 30,
					),
					'vw' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-track-pad-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'track_padding_right',
			array(
				'label'      => esc_html__( 'Rail Right Inset', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 240,
					),
					'%' => array(
						'min' => 0,
						'max' => 30,
					),
					'vw' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-track-pad-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'column_width',
			array(
				'label'      => esc_html__( 'Column Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 280,
				),
				'range'      => array(
					'px' => array(
						'min' => 180,
						'max' => 520,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-column-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'column_height',
			array(
				'label'      => esc_html__( 'Column Height', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 540,
				),
				'range'      => array(
					'px' => array(
						'min' => 280,
						'max' => 900,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-column-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_gap',
			array(
				'label'      => esc_html__( 'Stack Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 22,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-card-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'Card Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 26,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-card-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_header_style_controls() {
		$this->start_controls_section(
			'section_header_styles',
			array(
				'label' => esc_html__( 'Header', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'eyebrow_heading',
			array(
				'label'     => esc_html__( 'Eyebrow', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'eyebrow_gradient_start',
			array(
				'label'     => esc_html__( 'Gradient Start', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#07A079',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-eyebrow-start: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'eyebrow_gradient_end',
			array(
				'label'     => esc_html__( 'Gradient End', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.86)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-eyebrow-end: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .foundation-bounce-rail__eyebrow',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .foundation-bounce-rail__title',
			)
		);

		$this->add_control(
			'subtitle_heading',
			array(
				'label'     => esc_html__( 'Subtitle', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'Subtitle Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.82)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .foundation-bounce-rail__subtitle',
			)
		);

		$this->end_controls_section();
	}

	private function render_header_field( $content, $class_name, $default_tag = 'div' ) {
		$content = is_string( $content ) ? trim( $content ) : '';

		if ( '' === $content ) {
			return;
		}

		$rendered = do_shortcode( $content );
		$has_html = (bool) preg_match( '/<[^>]+>/', $rendered );

		if ( $has_html ) {
			printf(
				'<div class="%1$s">%2$s</div>',
				esc_attr( $class_name ),
				wp_kses_post( $rendered )
			);

			return;
		}

		printf(
			'<%1$s class="%2$s">%3$s</%1$s>',
			tag_escape( $default_tag ),
			esc_attr( $class_name ),
			wp_kses_post( nl2br( esc_html( $content ) ) )
		);
	}

	private function register_card_style_controls() {
		$this->start_controls_section(
			'section_card_styles',
			array(
				'label' => esc_html__( 'Cards & Reels', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_background',
			array(
				'label'     => esc_html__( 'Card Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#22263D',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__post-card, {{WRAPPER}} .foundation-bounce-rail__reel' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_background',
			array(
				'label'     => esc_html__( 'Overlay Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F5F5F7',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__overlay' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_text_color',
			array(
				'label'     => esc_html__( 'Overlay Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__overlay, {{WRAPPER}} .foundation-bounce-rail__overlay h3' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'overlay_padding',
			array(
				'label'      => esc_html__( 'Overlay Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 14,
					'right'    => 16,
					'bottom'   => 14,
					'left'     => 16,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail__overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'overlay_shadow',
				'selector' => '{{WRAPPER}} .foundation-bounce-rail__overlay',
			)
		);

		$this->add_responsive_control(
			'overlay_backdrop_blur',
			array(
				'label'      => esc_html__( 'Overlay Backdrop Blur', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail__overlay' => '-webkit-backdrop-filter: blur({{SIZE}}{{UNIT}}); backdrop-filter: blur({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_control(
			'overlay_meta_heading',
			array(
				'label'     => esc_html__( 'Overlay Meta', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'tag_background',
			array(
				'label'     => esc_html__( 'Tag Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#DFFF00',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__tag' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tag_color',
			array(
				'label'     => esc_html__( 'Tag Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__tag' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'time_color',
			array(
				'label'     => esc_html__( 'Time Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(17,17,17,0.7)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__time' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typography',
				'selector' => '{{WRAPPER}} .foundation-bounce-rail__meta',
			)
		);

		$this->add_control(
			'overlay_title_heading',
			array(
				'label'     => esc_html__( 'Overlay Title', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_title_typography',
				'selector' => '{{WRAPPER}} .foundation-bounce-rail__overlay h3',
			)
		);

		$this->add_control(
			'reel_chip_heading',
			array(
				'label'     => esc_html__( 'Reel Chip', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'reel_chip_background',
			array(
				'label'     => esc_html__( 'Reel Chip Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F0C33C',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__reel-chip' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reel_chip_color',
			array(
				'label'     => esc_html__( 'Reel Chip Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4A3200',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__reel-chip' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'reel_chip_backdrop_blur',
			array(
				'label'      => esc_html__( 'Reel Chip Backdrop Blur', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail__reel-chip' => '-webkit-backdrop-filter: blur({{SIZE}}{{UNIT}}); backdrop-filter: blur({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'reel_chip_typography',
				'selector' => '{{WRAPPER}} .foundation-bounce-rail__reel-chip',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .foundation-bounce-rail__post-card, {{WRAPPER}} .foundation-bounce-rail__reel',
			)
		);

		$this->end_controls_section();
	}

	private function register_control_style_controls() {
		$this->start_controls_section(
			'section_controls_styles',
			array(
				'label' => esc_html__( 'Buttons & Icons', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'arrow_heading',
			array(
				'label' => esc_html__( 'Arrow Buttons', 'foundation-elementor-plus' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'      => esc_html__( 'Arrow Button Size', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 52,
				),
				'range'      => array(
					'px' => array(
						'min' => 28,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-arrow-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_radius',
			array(
				'label'      => esc_html__( 'Arrow Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 48,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-arrow-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_icon_size',
			array(
				'label'      => esc_html__( 'Arrow Icon Size', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 48,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-arrow-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'     => esc_html__( 'Arrow Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__arrow' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'Arrow Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_border_color',
			array(
				'label'     => esc_html__( 'Arrow Border Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.18)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__arrow' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_backdrop_blur',
			array(
				'label'      => esc_html__( 'Arrow Backdrop Blur', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail__arrow' => '-webkit-backdrop-filter: blur({{SIZE}}{{UNIT}}); backdrop-filter: blur({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_control(
			'all_posts_heading',
			array(
				'label'     => esc_html__( 'All Posts Button', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'all_button_background',
			array(
				'label'     => esc_html__( 'All Posts Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__all' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'all_button_color',
			array(
				'label'     => esc_html__( 'All Posts Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .foundation-bounce-rail__all' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'all_button_height',
			array(
				'label'      => esc_html__( 'Button Height', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 52,
				),
				'range'      => array(
					'px' => array(
						'min' => 32,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-all-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'all_button_padding',
			array(
				'label'      => esc_html__( 'Button Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'      => 14,
					'right'    => 26,
					'bottom'   => 14,
					'left'     => 26,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-all-padding-y: {{TOP}}{{UNIT}}; --foundation-bounce-all-padding-x: {{RIGHT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'all_button_radius',
			array(
				'label'      => esc_html__( 'Button Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 999,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 999,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-all-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'all_button_gap',
			array(
				'label'      => esc_html__( 'Text / Icon Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 32,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-all-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'all_button_icon_size',
			array(
				'label'      => esc_html__( 'Button Icon Size', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 48,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-all-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'all_button_backdrop_blur',
			array(
				'label'      => esc_html__( 'All Posts Backdrop Blur', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail__all' => '-webkit-backdrop-filter: blur({{SIZE}}{{UNIT}}); backdrop-filter: blur({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'all_button_typography',
				'selector' => '{{WRAPPER}} .foundation-bounce-rail__all',
			)
		);

		$this->add_control(
			'meta_icons_heading',
			array(
				'label'     => esc_html__( 'Meta Icons', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'time_icon_size',
			array(
				'label'      => esc_html__( 'Read Time Icon Size', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 14,
				),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 32,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-bounce-rail' => '--foundation-bounce-time-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function get_default_columns() {
		return array(
			array(
				'column_type'              => 'stack',
				'stack_source'             => 'manual',
				'manual_card_1_title'      => esc_html__( 'How Internal Linking Shapes Compounding SEO Performance', 'foundation-elementor-plus' ),
				'manual_card_1_tag'        => 'SEO',
				'manual_card_1_time'       => '1 Min Read',
				'manual_card_1_image'      => array( 'url' => 'https://images.unsplash.com/photo-1558655146-d09347e92766?q=80&w=1200' ),
				'manual_card_2_title'      => esc_html__( 'Why Topical Authority Is The SEO That Survives Algorithm Swings', 'foundation-elementor-plus' ),
				'manual_card_2_tag'        => 'SEO',
				'manual_card_2_time'       => '1 Min Read',
				'manual_card_2_image'      => array( 'url' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=1200' ),
				'column_offset'            => 0,
			),
			array(
				'column_type'   => 'reel',
				'reel_chip'     => esc_html__( 'YouTube is trialling AI overviews', 'foundation-elementor-plus' ),
				'reel_image'    => array( 'url' => 'https://images.unsplash.com/photo-1559028012-481c04fa702d?q=80&w=1200' ),
				'column_offset' => 22,
			),
			array(
				'column_type'              => 'stack',
				'stack_source'             => 'manual',
				'manual_card_1_title'      => esc_html__( 'Micro-copy That Moves: UX Words That Reduce Doubt', 'foundation-elementor-plus' ),
				'manual_card_1_tag'        => 'Web Design',
				'manual_card_1_time'       => '1 Min Read',
				'manual_card_1_image'      => array( 'url' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=1200' ),
				'manual_card_2_title'      => esc_html__( 'Consent Without Dark Patterns', 'foundation-elementor-plus' ),
				'manual_card_2_tag'        => 'Web Design',
				'manual_card_2_time'       => '1 Min Read',
				'manual_card_2_image'      => array( 'url' => 'https://images.unsplash.com/photo-1492724441997-5dc865305da7?q=80&w=1200' ),
				'column_offset'            => 0,
			),
			array(
				'column_type'   => 'reel',
				'reel_chip'     => esc_html__( "When work gets so busy you haven't gossiped in an hour", 'foundation-elementor-plus' ),
				'reel_image'    => array( 'url' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?q=80&w=1200' ),
				'column_offset' => 22,
			),
		);
	}
}
