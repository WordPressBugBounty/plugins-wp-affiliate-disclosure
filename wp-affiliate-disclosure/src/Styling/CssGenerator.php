<?php
/**
 * Generates per-preset baseline CSS + per-rule custom CSS.
 *
 * @package WP_Affiliate_Disclosure
 */

declare(strict_types=1);

namespace WPADC\Styling;

use WP_Query;
use WP_Post;

/**
 * Outputs <style id="wpadc-custom-styles"> on wp_head.
 */
final class CssGenerator {

	const TRANSIENT_KEY = 'wpadc_custom_css';

	/**
	 * Hook output to wp_head + cache invalidation hooks.
	 */
	public function register(): void {
		add_action( 'wp_head', array( $this, 'output' ), 50 );
		add_action( 'save_post_wpadc', array( $this, 'invalidate' ) );
		add_action( 'before_delete_post', array( $this, 'invalidate' ) );
	}

	/**
	 * Invalidate cached CSS.
	 */
	public function invalidate( $post_id = 0 ): void {
		delete_transient( self::TRANSIENT_KEY );
	}

	/**
	 * Output the <style> block.
	 */
	public function output(): void {
		$css = get_transient( self::TRANSIENT_KEY );
		if ( false === $css ) {
			$css = $this->build_css();
			set_transient( self::TRANSIENT_KEY, $css, HOUR_IN_SECONDS * 12 );
		}

		if ( '' === $css ) {
			return;
		}

		echo '<style id="wpadc-custom-styles">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — CSS only, values sanitized.
	}

	/**
	 * Build the complete CSS string (baselines + per-rule + combo selectors).
	 *
	 * @return string
	 */
	public function build_css(): string {
		$out = '';

		// 1) Per-preset baselines (always emitted).
		foreach ( Presets::get_all() as $slug => $p ) {
			$out .= $this->preset_block( '.wpadc-disclosure.wpadc-preset-' . $slug, $p );
		}

		// Inline preset's wpautop margin reset.
		$out .= '.wpadc-disclosure.wpadc-preset-inline > p{margin:0;}';

		// 2) Per-rule custom styles (only for rules where appearance customization is enabled).
		$rules = $this->get_rules_with_style();
		foreach ( $rules as $rule ) {
			if ( 'on' !== get_post_meta( (int) $rule->ID, '_wpadc_customize_appearance', true ) ) {
				continue;
			}
			$style = $this->extract_style_from_meta( (int) $rule->ID );
			if ( empty( $style ) ) {
				continue;
			}

			$preset = isset( $style['preset'] ) ? $style['preset'] : '';

			// If preset is 'custom' (or empty but other keys set), emit per-rule selector.
			if ( 'custom' === $preset || ( '' === $preset && $this->has_any_value( $style ) ) ) {
				$out .= $this->preset_block( '.wpadc-disclosure.wpadc-rule-' . (int) $rule->ID, $style );
			}

			// Combo selector: rule + preset, so shortcode style="X" wins.
			if ( in_array( $preset, array( 'minimal', 'boxed', 'banner', 'inline' ), true ) ) {
				$preset_def = Presets::get( $preset );
				if ( $preset_def ) {
					$out .= $this->preset_block( '.wpadc-disclosure.wpadc-rule-' . (int) $rule->ID . '.wpadc-preset-' . $preset, $preset_def );
				}
			}
		}

		return $out;
	}

	/**
	 * Build a single CSS block from a style array.
	 *
	 * @param string $selector CSS selector.
	 * @param array  $s        Style values.
	 * @return string
	 */
	private function preset_block( string $selector, array $s ): string {
		$rules = array();

		if ( ! empty( $s['bg_color'] ) ) {
			$rules[] = 'background-color:' . $s['bg_color'];
		}
		if ( ! empty( $s['text_color'] ) ) {
			$rules[] = 'color:' . $s['text_color'];
		}

		$bs = isset( $s['border_style'] ) ? $s['border_style'] : '';
		$bw = isset( $s['border_width'] ) ? (int) $s['border_width'] : 0;
		$bc = isset( $s['border_color'] ) ? $s['border_color'] : '';

		if ( 'none' === $bs || 0 === $bw ) {
			$rules[] = 'border:none';
		} elseif ( $bs && $bw > 0 && $bc ) {
			$rules[] = 'border:' . $bw . 'px ' . $bs . ' ' . $bc;
		}

		if ( isset( $s['border_radius'] ) ) {
			$rules[] = 'border-radius:' . (int) $s['border_radius'] . 'px';
		}

		$py = isset( $s['padding_y'] ) ? (int) $s['padding_y'] : null;
		$px = isset( $s['padding_x'] ) ? (int) $s['padding_x'] : null;
		if ( null !== $py || null !== $px ) {
			$rules[] = 'padding:' . ( $py ?? 0 ) . 'px ' . ( $px ?? 0 ) . 'px';
		}

		$my = isset( $s['margin_y'] ) ? (int) $s['margin_y'] : null;
		$mx = isset( $s['margin_x'] ) ? (int) $s['margin_x'] : null;
		if ( null !== $my || null !== $mx ) {
			$rules[] = 'margin:' . ( $my ?? 0 ) . 'px ' . ( $mx ?? 0 ) . 'px';
		}

		if ( ! empty( $s['extra_css'] ) ) {
			$rules[] = rtrim( $s['extra_css'], ';' );
		}

		if ( empty( $rules ) ) {
			return '';
		}

		return $selector . '{' . implode( ';', $rules ) . ';}';
	}

	/**
	 * Whether any style key has a meaningful value.
	 *
	 * @param array $s Style array.
	 * @return bool
	 */
	private function has_any_value( array $s ): bool {
		foreach ( array( 'bg_color', 'text_color', 'border_color', 'border_style', 'border_width', 'border_radius', 'padding_y', 'padding_x', 'margin_y', 'margin_x' ) as $k ) {
			if ( isset( $s[ $k ] ) && '' !== $s[ $k ] && 0 !== $s[ $k ] && '0' !== $s[ $k ] ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get all published wpadc rules.
	 *
	 * @return WP_Post[]
	 */
	private function get_rules_with_style(): array {
		$query = new WP_Query(
			array(
				'post_type'      => 'wpadc',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'no_found_rows'  => true,
			)
		);
		return is_array( $query->posts ) ? $query->posts : array();
	}

	/**
	 * Extract the style array for a rule from post meta.
	 *
	 * @param int $rule_id Rule post id.
	 * @return array
	 */
	private function extract_style_from_meta( int $rule_id ): array {
		return array(
			'preset'        => (string) get_post_meta( $rule_id, '_wpadc_style_preset', true ),
			'bg_color'      => (string) get_post_meta( $rule_id, '_wpadc_style_bg_color', true ),
			'text_color'    => (string) get_post_meta( $rule_id, '_wpadc_style_text_color', true ),
			'border_color'  => (string) get_post_meta( $rule_id, '_wpadc_style_border_color', true ),
			'border_style'  => (string) get_post_meta( $rule_id, '_wpadc_style_border_style', true ),
			'border_width'  => (int) get_post_meta( $rule_id, '_wpadc_style_border_width', true ),
			'border_radius' => (int) get_post_meta( $rule_id, '_wpadc_style_border_radius', true ),
			'padding_y'     => (int) get_post_meta( $rule_id, '_wpadc_style_padding_y', true ),
			'padding_x'     => (int) get_post_meta( $rule_id, '_wpadc_style_padding_x', true ),
			'margin_y'      => (int) get_post_meta( $rule_id, '_wpadc_style_margin_y', true ),
			'margin_x'      => (int) get_post_meta( $rule_id, '_wpadc_style_margin_x', true ),
		);
	}
}
