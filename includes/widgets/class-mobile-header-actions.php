<?php
namespace FoundationElementorPlus\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Compact header actions. Social URLs remain owned by the widget's Social Links controls. */
final class Mobile_Header_Actions {
	public static function render( array $settings, array $social_links ): void {
		$show_project = 'yes' === ( $settings['show_topbar_project'] ?? 'yes' );
		$show_socials = 'yes' === ( $settings['show_topbar_socials'] ?? 'yes' );
		$label = trim( (string) ( $settings['topbar_project_label'] ?? '' ) );
		$label = '' !== $label ? $label : __( 'Start project', 'foundation-elementor-plus' );
		$links = array();
		foreach ( $social_links as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$name = trim( (string) ( $item['label'] ?? '' ) );
			$value = $item['url'] ?? '';
			$url = is_array( $value ) ? (string) ( $value['url'] ?? '' ) : (string) $value;
			$url = esc_url( $url, array( 'http', 'https' ) );
			if ( '' === $name || '' === $url ) {
				continue;
			}
			$key = self::icon_key( (string) ( $item['icon_class'] ?? '' ), $name );
			$links[] = array( 'label' => $name, 'url' => $url, 'key' => $key );
		}
		if ( ! $show_project && ( ! $show_socials || ! $links ) ) {
			return;
		}
		?>
		<div class="imh-header-actions">
			<?php if ( $show_socials && $links ) : ?>
				<div class="imh-topbar-socials" role="group" aria-label="<?php esc_attr_e( 'Inkfire social links', 'foundation-elementor-plus' ); ?>">
					<?php foreach ( array_slice( $links, 0, 5 ) as $index => $link ) : ?>
						<a class="imh-topbar-social<?php echo in_array( $link['key'], array( 'x', 'tiktok' ), true ) ? ' imh-topbar-social--extra' : ''; ?>"
							href="<?php echo $link['url']; ?>" target="_blank" rel="noopener noreferrer"
							aria-label="<?php echo esc_attr( sprintf( __( '%s (opens in a new tab)', 'foundation-elementor-plus' ), $link['label'] ) ); ?>"
							title="<?php echo esc_attr( $link['label'] ); ?>">
							<?php echo self::icon( $link['key'] ); // Fixed, plugin-owned SVG only. ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<?php if ( $show_project ) : ?>
				<a class="imh-project-cta imh-icon-button" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"
					data-foundation-calculator-open aria-haspopup="dialog"
					aria-label="<?php echo esc_attr( sprintf( __( '%s: open project calculator', 'foundation-elementor-plus' ), $label ) ); ?>"
					title="<?php esc_attr_e( 'Start a project', 'foundation-elementor-plus' ); ?>">
					<i class="fa-solid fa-lightbulb imh-project-cta__icon" aria-hidden="true"></i>
					<span class="imh-project-cta__label"><?php echo esc_html( $label ); ?></span>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}

	private static function icon_key( string $classes, string $label ): string {
		$keys = array( 'linkedin' => 'linkedin', 'instagram' => 'instagram', 'facebook' => 'facebook', 'x-twitter' => 'x', 'twitter' => 'x', 'tiktok' => 'tiktok', 'youtube' => 'youtube' );
		foreach ( $keys as $class => $key ) {
			if ( false !== stripos( $classes, 'fa-' . $class ) ) {
				return $key;
			}
		}
		$key = strtolower( trim( $label ) );
		return in_array( $key, array( 'linkedin', 'instagram', 'facebook', 'x', 'tiktok', 'youtube' ), true ) ? $key : '';
	}

	private static function icon( string $key ): string {
		// Reuse the icon collection already bundled in this plugin; no font/CDN dependency.
		if ( '' !== $key && function_exists( 'amh_social_quicklinks_svg' ) ) {
			return \amh_social_quicklinks_svg( $key );
		}
		return '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-2 2M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l2-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
	}
}
