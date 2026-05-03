<?php
/**
 * Admin help enhancements.
 *
 * @package WP_Affiliate_Disclosure
 */

declare(strict_types=1);

namespace WPADC\Admin;

use WP_Screen;

/**
 * Adds contextual help to legacy admin screens.
 */
final class HelpPage {

	/**
	 * Register help tabs for plugin screens.
	 */
	public function register_contextual_help(): void {
		$screen = get_current_screen();

		if ( ! $screen instanceof WP_Screen || false === strpos( (string) $screen->id, 'wpadc' ) ) {
			return;
		}

		$screen->add_help_tab(
			array(
				'id'      => 'wpadc-getting-started',
				'title'   => __( 'Getting Started', 'wp-affiliate-disclosure' ),
				'content' => '<p>' . esc_html__( 'Create a disclosure rule, choose where it should appear, then preview a matching post to confirm the disclosure displays.', 'wp-affiliate-disclosure' ) . '</p>',
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'wpadc-troubleshooting',
				'title'   => __( 'Troubleshooting', 'wp-affiliate-disclosure' ),
				'content' => '<p>' . esc_html__( 'If a disclosure does not appear, check the rule status, display position, post type, conditions, and any page cache before testing theme or plugin conflicts.', 'wp-affiliate-disclosure' ) . '</p>',
			)
		);

		$screen->set_help_sidebar(
			'<p><strong>' . esc_html__( 'WP Affiliate Disclosure', 'wp-affiliate-disclosure' ) . '</strong></p>' .
			'<p><a href="' . esc_url( admin_url( 'admin.php?page=wpadc-help&tab=faq' ) ) . '">' . esc_html__( 'Open Help & FAQ', 'wp-affiliate-disclosure' ) . '</a></p>'
		);
	}
}
