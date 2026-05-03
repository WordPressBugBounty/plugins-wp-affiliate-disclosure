<?php
/**
 * Disclosure template library.
 *
 * @package WP_Affiliate_Disclosure
 */

declare(strict_types=1);

namespace WPADC\Templates;

/**
 * Pre-built disclosure templates (Amazon, FTC).
 */
final class TemplateLibrary {

	/**
	 * Return all templates keyed by id.
	 *
	 * @return array
	 */
	public static function get_all(): array {
		return array(
			'amazon' => array(
				'id'                => 'amazon',
				'name'              => __( 'Amazon Associates', 'wp-affiliate-disclosure' ),
				'icon'              => 'dashicons-cart',
				'description'       => __( 'Required disclosure for Amazon affiliate links', 'wp-affiliate-disclosure' ),
				'short_text'        => 'As an Amazon Associate I earn from qualifying purchases.',
				'long_text'         => 'This post contains affiliate links. As an Amazon Associate I earn from qualifying purchases. If you click a link and make a purchase, I may receive a small commission at no extra cost to you.',
				'required_phrases'  => array( 'Amazon Associate', 'earn from qualifying purchases' ),
				'source'            => 'Amazon Associates Operating Agreement §5',
				'source_url'        => 'https://affiliate-program.amazon.com/help/operating/agreement',
				'last_verified'     => '2026-04-29',
			),
			'ftc' => array(
				'id'                => 'ftc',
				'name'              => __( 'General FTC Compliance', 'wp-affiliate-disclosure' ),
				'icon'              => 'dashicons-shield',
				'description'       => __( 'Standard FTC-compliant affiliate disclosure', 'wp-affiliate-disclosure' ),
				'short_text'        => 'This post contains affiliate links. I may earn a commission if you make a purchase through these links, at no additional cost to you.',
				'long_text'         => 'Disclosure: Some of the links in this post are affiliate links. This means if you click on the link and purchase an item, I will receive an affiliate commission at no extra cost to you. All opinions remain my own. I only recommend products or services I believe will add value to my readers.',
				'required_phrases'  => array(),
				'source'            => 'FTC Endorsement Guides, 16 CFR Part 255',
				'source_url'        => 'https://www.ftc.gov/business-guidance/resources/disclosures-101-social-media-influencers',
				'last_verified'     => '2026-04-29',
			),
		);
	}

	/**
	 * Get a single template by id.
	 *
	 * @param string $id Template id.
	 * @return array|null
	 */
	public static function get( string $id ): ?array {
		$all = self::get_all();
		return $all[ $id ] ?? null;
	}

	/**
	 * Get the text for a template + variant pair.
	 *
	 * @param string $id      Template id.
	 * @param string $variant short|long.
	 * @return string
	 */
	public static function get_text( string $id, string $variant = 'short' ): string {
		$template = self::get( $id );
		if ( ! $template ) {
			return '';
		}
		$key = 'long' === $variant ? 'long_text' : 'short_text';
		return (string) ( $template[ $key ] ?? $template['short_text'] ?? '' );
	}
}
