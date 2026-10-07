<?php
namespace FoundationElementorPlus;
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Preserve the execution order of interactive widgets and their dependencies. */
final class Frontend_Reliability {
    public function hooks() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_header_pin' ) );
        add_filter( 'script_loader_tag', array( $this, 'script_tag' ), 30, 3 );
        add_filter( 'script_loader_src', array( $this, 'script_src' ), 998, 2 );
        add_filter( 'wp_inline_script_attributes', array( $this, 'inline_attributes' ), 30 );
        foreach ( array( 'litespeed_optimize_js_excludes', 'litespeed_optm_js_defer_exc', 'litespeed_optm_gm_js_exc' ) as $hook ) {
            add_filter( $hook, array( $this, 'optimizer_excludes' ), 30 );
        }
    }

    /** Keep the absolutely positioned header containers in view while scrolling. */
    public function enqueue_header_pin() {
        if ( is_admin() ) { return; }
        $relative = 'assets/js/header-pin.js';
        $file     = FOUNDATION_ELEMENTOR_PLUS_PATH . $relative;
        if ( ! file_exists( $file ) ) { return; }
        wp_enqueue_script(
            'foundation-elementor-plus-header-pin',
            add_query_arg( '_litespeed_rm_qs', '0', FOUNDATION_ELEMENTOR_PLUS_URL . $relative ),
            array(),
            (string) filemtime( $file ),
            true
        );
    }

    private function protected_handles() {
        $scripts = wp_scripts();
        $handles = array();
        foreach ( $scripts->registered as $handle => $script ) {
            $src = is_string( $script->src ?? null ) ? $script->src : '';
            if ( preg_match( '/^(foundation-|elementor-|pro-elements-handlers$|fco-client-js$|jquery$|imagesloaded$)/', $handle )
                || preg_match( '~/wp-content/plugins/(?:elementor(?:-pro)?|foundation-[^/]+)/~i', $src ) ) {
                $handles[ $handle ] = true;
            }
        }
        $queue = array_keys( $handles );
        while ( $queue ) {
            $handle = array_pop( $queue );
            foreach ( $scripts->registered[ $handle ]->deps ?? array() as $dependency ) {
                if ( ! isset( $handles[ $dependency ] ) ) {
                    $handles[ $dependency ] = true;
                    $queue[] = $dependency;
                }
            }
        }
        return $handles;
    }

    public function script_tag( $tag, $handle, $src ) {
        if ( is_admin() || ! isset( $this->protected_handles()[ $handle ] ) ) { return $tag; }
        $tag = preg_replace( '/\sdata-cfasync\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $tag );
        // Cloudflare requires this attribute before src, including dependencies.
        // WordPress may pass before-config, external and after-config tags together.
        $tag = preg_replace( '/<script\b/i', '<script data-cfasync="false"', $tag );
        if ( 'elementor-admin-bar' === $handle ) {
            // Native Elementor 4.2.4 registers a repeatable ready listener. Keep
            // its renderer/configuration intact, but scope that bootstrap to once.
            // The event API is restored immediately after this blocking asset.
            $guard_file = FOUNDATION_ELEMENTOR_PLUS_PATH . 'assets/js/elementor-admin-bar-lifecycle.js';
            if ( is_readable( $guard_file ) ) {
                $guard = file_get_contents( $guard_file );
                $tag = wp_get_inline_script_tag( $guard, array(
                    'id' => 'foundation-elementor-admin-bar-lifecycle-before', 'data-cfasync' => 'false',
                ) ) . $tag . wp_get_inline_script_tag(
                    'if (window.FoundationElementorAdminBarLifecycle && window.FoundationElementorAdminBarLifecycle.restore) { window.FoundationElementorAdminBarLifecycle.restore(); }',
                    array( 'id' => 'foundation-elementor-admin-bar-lifecycle-after', 'data-cfasync' => 'false' )
                );
            }
        }
        return $tag;
    }

    public function script_src( $src, $handle ) {
        if ( is_admin() || ! $src || ! isset( $this->protected_handles()[ $handle ] ) ) { return $src; }
        return add_query_arg( '_litespeed_rm_qs', '0', $src );
    }

    public function inline_attributes( $attributes ) {
        if ( is_admin() || empty( $attributes['id'] ) ) { return $attributes; }
        foreach ( $this->protected_handles() as $handle => $_ ) {
            if ( str_starts_with( $attributes['id'], $handle . '-js' ) ) {
                $attributes['data-cfasync'] = 'false';
                break;
            }
        }
        return $attributes;
    }

    public function optimizer_excludes( $excludes ) {
        if ( is_admin() ) { return $excludes; }
        $excludes = is_array( $excludes ) ? $excludes : array();
        $scripts = wp_scripts();
        foreach ( $this->protected_handles() as $handle => $_ ) {
            $src = $scripts->registered[ $handle ]->src ?? '';
            if ( ! is_string( $src ) || '' === $src ) { continue; }
            $path = wp_parse_url( $src, PHP_URL_PATH );
            if ( $path ) { $excludes[] = $path; }
        }
        // Inline configuration must travel with the external controller it configures.
        return array_values( array_unique( array_merge( $excludes, array(
            // Defer exclusions are read before late Elementor registrations.
            // Stable path seeds protect that first pass; registered dependencies extend it later.
            '/wp-content/plugins/foundation-elementor-plus/',
            '/wp-content/plugins/elementor/assets/',
            '/wp-content/plugins/elementor-pro/assets/',
            '/wp-includes/js/jquery/', '/wp-includes/js/imagesloaded',
            '/wp-includes/js/dist/hooks', '/wp-includes/js/dist/i18n',
            'elementorFrontendConfig', 'elementorProFrontendConfig', 'ElementorProFrontendConfig',
            'FoundationProjectCalculatorLazy', 'FOUNDATION_INKFIRE_SPLASH_CONFIG',
            'FoundationElementorAdminBarLifecycle'
        ) ) ) );
    }
}
