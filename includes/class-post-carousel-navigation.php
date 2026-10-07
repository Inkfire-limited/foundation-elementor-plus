<?php
namespace FoundationElementorPlus;
if (!defined('ABSPATH')) { exit; }

/** External navigation for explicitly marked, independently duplicable post sections. */
final class Post_Carousel_Navigation {
    private $building_latest_work_ids = false;

    public function hooks() {
        add_action('elementor/frontend/before_render', array($this, 'before_render'));
        add_filter('elementor/widget/render_content', array($this, 'render_control'), 30, 2);
        add_action('elementor/query/foundation_latest_work', array($this, 'filter_latest_work_query'), 10, 2);
    }

    /**
     * Keep the Inkfire In Action carousel aligned with the actual Portfolio archive.
     *
     * The archive intentionally combines the newer ink_portfolio CPT with legacy
     * standard posts in the case-studies category. Elementor's native Loop Carousel
     * can only choose one post type at a time, so this query ID supplies the merged
     * source while leaving the loop template and Swiper behaviour native.
     */
    public function filter_latest_work_query($query, $widget = null) {
        if (!($query instanceof \WP_Query) || $this->building_latest_work_ids) {
            return;
        }

        $this->building_latest_work_ids = true;

        try {
            $portfolio_ids = get_posts(array(
                'post_type'           => 'ink_portfolio',
                'post_status'         => 'publish',
                'posts_per_page'      => -1,
                'fields'              => 'ids',
                'no_found_rows'       => true,
                'ignore_sticky_posts' => true,
            ));

            $case_study_ids = get_posts(array(
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => -1,
                'fields'              => 'ids',
                'category_name'       => 'case-studies',
                'no_found_rows'       => true,
                'ignore_sticky_posts' => true,
            ));
        } finally {
            $this->building_latest_work_ids = false;
        }

        $ids = array_values(array_unique(array_filter(array_map(
            'absint',
            array_merge((array) $portfolio_ids, (array) $case_study_ids)
        ))));

        $query->set('post_type', array('ink_portfolio', 'post'));
        $query->set('post_status', 'publish');
        $query->set('post__in', $ids ?: array(0));
        $query->set('orderby', 'date');
        $query->set('order', 'DESC');
        $query->set('ignore_sticky_posts', true);
    }
    private function has_class($element, $class) {
        foreach (array('_css_classes', 'css_classes') as $key) {
            $value = $element->get_settings_for_display($key);
            if (is_string($value) && in_array($class, preg_split('/\s+/', trim($value)), true)) { return true; }
        }
        return false;
    }
    public function before_render($element) {
        if (!$this->has_class($element, 'foundation-post-carousel') && !$this->has_class($element, 'foundation-post-slider')) { return; }
        $base = FOUNDATION_ELEMENTOR_PLUS_PATH;
        if (!wp_script_is('foundation-elementor-plus-runtime', 'registered')) {
            wp_register_script('foundation-elementor-plus-runtime', add_query_arg('_litespeed_rm_qs','0',FOUNDATION_ELEMENTOR_PLUS_URL . 'assets/js/widget-runtime.js'), array(), (string)filemtime($base . 'assets/js/widget-runtime.js'), true);
        }
        wp_enqueue_script('foundation-elementor-plus-post-navigation', add_query_arg('_litespeed_rm_qs','0',FOUNDATION_ELEMENTOR_PLUS_URL . 'assets/js/reliable/post-carousel-navigation.js'), array('foundation-elementor-plus-runtime'), (string)filemtime($base . 'assets/js/reliable/post-carousel-navigation.js'), true);
        wp_enqueue_style('foundation-elementor-plus-post-navigation', add_query_arg('_litespeed_rm_qs','0',FOUNDATION_ELEMENTOR_PLUS_URL . 'assets/css/post-carousel-navigation.css'), array(), (string)filemtime($base . 'assets/css/post-carousel-navigation.css'));
    }
    public function render_control($content, $widget) {
        if ($widget->get_name() !== 'icon') { return $content; }
        $direction = $this->has_class($widget, 'foundation-post-prev') ? 'previous' : ($this->has_class($widget, 'foundation-post-next') ? 'next' : '');
        if (!$direction) { return $content; }
        // Only the unlinked icon DIV is replaced; never rewrite an authored link.
        return preg_replace_callback('~<div\s+class="elementor-icon">(.*?)</div>~s', static function ($match) use ($direction) {
            $label = $direction === 'previous' ? __('Previous posts','foundation-elementor-plus') : __('Next posts','foundation-elementor-plus');
            return '<button type="button" class="elementor-icon" aria-label="' . esc_attr($label) . '" aria-disabled="true">' . $match[1] . '</button>';
        }, $content, 1);
    }
}
