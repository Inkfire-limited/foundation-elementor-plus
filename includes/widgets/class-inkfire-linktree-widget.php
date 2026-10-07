<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Inkfire_Linktree_Widget extends Widget_Base {
	public function get_name() {
		return 'foundation-inkfire-linktree';
	}

	public function get_title() {
		return esc_html__( 'Inkfire Linktree', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-share';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'inkfire', 'linktree', 'quicklinks', 'social', 'newsletter' );
	}

	protected function register_controls() {
		$this->register_brand_controls();
		$this->register_primary_link_controls();
		$this->register_social_controls();
		$this->register_section_copy_controls();
		$this->register_contact_controls();
		$this->register_layout_manager_controls();
		$this->register_palette_controls();
		$this->register_surface_controls();
		$this->register_layout_style_controls();
		$this->register_typography_controls();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$atts = array(
			'logo_url'              => $this->get_media_url( $settings, 'logo_url', $this->get_default_logo_url() ),
			'tagline'               => $this->normalize_text( $settings['tagline'] ?? '' ),
			'title'                 => $this->normalize_text( $settings['title'] ?? '' ),
			'bio'                   => $this->normalize_textarea( $settings['bio'] ?? '' ),
			'home_label'            => $this->normalize_text( $settings['home_label'] ?? '' ),
			'home_meta'             => $this->normalize_text( $settings['home_meta'] ?? '' ),
			'home_url'              => $this->normalize_text( $settings['home_url'] ?? '' ),
			'blog_label'            => $this->normalize_text( $settings['blog_label'] ?? '' ),
			'blog_meta'             => $this->normalize_text( $settings['blog_meta'] ?? '' ),
			'blog_url'              => $this->normalize_text( $settings['blog_url'] ?? '' ),
			'blog_category_slug'    => $this->normalize_text( $settings['blog_category_slug'] ?? '' ),
			'review_label'          => $this->normalize_text( $settings['review_label'] ?? '' ),
			'review_meta'           => $this->normalize_text( $settings['review_meta'] ?? '' ),
			'review_url'            => $this->normalize_text( $settings['review_url'] ?? '' ),
			'instagram_label'       => $this->normalize_text( $settings['instagram_label'] ?? '' ),
			'instagram_url'         => $this->normalize_text( $settings['instagram_url'] ?? '' ),
			'linkedin_label'        => $this->normalize_text( $settings['linkedin_label'] ?? '' ),
			'linkedin_url'          => $this->normalize_text( $settings['linkedin_url'] ?? '' ),
			'facebook_label'        => $this->normalize_text( $settings['facebook_label'] ?? '' ),
			'facebook_url'          => $this->normalize_text( $settings['facebook_url'] ?? '' ),
			'x_label'               => $this->normalize_text( $settings['x_label'] ?? '' ),
			'x_url'                 => $this->normalize_text( $settings['x_url'] ?? '' ),
			'tiktok_label'          => $this->normalize_text( $settings['tiktok_label'] ?? '' ),
			'tiktok_url'            => $this->normalize_text( $settings['tiktok_url'] ?? '' ),
			'youtube_label'         => $this->normalize_text( $settings['youtube_label'] ?? '' ),
			'youtube_url'           => $this->normalize_text( $settings['youtube_url'] ?? '' ),
			'youtube_id'            => $this->normalize_text( $settings['youtube_id'] ?? '' ),
			'video_eyebrow'         => $this->normalize_text( $settings['video_eyebrow'] ?? '' ),
			'newsletter_eyebrow'    => $this->normalize_text( $settings['newsletter_eyebrow'] ?? '' ),
			'newsletter_title'      => $this->normalize_text( $settings['newsletter_title'] ?? '' ),
			'newsletter_copy'       => $this->normalize_textarea( $settings['newsletter_copy'] ?? '' ),
			'newsletter_note'       => $this->normalize_text( $settings['newsletter_note'] ?? '' ),
			'newsletter_form_id'    => $this->normalize_text( $settings['newsletter_form_id'] ?? '' ),
			'newsletter_default_state' => $this->normalize_text( $settings['newsletter_default_state'] ?? '' ),
			'feed_eyebrow'          => $this->normalize_text( $settings['feed_eyebrow'] ?? '' ),
			'feed_title'            => $this->normalize_text( $settings['feed_title'] ?? '' ),
			'feed_archive_label'    => $this->normalize_text( $settings['feed_archive_label'] ?? '' ),
			'trust_eyebrow'         => $this->normalize_text( $settings['trust_eyebrow'] ?? '' ),
			'trust_title'           => $this->normalize_text( $settings['trust_title'] ?? '' ),
			'contact_eyebrow'       => $this->normalize_text( $settings['contact_eyebrow'] ?? '' ),
			'contact_title'         => $this->normalize_text( $settings['contact_title'] ?? '' ),
			'contact_phone_label'   => $this->normalize_text( $settings['contact_phone_label'] ?? '' ),
			'contact_phone'         => $this->normalize_text( $settings['contact_phone'] ?? '' ),
			'contact_email_label'   => $this->normalize_text( $settings['contact_email_label'] ?? '' ),
			'contact_email'         => $this->normalize_text( $settings['contact_email'] ?? '' ),
			'contact_hours_label'   => $this->normalize_text( $settings['contact_hours_label'] ?? '' ),
			'contact_hours'         => $this->normalize_text( $settings['contact_hours'] ?? '' ),
			'contact_address_label' => $this->normalize_text( $settings['contact_address_label'] ?? '' ),
			'contact_address'       => $this->normalize_textarea( $settings['contact_address'] ?? '' ),
			'company_number_label'  => $this->normalize_text( $settings['company_number_label'] ?? '' ),
			'company_number'        => $this->normalize_text( $settings['company_number'] ?? '' ),
			'vat_number_label'      => $this->normalize_text( $settings['vat_number_label'] ?? '' ),
			'vat_number'            => $this->normalize_text( $settings['vat_number'] ?? '' ),
			'layout_sections'       => $this->sanitize_layout_sections( $settings['layout_sections'] ?? array() ),
			'extra_links'           => $this->sanitize_extra_links( $settings['extra_links'] ?? array() ),
		);

		if ( function_exists( 'amh_social_quicklinks_shortcode' ) ) {
			echo amh_social_quicklinks_shortcode( $atts, null, 'inkfire_linktree' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		echo do_shortcode( '[inkfire_linktree]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function register_brand_controls() {
		$this->start_controls_section(
			'section_brand',
			array(
				'label' => esc_html__( 'Brand Copy', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'logo_url',
			array(
				'label'   => esc_html__( 'Main Logo', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => $this->get_default_logo_url(),
				),
			)
		);

		$this->add_control(
			'tagline',
			array(
				'label'       => esc_html__( 'Tagline', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Inkfire quicklinks', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'A quick route into Inkfire', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'bio',
			array(
				'label'       => esc_html__( 'Bio', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Disabled-led, inclusive, and built around real people. Our team brings together diverse expertise to create digital work that’s accessible, practical, and considered.', 'foundation-elementor-plus' ),
				'rows'        => 5,
			)
		);

		$this->end_controls_section();
	}

	private function register_primary_link_controls() {
		$this->start_controls_section(
			'section_primary_links',
			array(
				'label' => esc_html__( 'Primary Links', 'foundation-elementor-plus' ),
			)
		);

		$this->add_link_copy_controls(
			'home',
			esc_html__( 'Homepage Link', 'foundation-elementor-plus' ),
			'Visit the homepage',
			'Start with the latest Inkfire pages and services',
			home_url( '/' )
		);

		$this->add_link_copy_controls(
			'blog',
			esc_html__( 'Blog Link', 'foundation-elementor-plus' ),
			'What’s new at Inkfire',
			'Fresh updates, news, and recent thinking',
			home_url( '/category/news/' )
		);

		$this->add_control(
			'blog_category_slug',
			array(
				'label'       => esc_html__( 'Blog Category Slug', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'news',
				'description' => esc_html__( 'Controls which posts appear in the latest updates feed.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_link_copy_controls(
			'review',
			esc_html__( 'Reviews Link', 'foundation-elementor-plus' ),
			'Read our Google reviews',
			'See why clients keep giving us five stars',
			'https://www.google.com/search?q=inkfire+review'
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'label',
			array(
				'label'       => esc_html__( 'Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Custom link', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'meta',
			array(
				'label'       => esc_html__( 'Supporting Copy', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Add another route into Inkfire', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'url',
			array(
				'label'       => esc_html__( 'URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => home_url( '/' ),
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Icon', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'mail',
				'options' => $this->get_link_icon_options(),
			)
		);
		$repeater->add_control(
			'tone',
			array(
				'label'   => esc_html__( 'Card Style', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'is-neutral',
				'options' => $this->get_link_tone_options(),
			)
		);

		$this->add_control(
			'extra_links',
			array(
				'label'       => esc_html__( 'Extra Button Links', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(),
				'title_field' => '{{{ label }}}',
				'description' => esc_html__( 'Add extra button-style links under the default homepage, blog, and reviews cards.', 'foundation-elementor-plus' ),
			)
		);

		$this->end_controls_section();
	}

	private function register_social_controls() {
		$this->start_controls_section(
			'section_social_links',
			array(
				'label' => esc_html__( 'Social Links', 'foundation-elementor-plus' ),
			)
		);

		$this->add_social_controls( 'instagram', 'Instagram', 'https://www.instagram.com/inkfirelimited/' );
		$this->add_social_controls( 'linkedin', 'LinkedIn', 'https://uk.linkedin.com/company/inkfire' );
		$this->add_social_controls( 'facebook', 'Facebook', 'https://facebook.com/inkfirelimited' );
		$this->add_social_controls( 'x', 'X', 'https://twitter.com/Inkfirelimited' );
		$this->add_social_controls( 'tiktok', 'TikTok', 'https://www.tiktok.com/@inkfirelimited' );
		$this->add_social_controls( 'youtube', 'YouTube', 'https://www.youtube.com/@mali.and.m.e' );

		$this->end_controls_section();
	}

	private function register_section_copy_controls() {
		$this->start_controls_section(
			'section_sections_copy',
			array(
				'label' => esc_html__( 'Section Copy', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'video_eyebrow',
			array(
				'label'       => esc_html__( 'Video Eyebrow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Portfolio reel', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'youtube_id',
			array(
				'label'   => esc_html__( 'YouTube Video ID', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'A_WiCvOV72g',
			)
		);

		$this->add_control(
			'newsletter_eyebrow',
			array(
				'label'       => esc_html__( 'Newsletter Eyebrow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Newsletter', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'newsletter_title',
			array(
				'label'       => esc_html__( 'Newsletter Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Join the Inkfire newsletter', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'newsletter_copy',
			array(
				'label'       => esc_html__( 'Newsletter Copy', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Get our latest updates, opportunities, and behind-the-scenes thoughts without having to go looking for them.', 'foundation-elementor-plus' ),
				'rows'        => 4,
			)
		);

		$this->add_control(
			'newsletter_note',
			array(
				'label'       => esc_html__( 'Newsletter Note', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'We keep it human, occasional, and worth opening.', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'newsletter_form_id',
			array(
				'label'   => esc_html__( 'Sender Form ID', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'lo07zevpeekjkb1p38j',
			)
		);

		$this->add_control(
			'newsletter_default_state',
			array(
				'label'   => esc_html__( 'Newsletter Default State', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'closed',
				'options' => array(
					'closed' => esc_html__( 'Closed', 'foundation-elementor-plus' ),
					'open'   => esc_html__( 'Open', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'feed_eyebrow',
			array(
				'label'       => esc_html__( 'Feed Eyebrow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'What’s new at Inkfire', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'feed_title',
			array(
				'label'       => esc_html__( 'Feed Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Latest updates', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'feed_archive_label',
			array(
				'label'       => esc_html__( 'Feed Archive Link Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Open category', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'trust_eyebrow',
			array(
				'label'       => esc_html__( 'Recognition Eyebrow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Awards & recognition', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'trust_title',
			array(
				'label'       => esc_html__( 'Recognition Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Recognition we’re proud of', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();
	}

	private function register_contact_controls() {
		$this->start_controls_section(
			'section_contact_copy',
			array(
				'label' => esc_html__( 'Contact Copy', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'contact_eyebrow',
			array(
				'label'       => esc_html__( 'Contact Eyebrow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Contact & company details', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'contact_title',
			array(
				'label'       => esc_html__( 'Contact Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Need a quick route to us?', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_contact_controls( 'phone', 'Call', '+44 (0)333 613 4653' );
		$this->add_contact_controls( 'email', 'Email', 'hello@mediumblue-seal-942128.hostingersite.com' );
		$this->add_contact_controls( 'hours', 'Office hours', 'Mon – Fri, 9am – 5pm (UK time)' );

		$this->add_control(
			'contact_address_label',
			array(
				'label'   => esc_html__( 'Address Label', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Address', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'contact_address',
			array(
				'label'   => esc_html__( 'Address', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '9 Kingswell Road, Ensbury Park, Bournemouth, BH10 5DF', 'foundation-elementor-plus' ),
				'rows'    => 3,
			)
		);

		$this->add_control(
			'company_number_label',
			array(
				'label'   => esc_html__( 'Company Number Label', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Company No:', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'company_number',
			array(
				'label'   => esc_html__( 'Company Number', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '15153305',
			)
		);

		$this->add_control(
			'vat_number_label',
			array(
				'label'   => esc_html__( 'VAT Label', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'VAT:', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'vat_number',
			array(
				'label'   => esc_html__( 'VAT Number', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'GB483189752',
			)
		);

		$this->end_controls_section();
	}

	private function register_layout_manager_controls() {
		$this->start_controls_section(
			'section_layout_manager',
			array(
				'label' => esc_html__( 'Layout Manager', 'foundation-elementor-plus' ),
			)
		);

		$layout_repeater = new Repeater();
		$layout_repeater->add_control(
			'section_type',
			array(
				'label'   => esc_html__( 'Section', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'brand',
				'options' => $this->get_layout_section_options(),
			)
		);

		$this->add_control(
			'layout_sections',
			array(
				'label'       => esc_html__( 'Drag To Reorder Sections', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $layout_repeater->get_controls(),
				'default'     => $this->get_default_layout_sections(),
				'title_field' => '{{{ section_type }}}',
				'description' => esc_html__( 'Reorder, remove, or restore sections here. Removing an item hides that section from this widget instance.', 'foundation-elementor-plus' ),
			)
		);

		$this->end_controls_section();
	}

	private function register_palette_controls() {
		$this->start_controls_section(
			'section_style_palette',
			array(
				'label' => esc_html__( 'Palette', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_color_var_control( 'bg_a', esc_html__( 'Canvas Gradient Color 1', 'foundation-elementor-plus' ), '--amh-ql-bg-a', '#1a1c29' );
		$this->add_color_var_control( 'bg_b', esc_html__( 'Canvas Gradient Color 2', 'foundation-elementor-plus' ), '--amh-ql-bg-b', '#1a1c29' );
		$this->add_color_var_control( 'bg_c', esc_html__( 'Canvas Gradient Color 3', 'foundation-elementor-plus' ), '--amh-ql-bg-c', '#171824' );
		$this->add_color_var_control( 'bg_d', esc_html__( 'Canvas Gradient Color 4', 'foundation-elementor-plus' ), '--amh-ql-bg-d', '#151622' );
		$this->add_color_var_control( 'text_color', esc_html__( 'Main Text', 'foundation-elementor-plus' ), '--amh-ql-text', '#f8f5f2' );
		$this->add_color_var_control( 'muted_color', esc_html__( 'Muted Text', 'foundation-elementor-plus' ), '--amh-ql-muted', 'rgba(248, 245, 242, 0.78)' );
		$this->add_color_var_control( 'accent_color', esc_html__( 'Eyebrow Accent', 'foundation-elementor-plus' ), '--amh-ql-peach', '#fbccbf' );
		$this->add_color_var_control( 'focus_color', esc_html__( 'Focus Outline', 'foundation-elementor-plus' ), '--amh-ql-focus', '#11b3a1' );

		$this->add_control(
			'glass_border_color',
			array(
				'label'     => esc_html__( 'Glass Border Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-glass, {{WRAPPER}} .amh-ql-glass-soft, {{WRAPPER}} .amh-ql-social-pill, {{WRAPPER}} .amh-ql-link, {{WRAPPER}} .amh-ql-newsletter-chevron' => 'border-color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_surface_controls() {
		$this->start_controls_section(
			'section_style_surfaces',
			array(
				'label' => esc_html__( 'Surfaces', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'canvas_background_override',
			array(
				'label'       => esc_html__( 'Outer Canvas Background', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'description' => esc_html__( 'Optional advanced override. Paste a full CSS background value such as a gradient stack.', 'foundation-elementor-plus' ),
				'selectors'   => array(
					'{{WRAPPER}} .amh-ql-shell' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'canvas_shadow_override',
			array(
				'label'       => esc_html__( 'Outer Canvas Shadow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '0px 0px 10px 5px rgba(0, 0, 0, 0.5)',
				'selectors'   => array(
					'{{WRAPPER}} .amh-ql-shell' => 'box-shadow: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'canvas_border_color',
			array(
				'label'     => esc_html__( 'Outer Canvas Border', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-shell' => 'border-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'main_surface_background',
			array(
				'label'     => esc_html__( 'Main Card Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-main' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'canvas_hover_overlay',
			array(
				'label'       => esc_html__( 'Canvas Hover Overlay', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'description' => esc_html__( 'Optional advanced override for the subtle moving sheen behind the card.', 'foundation-elementor-plus' ),
				'selectors'   => array(
					'{{WRAPPER}} .amh-ql-backdrop' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'panel_surface_background',
			array(
				'label'     => esc_html__( 'Panel Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-video, {{WRAPPER}} .amh-ql-newsletter, {{WRAPPER}} .amh-ql-feed, {{WRAPPER}} .amh-ql-trust, {{WRAPPER}} .amh-ql-contact' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'social_pill_background',
			array(
				'label'     => esc_html__( 'Social Pill Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-social-pill' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'social_pill_text_color',
			array(
				'label'     => esc_html__( 'Social Pill Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-social-pill' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'primary_green_background',
			array(
				'label'     => esc_html__( 'Homepage Card Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-link.is-green' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'primary_orange_background',
			array(
				'label'     => esc_html__( 'Blog Card Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-link.is-orange' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'primary_peach_background',
			array(
				'label'     => esc_html__( 'Reviews Card Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-link.is-peach' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'primary_neutral_background',
			array(
				'label'     => esc_html__( 'Custom Card Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-link.is-neutral' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_layout_style_controls() {
		$this->start_controls_section(
			'section_style_layout',
			array(
				'label' => esc_html__( 'Layout', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'stage_width',
			array(
				'label'      => esc_html__( 'Card Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 480,
				),
				'range'      => array(
					'px' => array(
						'min' => 280,
						'max' => 900,
					),
					'%'  => array(
						'min' => 30,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .amh-ql-stage' => 'max-width: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'shell_padding',
			array(
				'label'      => esc_html__( 'Outer Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .amh-ql-shell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'main_gap',
			array(
				'label'      => esc_html__( 'Main Card Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .amh-ql-main' => 'gap: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'main_padding',
			array(
				'label'      => esc_html__( 'Main Card Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .amh-ql-main' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'main_radius',
			array(
				'label'      => esc_html__( 'Main Card Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 32,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .amh-ql-main' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'panel_radius',
			array(
				'label'      => esc_html__( 'Panel Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 28,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .amh-ql-video, {{WRAPPER}} .amh-ql-newsletter, {{WRAPPER}} .amh-ql-feed, {{WRAPPER}} .amh-ql-trust, {{WRAPPER}} .amh-ql-contact, {{WRAPPER}} .amh-ql-link, {{WRAPPER}} .amh-ql-post-card' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => esc_html__( 'Top Logo Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 280,
				),
				'tablet_default' => array(
					'unit' => 'px',
					'size' => 240,
				),
				'mobile_default' => array(
					'unit' => 'px',
					'size' => 200,
				),
				'range'      => array(
					'px' => array(
						'min' => 120,
						'max' => 600,
					),
					'%'  => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .amh-ql-logo' => 'width: min(100%, {{SIZE}}{{UNIT}}) !important; max-width: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_typography_controls() {
		$this->start_controls_section(
			'section_style_type',
			array(
				'label' => esc_html__( 'Typography', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_slider_control(
			'eyebrow_size',
			esc_html__( 'Eyebrow Size', 'foundation-elementor-plus' ),
			'{{WRAPPER}} .amh-ql-tagline, {{WRAPPER}} .amh-ql-eyebrow',
			0.82
		);

		$this->add_responsive_slider_control(
			'hero_title_size',
			esc_html__( 'Main Title Size', 'foundation-elementor-plus' ),
			'{{WRAPPER}} .amh-ql-copy h1',
			3
		);

		$this->add_responsive_slider_control(
			'section_title_size',
			esc_html__( 'Section Title Size', 'foundation-elementor-plus' ),
			'{{WRAPPER}} .amh-ql-section-head h2',
			2
		);

		$this->add_responsive_slider_control(
			'body_size',
			esc_html__( 'Body Text Size', 'foundation-elementor-plus' ),
			'{{WRAPPER}} .amh-ql-bio, {{WRAPPER}} .amh-ql-newsletter-copy, {{WRAPPER}} .amh-ql-newsletter-note, {{WRAPPER}} .amh-ql-link-copy span, {{WRAPPER}} .amh-ql-post-copy span, {{WRAPPER}} .amh-ql-contact-item span, {{WRAPPER}} .amh-ql-contact-item a, {{WRAPPER}} .amh-ql-contact-item address',
			1
		);

		$this->add_responsive_slider_control(
			'card_title_size',
			esc_html__( 'Card Title Size', 'foundation-elementor-plus' ),
			'{{WRAPPER}} .amh-ql-social-pill, {{WRAPPER}} .amh-ql-link-copy strong, {{WRAPPER}} .amh-ql-post-copy strong',
			1.05
		);

		$this->end_controls_section();
	}

	private function add_link_copy_controls( $prefix, $label, $default_label, $default_meta, $default_url ) {
		$this->add_control(
			$prefix . '_label',
			array(
				'label'       => sprintf( esc_html__( '%s Label', 'foundation-elementor-plus' ), $label ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_label,
				'label_block' => true,
			)
		);

		$this->add_control(
			$prefix . '_meta',
			array(
				'label'       => sprintf( esc_html__( '%s Supporting Copy', 'foundation-elementor-plus' ), $label ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_meta,
				'label_block' => true,
			)
		);

		$this->add_control(
			$prefix . '_url',
			array(
				'label'       => sprintf( esc_html__( '%s URL', 'foundation-elementor-plus' ), $label ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_url,
				'label_block' => true,
			)
		);
	}

	private function add_social_controls( $key, $default_label, $default_url ) {
		$control_label = ucfirst( $key );
		if ( 'x' === $key ) {
			$control_label = 'X';
		}

		$this->add_control(
			$key . '_label',
			array(
				'label'       => sprintf( esc_html__( '%s Label', 'foundation-elementor-plus' ), $control_label ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_label,
				'label_block' => true,
			)
		);

		$this->add_control(
			$key . '_url',
			array(
				'label'       => sprintf( esc_html__( '%s URL', 'foundation-elementor-plus' ), $control_label ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_url,
				'label_block' => true,
			)
		);
	}

	private function add_contact_controls( $key, $default_label, $default_value ) {
		$this->add_control(
			'contact_' . $key . '_label',
			array(
				'label'   => sprintf( esc_html__( '%s Label', 'foundation-elementor-plus' ), $default_label ),
				'type'    => Controls_Manager::TEXT,
				'default' => $default_label,
			)
		);

		$this->add_control(
			'contact_' . $key,
			array(
				'label'       => $default_label,
				'type'        => Controls_Manager::TEXT,
				'default'     => $default_value,
				'label_block' => true,
			)
		);
	}

	private function add_color_var_control( $id, $label, $css_var, $default ) {
		$this->add_control(
			$id,
			array(
				'label'     => $label,
				'type'      => Controls_Manager::COLOR,
				'default'   => $default,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-shell' => sprintf( '%s: {{VALUE}} !important;', $css_var ),
				),
			)
		);
	}

	private function add_responsive_slider_control( $id, $label, $selector, $default_size ) {
		$this->add_responsive_control(
			$id,
			array(
				'label'      => $label,
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'default'    => array(
					'unit' => 'rem',
					'size' => $default_size,
				),
				'range'      => array(
					'px'  => array(
						'min' => 8,
						'max' => 120,
					),
					'rem' => array(
						'min'  => 0.5,
						'max'  => 8,
						'step' => 0.05,
					),
				),
				'selectors'  => array(
					$selector => 'font-size: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);
	}

	private function get_default_logo_url() {
		$custom_logo_id  = (int) get_theme_mod( 'custom_logo' );
		$custom_logo_url = $custom_logo_id ? wp_get_attachment_image_url( $custom_logo_id, 'full' ) : '';

		return $custom_logo_url ? $custom_logo_url : home_url( '/wp-content/uploads/2024/01/Primary-Logo-White.svg' );
	}

	private function get_media_url( array $settings, $key, $fallback = '' ) {
		if ( empty( $settings[ $key ] ) || ! is_array( $settings[ $key ] ) ) {
			return $fallback;
		}

		if ( ! empty( $settings[ $key ]['url'] ) ) {
			return (string) $settings[ $key ]['url'];
		}

		return $fallback;
	}

	private function normalize_text( $value ) {
		return trim( preg_replace( '/\s+/', ' ', (string) $value ) );
	}

	private function normalize_textarea( $value ) {
		return trim( (string) $value );
	}

	private function sanitize_layout_sections( $items ) {
		$allowed = array_keys( $this->get_layout_section_options() );
		$order   = array();

		if ( is_array( $items ) ) {
			foreach ( $items as $item ) {
				$section = is_array( $item ) && ! empty( $item['section_type'] ) ? sanitize_key( (string) $item['section_type'] ) : '';
				if ( $section && in_array( $section, $allowed, true ) && ! in_array( $section, $order, true ) ) {
					$order[] = $section;
				}
			}
		}

		if ( empty( $order ) ) {
			$order = array_map(
				static function( $item ) {
					return $item['section_type'];
				},
				$this->get_default_layout_sections()
			);
		}

		return $order;
	}

	private function sanitize_extra_links( $items ) {
		$allowed_icons = array_keys( $this->get_link_icon_options() );
		$allowed_tones = array_keys( $this->get_link_tone_options() );
		$clean         = array();

		if ( ! is_array( $items ) ) {
			return $clean;
		}

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$label = $this->normalize_text( $item['label'] ?? '' );
			$url   = $this->normalize_text( $item['url'] ?? '' );

			if ( '' === $label || '' === $url ) {
				continue;
			}

			$icon = sanitize_key( (string) ( $item['icon'] ?? 'mail' ) );
			$tone = sanitize_key( (string) ( $item['tone'] ?? 'is-neutral' ) );

			$clean[] = array(
				'label' => $label,
				'meta'  => $this->normalize_text( $item['meta'] ?? '' ),
				'url'   => $url,
				'icon'  => in_array( $icon, $allowed_icons, true ) ? $icon : 'mail',
				'tone'  => in_array( $tone, $allowed_tones, true ) ? $tone : 'is-neutral',
			);
		}

		return $clean;
	}

	private function get_layout_section_options() {
		return array(
			'brand'         => esc_html__( 'Brand Header', 'foundation-elementor-plus' ),
			'social'        => esc_html__( 'Social Pills', 'foundation-elementor-plus' ),
			'primary_links' => esc_html__( 'Button Links', 'foundation-elementor-plus' ),
			'newsletter'    => esc_html__( 'Newsletter', 'foundation-elementor-plus' ),
			'video'         => esc_html__( 'Video', 'foundation-elementor-plus' ),
			'feed'          => esc_html__( 'Latest Updates', 'foundation-elementor-plus' ),
			'trust'         => esc_html__( 'Recognition', 'foundation-elementor-plus' ),
			'contact'       => esc_html__( 'Contact Details', 'foundation-elementor-plus' ),
		);
	}

	private function get_default_layout_sections() {
		return array(
			array( 'section_type' => 'brand' ),
			array( 'section_type' => 'social' ),
			array( 'section_type' => 'primary_links' ),
			array( 'section_type' => 'video' ),
			array( 'section_type' => 'newsletter' ),
			array( 'section_type' => 'feed' ),
			array( 'section_type' => 'trust' ),
			array( 'section_type' => 'contact' ),
		);
	}

	private function get_link_icon_options() {
		return array(
			'home'      => esc_html__( 'Home', 'foundation-elementor-plus' ),
			'blog'      => esc_html__( 'Blog', 'foundation-elementor-plus' ),
			'star'      => esc_html__( 'Star', 'foundation-elementor-plus' ),
			'mail'      => esc_html__( 'Mail', 'foundation-elementor-plus' ),
			'facebook'  => esc_html__( 'Facebook', 'foundation-elementor-plus' ),
			'instagram' => esc_html__( 'Instagram', 'foundation-elementor-plus' ),
			'linkedin'  => esc_html__( 'LinkedIn', 'foundation-elementor-plus' ),
			'tiktok'    => esc_html__( 'TikTok', 'foundation-elementor-plus' ),
			'x'         => esc_html__( 'X', 'foundation-elementor-plus' ),
			'youtube'   => esc_html__( 'YouTube', 'foundation-elementor-plus' ),
		);
	}

	private function get_link_tone_options() {
		return array(
			'is-green'   => esc_html__( 'Green', 'foundation-elementor-plus' ),
			'is-orange'  => esc_html__( 'Orange', 'foundation-elementor-plus' ),
			'is-peach'   => esc_html__( 'Peach', 'foundation-elementor-plus' ),
			'is-neutral' => esc_html__( 'Neutral', 'foundation-elementor-plus' ),
		);
	}
}
