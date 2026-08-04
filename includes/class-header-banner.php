<?php

namespace FoundationElementorPlus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Header_Banner {
	const OPTION_KEY = 'foundation_header_banner_settings';
	const MENU_SLUG  = 'foundation-header-banner';

	public function hooks() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_shortcode( 'foundation_header_banner', array( $this, 'render_shortcode' ) );
		add_action( 'wp_body_open', array( $this, 'render_site_banner' ), 20 );
	}

	public function register_settings() {
		register_setting(
			'foundation_header_banner',
			self::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => $this->get_default_settings(),
			)
		);

		add_settings_section(
			'foundation_header_banner_main',
			esc_html__( 'Banner Content', 'foundation-elementor-plus' ),
			'__return_false',
			self::MENU_SLUG
		);

		$this->add_text_field( 'label', esc_html__( 'Label', 'foundation-elementor-plus' ), esc_html__( 'Small tag shown before the message.', 'foundation-elementor-plus' ) );
		$this->add_textarea_field( 'message', esc_html__( 'Message', 'foundation-elementor-plus' ), esc_html__( 'Main banner copy shown in the header.', 'foundation-elementor-plus' ) );
		$this->add_text_field( 'button_text', esc_html__( 'Button Text', 'foundation-elementor-plus' ), esc_html__( 'Optional. Leave blank to hide the button.', 'foundation-elementor-plus' ) );
		$this->add_text_field( 'button_url', esc_html__( 'Button URL', 'foundation-elementor-plus' ), esc_html__( 'Optional. Relative or absolute URL.', 'foundation-elementor-plus' ) );
		add_settings_field(
			'enabled',
			esc_html__( 'Display Banner', 'foundation-elementor-plus' ),
			array( $this, 'render_enabled_field' ),
			self::MENU_SLUG,
			'foundation_header_banner_main'
		);
	}

	public function register_admin_menu() {
		add_menu_page(
			esc_html__( 'Banner', 'foundation-elementor-plus' ),
			esc_html__( 'Banner', 'foundation-elementor-plus' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render_admin_page' ),
			'dashicons-megaphone',
			32
		);
	}

	public function render_admin_page() {
		$settings = $this->get_settings();
		?>
		<div class="wrap" style="max-width:820px;">
			<h1><?php esc_html_e( 'Header Banner', 'foundation-elementor-plus' ); ?></h1>
			<p><?php esc_html_e( 'Update the small glass notice shown above the site header. Changes will apply anywhere the header banner shortcode is used.', 'foundation-elementor-plus' ); ?></p>

			<form method="post" action="options.php">
				<?php settings_fields( 'foundation_header_banner' ); ?>
				<?php do_settings_sections( self::MENU_SLUG ); ?>
				<?php submit_button( esc_html__( 'Save banner', 'foundation-elementor-plus' ) ); ?>
			</form>

			<div style="margin-top:28px;padding:20px 24px;border-radius:18px;background:#fff;border:1px solid #e5e7eb;box-shadow:0 10px 24px rgba(15,23,42,0.06);">
				<h2 style="margin-top:0;"><?php esc_html_e( 'Preview', 'foundation-elementor-plus' ); ?></h2>
				<div style="position:relative;height:96px;background:linear-gradient(180deg,#171826 0%,#13141f 100%);border-radius:18px;padding:20px;overflow:hidden;">
					<?php echo $this->render_shortcode(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function render_shortcode() {
		if ( ! is_admin() ) {
			return '<style>.elementor-element-fdhban1{display:none !important;}</style>';
		}

		return $this->get_banner_markup( true );
	}

	public function render_site_banner() {
		$settings = $this->get_settings();

		if ( empty( $settings['enabled'] ) || empty( $settings['message'] ) ) {
			return;
		}

		echo $this->get_banner_markup( false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function get_banner_markup( $is_preview = false ) {
		$settings = $this->get_settings();

		if ( empty( $settings['enabled'] ) || empty( $settings['message'] ) ) {
			return '';
		}

		$has_button = ! empty( $settings['button_text'] ) && ! empty( $settings['button_url'] );
		$wrap_class   = $is_preview ? 'foundation-header-build-banner-wrap foundation-header-build-banner-wrap--preview' : 'foundation-header-build-banner-wrap';
		$inner_class  = 'foundation-header-build-banner__inner' . ( $has_button ? '' : ' foundation-header-build-banner__inner--no-button' );
		$copy_class   = 'foundation-header-build-banner__copy' . ( $has_button ? '' : ' foundation-header-build-banner__copy--no-button' );

		ob_start();
		?>
		<div class="<?php echo esc_attr( $wrap_class ); ?>">
			<div class="foundation-header-build-banner" role="status" aria-live="polite">
				<div class="<?php echo esc_attr( $inner_class ); ?>">
					<div class="<?php echo esc_attr( $copy_class ); ?>">
						<?php if ( ! empty( $settings['label'] ) ) : ?>
							<span class="foundation-header-build-banner__label"><?php echo esc_html( $settings['label'] ); ?></span>
						<?php endif; ?>
						<span class="foundation-header-build-banner__text"><?php echo esc_html( $settings['message'] ); ?></span>
					</div>
					<?php if ( $has_button ) : ?>
						<a class="foundation-header-build-banner__button" href="<?php echo esc_url( $settings['button_url'] ); ?>">
							<span><?php echo esc_html( $settings['button_text'] ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<style>
			:root {
				--foundation-header-banner-offset: 65px;
			}

			.elementor-element-fdhban1 {
				display: none !important;
			}

			.elementor-location-header .elementor-element-c1a7520 {
				top: calc(var(--foundation-header-banner-offset, 0px) + 20px) !important;
			}

			body.admin-bar .elementor-location-header .elementor-element-c1a7520 {
				top: calc(var(--foundation-header-banner-offset, 0px) + 52px) !important;
			}

			.foundation-header-build-banner-wrap {
				position: relative;
				width: 100%;
				box-sizing: border-box;
				z-index: 1002;
				transition: opacity 180ms ease, visibility 180ms ease;
			}

			.foundation-mobile-header-open .foundation-header-build-banner-wrap {
				opacity: 0;
				visibility: hidden;
				pointer-events: none;
			}

			.foundation-header-build-banner-wrap--preview {
				position: absolute;
				inset: 0 auto auto 0;
			}

			.foundation-header-build-banner {
				position: relative;
				width: 100%;
				background-color: #F5977A;
				background-image: linear-gradient(135deg, rgba(226, 114, 0, 0.55) 0%, rgba(219, 88, 68, 0.60) 100%);
				border: 1px solid rgba(226, 114, 0, 0.35);
				border-top: 1px solid rgba(255, 255, 255, 0.25);
				box-shadow: inset 0 1px 0 rgba(255,255,255,0.16), inset 0 -10px 22px rgba(219,88,68,0.08);
				backdrop-filter: blur(12px);
				-webkit-backdrop-filter: blur(12px);
				border-radius: 0;
				color: #ffffff;
				box-sizing: border-box;
				font-family: "Atkinson Hyperlegible Next", "Atkinson Hyperlegible", system-ui, sans-serif;
			}

			.foundation-header-build-banner__inner {
				position: relative;
				z-index: 1;
				width: min(95%, 1800px);
				margin: 0 auto;
				padding: 12px 0;
				display: flex;
				align-items: center;
				justify-content: space-between;
				gap: 1rem;
			}

			.foundation-header-build-banner__copy {
				display: flex;
				align-items: center;
				gap: 0.85rem;
				min-width: 0;
			}

			.foundation-header-build-banner__inner--no-button {
				justify-content: center;
			}

			.foundation-header-build-banner__copy--no-button {
				flex: 1 1 auto;
				justify-content: center;
				text-align: center;
			}

			.foundation-header-build-banner__label {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				padding: 0.22rem 0.56rem;
				border-radius: 999px;
				background: rgba(36,24,34,0.18);
				border: 1px solid rgba(255,255,255,0.18);
				color: #FBCCBF;
				font-family: "Montserrat", system-ui, sans-serif;
				font-size: 0.68rem;
				font-weight: 700;
				letter-spacing: 0.08em;
				text-transform: uppercase;
				white-space: nowrap;
				flex: 0 0 auto;
			}

			.foundation-header-build-banner__text {
				font-size: 0.9rem;
				line-height: 1.45;
				font-weight: 600;
				color: #ffffff;
				min-width: 0;
			}

			.foundation-header-build-banner__button {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				min-height: 44px;
				padding: 0.5rem 0.95rem;
				border-radius: 999px;
				background: rgba(36,24,34,0.9);
				border: 1px solid rgba(255,255,255,0.2);
				color: #ffffff;
				font-size: 0.8rem;
				line-height: 1.2;
				font-weight: 700;
				text-decoration: none;
				white-space: nowrap;
				flex: 0 0 auto;
			}

			.foundation-header-build-banner__button:focus-visible {
				outline: 3px solid #241822;
				outline-offset: 3px;
			}

			.foundation-header-build-banner__button:hover {
				background: rgba(36,24,34,0.98);
			}

			@media (prefers-reduced-motion: reduce) {
				.foundation-header-build-banner__button {
					transition: none;
				}
			}

			@media (max-width: 767px) {
				:root {
					--foundation-header-banner-offset: 108px;
				}

				.foundation-header-build-banner__inner {
					width: min(95%, 520px);
					padding: 12px 0;
					gap: 0.6rem;
					flex-wrap: wrap;
				}

				.foundation-header-build-banner__copy {
					flex-wrap: wrap;
					justify-content: center;
				}

				.foundation-header-build-banner__text {
					font-size: 0.82rem;
					text-align: center;
				}

				.foundation-header-build-banner__label {
					font-size: 0.62rem;
					padding: 0.18rem 0.46rem;
				}

				.foundation-header-build-banner__button {
					font-size: 0.78rem;
					padding: 0.5rem 0.85rem;
				}
			}

			@media (max-width: 782px) {
				body.admin-bar .elementor-location-header .elementor-element-c1a7520 {
					top: calc(var(--foundation-header-banner-offset, 0px) + 66px) !important;
				}
			}
		</style>
		<script>
			(function() {
				if (<?php echo $is_preview ? 'true' : 'false'; ?>) {
					return;
				}

				function setFoundationBannerOffset() {
					var banner = document.querySelector('.foundation-header-build-banner-wrap');
					if (!banner) {
						return;
					}
					document.documentElement.style.setProperty('--foundation-header-banner-offset', banner.offsetHeight + 'px');
				}

				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', setFoundationBannerOffset, { once: true });
				} else {
					setFoundationBannerOffset();
				}

				window.addEventListener('load', setFoundationBannerOffset);
				window.addEventListener('resize', setFoundationBannerOffset);
			})();
		</script>
		<?php

		return ob_get_clean();
	}

	public function sanitize_settings( $input ) {
		$defaults = $this->get_default_settings();
		$input    = is_array( $input ) ? $input : array();

		return array(
			'enabled'     => ! empty( $input['enabled'] ) ? 1 : 0,
			'label'       => isset( $input['label'] ) ? sanitize_text_field( wp_unslash( $input['label'] ) ) : $defaults['label'],
			'message'     => isset( $input['message'] ) ? sanitize_text_field( wp_unslash( $input['message'] ) ) : $defaults['message'],
			'button_text' => isset( $input['button_text'] ) ? sanitize_text_field( wp_unslash( $input['button_text'] ) ) : $defaults['button_text'],
			'button_url'  => isset( $input['button_url'] ) ? esc_url_raw( wp_unslash( $input['button_url'] ) ) : $defaults['button_url'],
		);
	}

	private function add_text_field( $key, $label, $description ) {
		add_settings_field(
			$key,
			$label,
			array( $this, 'render_text_field' ),
			self::MENU_SLUG,
			'foundation_header_banner_main',
			array(
				'key'         => $key,
				'description' => $description,
			)
		);
	}

	private function add_textarea_field( $key, $label, $description ) {
		add_settings_field(
			$key,
			$label,
			array( $this, 'render_textarea_field' ),
			self::MENU_SLUG,
			'foundation_header_banner_main',
			array(
				'key'         => $key,
				'description' => $description,
			)
		);
	}

	public function render_enabled_field() {
		$settings = $this->get_settings();
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[enabled]" value="1" <?php checked( ! empty( $settings['enabled'] ) ); ?>>
			<?php esc_html_e( 'Show the banner in the header', 'foundation-elementor-plus' ); ?>
		</label>
		<?php
	}

	public function render_text_field( $args ) {
		$settings = $this->get_settings();
		$key      = $args['key'];
		?>
		<input type="text" class="regular-text" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $settings[ $key ] ?? '' ); ?>">
		<?php if ( ! empty( $args['description'] ) ) : ?>
			<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	public function render_textarea_field( $args ) {
		$settings = $this->get_settings();
		$key      = $args['key'];
		?>
		<textarea class="large-text" rows="4" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $settings[ $key ] ?? '' ); ?></textarea>
		<?php if ( ! empty( $args['description'] ) ) : ?>
			<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	private function get_settings() {
		$settings = get_option( self::OPTION_KEY, array() );
		return wp_parse_args( is_array( $settings ) ? $settings : array(), $this->get_default_settings() );
	}

	private function get_default_settings() {
		return array(
			'enabled'     => 1,
			'label'       => 'Beta update',
			'message'     => 'This website is still being built and refined. Some pages, links and content are still being updated.',
			'button_text' => '',
			'button_url'  => '',
		);
	}
}
