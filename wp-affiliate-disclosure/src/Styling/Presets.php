<?php
/**
 * Built-in style presets for v1.4.
 *
 * @package WP_Affiliate_Disclosure
 */

declare(strict_types=1);

namespace WPADC\Styling;

/**
 * Returns shipped style preset definitions.
 */
final class Presets {

	/**
	 * All shipped presets keyed by slug.
	 *
	 * Values mirror spec 01 § Presets exactly.
	 *
	 * @return array
	 */
	public static function get_all(): array {
		return array(
			'minimal' => array(
				'label'         => __( 'Minimal', 'wp-affiliate-disclosure' ),
				'bg_color'      => '#f9f9f9',
				'text_color'    => '#555555',
				'border_color'  => '#e0e0e0',
				'border_style'  => 'solid',
				'border_width'  => 1,
				'border_radius' => 4,
				'padding_y'     => 12,
				'padding_x'     => 12,
				'margin_y'      => 16,
				'margin_x'      => 0,
			),
			'boxed' => array(
				'label'         => __( 'Boxed', 'wp-affiliate-disclosure' ),
				'bg_color'      => '#ffffff',
				'text_color'    => '#333333',
				'border_color'  => '#cccccc',
				'border_style'  => 'solid',
				'border_width'  => 2,
				'border_radius' => 8,
				'padding_y'     => 20,
				'padding_x'     => 20,
				'margin_y'      => 24,
				'margin_x'      => 0,
			),
			'banner' => array(
				'label'         => __( 'Banner', 'wp-affiliate-disclosure' ),
				'bg_color'      => '#1a73e8',
				'text_color'    => '#ffffff',
				'border_color'  => '',
				'border_style'  => 'none',
				'border_width'  => 0,
				'border_radius' => 6,
				'padding_y'     => 16,
				'padding_x'     => 20,
				'margin_y'      => 24,
				'margin_x'      => 0,
			),
			'inline' => array(
				'label'         => __( 'Inline', 'wp-affiliate-disclosure' ),
				'bg_color'      => 'transparent',
				'text_color'    => '#666666',
				'border_color'  => '',
				'border_style'  => 'none',
				'border_width'  => 0,
				'border_radius' => 0,
				'padding_y'     => 0,
				'padding_x'     => 0,
				'margin_y'      => 12,
				'margin_x'      => 0,
				'extra_css'     => 'font-style: italic; font-size: 0.9em;',
			),
		);
	}

	/**
	 * Get a preset by slug.
	 *
	 * @param string $slug Preset slug.
	 * @return array|null
	 */
	public static function get( string $slug ): ?array {
		$all = self::get_all();
		return $all[ $slug ] ?? null;
	}
}
