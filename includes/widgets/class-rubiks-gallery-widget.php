<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Rubiks_Gallery_Widget extends Widget_Base {
	public function get_name() {
		return 'foundation-rubiks-gallery';
	}

	public function get_title() {
		return esc_html__( 'Foundation Rubiks Gallery', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'gallery', 'rubiks', 'portfolio', 'mixed media' );
	}

	public function get_style_depends(): array {
		return array( 'foundation-elementor-plus-rubiks-gallery' );
	}

	public function get_script_depends(): array {
		return array( 'foundation-elementor-plus-rubiks-gallery' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Media Items', 'foundation-elementor-plus' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'media_type',
			array(
				'label'   => esc_html__( 'Media Type', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => array(
					'image'  => esc_html__( 'Image / SVG', 'foundation-elementor-plus' ),
					'video'  => esc_html__( 'Video', 'foundation-elementor-plus' ),
					'lottie' => esc_html__( 'Lottie JSON', 'foundation-elementor-plus' ),
				),
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Media File', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'media_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'video_url',
			array(
				'label'       => esc_html__( 'Video URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com/video.mp4',
				'condition'   => array(
					'media_type' => 'video',
				),
			)
		);

		$repeater->add_control(
			'video_poster',
			array(
				'label'     => esc_html__( 'Video Poster', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'media_type' => 'video',
				),
			)
		);

		$repeater->add_control(
			'lottie_url',
			array(
				'label'       => esc_html__( 'Lottie JSON URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com/animation.json',
				'condition'   => array(
					'media_type' => 'lottie',
				),
			)
		);

		$repeater->add_control(
			'alt',
			array(
				'label'       => esc_html__( 'Accessible Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Portfolio media item', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Items', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ media_type }}}',
				'default'     => $this->get_default_items(),
			)
		);

		$this->add_control(
			'rotate_interval',
			array(
				'label'   => esc_html__( 'Move Interval (ms)', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1600,
				'min'     => 900,
				'max'     => 4000,
				'step'    => 50,
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => esc_html__( 'Pause On Hover / Focus', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'stage_padding',
			array(
				'label'      => esc_html__( 'Stage Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-rubiks-gallery' => '--foundation-rubiks-padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_gap',
			array(
				'label'      => esc_html__( 'Card Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 36,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-rubiks-gallery' => '--foundation-rubiks-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'Media Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 35,
				),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 48,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-rubiks-gallery' => '--foundation-rubiks-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'stage_ratio',
			array(
				'label'   => esc_html__( 'Stage Aspect Ratio', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3 / 2',
				'options' => array(
					'1 / 1'  => esc_html__( '1:1 Square', 'foundation-elementor-plus' ),
					'16 / 9' => esc_html__( '16:9 Widescreen', 'foundation-elementor-plus' ),
					'4 / 3'  => esc_html__( '4:3 Standard', 'foundation-elementor-plus' ),
					'3 / 2'  => esc_html__( '3:2 Landscape', 'foundation-elementor-plus' ),
					'2 / 3'  => esc_html__( '2:3 Portrait', 'foundation-elementor-plus' ),
					'9 / 16' => esc_html__( '9:16 Portrait Video', 'foundation-elementor-plus' ),
					'21 / 9' => esc_html__( '21:9 Cinematic', 'foundation-elementor-plus' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .foundation-rubiks-gallery' => '--foundation-rubiks-stage-ratio: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'stage_ratio_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'The four cards inherit the stage ratio. Use the media settings below to control how images and video crop inside each tile.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'label'    => esc_html__( 'Card Shadow', 'foundation-elementor-plus' ),
				'selector' => '{{WRAPPER}} .foundation-rubiks-gallery__card::before',
				'fields_options' => array(
					'box_shadow_type' => array(
						'default' => 'yes',
					),
					'color' => array(
						'default' => 'rgba(0, 0, 0, 0.5)',
					),
					'horizontal' => array(
						'default' => array(
							'size' => 0,
						),
					),
					'vertical' => array(
						'default' => array(
							'size' => 10,
						),
					),
					'blur' => array(
						'default' => array(
							'size' => 20,
						),
					),
					'spread' => array(
						'default' => array(
							'size' => -2,
						),
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_media',
			array(
				'label' => esc_html__( 'Media', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'media_fit',
			array(
				'label'   => esc_html__( 'Media Fit', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => array(
					'cover'   => esc_html__( 'Cover', 'foundation-elementor-plus' ),
					'contain' => esc_html__( 'Contain', 'foundation-elementor-plus' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .foundation-rubiks-gallery__image, {{WRAPPER}} .foundation-rubiks-gallery__video' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'media_position',
			array(
				'label'   => esc_html__( 'Media Focal Point', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top center',
				'options' => array(
					'top left'      => esc_html__( 'Top Left', 'foundation-elementor-plus' ),
					'top center'    => esc_html__( 'Top Center', 'foundation-elementor-plus' ),
					'top right'     => esc_html__( 'Top Right', 'foundation-elementor-plus' ),
					'center left'   => esc_html__( 'Center Left', 'foundation-elementor-plus' ),
					'center center' => esc_html__( 'Center', 'foundation-elementor-plus' ),
					'center right'  => esc_html__( 'Center Right', 'foundation-elementor-plus' ),
					'bottom left'   => esc_html__( 'Bottom Left', 'foundation-elementor-plus' ),
					'bottom center' => esc_html__( 'Bottom Center', 'foundation-elementor-plus' ),
					'bottom right'  => esc_html__( 'Bottom Right', 'foundation-elementor-plus' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .foundation-rubiks-gallery__image, {{WRAPPER}} .foundation-rubiks-gallery__video' => 'object-position: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$widget_id     = 'foundation-rubiks-gallery-' . $this->get_id();
		$items         = $this->normalize_items( $settings['items'] ?? array() );
		$seed_items    = $this->get_seed_items( $items );
		$interval      = ! empty( $settings['rotate_interval'] ) ? max( 900, (int) $settings['rotate_interval'] ) : 1600;
		$pause_on_hover = isset( $settings['pause_on_hover'] ) && 'yes' === $settings['pause_on_hover'];

		if ( empty( $items ) ) {
			return;
		}
		?>
		<section
			id="<?php echo esc_attr( $widget_id ); ?>"
			class="foundation-rubiks-gallery"
			data-foundation-rubiks-gallery
			data-interval="<?php echo esc_attr( (string) $interval ); ?>"
			data-pause-on-hover="<?php echo $pause_on_hover ? 'true' : 'false'; ?>"
			aria-label="<?php echo esc_attr__( 'Portfolio media gallery', 'foundation-elementor-plus' ); ?>"
		>
			<div class="foundation-rubiks-gallery__stage">
				<?php foreach ( $seed_items as $index => $item ) : ?>
					<?php
					$slot_label = ! empty( $item['alt'] ) ? $item['alt'] : sprintf( __( 'Portfolio media item %d', 'foundation-elementor-plus' ), $index + 1 );
					$slot_style = $this->get_seed_position_style( $index );
					?>
					<div class="foundation-rubiks-gallery__card" data-foundation-rubiks-slot="<?php echo esc_attr( (string) $index ); ?>" tabindex="0" aria-label="<?php echo esc_attr( $slot_label ); ?>" style="<?php echo esc_attr( $slot_style ); ?>">
						<div class="foundation-rubiks-gallery__surface">
							<?php $this->render_media( $item, true ); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<script type="application/json" class="foundation-rubiks-gallery__data"><?php echo wp_json_encode( $items, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); ?></script>
		</section>
		<?php
	}

	private function normalize_items( array $items ): array {
		$normalized = array();

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$media_type = isset( $item['media_type'] ) ? (string) $item['media_type'] : 'image';
			$entry      = array(
				'media_type'   => in_array( $media_type, array( 'image', 'video', 'lottie' ), true ) ? $media_type : 'image',
				'image_id'     => 0,
				'image_url'    => '',
				'image_render_url' => '',
				'video_url'    => '',
				'video_poster' => '',
				'lottie_url'   => '',
				'alt'          => isset( $item['alt'] ) ? sanitize_text_field( (string) $item['alt'] ) : '',
			);

			if ( 'image' === $entry['media_type'] && ! empty( $item['image']['url'] ) ) {
				$entry['image_id']  = ! empty( $item['image']['id'] ) ? absint( $item['image']['id'] ) : 0;
				$entry['image_url'] = esc_url_raw( (string) $item['image']['url'] );
				$entry['image_render_url'] = $this->get_safe_image_url( $entry['image_url'], $entry['image_id'] );
			}

			if ( 'video' === $entry['media_type'] ) {
				$entry['video_url']    = ! empty( $item['video_url']['url'] ) ? esc_url_raw( (string) $item['video_url']['url'] ) : '';
				$entry['video_poster'] = ! empty( $item['video_poster']['url'] ) ? esc_url_raw( (string) $item['video_poster']['url'] ) : '';
			}

			if ( 'lottie' === $entry['media_type'] ) {
				$entry['lottie_url'] = ! empty( $item['lottie_url']['url'] ) ? esc_url_raw( (string) $item['lottie_url']['url'] ) : '';
			}

			if ( '' === $entry['alt'] ) {
				$entry['alt'] = __( 'Portfolio media item', 'foundation-elementor-plus' );
			}

			if ( ( 'image' === $entry['media_type'] && '' === $entry['image_url'] ) || ( 'video' === $entry['media_type'] && '' === $entry['video_url'] ) || ( 'lottie' === $entry['media_type'] && '' === $entry['lottie_url'] ) ) {
				continue;
			}

			$normalized[] = $entry;
		}

		return $normalized;
	}

	private function get_seed_items( array $items ): array {
		$seed = $items;

		while ( count( $seed ) < 4 && ! empty( $items ) ) {
			$seed = array_merge( $seed, $items );
		}

		return array_slice( $seed, 0, 4 );
	}

	private function get_seed_position_style( int $index ): string {
		$positions = array(
			'top:0;left:0;',
			'top:0;left:calc(50% + (var(--foundation-rubiks-gap) / 2));',
			'top:calc(50% + (var(--foundation-rubiks-gap) / 2));left:0;',
			'top:calc(50% + (var(--foundation-rubiks-gap) / 2));left:calc(50% + (var(--foundation-rubiks-gap) / 2));',
		);

		return $positions[ $index ] ?? $positions[0];
	}

	private function render_media( array $item, bool $is_seed = false ) {
		switch ( $item['media_type'] ) {
			case 'video':
				$poster_attr = ! empty( $item['video_poster'] ) ? sprintf( ' poster="%s"', esc_url( $item['video_poster'] ) ) : '';
				?>
				<video
					class="foundation-rubiks-gallery__video"
					src="<?php echo esc_url( $item['video_url'] ); ?>"
					<?php echo $poster_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					muted
					loop
					playsinline
					preload="metadata"
				></video>
				<?php
				break;

			case 'lottie':
				?>
				<div class="foundation-rubiks-gallery__lottie" data-foundation-rubiks-lottie data-lottie-url="<?php echo esc_url( $item['lottie_url'] ); ?>" aria-hidden="true"></div>
				<?php
				break;

			case 'image':
			default:
				$image_url = ! empty( $item['image_render_url'] ) ? $item['image_render_url'] : $item['image_url'];
				$loading   = $is_seed ? 'eager' : 'lazy';
				?>
				<img class="foundation-rubiks-gallery__image" src="<?php echo esc_url( $image_url ); ?>" alt="" loading="<?php echo esc_attr( $loading ); ?>" decoding="async">
				<?php
				break;
		}
	}

	private function get_safe_image_url( string $url, int $attachment_id = 0 ): string {
		if ( '' === $url ) {
			return '';
		}

		$path = (string) wp_parse_url( $url, PHP_URL_PATH );
		if ( ! $path || ! str_ends_with( strtolower( $path ), '.avif' ) ) {
			return $url;
		}

		$fallback_url = $this->find_existing_image_fallback( $url );
		if ( $fallback_url ) {
			return $fallback_url;
		}

		if ( $attachment_id > 0 ) {
			$generated_url = $this->maybe_generate_image_fallback( $url );
			if ( $generated_url ) {
				return $generated_url;
			}
		}

		return $url;
	}

	private function find_existing_image_fallback( string $url ): string {
		$uploads = wp_upload_dir();
		if ( empty( $uploads['baseurl'] ) || empty( $uploads['basedir'] ) ) {
			return '';
		}

		$baseurl = trailingslashit( $uploads['baseurl'] );
		$basedir = trailingslashit( $uploads['basedir'] );

		if ( ! str_starts_with( $url, $baseurl ) ) {
			return '';
		}

		$relative = ltrim( substr( $url, strlen( $baseurl ) ), '/' );
		$source   = $basedir . $relative;
		$dirname  = dirname( $source );
		$basename = pathinfo( $source, PATHINFO_FILENAME );

		foreach ( array( 'webp', 'jpg', 'jpeg', 'png' ) as $extension ) {
			$candidate = $dirname . '/' . $basename . '.' . $extension;
			if ( file_exists( $candidate ) ) {
				return $baseurl . ltrim( dirname( $relative ), '/' ) . '/' . basename( $candidate );
			}
		}

		return '';
	}

	private function maybe_generate_image_fallback( string $url ): string {
		$uploads = wp_upload_dir();
		if ( empty( $uploads['baseurl'] ) || empty( $uploads['basedir'] ) ) {
			return '';
		}

		$baseurl = trailingslashit( $uploads['baseurl'] );
		$basedir = trailingslashit( $uploads['basedir'] );

		if ( ! str_starts_with( $url, $baseurl ) ) {
			return '';
		}

		$relative    = ltrim( substr( $url, strlen( $baseurl ) ), '/' );
		$source_path = $basedir . $relative;

		if ( ! file_exists( $source_path ) ) {
			return '';
		}

		$dirname       = dirname( $source_path );
		$basename      = pathinfo( $source_path, PATHINFO_FILENAME );
		$fallback_path = $dirname . '/' . $basename . '.jpg';
		$fallback_url  = $baseurl . ltrim( dirname( $relative ), '/' ) . '/' . basename( $fallback_path );

		if ( file_exists( $fallback_path ) ) {
			return $fallback_url;
		}

		if ( function_exists( 'imagecreatefromavif' ) && function_exists( 'imagejpeg' ) ) {
			$image = @imagecreatefromavif( $source_path );
			if ( $image ) {
				@imagejpeg( $image, $fallback_path, 88 );
				imagedestroy( $image );
				if ( file_exists( $fallback_path ) ) {
					return $fallback_url;
				}
			}
		}

		if ( class_exists( '\Imagick' ) ) {
			try {
				$imagick = new \Imagick( $source_path );
				$imagick->setImageFormat( 'jpeg' );
				$imagick->setImageCompressionQuality( 88 );
				$imagick->writeImage( $fallback_path );
				$imagick->clear();
				$imagick->destroy();
				if ( file_exists( $fallback_path ) ) {
					return $fallback_url;
				}
			} catch ( \Throwable $exception ) {
				return '';
			}
		}

		return '';
	}

	private function get_default_items(): array {
		return array(
			array(
				'media_type' => 'image',
				'image'      => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'alt'        => esc_html__( 'Portfolio screenshot one', 'foundation-elementor-plus' ),
			),
			array(
				'media_type' => 'image',
				'image'      => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'alt'        => esc_html__( 'Portfolio screenshot two', 'foundation-elementor-plus' ),
			),
			array(
				'media_type' => 'image',
				'image'      => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'alt'        => esc_html__( 'Portfolio screenshot three', 'foundation-elementor-plus' ),
			),
			array(
				'media_type' => 'image',
				'image'      => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'alt'        => esc_html__( 'Portfolio screenshot four', 'foundation-elementor-plus' ),
			),
		);
	}
}
