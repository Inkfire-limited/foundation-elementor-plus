<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Y_Hero_Widget extends Widget_Base {
	public function get_name() {
		return 'foundation-y-hero';
	}

	public function get_title() {
		return esc_html__( 'Foundation Y Hero', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'hero', 'slider', 'stack', 'stats' );
	}

	public function get_style_depends(): array {
		return array( 'foundation-elementor-plus-y-hero' );
	}

	public function get_script_depends(): array {
		return array( 'foundation-elementor-plus-y-hero' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_shell_style_controls();
		$this->register_left_card_style_controls();
		$this->register_right_content_style_controls();
		$this->register_control_style_controls();
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$heading_tag   = isset( $settings['heading_tag'] ) && in_array( $settings['heading_tag'], array( 'h1', 'h2', 'h3' ), true ) ? $settings['heading_tag'] : 'h1';
		$cards         = ! empty( $settings['cards'] ) && is_array( $settings['cards'] ) ? array_values( $settings['cards'] ) : array();
		$stats         = ! empty( $settings['stats'] ) && is_array( $settings['stats'] ) ? array_values( $settings['stats'] ) : array();
		$widget_id     = 'foundation-y-hero-' . $this->get_id();
		$breakpoint    = ! empty( $settings['mobile_breakpoint'] ) ? (int) $settings['mobile_breakpoint'] : 920;
		$stack_id      = $widget_id . '-stack';
		$prev_label    = ! empty( $settings['prev_label'] ) ? $settings['prev_label'] : '↑';
		$next_label    = ! empty( $settings['next_label'] ) ? $settings['next_label'] : '↓';
		$prev_icon     = $this->get_icon_markup( $settings['prev_icon'] ?? array(), 'foundation-y-hero__arrow-icon' );
		$next_icon     = $this->get_icon_markup( $settings['next_icon'] ?? array(), 'foundation-y-hero__arrow-icon' );
		$cta_icon      = $this->get_icon_markup( $settings['cta_icon'] ?? array(), 'foundation-y-hero__cta-icon' );
		$card_link_icon = $this->get_icon_markup( $settings['left_card_link_icon'] ?? array(), 'foundation-y-hero__card-link-icon' );
		$show_arrows   = isset( $settings['show_arrows'] ) && 'yes' === $settings['show_arrows'];
		$show_cta      = ! empty( $settings['cta_text'] );
		$show_controls = $show_arrows || $show_cta;

		if ( empty( $cards ) ) {
			return;
		}

		if ( $show_cta ) {
			$this->add_link_attributes( 'cta_link', $settings['cta_link'] );
		}
		?>
		<section id="<?php echo esc_attr( $widget_id ); ?>" class="foundation-y-hero" data-foundation-y-hero data-mobile-breakpoint="<?php echo esc_attr( (string) $breakpoint ); ?>">
			<div class="foundation-y-hero__shell">
				<div class="foundation-y-hero__grid">
					<div class="foundation-y-hero__left">
						<div id="<?php echo esc_attr( $stack_id ); ?>" class="foundation-y-hero__stack">
							<?php foreach ( $cards as $index => $card ) : ?>
								<?php
								$title       = ! empty( $card['title'] ) ? $card['title'] : sprintf( 'Card %d', $index + 1 );
								$description = ! empty( $card['description'] ) ? $card['description'] : '';
								$link_text   = ! empty( $card['link_text'] ) ? $card['link_text'] : '';
								$link_key    = 'card_link_' . $index;
								$link_data   = ( ! empty( $card['link'] ) && is_array( $card['link'] ) ) ? $card['link'] : array();
								$show_link   = ! empty( $link_text ) && ! empty( $link_data['url'] );

								if ( $show_link ) {
									$this->add_link_attributes( $link_key, $link_data );
								}
								?>
								<article class="foundation-y-hero__card">
									<h3><?php echo esc_html( $title ); ?></h3>
									<div class="foundation-y-hero__card-copy">
										<?php echo wp_kses_post( wpautop( $description ) ); ?>
									</div>
									<?php if ( $show_link ) : ?>
										<div class="foundation-y-hero__card-footer">
											<a class="foundation-y-hero__card-link" <?php echo $this->get_render_attribute_string( $link_key ); ?>>
												<span class="foundation-y-hero__card-link-text"><?php echo esc_html( $link_text ); ?></span>
												<?php echo $card_link_icon; ?>
											</a>
										</div>
									<?php endif; ?>
								</article>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="foundation-y-hero__right">
						<?php if ( ! empty( $settings['heading'] ) ) : ?>
							<<?php echo esc_attr( $heading_tag ); ?>><?php echo wp_kses_post( nl2br( esc_html( $settings['heading'] ) ) ); ?></<?php echo esc_attr( $heading_tag ); ?>>
						<?php endif; ?>

						<?php if ( ! empty( $settings['intro'] ) ) : ?>
							<div class="foundation-y-hero__intro">
								<?php echo wp_kses_post( wpautop( $settings['intro'] ) ); ?>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $stats ) ) : ?>
							<div class="foundation-y-hero__stats">
								<?php foreach ( $stats as $stat ) : ?>
									<div class="foundation-y-hero__stat">
										<?php if ( ! empty( $stat['value'] ) ) : ?>
											<strong><?php echo esc_html( $stat['value'] ); ?></strong>
										<?php endif; ?>
										<?php if ( ! empty( $stat['label'] ) ) : ?>
											<span><?php echo esc_html( $stat['label'] ); ?></span>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php if ( $show_controls ) : ?>
							<div class="foundation-y-hero__controls">
								<?php if ( $show_arrows ) : ?>
									<button type="button" class="foundation-y-hero__arrow foundation-y-hero__arrow--prev" data-yhero-prev aria-label="<?php echo esc_attr( ! empty( $settings['prev_aria_label'] ) ? $settings['prev_aria_label'] : 'Previous card' ); ?>">
										<?php
										if ( $prev_icon ) {
											echo $prev_icon;
										} else {
											?>
											<span><?php echo esc_html( $prev_label ); ?></span>
											<?php
										}
										?>
									</button>
									<button type="button" class="foundation-y-hero__arrow foundation-y-hero__arrow--next" data-yhero-next aria-label="<?php echo esc_attr( ! empty( $settings['next_aria_label'] ) ? $settings['next_aria_label'] : 'Next card' ); ?>">
										<?php
										if ( $next_icon ) {
											echo $next_icon;
										} else {
											?>
											<span><?php echo esc_html( $next_label ); ?></span>
											<?php
										}
										?>
									</button>
								<?php endif; ?>

								<?php if ( $show_cta ) : ?>
									<a class="foundation-y-hero__cta" <?php echo $this->get_render_attribute_string( 'cta_link' ); ?>>
										<span class="foundation-y-hero__cta-text"><?php echo esc_html( $settings['cta_text'] ); ?></span>
										<?php echo $cta_icon; ?>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}

	private function register_content_controls() {
		$this->start_controls_section(
			'section_main_content',
			array(
				'label' => esc_html__( 'Main Content', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'       => esc_html__( 'Heading', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => "London's Leading\nWordPress Agency",
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'heading_tag',
			array(
				'label'   => esc_html__( 'Heading HTML Tag', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => array( 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3' ),
			)
		);

		$this->add_control(
			'intro',
			array(
				'label'   => esc_html__( 'Intro Copy', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 5,
				'default' => esc_html__( 'We are a London WordPress web design agency that combines deep expertise, sharp creative thinking, and a results-first approach to deliver sites that perform.', 'foundation-elementor-plus' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'        => esc_html__( 'Show Arrow Controls', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'prev_label',
			array(
				'label'       => esc_html__( 'Previous Button Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '↑',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'prev_icon',
			array(
				'label'     => esc_html__( 'Previous Icon', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-arrow-up',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'next_label',
			array(
				'label'       => esc_html__( 'Next Button Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '↓',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'next_icon',
			array(
				'label'     => esc_html__( 'Next Icon', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-arrow-down',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'prev_aria_label',
			array(
				'label'       => esc_html__( 'Previous Button Aria Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Previous card', 'foundation-elementor-plus' ),
				'label_block' => true,
				'condition'   => array(
					'show_arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'next_aria_label',
			array(
				'label'       => esc_html__( 'Next Button Aria Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Next card', 'foundation-elementor-plus' ),
				'label_block' => true,
				'condition'   => array(
					'show_arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'       => esc_html__( 'CTA Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Get a WordPress quote ->', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'cta_icon',
			array(
				'label'   => esc_html__( 'CTA Icon', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'cta_link',
			array(
				'label'         => esc_html__( 'CTA Link', 'foundation-elementor-plus' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://',
				'default'       => array(
					'url' => '#',
				),
				'show_external' => true,
				'dynamic'       => array(
					'active' => true,
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cards',
			array(
				'label' => esc_html__( 'Left Cards', 'foundation-elementor-plus' ),
			)
		);

		$card_repeater = new Repeater();

		$card_repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Card title', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$card_repeater->add_control(
			'description',
			array(
				'label'   => esc_html__( 'Description', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 5,
				'default' => esc_html__( 'Add the supporting card copy here.', 'foundation-elementor-plus' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$card_repeater->add_control(
			'link_text',
			array(
				'label'       => esc_html__( 'Link Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$card_repeater->add_control(
			'link',
			array(
				'label'         => esc_html__( 'Link URL', 'foundation-elementor-plus' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://',
				'default'       => array(
					'url' => '',
				),
				'show_external' => true,
				'dynamic'       => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'cards',
			array(
				'label'       => esc_html__( 'Cards', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $card_repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => $this->get_default_cards(),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_stats',
			array(
				'label' => esc_html__( 'Stats', 'foundation-elementor-plus' ),
			)
		);

		$stat_repeater = new Repeater();

		$stat_repeater->add_control(
			'value',
			array(
				'label'       => esc_html__( 'Value', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '250+',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$stat_repeater->add_control(
			'label',
			array(
				'label'       => esc_html__( 'Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Websites Delivered', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'stats',
			array(
				'label'       => esc_html__( 'Stats', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $stat_repeater->get_controls(),
				'title_field' => '{{{ value }}} - {{{ label }}}',
				'default'     => $this->get_default_stats(),
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
			'widget_width',
			array(
				'label'      => esc_html__( 'Widget Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => '%',
					'size' => 95,
				),
				'range'      => array(
					'px'  => array(
						'min' => 240,
						'max' => 2400,
					),
					'%'   => array(
						'min' => 10,
						'max' => 100,
					),
					'vw'  => array(
						'min' => 10,
						'max' => 100,
					),
					'vh'  => array(
						'min' => 10,
						'max' => 150,
					),
					'em'  => array(
						'min' => 10,
						'max' => 160,
					),
					'rem' => array(
						'min' => 10,
						'max' => 160,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero__shell' => 'width: {{SIZE}}{{UNIT}}; max-width: none;',
				),
			)
		);

		$this->add_responsive_control(
			'shell_padding',
			array(
				'label'      => esc_html__( 'Shell Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'top'      => 40,
					'right'    => 48,
					'bottom'   => 40,
					'left'     => 48,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero__shell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'shell_radius',
			array(
				'label'      => esc_html__( 'Shell Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 48,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-shell-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'Columns Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 72,
				),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 160,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-grid-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'left_column_width',
			array(
				'label'      => esc_html__( 'Card Column Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 480,
				),
				'range'      => array(
					'px' => array(
						'min' => 240,
						'max' => 680,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-left-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'left_stage_height',
			array(
				'label'      => esc_html__( 'Slider Window Height', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 620,
				),
				'range'      => array(
					'px' => array(
						'min' => 220,
						'max' => 900,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-stage-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'left_stage_peek',
			array(
				'label'      => esc_html__( 'Top / Bottom Peek', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 84,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 220,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-stage-peek: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'stack_gap',
			array(
				'label'      => esc_html__( 'Space Between Cards', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 28,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-stack-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_min_height',
			array(
				'label'      => esc_html__( 'Card Height', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 220,
				),
				'range'      => array(
					'px' => array(
						'min' => 120,
						'max' => 480,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-card-min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'stats_columns',
			array(
				'label'      => esc_html__( 'Stats Columns', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 3,
				'min'        => 1,
				'max'        => 6,
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-stat-columns: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'stacked_stat_min_width',
			array(
				'label'      => esc_html__( 'Stacked Stat Min Width', 'foundation-elementor-plus' ),
				'description'=> esc_html__( 'When the layout stacks, stats stay in a row if they fit this minimum width.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 140,
				),
				'range'      => array(
					'px' => array(
						'min' => 80,
						'max' => 320,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-stacked-stat-min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'mobile_breakpoint',
			array(
				'label'      => esc_html__( 'Slider Breakpoint', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 920,
				'min'        => 480,
				'max'        => 1440,
			)
		);

		$this->end_controls_section();
	}

	private function register_shell_style_controls() {
		$this->start_controls_section(
			'section_shell_styles',
			array(
				'label' => esc_html__( 'Shell', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'shell_background',
			array(
				'label'     => esc_html__( 'Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F3F3F3',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__shell' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'shell_overlay_image',
			array(
				'label'       => esc_html__( 'Overlay Image', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::MEDIA,
				'label_block' => true,
				'default'     => array(
					'url' => 'https://beta.mediumblue-seal-942128.hostingersite.com/wp-content/uploads/2025/12/Untitled-1.png',
				),
				'dynamic'     => array(
					'active' => true,
				),
				'selectors'   => array(
					'{{WRAPPER}} .foundation-y-hero__shell' => '--foundation-y-hero-overlay-image: url("{{URL}}");',
				),
			)
		);

		$this->add_responsive_control(
			'shell_overlay_fit',
			array(
				'label'       => esc_html__( 'Overlay Fit', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Cover fills the panel by cropping, not stretching. All modes preserve the image proportions.', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'cover',
				'options'     => array(
					'cover'         => esc_html__( 'Cover', 'foundation-elementor-plus' ),
					'contain'       => esc_html__( 'Contain (whole image)', 'foundation-elementor-plus' ),
					'custom-width'  => esc_html__( 'Custom width (automatic height)', 'foundation-elementor-plus' ),
					'custom-height' => esc_html__( 'Custom height (automatic width)', 'foundation-elementor-plus' ),
				),
				'selectors_dictionary' => array(
					'cover'         => 'cover',
					'contain'       => 'contain',
					'custom-width'  => 'var(--foundation-y-hero-overlay-width) auto',
					'custom-height' => 'auto var(--foundation-y-hero-overlay-height)',
				),
				'selectors'   => array(
					'{{WRAPPER}} .foundation-y-hero__shell' => '--foundation-y-hero-overlay-size: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'shell_overlay_width',
			array(
				'label'      => esc_html__( 'Overlay Width', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Used only with Custom width fit. Height follows the original image proportions.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => '%',
					'size' => 102,
				),
				'range'      => array(
					'px'  => array(
						'min' => 120,
						'max' => 3200,
					),
					'%'   => array(
						'min' => 10,
						'max' => 300,
					),
					'vw'  => array(
						'min' => 10,
						'max' => 200,
					),
					'vh'  => array(
						'min' => 10,
						'max' => 200,
					),
					'em'  => array(
						'min' => 10,
						'max' => 240,
					),
					'rem' => array(
						'min' => 10,
						'max' => 240,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero__shell' => '--foundation-y-hero-overlay-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'shell_overlay_height',
			array(
				'label'      => esc_html__( 'Overlay Height', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Used only with Custom height fit. Width follows the original image proportions.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => '%',
					'size' => 102,
				),
				'range'      => array(
					'px'  => array(
						'min' => 120,
						'max' => 3200,
					),
					'%'   => array(
						'min' => 10,
						'max' => 300,
					),
					'vw'  => array(
						'min' => 10,
						'max' => 200,
					),
					'vh'  => array(
						'min' => 10,
						'max' => 200,
					),
					'em'  => array(
						'min' => 10,
						'max' => 240,
					),
					'rem' => array(
						'min' => 10,
						'max' => 240,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero__shell' => '--foundation-y-hero-overlay-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'shell_overlay_position_x',
			array(
				'label'      => esc_html__( 'Overlay Position X', 'foundation-elementor-plus' ),
				'description'=> esc_html__( 'Move the overlay left or right under the content.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'range'      => array(
					'px'  => array(
						'min' => -1200,
						'max' => 1200,
					),
					'%'   => array(
						'min' => -50,
						'max' => 150,
					),
					'vw'  => array(
						'min' => -50,
						'max' => 150,
					),
					'vh'  => array(
						'min' => -50,
						'max' => 150,
					),
					'em'  => array(
						'min' => -80,
						'max' => 80,
					),
					'rem' => array(
						'min' => -80,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero__shell' => '--foundation-y-hero-overlay-position-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'shell_overlay_position_y',
			array(
				'label'      => esc_html__( 'Overlay Position Y', 'foundation-elementor-plus' ),
				'description'=> esc_html__( 'Move the overlay up or down inside the shell.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'range'      => array(
					'px'  => array(
						'min' => -1200,
						'max' => 1200,
					),
					'%'   => array(
						'min' => -50,
						'max' => 150,
					),
					'vw'  => array(
						'min' => -50,
						'max' => 150,
					),
					'vh'  => array(
						'min' => -50,
						'max' => 150,
					),
					'em'  => array(
						'min' => -80,
						'max' => 80,
					),
					'rem' => array(
						'min' => -80,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero__shell' => '--foundation-y-hero-overlay-position-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'shell_overlay_opacity',
			array(
				'label'      => esc_html__( 'Overlay Opacity', 'foundation-elementor-plus' ),
				'description'=> esc_html__( 'Keeps the shell artwork decorative only. Use low values for a subtle glass overlay.', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 0.3,
						'step' => 0.01,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0.08,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero__shell' => '--foundation-y-hero-overlay-opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'shell_border',
				'selector' => '{{WRAPPER}} .foundation-y-hero__shell',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'shell_shadow',
				'selector' => '{{WRAPPER}} .foundation-y-hero__shell',
			)
		);

		$this->add_responsive_control(
			'shell_backdrop_blur',
			array(
				'label'      => esc_html__( 'Backdrop Blur', 'foundation-elementor-plus' ),
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
					'{{WRAPPER}} .foundation-y-hero__shell' => '-webkit-backdrop-filter: blur({{SIZE}}{{UNIT}}); backdrop-filter: blur({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_left_card_style_controls() {
		$this->start_controls_section(
			'section_left_card_styles',
			array(
				'label' => esc_html__( 'Left Cards', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'left_card_background',
			array(
				'label'     => esc_html__( 'Card Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.68)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__card' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'left_card_border_color',
			array(
				'label'     => esc_html__( 'Card Border Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(20, 22, 35, 0.06)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__card' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'left_card_radius',
			array(
				'label'      => esc_html__( 'Card Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 34,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-card-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'left_card_padding',
			array(
				'label'      => esc_html__( 'Card Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'top'      => 32,
					'right'    => 32,
					'bottom'   => 32,
					'left'     => 32,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'left_card_shadow',
				'selector' => '{{WRAPPER}} .foundation-y-hero__card',
			)
		);

		$this->add_responsive_control(
			'left_card_copy_width',
			array(
				'label'      => esc_html__( 'Copy Max Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'ch', 'px', '%', 'vw', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'ch',
					'size' => 30,
				),
				'range'      => array(
					'ch' => array(
						'min' => 16,
						'max' => 48,
					),
					'px' => array(
						'min' => 180,
						'max' => 700,
					),
					'%' => array(
						'min' => 30,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-card-copy-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'left_card_backdrop_blur',
			array(
				'label'      => esc_html__( 'Card Backdrop Blur', 'foundation-elementor-plus' ),
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
					'{{WRAPPER}} .foundation-y-hero__card' => '-webkit-backdrop-filter: blur({{SIZE}}{{UNIT}}); backdrop-filter: blur({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_control(
			'left_card_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#202233',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__card h3' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'left_card_title_typography',
				'selector' => '{{WRAPPER}} .foundation-y-hero__card h3',
			)
		);

		$this->add_responsive_control(
			'left_card_title_spacing',
			array(
				'label'      => esc_html__( 'Title Spacing', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 14,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero__card h3' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'left_card_copy_color',
			array(
				'label'     => esc_html__( 'Copy Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#5F6170',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__card-copy, {{WRAPPER}} .foundation-y-hero__card-copy p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'left_card_copy_typography',
				'selector' => '{{WRAPPER}} .foundation-y-hero__card-copy, {{WRAPPER}} .foundation-y-hero__card-copy p',
			)
		);

		$this->add_control(
			'left_card_link_heading',
			array(
				'label'     => esc_html__( 'Card Link', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'left_card_link_icon',
			array(
				'label'   => esc_html__( 'Link Icon', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'left_card_link_color',
			array(
				'label'     => esc_html__( 'Link Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#151622',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__card-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'left_card_link_hover_color',
			array(
				'label'     => esc_html__( 'Link Hover Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0E4F4D',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__card-link:hover, {{WRAPPER}} .foundation-y-hero__card-link:focus-visible' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'left_card_link_typography',
				'selector' => '{{WRAPPER}} .foundation-y-hero__card-link',
			)
		);

		$this->add_responsive_control(
			'left_card_link_spacing',
			array(
				'label'      => esc_html__( 'Link Top Spacing', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-card-link-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'left_card_link_gap',
			array(
				'label'      => esc_html__( 'Link Icon Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-card-link-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'left_card_link_icon_size',
			array(
				'label'      => esc_html__( 'Link Icon Size', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-card-link-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_right_content_style_controls() {
		$this->start_controls_section(
			'section_right_content_styles',
			array(
				'label' => esc_html__( 'Right Content', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'Heading Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#202233',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__right :is(h1, h2, h3)' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .foundation-y-hero__right :is(h1, h2, h3)',
			)
		);

		$this->add_responsive_control(
			'heading_width',
			array(
				'label'      => esc_html__( 'Heading Max Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'ch', 'px', '%', 'vw', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'ch',
					'size' => 10,
				),
				'range'      => array(
					'ch' => array(
						'min' => 4,
						'max' => 20,
					),
					'px' => array(
						'min' => 180,
						'max' => 900,
					),
					'%' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero__right :is(h1, h2, h3)' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'intro_color',
			array(
				'label'     => esc_html__( 'Intro Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#5B5D69',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__intro, {{WRAPPER}} .foundation-y-hero__intro p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'intro_typography',
				'selector' => '{{WRAPPER}} .foundation-y-hero__intro, {{WRAPPER}} .foundation-y-hero__intro p',
			)
		);

		$this->add_responsive_control(
			'intro_width',
			array(
				'label'      => esc_html__( 'Intro Max Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 760,
				),
				'range'      => array(
					'px' => array(
						'min' => 240,
						'max' => 1200,
					),
					'%' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero__intro' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'right_content_padding',
			array(
				'label'      => esc_html__( 'Right Content Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero__right' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'stat_value_color',
			array(
				'label'     => esc_html__( 'Stat Value Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#202233',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__stat strong' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'stat_value_typography',
				'selector' => '{{WRAPPER}} .foundation-y-hero__stat strong',
			)
		);

		$this->add_control(
			'stat_label_color',
			array(
				'label'     => esc_html__( 'Stat Label Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#5F6170',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__stat span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'stat_label_typography',
				'selector' => '{{WRAPPER}} .foundation-y-hero__stat span',
			)
		);

		$this->end_controls_section();
	}

	private function register_control_style_controls() {
		$this->start_controls_section(
			'section_controls_style',
			array(
				'label' => esc_html__( 'Arrows & CTA', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'controls_gap',
			array(
				'label'      => esc_html__( 'Controls Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
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
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-controls-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'      => esc_html__( 'Arrow Size', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 52,
				),
				'range'      => array(
					'px' => array(
						'min' => 32,
						'max' => 90,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-arrow-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_radius',
			array(
				'label'      => esc_html__( 'Arrow Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-arrow-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_icon_size',
			array(
				'label'      => esc_html__( 'Arrow Icon Size', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
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
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-arrow-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'     => esc_html__( 'Arrow Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.7)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__arrow' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'Arrow Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#202233',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_border_color',
			array(
				'label'     => esc_html__( 'Arrow Border Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(32, 34, 51, 0.14)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__arrow' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'arrow_typography',
				'selector' => '{{WRAPPER}} .foundation-y-hero__arrow',
			)
		);

		$this->add_control(
			'cta_background',
			array(
				'label'     => esc_html__( 'CTA Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#DFFF00',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__cta' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cta_color',
			array(
				'label'     => esc_html__( 'CTA Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#11131D',
				'selectors' => array(
					'{{WRAPPER}} .foundation-y-hero__cta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'cta_min_height',
			array(
				'label'      => esc_html__( 'CTA Height', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 52,
				),
				'range'      => array(
					'px' => array(
						'min' => 36,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-cta-min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cta_gap',
			array(
				'label'      => esc_html__( 'CTA Text / Icon Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
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
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-cta-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cta_icon_size',
			array(
				'label'      => esc_html__( 'CTA Icon Size', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
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
					'{{WRAPPER}} .foundation-y-hero' => '--foundation-y-hero-cta-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cta_padding',
			array(
				'label'      => esc_html__( 'CTA Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'default'    => array(
					'top'      => 14,
					'right'    => 28,
					'bottom'   => 14,
					'left'     => 28,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-y-hero__cta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cta_radius',
			array(
				'label'      => esc_html__( 'CTA Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
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
					'{{WRAPPER}} .foundation-y-hero__cta' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cta_typography',
				'selector' => '{{WRAPPER}} .foundation-y-hero__cta',
			)
		);

		$this->end_controls_section();
	}

	private function get_default_cards() {
		return array(
			array(
				'title'       => esc_html__( 'Creative firepower. Strategic focus.', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Our UK team of designers, developers, SEO and paid media specialists brings sharp thinking, speed, and commitment to every project.', 'foundation-elementor-plus' ),
				'link_text'   => '',
				'link'        => array(
					'url' => '',
				),
			),
			array(
				'title'       => esc_html__( 'London-based WordPress agency', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Working worldwide with ambitious brands who need performance, scalability and design excellence.', 'foundation-elementor-plus' ),
				'link_text'   => '',
				'link'        => array(
					'url' => '',
				),
			),
			array(
				'title'       => esc_html__( 'Complex builds. Clean delivery.', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'From high-performance marketing sites to large WordPress platforms and integrations.', 'foundation-elementor-plus' ),
				'link_text'   => '',
				'link'        => array(
					'url' => '',
				),
			),
			array(
				'title'       => esc_html__( 'Engineering-led design', 'foundation-elementor-plus' ),
				'description' => esc_html__( 'Where creative thinking meets technical architecture and real business outcomes.', 'foundation-elementor-plus' ),
				'link_text'   => '',
				'link'        => array(
					'url' => '',
				),
			),
		);
	}

	private function get_default_stats() {
		return array(
			array(
				'value' => '250+',
				'label' => esc_html__( 'Websites Delivered', 'foundation-elementor-plus' ),
			),
			array(
				'value' => '100+',
				'label' => esc_html__( '5-Star Reviews', 'foundation-elementor-plus' ),
			),
			array(
				'value' => '92%',
				'label' => esc_html__( 'Client Retention', 'foundation-elementor-plus' ),
			),
		);
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
}
