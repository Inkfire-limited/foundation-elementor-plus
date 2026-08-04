<?php

namespace FoundationElementorPlus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Team_Inline_Images {
	const OPTION_KEY   = 'foundation_team_inline_images';
	const MENU_SLUG    = 'foundation-team-inline-images';
	const NONCE_ACTION = 'foundation_team_inline_images_save';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 30 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_post_foundation_team_inline_images_save', array( $this, 'handle_form_submission' ) );
		add_action( 'init', array( $this, 'register_shortcodes' ), 100 );
	}

	/**
	 * Register submenu under Foundation.
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_submenu_page(
			Plugin::FOUNDATION_PARENT_SLUG,
			esc_html__( 'Team Inline Images', 'foundation-elementor-plus' ),
			esc_html__( 'Team Inline Images', 'foundation-elementor-plus' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Enqueue media modal for admin editor.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		if ( 'foundation_page_' . self::MENU_SLUG !== $hook_suffix ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/**
	 * Save settings.
	 *
	 * @return void
	 */
	public function handle_form_submission() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'foundation-elementor-plus' ) );
		}

		check_admin_referer( self::NONCE_ACTION );

		$current_tab = isset( $_POST['foundation_team_inline_current_tab'] ) ? sanitize_key( wp_unslash( (string) $_POST['foundation_team_inline_current_tab'] ) ) : 'all';
		$departments = $this->get_departments();
		$current_tab = isset( $departments[ $current_tab ] ) ? $current_tab : 'all';
		$raw         = isset( $_POST['foundation_team_inline_images'] ) ? wp_unslash( $_POST['foundation_team_inline_images'] ) : array();
		$data        = $this->get_option();
		$sanitized   = $this->sanitize_option(
			array(
				$current_tab => isset( $raw[ $current_tab ] ) ? $raw[ $current_tab ] : array(),
			)
		);

		$data[ $current_tab ] = $sanitized[ $current_tab ];

		update_option( self::OPTION_KEY, $data, false );

		$redirect_url = add_query_arg(
			array(
				'page'    => self::MENU_SLUG,
				'updated' => 'true',
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Register shortcodes.
	 *
	 * @return void
	 */
	public function register_shortcodes() {
		add_shortcode( 'ink_team', array( $this, 'render_shortcode' ) );

		foreach ( array_keys( $this->get_departments() ) as $department ) {
			if ( 'all' === $department ) {
				continue;
			}

			add_shortcode(
				'ink_team_' . $department,
				function ( $atts = array() ) use ( $department ) {
					$atts['department'] = $department;
					return $this->render_shortcode( $atts );
				}
			);
		}
	}

	/**
	 * Render shortcode output.
	 *
	 * @param array<string, mixed> $atts Shortcode attributes.
	 * @return string
	 */
	public function render_shortcode( $atts = array() ) {
		static $did_print_styles = false;

		$atts = shortcode_atts(
			array(
				'department' => 'all',
				'size'       => '45px',
				'class'      => '',
			),
			(array) $atts,
			'ink_team'
		);

		$department = sanitize_key( (string) $atts['department'] );
		$departments = $this->get_departments();

		if ( ! isset( $departments[ $department ] ) ) {
			$department = 'all';
		}

		$image_ids = $this->get_images_for_department( $department );

		if ( empty( $image_ids ) ) {
			return '';
		}

		$size         = $this->sanitize_size_value( (string) $atts['size'], '45px' );
		$border_color = '#0b0c15';
		$extra_class   = trim( (string) $atts['class'] );
		$is_hero_strip = false !== strpos( $extra_class, 'foundation-team-inline--hero' );
		$wrapper_class = trim( 'foundation-team-inline ' . ( $is_hero_strip ? '' : 'foundation-team-inline--stacked ' ) . $extra_class );
		$label = 'all' === $department
			? __( 'Selected team portraits', 'foundation-elementor-plus' )
			: sprintf(
				/* translators: %s is department name. */
				__( '%s team portraits', 'foundation-elementor-plus' ),
				$departments[ $department ]
			);

		$output  = '';

		if ( ! $did_print_styles ) {
			$output         .= '<style id="foundation-team-inline-styles">.foundation-team-inline{display:inline-flex;flex-wrap:wrap;align-items:center;justify-content:center;max-width:100%;vertical-align:middle;margin:0 5px;line-height:0;row-gap:12px}.foundation-team-inline__avatar{display:block;width:var(--foundation-team-inline-size,45px)!important;height:var(--foundation-team-inline-size,45px)!important;min-width:var(--foundation-team-inline-size,45px);min-height:var(--foundation-team-inline-size,45px);border-radius:50%!important;object-fit:cover;border:3px solid var(--foundation-team-inline-border,#0b0c15);box-sizing:content-box;background:#222;overflow:hidden;box-shadow:0 0 5px 1px rgba(0,0,0,.55),inset 0 1px 0 rgba(255,255,255,.01)}.foundation-team-inline--stacked{gap:0}.foundation-team-inline--stacked .foundation-team-inline__avatar{margin-left:-8px}.foundation-team-inline--stacked .foundation-team-inline__avatar:first-child{margin-left:0}.foundation-team-inline--hero{justify-content:flex-start;gap:10px 0;padding-left:15px}.foundation-team-inline--hero .foundation-team-inline__avatar{margin-left:-15px}.foundation-team-inline--hero .foundation-team-inline__avatar:first-child{margin-left:0}@media (max-width: 900px){.foundation-team-inline--hero{justify-content:center;gap:10px;padding-left:0}.foundation-team-inline--hero .foundation-team-inline__avatar{margin-left:0}}@media (max-width:768px){.foundation-team-inline--stacked .foundation-team-inline__avatar{margin-left:-10px;margin-bottom:-10px}}</style>';
			$did_print_styles = true;
		}

		$output .= '<span class="' . esc_attr( $wrapper_class ) . '" role="img" aria-label="' . esc_attr( $label ) . '" style="--foundation-team-inline-size:' . esc_attr( $size ) . ';--foundation-team-inline-border:' . esc_attr( $border_color ) . ';">';
		$z_index = count( $image_ids ) + 10;

		foreach ( $image_ids as $index => $image_id ) {
			$image_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );

			if ( ! $image_url ) {
				continue;
			}

			$style = $is_hero_strip ? 'position:relative;z-index:' . esc_attr( (string) ( $z_index - $index ) ) . ';' : '';
			$output .= '<img class="foundation-team-inline__avatar" src="' . esc_url( $image_url ) . '" alt="" aria-hidden="true" style="' . $style . '">';
		}

		$output .= '</span>';

		return $output;
	}

	/**
	 * Render admin screen.
	 *
	 * @return void
	 */
	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'foundation-elementor-plus' ) );
		}

		$departments  = $this->get_departments();
		$options      = $this->get_option();
		$current_tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( (string) $_GET['tab'] ) ) : 'all';
		$current_tab  = isset( $departments[ $current_tab ] ) ? $current_tab : 'all';
		$form_action  = admin_url( 'admin-post.php' );
		$page_url     = admin_url( 'admin.php?page=' . self::MENU_SLUG );
		$is_updated   = isset( $_GET['updated'] ) && 'true' === $_GET['updated'];
		$usage_report = $this->get_shortcode_usage_report( $current_tab );
		?>
		<div class="wrap foundation-team-inline-admin">
			<style>
				.foundation-team-inline-admin {
					max-width: 1180px;
				}
				.foundation-team-inline-admin__hero {
					margin-top: 24px;
					padding: 24px 28px;
					border-radius: 24px;
					background: linear-gradient(135deg, #13141f 0%, #1c1d2d 55%, #0e6055 100%);
					color: #f4f7fb;
					box-shadow: 0 18px 48px rgba(19, 20, 31, 0.18);
				}
				.foundation-team-inline-admin__hero h1 {
					margin: 0 0 8px;
					color: #fff;
					font-size: 28px;
				}
				.foundation-team-inline-admin__hero p {
					margin: 0;
					max-width: 760px;
					color: rgba(244, 247, 251, 0.84);
					line-height: 1.6;
				}
				.foundation-team-inline-admin__notice {
					margin: 16px 0 0;
				}
				.foundation-team-inline-admin__tabs {
					margin: 24px 0 18px;
					border-bottom: 1px solid #d9dfeb;
				}
				.foundation-team-inline-admin__layout {
					display: grid;
					grid-template-columns: minmax(0, 1.5fr) minmax(320px, 0.9fr);
					gap: 24px;
					align-items: start;
				}
				.foundation-team-inline-admin__panel {
					background: #fff;
					border: 1px solid #e5e9f1;
					border-radius: 22px;
					padding: 24px;
					box-shadow: 0 14px 36px rgba(19, 20, 31, 0.06);
				}
				.foundation-team-inline-admin__eyebrow {
					display: inline-block;
					margin-bottom: 10px;
					font-size: 12px;
					font-weight: 700;
					letter-spacing: 0.08em;
					text-transform: uppercase;
					color: #0e6055;
				}
				.foundation-team-inline-admin__shortcodes {
					display: grid;
					gap: 12px;
					margin: 18px 0 22px;
				}
				.foundation-team-inline-admin__code {
					display: flex;
					align-items: center;
					justify-content: space-between;
					gap: 14px;
					padding: 14px 16px;
					border-radius: 16px;
					background: #f5f7fb;
					border: 1px solid #e3e7f1;
				}
				.foundation-team-inline-admin__code code {
					font-size: 13px;
				}
				.foundation-team-inline-admin__images {
					display: grid;
					gap: 14px;
				}
				.foundation-team-inline-admin__row {
					display: grid;
					grid-template-columns: auto 72px minmax(0, 1fr) auto;
					gap: 14px;
					align-items: center;
					padding: 14px;
					border-radius: 16px;
					border: 1px solid #e4e8f1;
					background: #fafbfd;
				}
				.foundation-team-inline-admin__row.is-sorting {
					box-shadow: 0 18px 36px rgba(19, 20, 31, 0.14);
				}
				.foundation-team-inline-admin__row--placeholder {
					min-height: 100px;
					border: 2px dashed #9fb2c9;
					background: rgba(14, 96, 85, 0.06);
				}
				.foundation-team-inline-admin__drag {
					display: inline-flex;
					align-items: center;
					justify-content: center;
					width: 38px;
					height: 38px;
					padding: 0;
					border: 0;
					border-radius: 12px;
					background: transparent;
					color: #7b8399;
					cursor: grab;
				}
				.foundation-team-inline-admin__drag:hover,
				.foundation-team-inline-admin__drag:focus {
					background: #eef2f8;
					color: #13141f;
				}
				.foundation-team-inline-admin__drag:active {
					cursor: grabbing;
				}
				.foundation-team-inline-admin__drag .dashicons {
					width: 18px;
					height: 18px;
					font-size: 18px;
				}
				.foundation-team-inline-admin__thumb {
					width: 72px;
					height: 72px;
					border-radius: 18px;
					overflow: hidden;
					background: linear-gradient(135deg, #edf1f8 0%, #dfe8f2 100%);
				}
				.foundation-team-inline-admin__thumb img {
					display: block;
					width: 100%;
					height: 100%;
					object-fit: cover;
				}
				.foundation-team-inline-admin__meta strong {
					display: block;
					color: #13141f;
				}
				.foundation-team-inline-admin__meta span {
					display: block;
					margin-top: 4px;
					color: #5f6780;
					word-break: break-all;
				}
				.foundation-team-inline-admin__row-actions {
					display: flex;
					flex-direction: column;
					align-items: flex-end;
					gap: 8px;
				}
				.foundation-team-inline-admin__usage {
					display: grid;
					gap: 14px;
					margin-top: 24px;
					padding-top: 20px;
					border-top: 1px solid #e5e9f1;
				}
				.foundation-team-inline-admin__usage-summary {
					padding: 16px 18px;
					border-radius: 18px;
					background: #f7fafc;
					border: 1px solid #e4e8f1;
				}
				.foundation-team-inline-admin__usage-summary strong {
					display: block;
					color: #13141f;
				}
				.foundation-team-inline-admin__usage-summary p {
					margin: 6px 0 0;
					color: #5f6780;
					line-height: 1.6;
				}
				.foundation-team-inline-admin__usage-list {
					display: grid;
					gap: 12px;
				}
				.foundation-team-inline-admin__usage-item {
					padding: 16px 18px;
					border-radius: 18px;
					border: 1px solid #e4e8f1;
					background: #fafbfd;
				}
				.foundation-team-inline-admin__usage-head {
					display: flex;
					align-items: flex-start;
					justify-content: space-between;
					gap: 14px;
				}
				.foundation-team-inline-admin__usage-head strong {
					display: block;
					color: #13141f;
				}
				.foundation-team-inline-admin__usage-meta {
					display: flex;
					flex-wrap: wrap;
					gap: 8px;
					margin-top: 10px;
				}
				.foundation-team-inline-admin__badge {
					display: inline-flex;
					align-items: center;
					padding: 6px 10px;
					border-radius: 999px;
					background: #eef3f8;
					color: #42506a;
					font-size: 12px;
					font-weight: 600;
				}
				.foundation-team-inline-admin__usage-sources {
					margin: 10px 0 0;
					color: #5f6780;
					line-height: 1.6;
				}
				.foundation-team-inline-admin__empty {
					padding: 22px;
					border: 1px dashed #ccd5e2;
					border-radius: 18px;
					text-align: center;
					color: #66708a;
					background: #fcfdff;
				}
				.foundation-team-inline-admin__actions {
					display: flex;
					flex-wrap: wrap;
					gap: 12px;
					margin-top: 18px;
				}
				.foundation-team-inline-admin__preview-frame {
					padding: 18px 18px 22px;
					border-radius: 20px;
					background: radial-gradient(circle at 0% 0%, rgba(251,204,191,0.12), transparent 48%), radial-gradient(circle at 100% 0%, rgba(14,96,85,0.2), transparent 55%), #13141f;
					color: #fff;
				}
				.foundation-team-inline-admin__preview-label {
					margin: 0 0 12px;
					font-size: 12px;
					font-weight: 700;
					letter-spacing: 0.08em;
					text-transform: uppercase;
					color: rgba(255,255,255,0.72);
				}
				.foundation-team-inline-admin__preview {
					min-height: 56px;
					display: flex;
					align-items: center;
				}
				.foundation-team-inline-admin__help {
					margin-top: 16px;
					color: #5f6780;
					line-height: 1.6;
				}
				.foundation-team-inline-admin__fallback {
					margin-top: 10px;
					color: #5f6780;
					font-size: 13px;
				}
				.foundation-team-inline-admin .button.button-primary {
					background: #0e6055;
					border-color: #0e6055;
				}
				@media (max-width: 980px) {
					.foundation-team-inline-admin__layout {
						grid-template-columns: 1fr;
					}
				}
				@media (max-width: 720px) {
					.foundation-team-inline-admin__row {
						grid-template-columns: auto 56px minmax(0, 1fr);
					}
					.foundation-team-inline-admin__row-actions {
						grid-column: 2 / -1;
						flex-direction: row;
						align-items: center;
						justify-content: flex-start;
					}
					.foundation-team-inline-admin__usage-head {
						flex-direction: column;
					}
				}
			</style>

			<div class="foundation-team-inline-admin__hero">
				<h1><?php esc_html_e( 'Team Inline Images', 'foundation-elementor-plus' ); ?></h1>
				<p><?php esc_html_e( 'Manage the image stacks used by Inkfire team shortcodes. Keep the generic team strip for existing placements, then customise department-specific variants for management, IT, web, marketing, and branding.', 'foundation-elementor-plus' ); ?></p>
				<?php if ( $is_updated ) : ?>
					<div class="notice notice-success inline foundation-team-inline-admin__notice"><p><?php esc_html_e( 'Team image shortcodes updated.', 'foundation-elementor-plus' ); ?></p></div>
				<?php endif; ?>
			</div>

			<nav class="nav-tab-wrapper foundation-team-inline-admin__tabs" aria-label="<?php esc_attr_e( 'Team shortcode groups', 'foundation-elementor-plus' ); ?>">
				<?php foreach ( $departments as $key => $label ) : ?>
					<?php $tab_url = add_query_arg( array( 'page' => self::MENU_SLUG, 'tab' => $key ), admin_url( 'admin.php' ) ); ?>
					<a href="<?php echo esc_url( $tab_url ); ?>" class="nav-tab<?php echo $current_tab === $key ? ' nav-tab-active' : ''; ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</nav>

			<form action="<?php echo esc_url( $form_action ); ?>" method="post">
				<input type="hidden" name="action" value="foundation_team_inline_images_save" />
				<input type="hidden" name="foundation_team_inline_current_tab" value="<?php echo esc_attr( $current_tab ); ?>" />
				<?php wp_nonce_field( self::NONCE_ACTION ); ?>

				<div class="foundation-team-inline-admin__layout">
					<div class="foundation-team-inline-admin__panel">
						<span class="foundation-team-inline-admin__eyebrow"><?php esc_html_e( 'Editable Images', 'foundation-elementor-plus' ); ?></span>
						<h2><?php echo esc_html( $departments[ $current_tab ] ); ?></h2>

						<div class="foundation-team-inline-admin__shortcodes">
							<div class="foundation-team-inline-admin__code">
								<div>
									<strong><?php esc_html_e( 'Primary shortcode', 'foundation-elementor-plus' ); ?></strong><br />
									<code><?php echo esc_html( $this->get_shortcode_example( $current_tab, false ) ); ?></code>
								</div>
							</div>
							<?php if ( 'all' !== $current_tab ) : ?>
								<div class="foundation-team-inline-admin__code">
									<div>
										<strong><?php esc_html_e( 'Department alias', 'foundation-elementor-plus' ); ?></strong><br />
										<code><?php echo esc_html( $this->get_shortcode_example( $current_tab, true ) ); ?></code>
									</div>
								</div>
							<?php endif; ?>
						</div>

						<div class="foundation-team-inline-admin__images" id="foundation-team-inline-list">
							<?php foreach ( $options[ $current_tab ] as $image_id ) : ?>
								<?php echo $this->get_admin_row_markup( $image_id, $current_tab ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php endforeach; ?>
						</div>

						<div class="foundation-team-inline-admin__empty" id="foundation-team-inline-empty"<?php echo ! empty( $options[ $current_tab ] ) ? ' hidden' : ''; ?>>
							<?php esc_html_e( 'No images added yet. Use “Add images from Media Library” to build this shortcode.', 'foundation-elementor-plus' ); ?>
						</div>

						<div class="foundation-team-inline-admin__actions">
							<button type="button" class="button" id="foundation-team-inline-add"><?php esc_html_e( 'Add images from Media Library', 'foundation-elementor-plus' ); ?></button>
							<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Team Images', 'foundation-elementor-plus' ); ?></button>
						</div>
					</div>

					<div class="foundation-team-inline-admin__panel">
						<span class="foundation-team-inline-admin__eyebrow"><?php esc_html_e( 'Preview', 'foundation-elementor-plus' ); ?></span>
						<div class="foundation-team-inline-admin__preview-frame">
							<p class="foundation-team-inline-admin__preview-label"><?php esc_html_e( 'Live preview', 'foundation-elementor-plus' ); ?></p>
							<div class="foundation-team-inline-admin__preview" id="foundation-team-inline-preview">
								<?php echo $this->render_shortcode( array( 'department' => $current_tab ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						</div>
						<?php if ( 'all' !== $current_tab ) : ?>
							<p class="foundation-team-inline-admin__fallback">
								<?php esc_html_e( 'If this department is left empty, the shortcode will fall back to the generic [ink_team] image set until you add a custom list.', 'foundation-elementor-plus' ); ?>
							</p>
						<?php endif; ?>
						<p class="foundation-team-inline-admin__help">
							<?php esc_html_e( 'Use this area to keep the default team strip stable while building out department-specific groups. Each department can hold as many images as you need, and the preview updates as you edit.', 'foundation-elementor-plus' ); ?>
						</p>

						<div class="foundation-team-inline-admin__usage">
							<span class="foundation-team-inline-admin__eyebrow"><?php esc_html_e( 'Shortcode Usage', 'foundation-elementor-plus' ); ?></span>
							<div class="foundation-team-inline-admin__usage-summary">
								<strong>
									<?php
									echo esc_html(
										sprintf(
											/* translators: 1: number of placements, 2: number of locations. */
											__( 'Used %1$s across %2$s.', 'foundation-elementor-plus' ),
											sprintf(
												_n( '%d placement', '%d placements', $usage_report['total_occurrences'], 'foundation-elementor-plus' ),
												(int) $usage_report['total_occurrences']
											),
											sprintf(
												_n( '%d location', '%d locations', $usage_report['total_locations'], 'foundation-elementor-plus' ),
												(int) $usage_report['total_locations']
											)
										)
									);
									?>
								</strong>
								<p><?php esc_html_e( 'This scan checks both classic post content and Elementor data so you can see exactly where this image strip is currently live.', 'foundation-elementor-plus' ); ?></p>
							</div>
							<?php if ( empty( $usage_report['locations'] ) ) : ?>
								<div class="foundation-team-inline-admin__empty">
									<?php esc_html_e( 'No matching shortcode placements were found yet for this image set.', 'foundation-elementor-plus' ); ?>
								</div>
							<?php else : ?>
								<div class="foundation-team-inline-admin__usage-list">
									<?php foreach ( $usage_report['locations'] as $location ) : ?>
										<div class="foundation-team-inline-admin__usage-item">
											<div class="foundation-team-inline-admin__usage-head">
												<div>
													<strong><?php echo esc_html( $location['title'] ); ?></strong>
													<div class="foundation-team-inline-admin__usage-meta">
														<span class="foundation-team-inline-admin__badge"><?php echo esc_html( $location['post_type_label'] ); ?></span>
														<span class="foundation-team-inline-admin__badge"><?php echo esc_html( $location['status_label'] ); ?></span>
														<span class="foundation-team-inline-admin__badge">
															<?php
															echo esc_html(
																sprintf(
																	_n( '%d placement', '%d placements', $location['occurrences'], 'foundation-elementor-plus' ),
																	(int) $location['occurrences']
																)
															);
															?>
														</span>
													</div>
												</div>
												<a class="button button-secondary" href="<?php echo esc_url( $location['edit_url'] ); ?>"><?php esc_html_e( 'Edit', 'foundation-elementor-plus' ); ?></a>
											</div>
											<p class="foundation-team-inline-admin__usage-sources"><?php echo esc_html( implode( '  |  ', $location['sources'] ) ); ?></p>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</form>

			<script type="text/template" id="foundation-team-inline-row-template">
				<div class="foundation-team-inline-admin__row" data-image-id="{{id}}">
					<button type="button" class="foundation-team-inline-admin__drag" aria-label="<?php esc_attr_e( 'Drag to reorder', 'foundation-elementor-plus' ); ?>">
						<span class="dashicons dashicons-menu-alt3" aria-hidden="true"></span>
					</button>
					<div class="foundation-team-inline-admin__thumb">
						<img src="{{thumbnail}}" alt="" />
					</div>
					<div class="foundation-team-inline-admin__meta">
						<strong>{{title}}</strong>
						<span>{{url}}</span>
						<input type="hidden" name="foundation_team_inline_images[<?php echo esc_attr( $current_tab ); ?>][]" value="{{id}}" />
					</div>
					<div class="foundation-team-inline-admin__row-actions">
						<button type="button" class="button-link foundation-team-inline-replace"><?php esc_html_e( 'Replace', 'foundation-elementor-plus' ); ?></button>
						<button type="button" class="button-link-delete foundation-team-inline-remove"><?php esc_html_e( 'Remove', 'foundation-elementor-plus' ); ?></button>
					</div>
				</div>
			</script>

			<script>
				(function() {
					const addButton = document.getElementById('foundation-team-inline-add');
					const list = document.getElementById('foundation-team-inline-list');
					const emptyState = document.getElementById('foundation-team-inline-empty');
					const preview = document.getElementById('foundation-team-inline-preview');
					const template = document.getElementById('foundation-team-inline-row-template');
					const previewLabel = <?php echo wp_json_encode( 'all' === $current_tab ? __( 'Selected team portraits', 'foundation-elementor-plus' ) : sprintf( __( '%s team portraits', 'foundation-elementor-plus' ), $departments[ $current_tab ] ) ); ?>;
					const replaceText = <?php echo wp_json_encode( __( 'Replace image', 'foundation-elementor-plus' ) ); ?>;
					const replaceButtonText = <?php echo wp_json_encode( __( 'Use this image', 'foundation-elementor-plus' ) ); ?>;
					let dragArmedRow = null;
					let draggedRow = null;

					const renderPreview = () => {
						const rows = Array.from(list.querySelectorAll('[data-image-id]'));
						if (!rows.length) {
							preview.innerHTML = '';
							emptyState.hidden = false;
							return;
						}

						emptyState.hidden = true;

						const html = rows.map((row, index) => {
							const src = row.querySelector('img').getAttribute('src');
							return '<img src="' + src + '" alt="" aria-hidden="true" style="width:45px;height:45px;border-radius:50%;object-fit:cover;border:3px solid #0b0c15;margin-left:' + (index === 0 ? '0' : '-15px') + ';position:relative;z-index:' + (rows.length + 10 - index) + ';box-sizing:content-box;">';
						}).join('');

						preview.innerHTML = '<span role="img" aria-label="' + previewLabel.replace(/"/g, '&quot;') + '" style="display:inline-flex;vertical-align:middle;align-items:center;margin:0 5px;">' + html + '</span>';
					};

					const getRows = () => Array.from(list.querySelectorAll('[data-image-id]'));

					const resetDragState = () => {
						getRows().forEach((row) => {
							row.setAttribute('draggable', 'false');
							row.classList.remove('is-sorting');
						});
						dragArmedRow = null;
						draggedRow = null;
					};

					const armRowForDrag = (row) => {
						resetDragState();
						if (!row) {
							return;
						}

						row.setAttribute('draggable', 'true');
						dragArmedRow = row;
					};

					const escapeHtml = (value) => {
						return String(value)
							.replace(/&/g, '&amp;')
							.replace(/</g, '&lt;')
							.replace(/>/g, '&gt;')
							.replace(/"/g, '&quot;')
							.replace(/'/g, '&#039;');
					};

					const addRow = (attachment) => {
						const thumbnail = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
						const markup = template.innerHTML
							.split('{{id}}').join(String(attachment.id))
							.split('{{thumbnail}}').join(escapeHtml(thumbnail))
							.split('{{title}}').join(escapeHtml(attachment.title || 'Media image'))
							.split('{{url}}').join(escapeHtml(attachment.url));

						list.insertAdjacentHTML('beforeend', markup);
						resetDragState();
						renderPreview();
					};

					const updateRow = (row, attachment) => {
						const thumbnail = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
						row.setAttribute('data-image-id', String(attachment.id));
						row.querySelector('img').setAttribute('src', thumbnail);
						row.querySelector('.foundation-team-inline-admin__meta strong').textContent = attachment.title || 'Media image';
						row.querySelector('.foundation-team-inline-admin__meta span').textContent = attachment.url;
						row.querySelector('input[type="hidden"]').value = String(attachment.id);
						resetDragState();
						renderPreview();
					};

					const openMediaFrame = (multiple, onSelect, titleText, buttonText) => {
						const frame = wp.media({
							title: titleText,
							library: { type: 'image' },
							button: { text: buttonText },
							multiple: multiple
						});

						frame.on('select', function() {
							onSelect(frame.state().get('selection'));
						});

						frame.open();
					};

					if (addButton) {
						addButton.addEventListener('click', function(event) {
							event.preventDefault();

							openMediaFrame(
								true,
								function(selection) {
									selection.toJSON().forEach(addRow);
								},
								<?php echo wp_json_encode( __( 'Select team images', 'foundation-elementor-plus' ) ); ?>,
								<?php echo wp_json_encode( __( 'Use selected images', 'foundation-elementor-plus' ) ); ?>
							);
						});
					}

					list.addEventListener('click', function(event) {
						const replaceButton = event.target.closest('.foundation-team-inline-replace');
						if (replaceButton) {
							event.preventDefault();
							const row = replaceButton.closest('[data-image-id]');
							if (!row) {
								return;
							}

							openMediaFrame(
								false,
								function(selection) {
									const attachment = selection.first();
									if (attachment) {
										updateRow(row, attachment.toJSON());
									}
								},
								replaceText,
								replaceButtonText
							);
							return;
						}

						const button = event.target.closest('.foundation-team-inline-remove');
						if (!button) {
							return;
						}

						event.preventDefault();
						const row = button.closest('[data-image-id]');
						if (row) {
							row.remove();
						}
						renderPreview();
					});

					list.addEventListener('pointerdown', function(event) {
						const handle = event.target.closest('.foundation-team-inline-admin__drag');
						if (!handle) {
							return;
						}

						armRowForDrag(handle.closest('[data-image-id]'));
					});

					list.addEventListener('dragstart', function(event) {
						const row = event.target.closest('[data-image-id]');
						if (!row || row !== dragArmedRow) {
							event.preventDefault();
							return;
						}

						draggedRow = row;
						row.classList.add('is-sorting');

						if (event.dataTransfer) {
							event.dataTransfer.effectAllowed = 'move';
							event.dataTransfer.setData('text/plain', row.getAttribute('data-image-id') || '');
						}
					});

					list.addEventListener('dragover', function(event) {
						if (!draggedRow) {
							return;
						}

						event.preventDefault();

						const row = event.target.closest('[data-image-id]');
						if (!row || row === draggedRow) {
							return;
						}

						const rect = row.getBoundingClientRect();
						const shouldInsertBefore = event.clientY < rect.top + (rect.height / 2);
						const targetSibling = shouldInsertBefore ? row : row.nextSibling;

						if (targetSibling !== draggedRow) {
							list.insertBefore(draggedRow, targetSibling);
						}
					});

					list.addEventListener('drop', function(event) {
						if (!draggedRow) {
							return;
						}

						event.preventDefault();
						renderPreview();
					});

					list.addEventListener('dragend', function() {
						resetDragState();
						renderPreview();
					});

					window.addEventListener('pointerup', function() {
						if (!draggedRow) {
							resetDragState();
						}
					});

					resetDragState();
					renderPreview();
				})();
			</script>
		</div>
		<?php
	}

	/**
	 * Sanitize option value.
	 *
	 * @param mixed $raw Raw option value.
	 * @return array<string, array<int, int>>
	 */
	private function sanitize_option( $raw ) {
		$raw         = is_array( $raw ) ? $raw : array();
		$departments = array_keys( $this->get_departments() );
		$sanitized   = array();

		foreach ( $departments as $department ) {
			$sanitized[ $department ] = array();

			if ( empty( $raw[ $department ] ) || ! is_array( $raw[ $department ] ) ) {
				continue;
			}

			foreach ( $raw[ $department ] as $image_id ) {
				$image_id = absint( $image_id );

				if ( $image_id > 0 ) {
					$sanitized[ $department ][] = $image_id;
				}
			}

			$sanitized[ $department ] = array_values( array_unique( $sanitized[ $department ] ) );
		}

		return $sanitized;
	}

	/**
	 * Get merged option.
	 *
	 * @return array<string, array<int, int>>
	 */
	private function get_option() {
		$defaults = $this->get_default_option();
		$stored   = get_option( self::OPTION_KEY, array() );

		if ( ! is_array( $stored ) ) {
			return $defaults;
		}

		$stored = $this->sanitize_option( $stored );

		return wp_parse_args( $stored, $defaults );
	}

	/**
	 * Get default option values.
	 *
	 * @return array<string, array<int, int>>
	 */
	private function get_default_option() {
		$defaults = array();

		foreach ( array_keys( $this->get_departments() ) as $department ) {
			$defaults[ $department ] = array();
		}

		$defaults['all'] = array_values( array_filter( array_map( array( $this, 'legacy_url_to_attachment_id' ), $this->get_legacy_image_urls() ) ) );

		return $defaults;
	}

	/**
	 * Get image IDs for a department, with fallback to all.
	 *
	 * @param string $department Department key.
	 * @return array<int, int>
	 */
	private function get_images_for_department( $department ) {
		$options = $this->get_option();
		$images  = isset( $options[ $department ] ) ? $options[ $department ] : array();

		if ( empty( $images ) && 'all' !== $department ) {
			$images = isset( $options['all'] ) ? $options['all'] : array();
		}

		return array_values( array_filter( array_map( 'absint', $images ) ) );
	}

	/**
	 * Get department labels.
	 *
	 * @return array<string, string>
	 */
	private function get_departments() {
		return self::get_department_options();
	}

	/**
	 * Get shared department labels.
	 *
	 * @return array<string, string>
	 */
	public static function get_department_options() {
		return array(
			'all'       => __( 'All Team', 'foundation-elementor-plus' ),
			'management' => __( 'Management', 'foundation-elementor-plus' ),
			'it'        => __( 'IT', 'foundation-elementor-plus' ),
			'web'       => __( 'Web', 'foundation-elementor-plus' ),
			'marketing' => __( 'Marketing', 'foundation-elementor-plus' ),
			'branding'  => __( 'Branding', 'foundation-elementor-plus' ),
		);
	}

	/**
	 * Legacy image URLs used to seed the generic team strip.
	 *
	 * @return array<int, string>
	 */
	private function get_legacy_image_urls() {
		return array(
			home_url( '/wp-content/uploads/2025/11/IMG_0054.jpeg' ),
			home_url( '/wp-content/uploads/2025/11/IMG_0053.jpeg' ),
			home_url( '/wp-content/uploads/2025/11/IMG_0049.png' ),
			home_url( '/wp-content/uploads/2025/11/IMG_0050.png' ),
			home_url( '/wp-content/uploads/2025/11/IMG_0054.jpeg' ),
		);
	}

	/**
	 * Convert legacy URL to attachment ID.
	 *
	 * @param string $url Image URL.
	 * @return int
	 */
	private function legacy_url_to_attachment_id( $url ) {
		$url = trim( (string) $url );

		if ( '' === $url ) {
			return 0;
		}

		$attachment_id = attachment_url_to_postid( $url );

		if ( $attachment_id > 0 ) {
			return $attachment_id;
		}

		$parsed = wp_parse_url( $url );

		if ( empty( $parsed['path'] ) ) {
			return 0;
		}

		$uploads = wp_get_upload_dir();

		if ( empty( $uploads['baseurl'] ) ) {
			return 0;
		}

		$fallback_url = trailingslashit( $uploads['baseurl'] ) . ltrim( basename( dirname( $parsed['path'] ) ) . '/' . basename( $parsed['path'] ), '/' );

		return (int) attachment_url_to_postid( $fallback_url );
	}

	/**
	 * Build shortcode example text.
	 *
	 * @param string $department Department key.
	 * @param bool   $alias      Whether to show alias shortcode.
	 * @return string
	 */
	private function get_shortcode_example( $department, $alias ) {
		if ( 'all' === $department ) {
			return '[ink_team]';
		}

		if ( $alias ) {
			return '[ink_team_' . $department . ']';
		}

		return '[ink_team department="' . $department . '"]';
	}

	/**
	 * Build shortcode usage report for one department.
	 *
	 * @param string $department Department key.
	 * @return array<string, mixed>
	 */
	private function get_shortcode_usage_report( $department ) {
		global $wpdb;

		$department  = sanitize_key( (string) $department );
		$departments = $this->get_departments();

		if ( ! isset( $departments[ $department ] ) ) {
			$department = 'all';
		}

		$like_terms = array( '%[ink_team%' );

		if ( 'all' !== $department ) {
			$like_terms[] = '%[ink_team_' . $department . '%';
		}

		$where_like = array();
		$query_args = array();

		foreach ( $like_terms as $like_term ) {
			$where_like[] = '(p.post_content LIKE %s OR pm.meta_value LIKE %s)';
			$query_args[] = $like_term;
			$query_args[] = $like_term;
		}

		$sql = "
			SELECT p.ID, p.post_title, p.post_type, p.post_status, p.post_content, pm.meta_value AS elementor_data
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} pm
				ON pm.post_id = p.ID
				AND pm.meta_key = '_elementor_data'
			WHERE p.post_status NOT IN ('auto-draft', 'trash', 'inherit')
				AND p.post_type NOT IN ('revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request')
				AND (" . implode( ' OR ', $where_like ) . ')
			ORDER BY p.post_type ASC, p.post_title ASC
		';

		$rows = $wpdb->get_results( $wpdb->prepare( $sql, $query_args ) );

		$locations         = array();
		$total_occurrences = 0;

		foreach ( (array) $rows as $row ) {
			$post_content_count = $this->get_shortcode_occurrence_count( isset( $row->post_content ) ? $row->post_content : '', $department );
			$elementor_count    = $this->get_shortcode_occurrence_count( isset( $row->elementor_data ) ? $row->elementor_data : '', $department );
			$occurrences        = $post_content_count + $elementor_count;

			if ( $occurrences < 1 ) {
				continue;
			}

			$post_type_object = get_post_type_object( (string) $row->post_type );
			$status_object    = get_post_status_object( (string) $row->post_status );
			$sources          = array();

			if ( $post_content_count > 0 ) {
				$sources[] = sprintf(
					/* translators: %d: placements in post content. */
					_n( 'Post content: %d placement', 'Post content: %d placements', $post_content_count, 'foundation-elementor-plus' ),
					$post_content_count
				);
			}

			if ( $elementor_count > 0 ) {
				$sources[] = sprintf(
					/* translators: %d: placements in Elementor data. */
					_n( 'Elementor data: %d placement', 'Elementor data: %d placements', $elementor_count, 'foundation-elementor-plus' ),
					$elementor_count
				);
			}

			$locations[] = array(
				'id'              => (int) $row->ID,
				'title'           => '' !== (string) $row->post_title ? (string) $row->post_title : __( '(no title)', 'foundation-elementor-plus' ),
				'post_type_label' => $post_type_object && isset( $post_type_object->labels->singular_name ) ? (string) $post_type_object->labels->singular_name : ucwords( str_replace( '_', ' ', (string) $row->post_type ) ),
				'status_label'    => $status_object && isset( $status_object->label ) ? (string) $status_object->label : ucwords( str_replace( '-', ' ', (string) $row->post_status ) ),
				'edit_url'        => admin_url( 'post.php?post=' . (int) $row->ID . '&action=edit' ),
				'occurrences'     => $occurrences,
				'sources'         => $sources,
			);

			$total_occurrences += $occurrences;
		}

		usort(
			$locations,
			static function ( $left, $right ) {
				if ( $left['occurrences'] === $right['occurrences'] ) {
					return strcasecmp( (string) $left['title'], (string) $right['title'] );
				}

				return $right['occurrences'] <=> $left['occurrences'];
			}
		);

		return array(
			'total_occurrences' => $total_occurrences,
			'total_locations'   => count( $locations ),
			'locations'         => $locations,
		);
	}

	/**
	 * Count shortcode matches in content.
	 *
	 * @param string $content Content string.
	 * @param string $department Department key.
	 * @return int
	 */
	private function get_shortcode_occurrence_count( $content, $department ) {
		$content = (string) $content;

		if ( '' === $content || false === strpos( $content, '[ink_team' ) ) {
			return 0;
		}

		$tags = array( 'ink_team' );

		if ( 'all' !== $department ) {
			$tags[] = 'ink_team_' . $department;
		}

		$pattern = '/' . get_shortcode_regex( $tags ) . '/';

		if ( ! preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
			return 0;
		}

		$count = 0;

		foreach ( $matches as $match ) {
			$tag       = isset( $match[2] ) ? sanitize_key( (string) $match[2] ) : '';
			$attr_text = isset( $match[3] ) ? (string) $match[3] : '';

			if ( $this->shortcode_matches_department( $tag, $attr_text, $department ) ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Check whether a shortcode belongs to the requested department.
	 *
	 * @param string $tag Shortcode tag.
	 * @param string $attr_text Raw shortcode attrs.
	 * @param string $department Department key.
	 * @return bool
	 */
	private function shortcode_matches_department( $tag, $attr_text, $department ) {
		$tag        = sanitize_key( (string) $tag );
		$department = sanitize_key( (string) $department );

		if ( 'all' !== $department && 'ink_team_' . $department === $tag ) {
			return true;
		}

		if ( 'ink_team' !== $tag ) {
			return false;
		}

		$atts            = shortcode_parse_atts( $attr_text );
		$attr_department = '';

		if ( is_array( $atts ) && isset( $atts['department'] ) ) {
			$attr_department = sanitize_key( (string) $atts['department'] );
		}

		if ( 'all' === $department ) {
			return '' === $attr_department || 'all' === $attr_department;
		}

		return $department === $attr_department;
	}

	/**
	 * Build one admin row.
	 *
	 * @param int    $image_id Attachment ID.
	 * @param string $department Department key.
	 * @return string
	 */
	private function get_admin_row_markup( $image_id, $department ) {
		$image_id = absint( $image_id );
		$department = sanitize_key( (string) $department );

		if ( $image_id <= 0 ) {
			return '';
		}

		$thumb = wp_get_attachment_image_url( $image_id, 'thumbnail' );
		$full  = wp_get_attachment_image_url( $image_id, 'full' );
		$title = get_the_title( $image_id );

		if ( ! $thumb || ! $full ) {
			return '';
		}

		ob_start();
		?>
		<div class="foundation-team-inline-admin__row" data-image-id="<?php echo esc_attr( (string) $image_id ); ?>">
			<button type="button" class="foundation-team-inline-admin__drag" aria-label="<?php esc_attr_e( 'Drag to reorder', 'foundation-elementor-plus' ); ?>">
				<span class="dashicons dashicons-menu-alt3" aria-hidden="true"></span>
			</button>
			<div class="foundation-team-inline-admin__thumb">
				<img src="<?php echo esc_url( $thumb ); ?>" alt="" />
			</div>
			<div class="foundation-team-inline-admin__meta">
				<strong><?php echo esc_html( $title ? $title : __( 'Media image', 'foundation-elementor-plus' ) ); ?></strong>
				<span><?php echo esc_html( $full ); ?></span>
				<input type="hidden" name="foundation_team_inline_images[<?php echo esc_attr( $department ); ?>][]" value="<?php echo esc_attr( (string) $image_id ); ?>" />
			</div>
			<div class="foundation-team-inline-admin__row-actions">
				<button type="button" class="button-link foundation-team-inline-replace"><?php esc_html_e( 'Replace', 'foundation-elementor-plus' ); ?></button>
				<button type="button" class="button-link-delete foundation-team-inline-remove"><?php esc_html_e( 'Remove', 'foundation-elementor-plus' ); ?></button>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Keep size attr safe for inline use.
	 *
	 * @param string $value Size value.
	 * @param string $fallback Fallback size.
	 * @return string
	 */
	private function sanitize_size_value( $value, $fallback ) {
		$value = trim( $value );

		if ( preg_match( '/^\d+(?:\.\d+)?(?:px|rem|em|vw|vh|%)$/', $value ) ) {
			return $value;
		}

		return $fallback;
	}
}
