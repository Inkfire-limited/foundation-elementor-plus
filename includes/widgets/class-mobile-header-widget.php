<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/class-mobile-header-actions.php';

class Mobile_Header_Widget extends Widget_Base {
	public function get_name() {
		return 'foundation-mobile-header';
	}

	public function get_title() {
		return esc_html__( 'Foundation Mobile Glass Header', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-menu-toggle';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'mobile', 'header', 'menu', 'accordion', 'glass' );
	}

	public function get_style_depends(): array {
		return array( 'foundation-elementor-plus-mobile-header' );
	}

	public function get_script_depends(): array {
		return array( 'foundation-elementor-plus-mobile-header' );
	}

	protected function register_controls() {
		$this->register_general_controls();
		$this->register_topbar_action_controls();
		$this->register_quick_action_controls();
		$this->register_menu_controls();
		$this->register_footer_controls();
	}

	protected function render() {
		$settings       = $this->get_settings_for_display();
		$widget_id      = 'inkfire-mobile-header-' . $this->get_id();
		$logo_url       = $this->get_logo_url( $settings );
		$brand_text     = isset( $settings['brand_text'] ) ? trim( (string) $settings['brand_text'] ) : '';
		$brand_alt      = $brand_text ? $brand_text : get_bloginfo( 'name' );
		$home_link_class = 'imh-home' . ( '' === $brand_text ? ' imh-home--logo-only' : '' );
		$home_url       = $this->get_link_url( $settings['home_url'] ?? array(), home_url( '/' ) );
		$search_action  = home_url( '/' );
		$search_copy    = ! empty( $settings['search_placeholder'] ) ? $settings['search_placeholder'] : 'Search services, resources, case studies...';
		$quick_actions  = ! empty( $settings['quick_actions'] ) && is_array( $settings['quick_actions'] ) ? array_values( $settings['quick_actions'] ) : array();
		$menu_sections   = ! empty( $settings['menu_sections'] ) && is_array( $settings['menu_sections'] ) ? array_values( $settings['menu_sections'] ) : array();
		$menu_sections   = $this->normalize_panel_keys( $this->backfill_menu_sections( $menu_sections ) );
		$social_links    = ! empty( $settings['social_links'] ) && is_array( $settings['social_links'] ) ? array_values( $settings['social_links'] ) : array();
		$default_index   = $this->get_default_open_index( $menu_sections );

		if ( empty( $menu_sections ) ) {
			return;
		}
		?>
		<div class="inkfire-mobile-header" id="<?php echo esc_attr( $widget_id ); ?>" data-inkfire-header>
			<header class="imh-header" aria-label="<?php esc_attr_e( 'Mobile site header', 'foundation-elementor-plus' ); ?>">
				<div class="imh-topbar">
					<a class="<?php echo esc_attr( $home_link_class ); ?>" href="<?php echo esc_url( $home_url ); ?>" aria-label="<?php esc_attr_e( 'Go to homepage', 'foundation-elementor-plus' ); ?>">
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $brand_alt ); ?>" class="imh-logo" />
						<?php if ( '' !== $brand_text ) : ?>
							<span class="imh-brand"><?php echo esc_html( $brand_text ); ?></span>
						<?php endif; ?>
					</a>

					<?php Mobile_Header_Actions::render( $settings, $social_links ); ?>

					<button type="button" class="imh-icon-button imh-search-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $widget_id ); ?>-search">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
						<span class="screen-reader-text"><?php esc_html_e( 'Open search', 'foundation-elementor-plus' ); ?></span>
					</button>

					<button type="button" class="imh-icon-button imh-menu-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $widget_id ); ?>-menu">
						<span class="imh-burger" aria-hidden="true">
							<span></span>
							<span></span>
							<span></span>
						</span>
						<span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'foundation-elementor-plus' ); ?></span>
					</button>
				</div>

				<div class="imh-search-sheet" id="<?php echo esc_attr( $widget_id ); ?>-search" hidden>
					<div class="imh-search-inner">
						<button type="button" class="imh-search-submit" aria-label="<?php esc_attr_e( 'Submit search', 'foundation-elementor-plus' ); ?>">
							<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
						</button>
						<form role="search" method="get" action="<?php echo esc_url( $search_action ); ?>" class="imh-search-form">
							<label class="screen-reader-text" for="<?php echo esc_attr( $widget_id ); ?>-s"><?php esc_html_e( 'Search site', 'foundation-elementor-plus' ); ?></label>
							<input id="<?php echo esc_attr( $widget_id ); ?>-s" type="search" name="s" placeholder="<?php echo esc_attr( $search_copy ); ?>" />
						</form>
					</div>
				</div>
			</header>

			<div class="imh-overlay" hidden></div>

			<aside class="imh-menu" id="<?php echo esc_attr( $widget_id ); ?>-menu" aria-hidden="true" hidden>
				<div class="imh-menu-shell">
					<div class="imh-menu-topbar-wrap">
						<div class="imh-topbar">
							<a class="<?php echo esc_attr( $home_link_class ); ?>" href="<?php echo esc_url( $home_url ); ?>" aria-label="<?php esc_attr_e( 'Go to homepage', 'foundation-elementor-plus' ); ?>">
								<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $brand_alt ); ?>" class="imh-logo" />
								<?php if ( '' !== $brand_text ) : ?>
									<span class="imh-brand"><?php echo esc_html( $brand_text ); ?></span>
								<?php endif; ?>
							</a>

							<?php Mobile_Header_Actions::render( $settings, $social_links ); ?>

							<button type="button" class="imh-icon-button imh-search-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $widget_id ); ?>-search-menu">
								<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
								<span class="screen-reader-text"><?php esc_html_e( 'Open search', 'foundation-elementor-plus' ); ?></span>
							</button>

							<button type="button" class="imh-icon-button imh-menu-toggle is-open" aria-expanded="true" aria-controls="<?php echo esc_attr( $widget_id ); ?>-menu">
								<span class="imh-burger" aria-hidden="true">
									<span></span>
									<span></span>
									<span></span>
								</span>
								<span class="screen-reader-text"><?php esc_html_e( 'Close menu', 'foundation-elementor-plus' ); ?></span>
							</button>
						</div>

						<div class="imh-search-sheet" id="<?php echo esc_attr( $widget_id ); ?>-search-menu" hidden>
							<div class="imh-search-inner">
								<button type="button" class="imh-search-submit" aria-label="<?php esc_attr_e( 'Submit search', 'foundation-elementor-plus' ); ?>">
									<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
								</button>
								<form role="search" method="get" action="<?php echo esc_url( $search_action ); ?>" class="imh-search-form">
									<label class="screen-reader-text" for="<?php echo esc_attr( $widget_id ); ?>-menu-s"><?php esc_html_e( 'Search site', 'foundation-elementor-plus' ); ?></label>
									<input id="<?php echo esc_attr( $widget_id ); ?>-menu-s" type="search" name="s" placeholder="<?php echo esc_attr( $search_copy ); ?>" />
								</form>
							</div>
						</div>
					</div>

					<div class="imh-menu-body">
						<?php if ( ! empty( $quick_actions ) ) : ?>
							<div class="imh-menu-group imh-menu-group-actions">
								<div class="imh-menu-group-header">
									<span class="imh-menu-group-label"><?php esc_html_e( 'Quick Actions', 'foundation-elementor-plus' ); ?></span>
								</div>
								<div class="imh-quick-actions">
									<?php foreach ( $quick_actions as $action ) : ?>
										<?php
										$action_label = ! empty( $action['label'] ) ? $action['label'] : '';
										$action_display_label = $this->get_menu_quick_action_label( $action_label );
										$action_url   = $this->get_link_url( $action['url'] ?? array() );
										$action_class = ! empty( $action['style_variant'] ) && 'primary' === $action['style_variant']
											? 'imh-quick-action imh-quick-action-primary'
											: 'imh-quick-action';

										if ( '' === $action_label ) {
											continue;
										}
										?>
										<a class="<?php echo esc_attr( $action_class ); ?>" href="<?php echo esc_url( $action_url ? $action_url : '#' ); ?>">
											<?php echo esc_html( $action_display_label ); ?>
										</a>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<div class="imh-menu-group imh-menu-group-nav">
							<div class="imh-menu-group-header">
								<span class="imh-menu-group-label"><?php esc_html_e( 'Browse', 'foundation-elementor-plus' ); ?></span>
							</div>
						</div>

						<div class="imh-accordion">
							<?php foreach ( $menu_sections as $index => $item ) : ?>
								<?php
								$item_title       = ! empty( $item['title'] ) ? $item['title'] : sprintf( 'Section %d', $index + 1 );
								$item_subtitle    = ! empty( $item['subtitle'] ) ? $item['subtitle'] : '';
								$item_featured    = ! empty( $item['show_featured'] ) && 'yes' === $item['show_featured'];
								$item_pills       = $this->parse_pill_rows( (string) ( $item['utility_rows'] ?? '' ) );
								$item_sections    = $this->get_panel_sections( $item );
								$item_link_count  = 0;
								foreach ( $item_sections as $section_group ) {
									$item_link_count += ! empty( $section_group['items'] ) && is_array( $section_group['items'] ) ? count( $section_group['items'] ) : 0;
								}
								$item_panel_id    = $widget_id . '-panel-' . ( $index + 1 );
								$is_open          = $default_index >= 0 && $index === $default_index;
								$item_state_class = $is_open ? ' is-active' : '';
								?>
								<section class="imh-accordion-item<?php echo esc_attr( $item_state_class ); ?>" data-accordion-item>
									<button type="button" class="imh-accordion-toggle" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $item_panel_id ); ?>">
										<span class="imh-accordion-accent"></span>
										<span class="imh-accordion-copy">
											<span class="imh-accordion-title"><?php echo esc_html( $item_title ); ?></span>
											<?php if ( $item_subtitle ) : ?>
												<span class="imh-accordion-subtitle"><?php echo esc_html( $item_subtitle ); ?></span>
											<?php endif; ?>
										</span>
										<span class="imh-accordion-meta">
											<?php if ( $item_link_count > 0 ) : ?>
												<span class="imh-accordion-count">
													<?php
													echo esc_html(
														sprintf(
															/* translators: %d: number of links in the panel. */
															_n( '%d link', '%d links', $item_link_count, 'foundation-elementor-plus' ),
															$item_link_count
														)
													);
													?>
												</span>
											<?php endif; ?>
											<span class="imh-chevron" aria-hidden="true"></span>
										</span>
									</button>

									<div class="imh-accordion-panel" id="<?php echo esc_attr( $item_panel_id ); ?>"<?php echo $is_open ? '' : ' hidden'; ?>>
										<?php if ( $item_featured ) : ?>
											<?php
											$featured_cta_text = ! empty( $item['featured_cta_text'] ) ? $item['featured_cta_text'] : '';
											$featured_cta_url  = $this->get_link_url( $item['featured_cta_url'] ?? array() );
											?>
											<div class="imh-feature-card">
												<?php if ( ! empty( $item['featured_eyebrow'] ) ) : ?>
													<div class="imh-pill imh-pill-green"><?php echo esc_html( $item['featured_eyebrow'] ); ?></div>
												<?php endif; ?>
												<?php if ( ! empty( $item['featured_title'] ) ) : ?>
													<h3><?php echo esc_html( $item['featured_title'] ); ?></h3>
												<?php endif; ?>
												<?php if ( ! empty( $item['featured_body'] ) ) : ?>
													<p><?php echo esc_html( $item['featured_body'] ); ?></p>
												<?php endif; ?>
												<?php if ( $featured_cta_text ) : ?>
													<a class="imh-pill-button imh-pill-button-orange" href="<?php echo esc_url( $featured_cta_url ? $featured_cta_url : '#' ); ?>">
														<?php echo esc_html( $featured_cta_text ); ?>
													</a>
												<?php endif; ?>
											</div>
										<?php endif; ?>

										<?php if ( ! empty( $item_pills ) ) : ?>
											<div class="imh-utility-pills">
												<?php foreach ( $item_pills as $pill ) : ?>
													<a class="imh-pill imh-pill-green" href="<?php echo esc_url( ! empty( $pill['url'] ) ? $pill['url'] : '#' ); ?>">
														<?php echo esc_html( $pill['label'] ); ?>
													</a>
												<?php endforeach; ?>
											</div>
										<?php endif; ?>

										<?php if ( ! empty( $item_sections ) ) : ?>
											<div class="imh-card-stack">
												<?php foreach ( $item_sections as $section_index => $section ) : ?>
													<?php
													$card_panel_id = $item_panel_id . '-card-' . ( $section_index + 1 );
													$link_count    = ! empty( $section['items'] ) ? count( $section['items'] ) : 0;
													?>
													<div class="imh-card" data-card-item>
														<button type="button" class="imh-card-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $card_panel_id ); ?>">
															<span class="imh-pill imh-pill-green"><?php echo esc_html( $section['label'] ); ?></span>
															<span class="imh-card-toggle-meta">
																<span class="imh-card-toggle-count">
																	<?php
																	echo esc_html(
																		sprintf(
																			/* translators: %d: number of links in section. */
																			_n( '%d link', '%d links', $link_count, 'foundation-elementor-plus' ),
																			$link_count
																		)
																	);
																	?>
																</span>
																<span class="imh-card-chevron" aria-hidden="true"></span>
															</span>
														</button>
														<div class="imh-card-panel" id="<?php echo esc_attr( $card_panel_id ); ?>" hidden>
															<div class="imh-card-links">
															<?php foreach ( $section['items'] as $link ) : ?>
																<a class="imh-card-link" href="<?php echo esc_url( ! empty( $link['url'] ) ? $link['url'] : '#' ); ?>">
																	<span>
																		<strong><?php echo esc_html( $link['title'] ); ?></strong>
																		<?php if ( ! empty( $link['description'] ) ) : ?>
																			<small><?php echo esc_html( $link['description'] ); ?></small>
																		<?php endif; ?>
																	</span>
																	<span class="imh-arrow" aria-hidden="true">
																		<i class="fa-solid fa-arrow-up-right-from-square"></i>
																	</span>
																</a>
															<?php endforeach; ?>
															</div>
														</div>
													</div>
												<?php endforeach; ?>
											</div>
										<?php endif; ?>
									</div>
								</section>
							<?php endforeach; ?>
						</div>

						<?php if ( $this->has_footer_content( $settings, $social_links ) ) : ?>
							<div class="imh-menu-footer">
								<div class="imh-menu-group-header">
									<span class="imh-menu-group-label"><?php esc_html_e( 'Contact', 'foundation-elementor-plus' ); ?></span>
								</div>
								<?php if ( ! empty( $settings['footer_phone'] ) || ! empty( $settings['footer_email'] ) || ! empty( $settings['footer_address'] ) || ! empty( $settings['footer_hours'] ) ) : ?>
									<div class="imh-contact-block" aria-label="<?php esc_attr_e( 'Contact details', 'foundation-elementor-plus' ); ?>">
										<?php if ( ! empty( $settings['footer_phone'] ) ) : ?>
											<div class="imh-contact-item">
												<span class="imh-contact-label"><?php esc_html_e( 'Call', 'foundation-elementor-plus' ); ?></span>
												<a class="imh-contact-value" href="<?php echo esc_url( $this->format_tel_url( $settings['footer_phone'] ) ); ?>">
													<?php echo esc_html( $settings['footer_phone'] ); ?>
												</a>
											</div>
										<?php endif; ?>
										<?php if ( ! empty( $settings['footer_email'] ) ) : ?>
											<div class="imh-contact-item">
												<span class="imh-contact-label"><?php esc_html_e( 'Email', 'foundation-elementor-plus' ); ?></span>
												<a class="imh-contact-value" href="<?php echo esc_url( 'mailto:' . sanitize_email( $settings['footer_email'] ) ); ?>">
													<?php echo esc_html( $settings['footer_email'] ); ?>
												</a>
											</div>
										<?php endif; ?>
										<?php if ( ! empty( $settings['footer_address'] ) ) : ?>
											<div class="imh-contact-item">
												<span class="imh-contact-label"><?php esc_html_e( 'Address', 'foundation-elementor-plus' ); ?></span>
												<div class="imh-contact-value imh-contact-value-text"><?php echo esc_html( $settings['footer_address'] ); ?></div>
											</div>
										<?php endif; ?>
										<?php if ( ! empty( $settings['footer_hours'] ) ) : ?>
											<div class="imh-contact-item">
												<span class="imh-contact-label"><?php esc_html_e( 'Office Hours', 'foundation-elementor-plus' ); ?></span>
												<div class="imh-contact-value imh-contact-value-text"><?php echo esc_html( $settings['footer_hours'] ); ?></div>
											</div>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $social_links ) ) : ?>
									<div class="imh-social-row" role="list" aria-label="<?php esc_attr_e( 'Social links', 'foundation-elementor-plus' ); ?>">
										<?php foreach ( $social_links as $social_item ) : ?>
											<?php
											$social_label = ! empty( $social_item['label'] ) ? $social_item['label'] : '';
											$social_url   = $this->get_link_url( $social_item['url'] ?? array() );
											$icon_class   = $this->sanitize_icon_class( $social_item['icon_class'] ?? '' );

											if ( '' === $social_label || '' === $social_url ) {
												continue;
											}
											?>
											<a class="imh-social-link" href="<?php echo esc_url( $social_url ); ?>" target="_blank" rel="noopener" role="listitem" aria-label="<?php echo esc_attr( $social_label ); ?>">
												<?php if ( $icon_class ) : ?>
													<i class="<?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></i>
												<?php endif; ?>
												<span class="screen-reader-text"><?php echo esc_html( $social_label ); ?></span>
											</a>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</aside>
		</div>
		<?php
	}

	private function register_topbar_action_controls() {
		$this->start_controls_section( 'section_topbar_actions', array(
			'label' => esc_html__( 'Header Actions', 'foundation-elementor-plus' ),
		) );
		$this->add_control( 'show_topbar_project', array(
			'label' => esc_html__( 'Show Project Calculator CTA', 'foundation-elementor-plus' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'yes',
			'return_value' => 'yes',
			'description' => esc_html__( 'Uses the existing project calculator launcher, with Contact Us as the no-JavaScript fallback.', 'foundation-elementor-plus' ),
		) );
		$this->add_control( 'topbar_project_label', array(
			'label' => esc_html__( 'Compact Project Label', 'foundation-elementor-plus' ),
			'type' => Controls_Manager::TEXT,
			'default' => esc_html__( 'Start project', 'foundation-elementor-plus' ),
			'condition' => array( 'show_topbar_project' => 'yes' ),
			'description' => esc_html__( 'Shown when the bar has room. Narrow layouts keep a labelled project icon.', 'foundation-elementor-plus' ),
		) );
		$this->add_control( 'show_topbar_socials', array(
			'label' => esc_html__( 'Show Tablet Social Icons', 'foundation-elementor-plus' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'yes',
			'return_value' => 'yes',
			'description' => esc_html__( 'Reuses Social Links from Menu Footer. Icons reduce as space narrows; all existing links remain in the menu.', 'foundation-elementor-plus' ),
		) );
		$this->end_controls_section();
	}

	private function register_general_controls() {
		$this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'General', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'logo',
			array(
				'label'   => esc_html__( 'Logo', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => '',
				),
			)
		);

		$this->add_control(
			'brand_text',
			array(
				'label'       => esc_html__( 'Brand Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'Leave blank to use logo only', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'description' => esc_html__( 'Leave blank when your uploaded logo already includes the Inkfire wordmark.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'home_url',
			array(
				'label'       => esc_html__( 'Home URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => home_url( '/' ),
				'default'     => array(
					'url' => home_url( '/' ),
				),
			)
		);

		$this->add_control(
			'search_placeholder',
			array(
				'label'       => esc_html__( 'Search Placeholder', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Search services, resources, case studies...', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'header_width',
			array(
				'label'      => esc_html__( 'Header Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 240,
						'max' => 1400,
					),
					'%' => array(
						'min' => 30,
						'max' => 100,
					),
					'vw' => array(
						'min' => 30,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-mobile-header' => 'width: min(100%, {{SIZE}}{{UNIT}}); max-width: none;',
				),
			)
		);

		$this->add_responsive_control(
			'glass_header_padding',
			array(
				'label'      => esc_html__( 'Glass Header Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'    => 8,
					'right'  => 10,
					'bottom' => 8,
					'left'   => 10,
					'unit'   => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-mobile-header .imh-topbar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'glass_header_button_gap',
			array(
				'label'      => esc_html__( 'Header Button Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array(
					'px'  => array(
						'min' => 0,
						'max' => 40,
					),
					'rem' => array(
						'min' => 0,
						'max' => 3,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 8,
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-mobile-header .imh-topbar' => 'gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .inkfire-mobile-header .imh-inline-cta' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => esc_html__( 'Logo Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 32,
						'max' => 260,
					),
					'%' => array(
						'min' => 5,
						'max' => 80,
					),
					'vw' => array(
						'min' => 4,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 44,
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-mobile-header .imh-logo' => 'width: {{SIZE}}{{UNIT}}; max-width: none;',
				),
			)
		);

		$this->add_responsive_control(
			'logo_size',
			array(
				'label'      => esc_html__( 'Logo Height', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 24,
						'max' => 140,
					),
					'%' => array(
						'min' => 5,
						'max' => 50,
					),
					'vw' => array(
						'min' => 3,
						'max' => 20,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 44,
				),
				'selectors'  => array(
					'{{WRAPPER}} .inkfire-mobile-header .imh-logo' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'logo_object_fit',
			array(
				'label'   => esc_html__( 'Logo Object Fit', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'contain',
				'options' => array(
					'contain'    => esc_html__( 'Contain', 'foundation-elementor-plus' ),
					'cover'      => esc_html__( 'Cover', 'foundation-elementor-plus' ),
					'fill'       => esc_html__( 'Fill', 'foundation-elementor-plus' ),
					'none'       => esc_html__( 'None', 'foundation-elementor-plus' ),
					'scale-down' => esc_html__( 'Scale Down', 'foundation-elementor-plus' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .inkfire-mobile-header .imh-logo' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'logo_object_position',
			array(
				'label'   => esc_html__( 'Logo Position', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => array(
					'left top'       => esc_html__( 'Left Top', 'foundation-elementor-plus' ),
					'left center'    => esc_html__( 'Left Center', 'foundation-elementor-plus' ),
					'left bottom'    => esc_html__( 'Left Bottom', 'foundation-elementor-plus' ),
					'center top'     => esc_html__( 'Center Top', 'foundation-elementor-plus' ),
					'center center'  => esc_html__( 'Center Center', 'foundation-elementor-plus' ),
					'center bottom'  => esc_html__( 'Center Bottom', 'foundation-elementor-plus' ),
					'right top'      => esc_html__( 'Right Top', 'foundation-elementor-plus' ),
					'right center'   => esc_html__( 'Right Center', 'foundation-elementor-plus' ),
					'right bottom'   => esc_html__( 'Right Bottom', 'foundation-elementor-plus' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .inkfire-mobile-header .imh-logo' => 'object-position: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_quick_action_controls() {
		$this->start_controls_section(
			'section_quick_actions',
			array(
				'label' => esc_html__( 'Quick Actions', 'foundation-elementor-plus' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'label',
			array(
				'label'       => esc_html__( 'Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Quick Action', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'url',
			array(
				'label'       => esc_html__( 'URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'default'     => array(
					'url' => '',
				),
			)
		);

		$repeater->add_control(
			'style_variant',
			array(
				'label'   => esc_html__( 'Style', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => esc_html__( 'Default', 'foundation-elementor-plus' ),
					'primary' => esc_html__( 'Primary', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'quick_actions',
			array(
				'label'       => esc_html__( 'Actions', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => $this->get_default_quick_actions(),
			)
		);

		$this->end_controls_section();
	}

	private function register_menu_controls() {
		$panel_options = $this->get_panel_key_options();

		$this->start_controls_section(
			'section_menu',
			array(
				'label' => esc_html__( 'Menu Panels', 'foundation-elementor-plus' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Panel Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Menu Item', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'panel_key',
			array(
				'label'       => esc_html__( 'Panel Key', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $panel_options,
				'default'     => 'about_us',
				'label_block' => true,
				'description' => esc_html__( 'Used to connect the structured utility pills and submenu link fields below.', 'foundation-elementor-plus' ),
			)
		);

		$repeater->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'Panel Subtitle', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'open_by_default',
			array(
				'label'        => esc_html__( 'Open By Default', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$repeater->add_control(
			'show_featured',
			array(
				'label'        => esc_html__( 'Show Featured Card', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$repeater->add_control(
			'featured_eyebrow',
			array(
				'label'     => esc_html__( 'Featured Eyebrow', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'show_featured' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'featured_title',
			array(
				'label'     => esc_html__( 'Featured Title', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'label_block' => true,
				'condition' => array(
					'show_featured' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'featured_body',
			array(
				'label'     => esc_html__( 'Featured Body', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 4,
				'default'   => '',
				'condition' => array(
					'show_featured' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'featured_cta_text',
			array(
				'label'     => esc_html__( 'Featured CTA Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'show_featured' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'featured_cta_url',
			array(
				'label'       => esc_html__( 'Featured CTA URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'default'     => array(
					'url' => '',
				),
				'condition'   => array(
					'show_featured' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'utility_rows',
			array(
				'label'       => esc_html__( 'Quick Links', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 5,
				'label_block' => true,
				'description' => esc_html__( 'One quick link per line using: Label | URL', 'foundation-elementor-plus' ),
				'default'     => '',
			)
		);

		$section_link_repeater = new Repeater();

		$section_link_repeater->add_control(
			'section_label',
			array(
				'label'       => esc_html__( 'Group Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$section_link_repeater->add_control(
			'item_title',
			array(
				'label'       => esc_html__( 'Link Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$section_link_repeater->add_control(
			'item_description',
			array(
				'label'       => esc_html__( 'Short Description', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => '',
				'label_block' => true,
			)
		);

		$section_link_repeater->add_control(
			'item_url',
			array(
				'label'       => esc_html__( 'Link URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'default'     => array(
					'url' => '',
				),
			)
		);

		$repeater->add_control(
			'section_links',
			array(
				'label'       => esc_html__( 'Sublinks', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $section_link_repeater->get_controls(),
				'title_field' => '{{{ section_label }}}: {{{ item_title }}}',
				'button_text' => esc_html__( 'Add Sublink', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Add one sublink per row. The group label becomes the card heading.', 'foundation-elementor-plus' ),
				'default'     => array(),
			)
		);

		$this->add_control(
			'menu_sections',
			array(
				'label'       => esc_html__( 'Panels', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => $this->get_default_menu_sections(),
			)
		);

		$this->end_controls_section();
	}

	private function register_footer_controls() {
		$this->start_controls_section(
			'section_footer',
			array(
				'label' => esc_html__( 'Menu Footer', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'footer_phone',
			array(
				'label'       => esc_html__( 'Phone', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '+44 (0)333 613 4653',
				'label_block' => true,
			)
		);

		$this->add_control(
			'footer_email',
			array(
				'label'       => esc_html__( 'Email', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Hello@infire.co.uk',
				'label_block' => true,
			)
		);

		$this->add_control(
			'footer_address',
			array(
				'label'       => esc_html__( 'Address', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => '9 Kingswell Road, Ensbury Park, Bournemouth, BH10 5DF',
				'label_block' => true,
			)
		);

		$this->add_control(
			'footer_hours',
			array(
				'label'       => esc_html__( 'Office Hours', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Mon – Fri, 9am – 5pm (UK time)',
				'label_block' => true,
			)
		);

		$social_repeater = new Repeater();

		$social_repeater->add_control(
			'label',
			array(
				'label'       => esc_html__( 'Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Social Link', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$social_repeater->add_control(
			'icon_class',
			array(
				'label'       => esc_html__( 'Font Awesome Class', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'fa-brands fa-linkedin-in',
				'label_block' => true,
			)
		);

		$social_repeater->add_control(
			'url',
			array(
				'label'       => esc_html__( 'URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'default'     => array(
					'url' => '',
				),
			)
		);

		$this->add_control(
			'social_links',
			array(
				'label'       => esc_html__( 'Social Links', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $social_repeater->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => $this->get_default_social_links(),
			)
		);

		$this->end_controls_section();
	}

	private function get_logo_url( array $settings ): string {
		if ( ! empty( $settings['logo']['url'] ) ) {
			return (string) $settings['logo']['url'];
		}

		$custom_logo_id = (int) get_theme_mod( 'custom_logo' );
		if ( $custom_logo_id ) {
			$custom_logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
			if ( $custom_logo_url ) {
				return (string) $custom_logo_url;
			}
		}

		return Utils::get_placeholder_image_src();
	}

	private function get_link_url( $value, string $fallback = '' ): string {
		if ( is_array( $value ) && ! empty( $value['url'] ) ) {
			return (string) $value['url'];
		}

		if ( is_string( $value ) && '' !== $value ) {
			return $value;
		}

		return $fallback;
	}

	private function get_default_open_index( array $menu_sections ): int {
		foreach ( $menu_sections as $index => $section ) {
			if ( ! empty( $section['open_by_default'] ) && 'yes' === $section['open_by_default'] ) {
				return (int) $index;
			}
		}

		return -1;
	}

	private function parse_pill_rows( string $rows ): array {
		$items = array();

		foreach ( preg_split( "/\\r\\n|\\r|\\n/", $rows ) as $line ) {
			$line = trim( (string) $line );
			if ( '' === $line ) {
				continue;
			}

			$parts = array_map( 'trim', explode( '|', $line, 2 ) );
			$label = $parts[0] ?? '';
			$url   = $parts[1] ?? '';

			if ( '' === $label ) {
				continue;
			}

			$items[] = array(
				'label' => $label,
				'url'   => $url,
			);
		}

		return $items;
	}

	private function parse_section_rows( string $rows ): array {
		$grouped = array();

		foreach ( $this->parse_section_row_items( $rows ) as $item ) {
			$section_label    = $item['section_label'];
			$item_title       = $item['item_title'];
			$item_description = $item['item_description'];
			$item_url         = $item['item_url'];

			if ( '' === $section_label || '' === $item_title ) {
				continue;
			}

			if ( ! isset( $grouped[ $section_label ] ) ) {
				$grouped[ $section_label ] = array(
					'label' => $section_label,
					'items' => array(),
				);
			}

			$grouped[ $section_label ]['items'][] = array(
				'title'       => $item_title,
				'description' => $item_description,
				'url'         => $item_url,
			);
		}

		return array_values( $grouped );
	}

	private function parse_section_row_items( string $rows ): array {
		$items = array();

		foreach ( preg_split( "/\\r\\n|\\r|\\n/", $rows ) as $line ) {
			$line = trim( (string) $line );
			if ( '' === $line ) {
				continue;
			}

			$parts = array_map( 'trim', explode( '|', $line ) );
			$parts = array_pad( $parts, 4, '' );

			list( $section_label, $item_title, $item_description, $item_url ) = $parts;

			if ( '' === $section_label || '' === $item_title ) {
				continue;
			}

			$items[] = array(
				'section_label'    => $section_label,
				'item_title'       => $item_title,
				'item_description' => $item_description,
				'item_url'         => $item_url,
			);
		}

		return $items;
	}

	private function normalize_panel_section_links( array $rows ): array {
		$items = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$section_label    = trim( (string) ( $row['section_label'] ?? '' ) );
			$item_title       = trim( (string) ( $row['item_title'] ?? '' ) );
			$item_description = trim( (string) ( $row['item_description'] ?? '' ) );
			$item_url         = $this->get_link_url( $row['item_url'] ?? array() );

			if ( '' === $section_label || '' === $item_title ) {
				continue;
			}

			$items[] = array(
				'section_label'    => $section_label,
				'item_title'       => $item_title,
				'item_description' => $item_description,
				'item_url'         => $item_url,
			);
		}

		return $items;
	}

	private function get_panel_sections( array $section ): array {
		$structured_rows = $this->normalize_panel_section_links( $section['section_links'] ?? array() );

		if ( ! empty( $structured_rows ) ) {
			$grouped = array();

			foreach ( $structured_rows as $item ) {
				$section_label = $item['section_label'];

				if ( ! isset( $grouped[ $section_label ] ) ) {
					$grouped[ $section_label ] = array(
						'label' => $section_label,
						'items' => array(),
					);
				}

				$grouped[ $section_label ]['items'][] = array(
					'title'       => $item['item_title'],
					'description' => $item['item_description'],
					'url'         => $item['item_url'],
				);
			}

			return array_values( $grouped );
		}

		return $this->parse_section_rows( (string) ( $section['section_rows'] ?? '' ) );
	}

	private function normalize_panel_keys( array $menu_sections ): array {
		foreach ( $menu_sections as $index => $section ) {
			if ( ! is_array( $section ) ) {
				continue;
			}

			$menu_sections[ $index ]['panel_key'] = $this->get_panel_key_for_section( $section );
		}

		return $menu_sections;
	}

	private function get_panel_key_for_section( array $section ): string {
		$panel_key = $this->sanitize_panel_key( $section['panel_key'] ?? '' );

		if ( '' !== $panel_key ) {
			return $panel_key;
		}

		$title = strtolower( trim( wp_strip_all_tags( (string) ( $section['title'] ?? '' ) ) ) );

		switch ( $title ) {
			case 'about us':
				return 'about_us';
			case 'our services':
				return 'our_services';
			case 'resource hub':
				return 'resource_hub';
			case 'portfolio':
				return 'portfolio';
			case 'foundation':
				return 'foundation';
			default:
				return 'about_us';
		}
	}

	private function sanitize_panel_key( $value ): string {
		$value = sanitize_key( (string) $value );

		return array_key_exists( $value, $this->get_panel_key_options() ) ? $value : '';
	}

	private function get_default_quick_actions(): array {
		return array(
			array(
				'label'         => 'Start a Project',
				'url'           => array( 'url' => home_url( '/contact-us/' ) ),
				'style_variant' => 'primary',
			),
			array(
				'label'         => 'Contact',
				'url'           => array( 'url' => home_url( '/contact-us/' ) ),
				'style_variant' => 'default',
			),
			array(
				'label'         => 'Portfolio',
				'url'           => array( 'url' => home_url( '/portfolio/' ) ),
				'style_variant' => 'default',
			),
			array(
				'label'         => 'Blog',
				'url'           => array( 'url' => home_url( '/blog/' ) ),
				'style_variant' => 'default',
			),
		);
	}

	private function get_inline_actions( array $quick_actions ): array {
		$items = array();

		foreach ( $quick_actions as $action ) {
			$label = ! empty( $action['label'] ) ? $action['label'] : '';
			$url   = $this->get_link_url( $action['url'] ?? array() );

			if ( '' === $label || '' === $url ) {
				continue;
			}

			$items[] = array(
				'label' => $label,
				'url'   => $url,
			);

			if ( count( $items ) >= 2 ) {
				break;
			}
		}

		return $items;
	}

	private function get_menu_quick_action_label( string $label ): string {
		$normalized = strtolower( trim( wp_strip_all_tags( $label ) ) );

		switch ( $normalized ) {
			case 'start a project':
				return esc_html__( 'Start Project', 'foundation-elementor-plus' );
			case 'contact':
				return esc_html__( 'Contact Us', 'foundation-elementor-plus' );
			case 'portfolio':
				return esc_html__( 'Our Work', 'foundation-elementor-plus' );
			case 'blog':
				return esc_html__( 'Insights', 'foundation-elementor-plus' );
			default:
				return $label;
		}
	}

	private function backfill_menu_sections( array $menu_sections ): array {
		foreach ( $menu_sections as $index => $section ) {
			$title = isset( $section['title'] ) ? strtolower( trim( wp_strip_all_tags( (string) $section['title'] ) ) ) : '';

			$current_rows = isset( $section['section_rows'] ) ? (string) $section['section_rows'] : '';

			if ( empty( $section['section_links'] ) && '' !== $current_rows ) {
				$menu_sections[ $index ]['section_links'] = $this->parse_section_row_items( $current_rows );
			}

			if ( 'our services' !== $title ) {
				continue;
			}

			$menu_sections[ $index ]['section_rows']  = $this->backfill_service_section_rows( $current_rows );
			$menu_sections[ $index ]['section_links'] = $this->parse_section_row_items( $menu_sections[ $index ]['section_rows'] );
			break;
		}

		return $menu_sections;
	}

	private function backfill_service_section_rows( string $rows ): string {
		$lines               = array();
		$has_business_support = false;

		foreach ( preg_split( "/\\r\\n|\\r|\\n/", $rows ) as $line ) {
			$line = trim( (string) $line );

			if ( '' === $line ) {
				continue;
			}

			if ( preg_match( '/^Business Support\\s*\\|/i', $line ) ) {
				$has_business_support = true;
			}

			$lines[] = $line;
		}

		if ( ! $has_business_support ) {
			$lines = array_merge( $lines, $this->get_business_support_section_rows() );
		}

		return implode( "\n", $lines );
	}

	private function get_business_support_section_rows(): array {
		return array(
			'Business Support | Virtual Business Support | Flexible remote support for admin, digital systems and day-to-day tasks | ' . home_url( '/services/business-support/virtual-business-support/' ),
			'Business Support | Digital & Business Planning | Strategic planning for digital projects, services and sustainable growth | ' . home_url( '/services/business-support/digital-business-planning/' ),
			'Business Support | Access to Work Support | Guidance and support for Access to Work applications and workplace adjustments | ' . home_url( '/services/business-support/access-to-work-support/' ),
			'Business Support | Training & Handover Support | Training sessions and documentation to help teams manage tools and systems | ' . home_url( '/services/business-support/training-handover-support/' ),
		);
	}

	private function get_panel_key_options(): array {
		return array(
			'about_us'      => esc_html__( 'About Us', 'foundation-elementor-plus' ),
			'our_services'  => esc_html__( 'Our Services', 'foundation-elementor-plus' ),
			'resource_hub'  => esc_html__( 'Resource Hub', 'foundation-elementor-plus' ),
			'portfolio'     => esc_html__( 'Portfolio', 'foundation-elementor-plus' ),
			'foundation'    => esc_html__( 'Foundation', 'foundation-elementor-plus' ),
		);
	}

	private function get_default_menu_sections(): array {
		$about_rows = implode(
			"\n",
			array(
				'Who we are | Our Story | How Inkfire came to be | ' . home_url( '/about-us/' ),
				'Who we are | Our Values | Culture, care and high standards | ' . home_url( '/about-us/' ),
				'Our People | Meet the Team | The strategists, creatives and developers behind the scenes | ' . home_url( '/about-us/' ),
				'Our People | Careers | Remote-first, inclusive roles that matter | ' . home_url( '/about-us/' ),
				'Our Impact | Client Stories | Real-world outcomes for organisations | ' . home_url( '/portfolio/' ),
				'Our Impact | Awards & Recognition | Recognition for inclusive design and tech | ' . home_url( '/about-us/' ),
			)
		);
		$services_rows = implode(
			"\n",
			array(
				'Tech & Support | IT Support | Ongoing IT, Microsoft 365, security | ' . home_url( '/services/tech-support/it-support/' ),
				'Tech & Support | Customer Helpdesk & Support | Helpdesk setup and support systems | ' . home_url( '/services/tech-support/customer-helpdesk-support/' ),
				'Tech & Support | Project-Based IT Support | Migrations, systems and security improvements | ' . home_url( '/services/tech-support/project-based-it-support/' ),
				'Tech & Support | Virtual Assistance | Admin, inbox and scheduling support | ' . home_url( '/services/tech-support/virtual-assistance/' ),
				'Web & Accessibility | Web Development | High-performing, scalable websites | ' . home_url( '/services/web-accessibility/web-development/' ),
				'Web & Accessibility | Maintenance & Hosting | Managed hosting and proactive support | ' . home_url( '/services/web-accessibility/maintenance-hosting-performance/' ),
				'Web & Accessibility | Copywriting & Messaging | Clear, accessible content and messaging | ' . home_url( '/services/web-accessibility/copywriting-messaging/' ),
				'Web & Accessibility | Accessibility & Inclusive Design | Audits, guidance and inclusive UX | ' . home_url( '/services/web-accessibility/accessibility-inclusive-design/' ),
				'Creative & Marketing | Branding | Distinctive logos and identity systems | ' . home_url( '/services/creative-marketing/branding/' ),
				'Creative & Marketing | Marketing & PR | Campaigns, outreach and visibility | ' . home_url( '/services/creative-marketing/marketing-pr/' ),
				'Creative & Marketing | Social Media Management | Content planning and inclusive socials | ' . home_url( '/services/creative-marketing/social-media-management/' ),
				'Creative & Marketing | Print & Packaging | Custom labels, packaging and print design | ' . home_url( '/services/creative-marketing/print-packaging/' ),
				'Business Support | Virtual Business Support | Flexible remote support for admin, digital systems and day-to-day tasks | ' . home_url( '/services/business-support/virtual-business-support/' ),
				'Business Support | Digital & Business Planning | Strategic planning for digital projects, services and sustainable growth | ' . home_url( '/services/business-support/digital-business-planning/' ),
				'Business Support | Access to Work Support | Guidance and support for Access to Work applications and workplace adjustments | ' . home_url( '/services/business-support/access-to-work-support/' ),
				'Business Support | Training & Handover Support | Training sessions and documentation to help teams manage tools and systems | ' . home_url( '/services/business-support/training-handover-support/' ),
			)
		);
		$resource_rows = implode(
			"\n",
			array(
				'Browse | Knowledge Base | Step-by-step guides and troubleshooting help | https://help.mediumblue-seal-942128.hostingersite.com/',
				'Browse | Press, Media & Community Highlights | Announcements and interviews | ' . home_url( '/category/press-and-media/' ),
				'Browse | What’s New at Inkfire | Latest projects, tips and behind-the-scenes updates | ' . home_url( '/category/news/' ),
				'Browse | Mali TV | Short practical videos on tech, design and Inkfire life | https://www.youtube.com/@mali.and.m.e',
				'Browse | Case Studies | Examples of accessible digital work in action | ' . home_url( '/portfolio/' ),
			)
		);
		$portfolio_rows = implode(
			"\n",
			array(
				'Portfolio archive | All Work | Open the full archive and browse everything in one place | ' . home_url( '/portfolio/' ),
				'Portfolio archive | Web Projects | Sites, platforms, UX and digital builds | ' . home_url( '/services/web-accessibility/' ),
				'Portfolio archive | Marketing Work | Branding, campaigns and creative delivery | ' . home_url( '/services/creative-marketing/' ),
				'Portfolio archive | IT & Support | Technical support and dependable delivery | ' . home_url( '/services/tech-support/' ),
			)
		);
		$foundation_rows = implode(
			"\n",
			array(
				'Featured Work | Work that performs under pressure | Designed for disabled users and organisations that care about quality | ' . home_url( '/foundation/' ),
				'Featured Work | Web Development | Accessible, scalable websites built to last | ' . home_url( '/services/web-accessibility/' ),
				'Featured Work | IT & Technical Support | Calm, reliable systems and support | ' . home_url( '/services/tech-support/' ),
				'Featured Work | Branding & Identity | Clear brands rooted in lived experience | ' . home_url( '/services/creative-marketing/' ),
			)
		);

		return array(
			array(
				'panel_key'        => 'about_us',
				'title'            => 'About Us',
				'subtitle'         => 'Who we are, our people, our impact',
				'open_by_default'  => '',
				'show_featured'    => 'yes',
				'featured_eyebrow' => 'Disabled-led',
				'featured_title'   => 'Inclusive by design.',
				'featured_body'    => 'Building accessible products and a truly inclusive agency.',
				'featured_cta_text' => 'Read the manifesto',
				'featured_cta_url' => array( 'url' => home_url( '/about-us/' ) ),
				'utility_rows'     => '',
				'section_links'    => $this->parse_section_row_items( $about_rows ),
				'section_rows'     => $about_rows,
			),
			array(
				'panel_key'        => 'our_services',
				'title'            => 'Our Services',
				'subtitle'         => 'Tech, web, creative and business support',
				'open_by_default'  => 'yes',
				'show_featured'    => 'yes',
				'featured_eyebrow' => 'Built to deliver',
				'featured_title'   => 'Technical excellence, clear outcomes.',
				'featured_body'    => 'Support, development, accessibility and strategic delivery in one service ecosystem.',
				'featured_cta_text' => 'Explore services',
				'featured_cta_url' => array( 'url' => home_url( '/services/' ) ),
				'utility_rows'     => '',
				'section_links'    => $this->parse_section_row_items( $services_rows ),
				'section_rows'     => $services_rows,
			),
			array(
				'panel_key'       => 'resource_hub',
				'title'           => 'Resource Hub',
				'subtitle'        => 'Guides, case studies and latest updates',
				'open_by_default' => '',
				'show_featured'   => '',
				'utility_rows'    => implode(
					"\n",
					array(
						'Explore Blog | ' . home_url( '/blog/' ),
						'Knowledge Base | https://help.mediumblue-seal-942128.hostingersite.com/',
						'Case Studies | ' . home_url( '/portfolio/' ),
					)
				),
				'section_links'   => $this->parse_section_row_items( $resource_rows ),
				'section_rows'    => $resource_rows,
			),
			array(
				'panel_key'       => 'portfolio',
				'title'           => 'Portfolio',
				'subtitle'        => 'Browse work by category',
				'open_by_default' => '',
				'show_featured'   => '',
				'utility_rows'    => implode(
					"\n",
					array(
						'All Work | ' . home_url( '/portfolio/' ),
						'Web Projects | ' . home_url( '/services/web-accessibility/' ),
						'Marketing Work | ' . home_url( '/services/creative-marketing/' ),
						'IT & Support | ' . home_url( '/services/tech-support/' ),
					)
				),
				'section_links'   => $this->parse_section_row_items( $portfolio_rows ),
				'section_rows'    => $portfolio_rows,
			),
			array(
				'panel_key'       => 'foundation',
				'title'           => 'Foundation',
				'subtitle'        => 'Mission-led work and featured projects',
				'open_by_default' => '',
				'show_featured'   => '',
				'utility_rows'    => '',
				'section_links'   => $this->parse_section_row_items( $foundation_rows ),
				'section_rows'    => $foundation_rows,
			),
		);
	}

	private function get_default_social_links(): array {
		return array(
			array(
				'label'      => 'LinkedIn',
				'icon_class' => 'fa-brands fa-linkedin-in',
				'url'        => array( 'url' => 'https://uk.linkedin.com/company/inkfire' ),
			),
			array(
				'label'      => 'X',
				'icon_class' => 'fa-brands fa-x-twitter',
				'url'        => array( 'url' => 'https://twitter.com/Inkfirelimited' ),
			),
			array(
				'label'      => 'Facebook',
				'icon_class' => 'fa-brands fa-facebook-f',
				'url'        => array( 'url' => 'https://facebook.com/inkfirelimited' ),
			),
			array(
				'label'      => 'Instagram',
				'icon_class' => 'fa-brands fa-instagram',
				'url'        => array( 'url' => 'https://www.instagram.com/inkfirelimited/' ),
			),
			array(
				'label'      => 'TikTok',
				'icon_class' => 'fa-brands fa-tiktok',
				'url'        => array( 'url' => 'https://www.tiktok.com/@inkfirelimited' ),
			),
		);
	}

	private function has_footer_content( array $settings, array $social_links ): bool {
		return ! empty( $settings['footer_phone'] )
			|| ! empty( $settings['footer_email'] )
			|| ! empty( $settings['footer_address'] )
			|| ! empty( $settings['footer_hours'] )
			|| ! empty( $social_links );
	}

	private function format_tel_url( string $phone ): string {
		$sanitized = preg_replace( '/[^0-9+]/', '', $phone );

		return $sanitized ? 'tel:' . $sanitized : '#';
	}

	private function sanitize_icon_class( string $value ): string {
		$value = trim( preg_replace( '/[^a-z0-9\\-\\s]/i', '', $value ) );

		return preg_replace( '/\\s+/', ' ', $value );
	}
}
