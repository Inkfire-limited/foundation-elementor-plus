<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use FoundationElementorPlus\Widgets\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Awards_Recognition_Wall_Widget extends Base_Widget {
	public function get_name() {
		return 'foundation-awards-recognition-wall';
	}

	public function get_title() {
		return esc_html__( 'Awards Wall', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'awards', 'recognition', 'timeline', 'wall', 'grid' );
	}

	public function get_style_depends(): array {
		return $this->get_foundation_style_depends( array( 'foundation-elementor-plus-awards-wall' ) );
	}

	public function get_script_depends(): array {
		return array( 'foundation-elementor-plus-awards-wall' );
	}

	protected function register_controls() {
		$this->register_header_controls();
		$this->register_source_controls();
		$this->register_cards_controls();
		$this->register_layout_style_controls();
		$this->register_header_style_controls();
		$this->register_card_style_controls();
		$this->register_accessibility_controls();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$widget_id = 'foundation-awards-wall-' . $this->get_id();
		$cards     = $this->get_render_cards( $settings );
		$blocks    = $this->group_cards_by_block( $cards );
		$classes   = array( 'foundation-awards-wall' );

		if ( $this->is_dynamic_posts_mode( $settings['content_mode'] ?? 'manual' ) ) {
			$classes[] = 'foundation-awards-wall--posts';
		}

		if ( $this->is_dynamic_posts_mode( $settings['content_mode'] ?? 'manual' ) && 'yes' === ( $settings['posts_compact_incomplete_blocks'] ?? 'yes' ) ) {
			$classes[] = 'foundation-awards-wall--compact-incomplete';
		}

		if ( empty( $cards ) && 'yes' !== ( $settings['show_header'] ?? 'yes' ) ) {
			return;
		}
		?>
		<section <?php echo $this->get_widget_root_attributes( $settings, array( 'id' => $widget_id, 'class' => implode( ' ', $classes ), 'aria-labelledby' => $widget_id . '-title' ) ); ?>>
			<div class="foundation-awards-wall__inner">
				<?php if ( 'yes' === ( $settings['show_header'] ?? 'yes' ) ) : ?>
					<header class="foundation-awards-wall__header">
						<?php if ( ! empty( $settings['eyebrow'] ) ) : ?>
							<span class="foundation-awards-wall__pill"><?php echo esc_html( $settings['eyebrow'] ); ?></span>
						<?php endif; ?>

						<?php if ( ! empty( $settings['title'] ) ) : ?>
							<h2 id="<?php echo esc_attr( $widget_id . '-title' ); ?>" class="foundation-awards-wall__title">
								<?php echo wp_kses_post( nl2br( esc_html( $settings['title'] ) ) ); ?>
							</h2>
						<?php endif; ?>

						<?php if ( ! empty( $settings['description'] ) ) : ?>
							<div class="foundation-awards-wall__description">
								<?php echo wp_kses_post( wpautop( $settings['description'] ) ); ?>
							</div>
						<?php endif; ?>
					</header>
				<?php endif; ?>

				<?php if ( ! empty( $cards ) ) : ?>
					<div class="foundation-awards-wall__blocks">
						<?php foreach ( $blocks as $block_index => $block ) : ?>
							<?php
							if ( empty( $block['items'] ) && empty( $block['full'] ) ) {
								continue;
							}

							$this->render_block( $block, $widget_id, $block_index );
							?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	private function register_header_controls() {
		$this->start_controls_section(
			'section_header',
			array(
				'label' => esc_html__( 'Header', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'show_header',
			array(
				'label'        => esc_html__( 'Show Header', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => esc_html__( 'Eyebrow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Recognition & Impact', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => 'Awards & recognition',
				'label_block' => true,
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => esc_html__( 'Description', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => esc_html__( 'A living wall of receipts - award wins, nominations, marks, and moments that show the work (and the people) behind Inkfire.', 'foundation-elementor-plus' ),
			)
		);

		$this->end_controls_section();
	}

	private function register_source_controls() {
		$dynamic_posts_condition = array(
			'content_mode' => array( 'posts', 'category_posts' ),
		);

		$this->start_controls_section(
			'section_source',
			array(
				'label' => esc_html__( 'Content Source', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'content_mode',
			array(
				'label'   => esc_html__( 'Content Mode', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => array(
					'manual'         => esc_html__( 'Manual cards', 'foundation-elementor-plus' ),
					'posts'          => esc_html__( 'Blog posts', 'foundation-elementor-plus' ),
					'category_posts' => esc_html__( 'Blog posts by category', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'category_posts_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Choose Blog posts by category, then select one or more categories below. Leave the selector empty to fall back to all blog posts.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'content_mode' => 'category_posts',
				),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'     => esc_html__( 'Posts to Show', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 24,
				'step'      => 1,
				'default'   => 8,
				'condition' => $dynamic_posts_condition,
			)
		);

		$this->add_control(
			'posts_categories',
			array(
				'label'       => esc_html__( 'Include Categories', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->get_post_category_options(),
				'description' => esc_html__( 'Select categories for the dynamic post query. Leave empty to include all blog posts.', 'foundation-elementor-plus' ),
				'condition'   => $dynamic_posts_condition,
			)
		);

		$this->add_control(
			'posts_category_ids',
			array(
				'label'       => esc_html__( 'Category IDs', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Advanced fallback. Optional comma-separated post category IDs.', 'foundation-elementor-plus' ),
				'condition'   => $dynamic_posts_condition,
			)
		);

		$this->add_control(
			'posts_exclude_category_ids',
			array(
				'label'       => esc_html__( 'Exclude Category IDs', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Optional comma-separated category IDs to exclude.', 'foundation-elementor-plus' ),
				'condition'   => $dynamic_posts_condition,
			)
		);

		$this->add_control(
			'posts_offset',
			array(
				'label'     => esc_html__( 'Offset', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'step'      => 1,
				'default'   => 0,
				'condition' => $dynamic_posts_condition,
			)
		);

		$this->add_control(
			'posts_orderby',
			array(
				'label'     => esc_html__( 'Order By', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'date'       => esc_html__( 'Date', 'foundation-elementor-plus' ),
					'title'      => esc_html__( 'Title', 'foundation-elementor-plus' ),
					'menu_order' => esc_html__( 'Menu Order', 'foundation-elementor-plus' ),
					'rand'       => esc_html__( 'Random', 'foundation-elementor-plus' ),
				),
				'condition' => $dynamic_posts_condition,
			)
		);

		$this->add_control(
			'posts_order',
			array(
				'label'     => esc_html__( 'Order', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => array(
					'DESC' => esc_html__( 'Descending', 'foundation-elementor-plus' ),
					'ASC'  => esc_html__( 'Ascending', 'foundation-elementor-plus' ),
				),
				'condition' => $dynamic_posts_condition,
			)
		);

		$this->add_control(
			'posts_excerpt_policy',
			array(
				'label'     => esc_html__( 'Excerpt Visibility', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'feature_only',
				'options'   => array(
					'all'          => esc_html__( 'All cards', 'foundation-elementor-plus' ),
					'feature_only' => esc_html__( 'Feature cards only', 'foundation-elementor-plus' ),
					'none'         => esc_html__( 'No excerpts', 'foundation-elementor-plus' ),
				),
				'condition' => $dynamic_posts_condition,
			)
		);

		$this->add_control(
			'posts_excerpt_length',
			array(
				'label'     => esc_html__( 'Small Card Excerpt Length', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 8,
				'max'       => 80,
				'step'      => 1,
				'default'   => 22,
				'condition' => $dynamic_posts_condition,
			)
		);

		$this->add_control(
			'posts_feature_excerpt_length',
			array(
				'label'     => esc_html__( 'Feature Card Excerpt Length', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 12,
				'max'       => 120,
				'step'      => 1,
				'default'   => 34,
				'condition' => $dynamic_posts_condition,
			)
		);

		$this->add_control(
			'posts_compact_incomplete_blocks',
			array(
				'label'        => esc_html__( 'Compact Incomplete Blocks', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Prevents partial final blocks from leaving large editorial gaps.', 'foundation-elementor-plus' ),
				'condition'    => $dynamic_posts_condition,
			)
		);

		$this->add_control(
			'posts_include_a11y_card',
			array(
				'label'        => esc_html__( 'Include [ink_access_meta] Card', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => $dynamic_posts_condition,
			)
		);

		$this->add_control(
			'posts_a11y_slot',
			array(
				'label'     => esc_html__( 'A11y Card Slot', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'b1_full',
				'options'   => $this->get_slot_options(),
				'condition' => array(
					'content_mode'             => array( 'posts', 'category_posts' ),
					'posts_include_a11y_card' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_cards_controls() {
		$this->start_controls_section(
			'section_cards',
			array(
				'label' => esc_html__( 'Cards', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'cards_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Each card is assigned to a fixed editorial slot, just like the portfolio layout system. Duplicate an item, move it to a later block slot, and change the surface preset or card type as the wall grows.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'admin_label',
			array(
				'label'       => esc_html__( 'Admin Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Award Card', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'layout_slot',
			array(
				'label'   => esc_html__( 'Layout Slot', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'b1_feature',
				'options' => $this->get_slot_options(),
			)
		);

		$repeater->add_control(
			'card_type',
			array(
				'label'   => esc_html__( 'Card Type', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => array(
					'feature' => esc_html__( 'Feature Split Card', 'foundation-elementor-plus' ),
					'text'    => esc_html__( 'Text Card', 'foundation-elementor-plus' ),
					'logo'    => esc_html__( 'Logo / Mark Card', 'foundation-elementor-plus' ),
					'image'   => esc_html__( 'Image Card', 'foundation-elementor-plus' ),
					'photo'   => esc_html__( 'Photo Overlay Card', 'foundation-elementor-plus' ),
					'shortcode' => esc_html__( 'Shortcode Card', 'foundation-elementor-plus' ),
				),
			)
		);

		$repeater->add_control(
			'surface_preset',
			array(
				'label'   => esc_html__( 'Surface Preset', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'teal',
				'options' => array(
					'teal'   => esc_html__( 'Teal Glass', 'foundation-elementor-plus' ),
					'navy'   => esc_html__( 'Navy Glass', 'foundation-elementor-plus' ),
					'orange' => esc_html__( 'Orange Glass', 'foundation-elementor-plus' ),
					'dark'   => esc_html__( 'Dark Glass', 'foundation-elementor-plus' ),
					'green'  => esc_html__( 'Green Glass', 'foundation-elementor-plus' ),
					'white'  => esc_html__( 'White Glass', 'foundation-elementor-plus' ),
				),
			)
		);

		$repeater->add_control(
			'eyebrow',
			array(
				'label'       => esc_html__( 'Eyebrow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( '2025', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => esc_html__( 'Recognition Title', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'meta',
			array(
				'label'       => esc_html__( 'Meta / Organisation', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label' => esc_html__( 'Description', 'foundation-elementor-plus' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 4,
			)
		);

		$repeater->add_control(
			'shortcode',
			array(
				'label'       => esc_html__( 'Shortcode', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => '[ink_access_meta]',
				'label_block' => true,
				'condition'   => array(
					'card_type' => 'shortcode',
				),
			)
		);

		$repeater->add_control(
			'tags',
			array(
				'label'       => esc_html__( 'Tags', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'description' => esc_html__( 'One tag per line. Used on feature cards only.', 'foundation-elementor-plus' ),
				'condition'   => array(
					'card_type' => 'feature',
				),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'       => esc_html__( 'Link', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'options'     => array( 'url', 'is_external', 'nofollow' ),
			)
		);

		$repeater->add_control(
			'media_heading',
			array(
				'label'     => esc_html__( 'Media', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'media_mode',
			array(
				'label'   => esc_html__( 'Media Mode', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => array(
					'none'  => esc_html__( 'No Media', 'foundation-elementor-plus' ),
					'image' => esc_html__( 'Image', 'foundation-elementor-plus' ),
					'embed' => esc_html__( 'Embed URL', 'foundation-elementor-plus' ),
				),
			)
		);

		$repeater->add_control(
			'media_image_url',
			array(
				'label'       => esc_html__( 'Image URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => array(
					'media_mode' => array( 'image' ),
				),
			)
		);

		$repeater->add_control(
			'media_image_alt',
			array(
				'label'       => esc_html__( 'Image Alt Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => array(
					'media_mode' => array( 'image' ),
				),
			)
		);

		$repeater->add_control(
			'media_embed_url',
			array(
				'label'       => esc_html__( 'Embed URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => array(
					'media_mode' => array( 'embed' ),
				),
			)
		);

		$repeater->add_control(
			'media_embed_title',
			array(
				'label'       => esc_html__( 'Embed Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => array(
					'media_mode' => array( 'embed' ),
				),
			)
		);

		$this->add_control(
			'cards',
			array(
				'label'       => esc_html__( 'Editorial Cards', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->get_default_cards(),
				'title_field' => '{{{ admin_label }}}',
				'condition'   => array(
					'content_mode' => 'manual',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_layout_style_controls() {
		$this->start_controls_section(
			'section_layout_style',
			array(
				'label' => esc_html__( 'Layout', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'section_padding',
			array(
				'label'      => esc_html__( 'Section Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ),
				'default'    => array(
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-awards-wall' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'Block Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 28,
				),
				'range'      => array(
					'px'  => array(
						'min' => 12,
						'max' => 80,
					),
					'rem' => array(
						'min' => 1,
						'max' => 5,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-awards-wall' => '--foundation-awards-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_spacing',
			array(
				'label'      => esc_html__( 'Header Bottom Space', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 160,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-awards-wall__header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_max_width',
			array(
				'label'      => esc_html__( 'Header Max Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 320,
						'max' => 1400,
					),
					'%' => array(
						'min' => 40,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-awards-wall__header' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'Card Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ),
				'default'    => array(
					'top'      => 30,
					'right'    => 30,
					'bottom'   => 30,
					'left'     => 30,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-awards-wall' => '--foundation-awards-card-padding-top: {{TOP}}{{UNIT}}; --foundation-awards-card-padding-right: {{RIGHT}}{{UNIT}}; --foundation-awards-card-padding-bottom: {{BOTTOM}}{{UNIT}}; --foundation-awards-card-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'Card Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 36,
				),
				'range'      => array(
					'px'  => array(
						'min' => 18,
						'max' => 70,
					),
					'rem' => array(
						'min' => 1,
						'max' => 5,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-awards-wall' => '--foundation-awards-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_min_height',
			array(
				'label'      => esc_html__( 'Card Min Height', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 180,
						'max' => 900,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-awards-wall__card-inner' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'feature_media_min_height',
			array(
				'label'      => esc_html__( 'Feature Media Min Height', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 180,
						'max' => 900,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-awards-wall__feature-image' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_header_style_controls() {
		$this->start_controls_section(
			'section_header_style',
			array(
				'label' => esc_html__( 'Header Text', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'eyebrow_heading',
			array(
				'label' => esc_html__( 'Eyebrow Typography', 'foundation-elementor-plus' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_eyebrow_typography',
				'selector' => '{{WRAPPER}} .foundation-awards-wall__pill',
			)
		);

		$this->add_control(
			'header_eyebrow_color',
			array(
				'label'     => esc_html__( 'Eyebrow Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-awards-wall__pill' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_eyebrow_background',
			array(
				'label'     => esc_html__( 'Eyebrow Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-awards-wall__pill' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_heading',
			array(
				'label'     => esc_html__( 'Header Title Typography', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_title_typography',
				'selector' => '{{WRAPPER}} .foundation-awards-wall__title',
			)
		);

		$this->add_control(
			'header_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-awards-wall__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'description_heading',
			array(
				'label'     => esc_html__( 'Description Typography', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_description_typography',
				'selector' => '{{WRAPPER}} .foundation-awards-wall__description',
			)
		);

		$this->add_control(
			'header_description_color',
			array(
				'label'     => esc_html__( 'Description Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-awards-wall__description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_card_style_controls() {
		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => esc_html__( 'Cards & Media', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_border_color',
			array(
				'label'     => esc_html__( 'Card Border Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-awards-wall__card-inner' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .foundation-awards-wall__card-inner',
			)
		);

		$this->add_responsive_control(
			'feature_media_radius',
			array(
				'label'      => esc_html__( 'Feature Media Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 48,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-awards-wall__feature-image' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'feature_media_fit',
			array(
				'label'   => esc_html__( 'Feature Media Fit', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => array(
					'cover'   => esc_html__( 'Cover', 'foundation-elementor-plus' ),
					'contain' => esc_html__( 'Contain', 'foundation-elementor-plus' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .foundation-awards-wall__feature-image' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_eyebrow_heading',
			array(
				'label' => esc_html__( 'Card Eyebrow Typography', 'foundation-elementor-plus' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_eyebrow_typography',
				'selector' => '{{WRAPPER}} .foundation-awards-wall__eyebrow',
			)
		);

		$this->add_control(
			'card_eyebrow_color',
			array(
				'label'     => esc_html__( 'Eyebrow Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-awards-wall__eyebrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_title_heading',
			array(
				'label'     => esc_html__( 'Card Title Typography', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_title_typography',
				'selector' => '{{WRAPPER}} .foundation-awards-wall__card-title',
			)
		);

		$this->add_control(
			'card_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-awards-wall__card-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_meta_heading',
			array(
				'label'     => esc_html__( 'Card Meta Typography', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_meta_typography',
				'selector' => '{{WRAPPER}} .foundation-awards-wall__meta',
			)
		);

		$this->add_control(
			'card_meta_color',
			array(
				'label'     => esc_html__( 'Meta Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-awards-wall__meta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_copy_heading',
			array(
				'label'     => esc_html__( 'Card Copy Typography', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_copy_typography',
				'selector' => '{{WRAPPER}} .foundation-awards-wall__text',
			)
		);

		$this->add_control(
			'card_copy_color',
			array(
				'label'     => esc_html__( 'Copy Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-awards-wall__text, {{WRAPPER}} .foundation-awards-wall__text p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tag_heading',
			array(
				'label'     => esc_html__( 'Tag Typography', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_tag_typography',
				'selector' => '{{WRAPPER}} .foundation-awards-wall__tag',
			)
		);

		$this->add_control(
			'card_tag_text_color',
			array(
				'label'     => esc_html__( 'Tag Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-awards-wall__tag' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_tag_background',
			array(
				'label'     => esc_html__( 'Tag Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-awards-wall__tag' => 'background: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_accessibility_controls() {
		// Reserved for future a11y-specific controls while keeping older widget
		// registration flows stable for existing templates and CSS generation.
	}

	private function get_render_cards( array $settings ) {
		if ( $this->is_dynamic_posts_mode( $settings['content_mode'] ?? 'manual' ) ) {
			return $this->prepare_post_cards( $settings );
		}

		return $this->prepare_cards( $settings['cards'] ?? array() );
	}

	private function is_dynamic_posts_mode( $mode ) {
		return in_array( (string) $mode, array( 'posts', 'category_posts' ), true );
	}

	private function prepare_post_cards( array $settings ) {
		$slots      = array_keys( $this->get_slot_map() );
		$a11y_slot  = 'yes' === ( $settings['posts_include_a11y_card'] ?? '' ) ? (string) ( $settings['posts_a11y_slot'] ?? 'b1_full' ) : '';
		$post_slots = array_values(
			array_filter(
				$slots,
				static function( $slot ) use ( $a11y_slot ) {
					return $slot !== $a11y_slot;
				}
			)
		);
		$limit      = max( 1, min( 24, absint( $settings['posts_per_page'] ?? 8 ) ) );
		$query_args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'orderby'             => $this->normalize_posts_orderby( $settings['posts_orderby'] ?? 'date' ),
			'order'               => 'ASC' === ( $settings['posts_order'] ?? 'DESC' ) ? 'ASC' : 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		$selected_category_ids = isset( $settings['posts_categories'] ) ? wp_parse_id_list( $settings['posts_categories'] ) : array();
		$category_ids          = ! empty( $selected_category_ids ) ? $selected_category_ids : ( isset( $settings['posts_category_ids'] ) ? wp_parse_id_list( $settings['posts_category_ids'] ) : array() );
		$exclude_category_ids  = isset( $settings['posts_exclude_category_ids'] ) ? wp_parse_id_list( $settings['posts_exclude_category_ids'] ) : array();
		$offset                = absint( $settings['posts_offset'] ?? 0 );

		if ( ! empty( $category_ids ) ) {
			$query_args['category__in'] = $category_ids;
		}

		if ( ! empty( $exclude_category_ids ) ) {
			$query_args['category__not_in'] = $exclude_category_ids;
		}

		if ( $offset > 0 ) {
			$query_args['offset'] = $offset;
		}

		$query     = new \WP_Query( $query_args );
		$raw_cards = array();
		$surfaces  = array( 'teal', 'navy', 'green', 'dark', 'navy' );
		$excerpt_policy         = isset( $settings['posts_excerpt_policy'] ) ? (string) $settings['posts_excerpt_policy'] : 'feature_only';
		$small_excerpt_length   = max( 8, min( 80, absint( $settings['posts_excerpt_length'] ?? 22 ) ) );
		$feature_excerpt_length  = max( 12, min( 120, absint( $settings['posts_feature_excerpt_length'] ?? 34 ) ) );

		foreach ( $query->posts as $index => $post ) {
			if ( ! isset( $post_slots[ $index ] ) ) {
				break;
			}

			$slot_key  = $post_slots[ $index ];
			$is_feature = false !== strpos( $slot_key, '_feature' );
			$image_url = get_the_post_thumbnail_url( $post, 'large' );
			$terms     = get_the_category( $post->ID );
			$term_name = $this->resolve_post_eyebrow_label( $post, $terms );
			$tag_names = array();

			foreach ( array_slice( $terms, 0, 4 ) as $term ) {
				if ( in_array( $term->slug, array( 'featured', 'blog' ), true ) ) {
					continue;
				}

				$tag_names[] = $term->name;
			}

			$show_excerpt = 'none' !== $excerpt_policy && ( 'all' === $excerpt_policy || $is_feature );
			$description  = '';

			if ( $show_excerpt ) {
				$description = wp_trim_words(
					get_the_excerpt( $post ),
					$is_feature ? $feature_excerpt_length : $small_excerpt_length
				);
			}

			$raw_cards[] = array(
				'admin_label'     => get_the_title( $post ),
				'layout_slot'     => $slot_key,
				'card_type'       => $is_feature ? 'feature' : ( $image_url ? 'photo' : 'text' ),
				'surface_preset'  => $surfaces[ $index % count( $surfaces ) ],
				'eyebrow'         => $term_name,
				'title'           => get_the_title( $post ),
				'meta'            => get_the_date( 'j M Y', $post ),
				'description'     => $description,
				'tags'            => implode( "\n", $tag_names ),
				'link'            => array( 'url' => get_permalink( $post ) ),
				'media_mode'      => $image_url ? 'image' : 'none',
				'media_image_url' => $image_url ? $image_url : '',
				'media_image_alt' => get_the_title( $post ),
			);
		}

		if ( 'yes' === ( $settings['posts_include_a11y_card'] ?? '' ) && '' !== $a11y_slot ) {
			$raw_cards[] = array(
				'admin_label'    => esc_html__( 'Accessibility Tools', 'foundation-elementor-plus' ),
				'layout_slot'    => $a11y_slot,
				'card_type'      => 'shortcode',
				'surface_preset' => 'dark',
				'eyebrow'        => esc_html__( 'Accessibility', 'foundation-elementor-plus' ),
				'title'          => esc_html__( 'Reading & accessibility tools', 'foundation-elementor-plus' ),
				'meta'           => esc_html__( 'Built by Inkfire', 'foundation-elementor-plus' ),
				'shortcode'      => '[ink_access_meta]',
				'media_mode'     => 'none',
			);
		}

		wp_reset_postdata();

		return $this->prepare_cards( $raw_cards );
	}

	private function normalize_posts_orderby( $value ) {
		$allowed = array( 'date', 'title', 'menu_order', 'rand' );

		return in_array( $value, $allowed, true ) ? $value : 'date';
	}

	private function resolve_post_eyebrow_label( \WP_Post $post, array $terms ) {
		$term_signals = array();

		foreach ( $terms as $term ) {
			$term_signals[] = strtolower( trim( $term->slug . ' ' . $term->name ) );
		}

		$title_signal = strtolower( get_the_title( $post ) );
		$signals      = array_merge( $term_signals, array( $title_signal ) );
		$label_map    = array(
			esc_html__( 'Case Study', 'foundation-elementor-plus' ) => array( 'case-stud', 'client-stor', 'client stor' ),
			esc_html__( 'Recognition', 'foundation-elementor-plus' ) => array( 'award', 'recognition', 'press', 'media', 'winner' ),
			esc_html__( 'Accessibility', 'foundation-elementor-plus' ) => array( 'accessib', 'a11y', 'inclusive design' ),
			esc_html__( 'Guide', 'foundation-elementor-plus' ) => array( 'guide', 'how to', 'how-to', 'tool', 'resource', 'tips', 'seo', 'marketing', 'mentoring', 'blog' ),
			esc_html__( "What's New", 'foundation-elementor-plus' ) => array( 'what', 'news', 'update', 'journal' ),
		);

		foreach ( $label_map as $label => $needles ) {
			foreach ( $signals as $signal ) {
				foreach ( $needles as $needle ) {
					if ( false !== strpos( $signal, $needle ) ) {
						return $label;
					}
				}
			}
		}

		if ( ! empty( $terms ) ) {
			return $terms[0]->name;
		}

		return esc_html__( 'Journal', 'foundation-elementor-plus' );
	}

	private function get_post_category_options() {
		$options = array();
		$terms   = get_categories(
			array(
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( is_wp_error( $terms ) ) {
			return $options;
		}

		foreach ( $terms as $term ) {
			$options[ (string) $term->term_id ] = sprintf( '%s (%d)', $term->name, $term->count );
		}

		return $options;
	}

	private function prepare_cards( array $raw_cards ) {
		$slot_map = $this->get_slot_map();
		$cards    = array();

		foreach ( $raw_cards as $index => $card ) {
			$slot_key = isset( $card['layout_slot'] ) ? (string) $card['layout_slot'] : '';

			if ( ! isset( $slot_map[ $slot_key ] ) ) {
				continue;
			}

			$slot = $slot_map[ $slot_key ];

			$cards[] = array(
				'index'       => $index,
				'admin_label' => ! empty( $card['admin_label'] ) ? $card['admin_label'] : $slot['label'],
				'slot_key'    => $slot_key,
				'block'       => $slot['block'],
				'role'        => $slot['role'],
				'orientation' => $slot['orientation'],
				'order'       => $slot['order'],
				'type'        => $this->normalize_card_type( $card['card_type'] ?? 'text' ),
				'surface'     => $this->normalize_surface_preset( $card['surface_preset'] ?? 'teal' ),
				'eyebrow'     => isset( $card['eyebrow'] ) ? (string) $card['eyebrow'] : '',
				'title'       => isset( $card['title'] ) ? (string) $card['title'] : '',
				'meta'        => isset( $card['meta'] ) ? (string) $card['meta'] : '',
				'description' => isset( $card['description'] ) ? (string) $card['description'] : '',
				'shortcode'   => isset( $card['shortcode'] ) ? (string) $card['shortcode'] : '',
				'tags'        => $this->parse_tags( $card['tags'] ?? '' ),
				'link'        => $card['link'] ?? array(),
				'media_mode'  => $this->normalize_media_mode( $card['media_mode'] ?? 'image' ),
				'image_url'   => isset( $card['media_image_url'] ) ? (string) $card['media_image_url'] : '',
				'image_alt'   => isset( $card['media_image_alt'] ) ? (string) $card['media_image_alt'] : '',
				'embed_url'   => isset( $card['media_embed_url'] ) ? (string) $card['media_embed_url'] : '',
				'embed_title' => isset( $card['media_embed_title'] ) ? (string) $card['media_embed_title'] : '',
			);
		}

		usort(
			$cards,
			static function( $left, $right ) {
				if ( $left['block'] === $right['block'] ) {
					if ( $left['order'] === $right['order'] ) {
						return $left['index'] <=> $right['index'];
					}

					return $left['order'] <=> $right['order'];
				}

				return $left['block'] <=> $right['block'];
			}
		);

		return $cards;
	}

	private function group_cards_by_block( array $cards ) {
		$blocks = array();

		for ( $index = 1; $index <= 4; $index++ ) {
			$blocks[ $index ] = array(
				'index'       => $index,
				'orientation' => 0 === $index % 2 ? 'right' : 'left',
				'items'       => array(),
				'full'        => array(),
				'overflow'    => array(),
			);
		}

		foreach ( $cards as $card ) {
			$block_index = $card['block'];

			if ( 'full' === $card['role'] ) {
				$blocks[ $block_index ]['full'][] = $card;
				continue;
			}

			if ( isset( $blocks[ $block_index ]['items'][ $card['role'] ] ) ) {
				$blocks[ $block_index ]['overflow'][] = $card;
				continue;
			}

			$blocks[ $block_index ]['items'][ $card['role'] ] = $card;
		}

		return $blocks;
	}

	private function render_block( array $block, $widget_id, $block_index ) {
		$cards        = $block['items'];
		$overflow     = $block['overflow'];
		$full_cards   = $block['full'];
		$orientation  = $block['orientation'];
		$has_feature  = isset( $cards['feature'] );
		$has_any_rows = isset( $cards['row_1'] ) || isset( $cards['row_2'] ) || isset( $cards['row_3'] );
		$has_rows     = isset( $cards['row_1'] ) && isset( $cards['row_2'] ) && isset( $cards['row_3'] );
		$partial_rows = array();
		$has_stacks   = isset( $cards['stack_top'] ) && isset( $cards['stack_bottom'] );
		$is_fallback  = ! $has_feature || ! $has_stacks;
		$block_class  = array(
			'foundation-awards-wall__block',
			'foundation-awards-wall__block--feature-' . $orientation,
			$has_rows ? 'foundation-awards-wall__block--with-rows' : 'foundation-awards-wall__block--without-rows',
		);

		if ( $has_any_rows && ! $has_rows ) {
			$block_class[] = 'foundation-awards-wall__block--partial-rows';
		}

		if ( $is_fallback ) {
			$block_class[] = 'foundation-awards-wall__block--fallback';
		}

		$ordered_roles = array( 'feature', 'stack_top', 'stack_bottom', 'row_1', 'row_2', 'row_3' );

		if ( 'right' === $orientation ) {
			$ordered_roles = array( 'stack_top', 'stack_bottom', 'feature', 'row_1', 'row_2', 'row_3' );
		}

		if ( ! $has_rows ) {
			foreach ( array( 'row_1', 'row_2', 'row_3' ) as $row_role ) {
				if ( isset( $cards[ $row_role ] ) ) {
					$partial_rows[] = $cards[ $row_role ];
				}
			}

			$ordered_roles = array_values(
				array_filter(
					$ordered_roles,
					static function( $role ) {
						return 0 !== strpos( $role, 'row_' );
					}
				)
			);
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $block_class ) ); ?>">
			<div class="foundation-awards-wall__block-grid" role="list" aria-label="<?php echo esc_attr( sprintf( 'Recognition block %d', $block_index ) ); ?>">
				<?php foreach ( $ordered_roles as $role ) : ?>
					<?php
					if ( ! isset( $cards[ $role ] ) ) {
						continue;
					}

					$this->render_card( $cards[ $role ], $widget_id, $block_index );
					?>
				<?php endforeach; ?>

				<?php if ( $is_fallback && ! empty( $overflow ) ) : ?>
					<?php foreach ( $overflow as $extra_card ) : ?>
						<?php $this->render_card( $extra_card, $widget_id, $block_index ); ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $full_cards ) || ! empty( $partial_rows ) || ( ! $is_fallback && ! empty( $overflow ) ) ) : ?>
				<div class="foundation-awards-wall__full-stack" role="list" aria-label="<?php echo esc_attr( sprintf( 'Extended recognition cards %d', $block_index ) ); ?>">
					<?php foreach ( $full_cards as $full_card ) : ?>
						<?php $this->render_card( $full_card, $widget_id, $block_index ); ?>
					<?php endforeach; ?>

					<?php foreach ( $partial_rows as $partial_row_card ) : ?>
						<?php $this->render_card( $partial_row_card, $widget_id, $block_index, true ); ?>
					<?php endforeach; ?>

					<?php if ( ! $is_fallback ) : ?>
						<?php foreach ( $overflow as $extra_card ) : ?>
							<?php $this->render_card( $extra_card, $widget_id, $block_index, true ); ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_card( array $card, $widget_id, $block_index, $force_full = false ) {
		$role      = $force_full ? 'full' : $card['role'];
		$card_id   = $widget_id . '-b' . $block_index . '-' . sanitize_title( $card['slot_key'] . '-' . $card['admin_label'] );
		$classes   = array(
			'foundation-awards-wall__card',
			'foundation-awards-wall__card--role-' . str_replace( '_', '-', $role ),
			'foundation-awards-wall__card--type-' . $card['type'],
			'foundation-awards-wall__card--surface-' . $card['surface'],
			'foundation-awards-wall__card--size-' . $this->get_card_size_variant( $card, $force_full ),
		);
		$tag       = ! empty( $card['link']['url'] ) ? 'a' : 'div';
		$link_attr = $this->build_link_attributes( $card['link'] ?? array() );

		if ( 'feature' === $card['type'] ) {
			$classes[] = 'foundation-awards-wall__card--feature';
		}

		if ( 'logo' === $card['type'] ) {
			$classes[] = 'foundation-awards-wall__card--logo';
		}

		if ( 'image' === $card['type'] ) {
			$classes[] = 'foundation-awards-wall__card--image';
		}

		if ( 'photo' === $card['type'] ) {
			$classes[] = 'foundation-awards-wall__card--photo';
		}

		if ( 'shortcode' === $card['type'] ) {
			$classes[] = 'foundation-awards-wall__card--shortcode';
		}
		?>
		<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-foundation-awards-hover role="listitem" aria-labelledby="<?php echo esc_attr( $card_id ); ?>">
			<?php if ( 'feature' === $card['type'] ) : ?>
				<div class="foundation-awards-wall__card-inner foundation-awards-wall__card-inner--split">
					<div class="foundation-awards-wall__copy">
						<?php $this->render_text_content( $card, $card_id ); ?>
						<?php $this->render_tags( $card['tags'] ); ?>
					</div>
					<div class="foundation-awards-wall__media-pane">
						<?php $this->render_media_content( $card, $tag, $link_attr, true ); ?>
					</div>
				</div>
			<?php elseif ( 'logo' === $card['type'] ) : ?>
				<<?php echo $tag; ?> class="foundation-awards-wall__card-inner foundation-awards-wall__media-link"<?php echo $link_attr; ?>>
					<div class="foundation-awards-wall__top">
						<?php $this->render_text_content( $card, $card_id ); ?>
					</div>
					<div class="foundation-awards-wall__logo-pane">
						<?php if ( ! empty( $card['image_url'] ) ) : ?>
							<img src="<?php echo esc_url( $card['image_url'] ); ?>" alt="<?php echo esc_attr( $this->get_card_image_alt( $card ) ); ?>" loading="lazy">
						<?php endif; ?>
					</div>
				</<?php echo $tag; ?>>
			<?php elseif ( 'shortcode' === $card['type'] ) : ?>
				<div class="foundation-awards-wall__card-inner foundation-awards-wall__shortcode-card">
					<div class="foundation-awards-wall__top">
						<?php $this->render_text_content( $card, $card_id ); ?>
					</div>
					<?php if ( ! empty( $card['shortcode'] ) ) : ?>
						<div class="foundation-awards-wall__shortcode-content">
							<?php echo do_shortcode( wp_kses_post( $card['shortcode'] ) ); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php elseif ( 'image' === $card['type'] ) : ?>
				<<?php echo $tag; ?> class="foundation-awards-wall__card-inner foundation-awards-wall__image-link"<?php echo $link_attr; ?>>
					<?php if ( ! empty( $card['image_url'] ) ) : ?>
						<img class="foundation-awards-wall__cover" src="<?php echo esc_url( $card['image_url'] ); ?>" alt="<?php echo esc_attr( $this->get_card_image_alt( $card ) ); ?>" loading="lazy">
					<?php endif; ?>
					<span id="<?php echo esc_attr( $card_id ); ?>" class="screen-reader-text"><?php echo esc_html( $card['title'] ? $card['title'] : $this->get_card_image_alt( $card ) ); ?></span>
				</<?php echo $tag; ?>>
			<?php elseif ( 'photo' === $card['type'] ) : ?>
				<<?php echo $tag; ?> class="foundation-awards-wall__card-inner foundation-awards-wall__photo-link"<?php echo $link_attr; ?>>
					<?php if ( ! empty( $card['image_url'] ) ) : ?>
						<img class="foundation-awards-wall__cover" src="<?php echo esc_url( $card['image_url'] ); ?>" alt="<?php echo esc_attr( $this->get_card_image_alt( $card ) ); ?>" loading="lazy">
					<?php endif; ?>
					<div class="foundation-awards-wall__scrim">
						<?php $this->render_text_content( $card, $card_id ); ?>
					</div>
				</<?php echo $tag; ?>>
			<?php else : ?>
				<<?php echo $tag; ?> class="foundation-awards-wall__card-inner foundation-awards-wall__text-link"<?php echo $link_attr; ?>>
					<?php $this->render_text_content( $card, $card_id ); ?>
				</<?php echo $tag; ?>>
			<?php endif; ?>
		</article>
		<?php
	}

	private function get_card_size_variant( array $card, $force_full = false ) {
		$role = $force_full ? 'full' : $card['role'];

		if ( 'shortcode' === $card['type'] ) {
			return 'utility';
		}

		if ( 'feature' === $card['type'] || 'feature' === $role ) {
			return 'feature';
		}

		if ( in_array( $role, array( 'stack_top', 'stack_bottom', 'row_1', 'row_2', 'row_3' ), true ) ) {
			return 'compact';
		}

		return 'standard';
	}

	private function render_text_content( array $card, $title_id ) {
		if ( '' !== $card['eyebrow'] ) {
			echo '<p class="foundation-awards-wall__eyebrow">' . esc_html( $card['eyebrow'] ) . '</p>';
		}

		if ( '' !== $card['title'] ) {
			echo '<h3 id="' . esc_attr( $title_id ) . '" class="foundation-awards-wall__card-title">' . wp_kses_post( nl2br( esc_html( $card['title'] ) ) ) . '</h3>';
		}

		if ( '' !== $card['meta'] ) {
			echo '<p class="foundation-awards-wall__meta">' . esc_html( $card['meta'] ) . '</p>';
		}

		if ( '' !== $card['description'] ) {
			echo '<div class="foundation-awards-wall__text">' . wp_kses_post( wpautop( $card['description'] ) ) . '</div>';
		}
	}

	private function render_tags( array $tags ) {
		if ( empty( $tags ) ) {
			return;
		}

		echo '<div class="foundation-awards-wall__tags" aria-label="' . esc_attr__( 'Key themes', 'foundation-elementor-plus' ) . '">';
		foreach ( $tags as $tag ) {
			echo '<span class="foundation-awards-wall__tag">' . esc_html( $tag ) . '</span>';
		}
		echo '</div>';
	}

	private function render_media_content( array $card, $tag, $link_attr, $feature_media = false ) {
		if ( 'embed' === $card['media_mode'] && ! empty( $card['embed_url'] ) ) {
			?>
			<div class="foundation-awards-wall__embed" aria-label="<?php echo esc_attr( $card['embed_title'] ? $card['embed_title'] : $card['title'] ); ?>">
				<iframe
					src="<?php echo esc_url( $card['embed_url'] ); ?>"
					title="<?php echo esc_attr( $card['embed_title'] ? $card['embed_title'] : $card['title'] ); ?>"
					loading="lazy"
					referrerpolicy="strict-origin-when-cross-origin"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
					allowfullscreen></iframe>
			</div>
			<?php
			return;
		}

		if ( 'image' === $card['media_mode'] && ! empty( $card['image_url'] ) ) {
			$classes = $feature_media ? 'foundation-awards-wall__feature-image' : 'foundation-awards-wall__cover';
			?>
			<<?php echo $tag; ?> class="foundation-awards-wall__feature-media-link"<?php echo $link_attr; ?>>
				<img class="<?php echo esc_attr( $classes ); ?>" src="<?php echo esc_url( $card['image_url'] ); ?>" alt="<?php echo esc_attr( $this->get_card_image_alt( $card ) ); ?>" loading="lazy">
			</<?php echo $tag; ?>>
			<?php
		}
	}

	private function build_link_attributes( array $link ) {
		if ( empty( $link['url'] ) ) {
			return '';
		}

		$attributes = ' href="' . esc_url( $link['url'] ) . '"';

		if ( ! empty( $link['is_external'] ) ) {
			$attributes .= ' target="_blank"';
		}

		$rel = array();

		if ( ! empty( $link['nofollow'] ) ) {
			$rel[] = 'nofollow';
		}

		if ( ! empty( $link['is_external'] ) ) {
			$rel[] = 'noopener';
		}

		if ( ! empty( $rel ) ) {
			$attributes .= ' rel="' . esc_attr( implode( ' ', array_unique( $rel ) ) ) . '"';
		}

		return $attributes;
	}

	private function get_card_image_alt( array $card ) {
		if ( '' !== $card['image_alt'] ) {
			return $card['image_alt'];
		}

		if ( '' !== $card['title'] ) {
			return $card['title'];
		}

		return esc_html__( 'Recognition media', 'foundation-elementor-plus' );
	}

	private function parse_tags( $value ) {
		return array_values(
			array_filter(
				array_map(
					'trim',
					preg_split( '/\r\n|\r|\n/', (string) $value )
				)
			)
		);
	}

	private function normalize_card_type( $value ) {
		$allowed = array( 'feature', 'text', 'logo', 'image', 'photo', 'shortcode' );

		return in_array( $value, $allowed, true ) ? $value : 'text';
	}

	private function normalize_surface_preset( $value ) {
		$allowed = array( 'teal', 'navy', 'orange', 'dark', 'green', 'white' );

		return in_array( $value, $allowed, true ) ? $value : 'teal';
	}

	private function normalize_media_mode( $value ) {
		$allowed = array( 'none', 'image', 'embed' );

		return in_array( $value, $allowed, true ) ? $value : 'none';
	}

	private function get_slot_options() {
		$options = array();

		foreach ( $this->get_slot_map() as $key => $slot ) {
			$options[ $key ] = $slot['label'];
		}

		return $options;
	}

	private function get_slot_map() {
		$map = array();

		for ( $block = 1; $block <= 4; $block++ ) {
			$orientation = 0 === $block % 2 ? 'right' : 'left';
			$prefix      = 'Block ' . $block . ' ';

			$map[ 'b' . $block . '_feature' ] = array(
				'label'       => $prefix . ( 'left' === $orientation ? 'Feature Card' : 'Feature Card' ),
				'block'       => $block,
				'role'        => 'feature',
				'order'       => 10,
				'orientation' => $orientation,
			);
			$map[ 'b' . $block . '_stack_top' ] = array(
				'label'       => $prefix . 'Stack Card Top',
				'block'       => $block,
				'role'        => 'stack_top',
				'order'       => 20,
				'orientation' => $orientation,
			);
			$map[ 'b' . $block . '_stack_bottom' ] = array(
				'label'       => $prefix . 'Stack Card Bottom',
				'block'       => $block,
				'role'        => 'stack_bottom',
				'order'       => 30,
				'orientation' => $orientation,
			);
			$map[ 'b' . $block . '_row_1' ] = array(
				'label'       => $prefix . 'Row Card 1',
				'block'       => $block,
				'role'        => 'row_1',
				'order'       => 40,
				'orientation' => $orientation,
			);
			$map[ 'b' . $block . '_row_2' ] = array(
				'label'       => $prefix . 'Row Card 2',
				'block'       => $block,
				'role'        => 'row_2',
				'order'       => 50,
				'orientation' => $orientation,
			);
			$map[ 'b' . $block . '_row_3' ] = array(
				'label'       => $prefix . 'Row Card 3',
				'block'       => $block,
				'role'        => 'row_3',
				'order'       => 60,
				'orientation' => $orientation,
			);
			$map[ 'b' . $block . '_full' ] = array(
				'label'       => $prefix . 'Full Width Card',
				'block'       => $block,
				'role'        => 'full',
				'order'       => 70,
				'orientation' => $orientation,
			);
		}

		return $map;
	}

	private function get_default_cards() {
		return array(
			array(
				'admin_label'       => 'Feature Winner',
				'layout_slot'       => 'b1_feature',
				'card_type'         => 'feature',
				'surface_preset'    => 'orange',
				'eyebrow'           => '2025 · Winner',
				'title'             => 'Inclusive Workplace',
				'meta'              => 'Disability Smart Awards',
				'description'       => 'A proud moment for Inkfire and the team: recognised by the Disability Smart Awards 2025 for creating a workplace shaped by access, trust, and lived experience.',
				'tags'              => "Inclusive workplace\nAccessibility\nLived experience",
				'media_mode'        => 'embed',
				'media_embed_url'   => 'https://www.youtube-nocookie.com/embed/kjyt9hCBE0Y',
				'media_embed_title' => 'Inclusive Workplace Winner Announcement',
			),
			array(
				'admin_label'     => 'Scope Mark',
				'layout_slot'     => 'b1_stack_top',
				'card_type'       => 'logo',
				'surface_preset'  => 'navy',
				'eyebrow'         => 'Scope Awards · 2025',
				'title'           => 'Winner mark',
				'meta'            => 'Inclusive Workplace category',
				'description'     => 'The official Scope Awards winner mark recognising Inkfire in the Inclusive Workplace category.',
				'link'            => array( 'url' => 'https://beta.mediumblue-seal-942128.hostingersite.com/wp-content/uploads/2026/02/WINNER-02.png' ),
				'media_mode'      => 'image',
				'media_image_url' => 'https://beta.mediumblue-seal-942128.hostingersite.com/wp-content/uploads/2026/02/WINNER-02.png',
				'media_image_alt' => 'Scope Awards 2025 - Inclusive Workplace award mark',
			),
			array(
				'admin_label'     => 'Nomination Graphic',
				'layout_slot'     => 'b1_stack_bottom',
				'card_type'       => 'image',
				'surface_preset'  => 'navy',
				'link'            => array( 'url' => 'https://beta.mediumblue-seal-942128.hostingersite.com/wp-content/uploads/2026/02/Screenshot-2026-02-28-at-02.57.54.png' ),
				'media_mode'      => 'image',
				'media_image_url' => 'https://beta.mediumblue-seal-942128.hostingersite.com/wp-content/uploads/2026/02/Screenshot-2026-02-28-at-02.57.54.png',
				'media_image_alt' => 'Scope Awards 2025 nomination graphic: Inkfire nominated for Inclusive Workplace',
				'title'           => 'Awards campaign artwork',
			),
			array(
				'admin_label'    => 'Recognition Row One',
				'layout_slot'    => 'b1_row_1',
				'card_type'      => 'text',
				'surface_preset' => 'teal',
				'eyebrow'        => '2025',
				'title'          => 'Accessibility-led culture',
				'meta'           => 'People-first workplace',
				'description'    => 'Recognition for building a working culture designed around accessibility, flexibility, and lived experience.',
			),
			array(
				'admin_label'    => 'Recognition Row Two',
				'layout_slot'    => 'b1_row_2',
				'card_type'      => 'text',
				'surface_preset' => 'teal',
				'eyebrow'        => '2025',
				'title'          => 'Disability Confident Award',
				'meta'           => 'Business Disability Forum',
				'description'    => 'Awarded for leadership in disability confidence, championing inclusive hiring and accessible workplaces.',
			),
			array(
				'admin_label'    => 'Recognition Row Three',
				'layout_slot'    => 'b1_row_3',
				'card_type'      => 'text',
				'surface_preset' => 'teal',
				'eyebrow'        => '2025',
				'title'          => 'Barrier-free by design',
				'meta'           => 'Scope Awards recognition',
				'description'    => 'Recognised for creating ways of working that remove friction and help disabled people thrive.',
			),
			array(
				'admin_label'     => 'Winner Badge',
				'layout_slot'     => 'b2_stack_top',
				'card_type'       => 'logo',
				'surface_preset'  => 'navy',
				'eyebrow'         => 'Winner',
				'title'           => 'Disability Smart Awards',
				'meta'            => 'Winners · 2025',
				'link'            => array( 'url' => 'https://beta.mediumblue-seal-942128.hostingersite.com/wp-content/uploads/2026/02/WINNER-01.png' ),
				'media_mode'      => 'image',
				'media_image_url' => 'https://beta.mediumblue-seal-942128.hostingersite.com/wp-content/uploads/2026/02/WINNER-01.png',
				'media_image_alt' => 'Disability Smart Awards 2025 winner badge',
			),
			array(
				'admin_label'     => 'Scope Team Photo',
				'layout_slot'     => 'b2_stack_bottom',
				'card_type'       => 'photo',
				'surface_preset'  => 'dark',
				'eyebrow'         => 'The team',
				'title'           => 'Awards night',
				'description'     => 'A moment to celebrate the people behind the work and the values that shape how Inkfire runs every day.',
				'link'            => array( 'url' => 'https://mediumblue-seal-942128.hostingersite.com/wp-content/uploads/2026/09/ChatGPT-Image-Sep-11-2026-02_21_26-AM.png' ),
				'media_mode'      => 'image',
				'media_image_url' => 'https://mediumblue-seal-942128.hostingersite.com/wp-content/uploads/2026/09/ChatGPT-Image-Sep-11-2026-02_21_26-AM.png',
				'media_image_alt' => 'Inkfire team at the Scope Awards 2025',
			),
			array(
				'admin_label'       => 'Lilac Review Story',
				'layout_slot'       => 'b2_feature',
				'card_type'         => 'feature',
				'surface_preset'    => 'green',
				'eyebrow'           => 'Recognition',
				'title'             => 'Lilac Review · House of Lords',
				'meta'              => 'Final report event',
				'description'       => 'Our Co-Founder Imali had a wonderful time this week visiting the House of Lords, to be part of the final report for the Lilac Review.',
				'link'              => array( 'url' => 'https://youtu.be/hb9NJ_v_DpA', 'is_external' => 'on' ),
				'media_mode'        => 'embed',
				'media_embed_url'   => 'https://www.youtube-nocookie.com/embed/hb9NJ_v_DpA',
				'media_embed_title' => 'Lilac Review House of Lords',
			),
			array(
				'admin_label'     => 'Innovate Access',
				'layout_slot'     => 'b2_full',
				'card_type'       => 'photo',
				'surface_preset'  => 'white',
				'eyebrow'         => 'Community',
				'title'           => 'Innovate Access',
				'description'     => 'Our Co-Founders at Innovate Access - discussing AI with disabled entrepreneurs and the Lilac Centre community.',
				'link'            => array( 'url' => 'https://beta.mediumblue-seal-942128.hostingersite.com/wp-content/uploads/2026/02/Screenshot-2026-02-28-at-03.43.52.png' ),
				'media_mode'      => 'image',
				'media_image_url' => 'https://beta.mediumblue-seal-942128.hostingersite.com/wp-content/uploads/2026/02/Screenshot-2026-02-28-at-03.43.52.png',
				'media_image_alt' => 'Cameron and Imali at Innovate Access',
			),
		);
	}
}
