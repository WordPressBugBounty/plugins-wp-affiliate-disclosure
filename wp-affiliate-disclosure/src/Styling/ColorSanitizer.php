<?php
/**
 * Color sanitization for v1.4 styling.
 *
 * @package WP_Affiliate_Disclosure
 */

declare(strict_types=1);

namespace WPADC\Styling;

/**
 * Sanitizes color values that may be hex or the literal token "transparent".
 */
final class ColorSanitizer {

	/**
	 * Sanitize a color value.
	 *
	 * Accepts:
	 *   - hex colors (#rgb or #rrggbb) via sanitize_hex_color()
	 *   - the literal string "transparent"
	 *
	 * @param mixed $value Raw color value.
	 * @return string Sanitized value or empty string on failure.
	 */
	public static function sanitize( $value ): string {
		if ( ! is_string( $value ) ) {
			return '';
		}

		$value = trim( $value );

		if ( '' === $value ) {
			return '';
		}

		if ( 'transparent' === strtolower( $value ) ) {
			return 'transparent';
		}

		$hex = sanitize_hex_color( $value );

		return is_string( $hex ) ? $hex : '';
	}
}

