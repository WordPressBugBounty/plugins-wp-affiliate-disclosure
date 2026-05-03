<?php
/**
 * Compatibility checks for WP Affiliate Disclosure.
 *
 * This file must remain compatible with PHP 5.6 so older hosts can fail
 * gracefully before any PHP 7.4+ source files are loaded.
 *
 * @package WP_Affiliate_Disclosure
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wp_affiliate_disclosure_check_requirements' ) ) :
/**
 * Check whether the current environment can run this plugin version.
 *
 * @return bool
 */
function wp_affiliate_disclosure_check_requirements() {
	$php_min     = '7.4';
	$wp_min      = '5.8';
	$plugin_name = 'WP Affiliate Disclosure';
	$errors      = array();

	if ( version_compare( PHP_VERSION, $php_min, '<' ) ) {
		$errors[] = sprintf(
			'%1$s requires PHP %2$s or higher. You are running PHP %3$s.',
			$plugin_name,
			$php_min,
			PHP_VERSION
		);
	}

	global $wp_version;

	if ( version_compare( $wp_version, $wp_min, '<' ) ) {
		$errors[] = sprintf(
			'%1$s requires WordPress %2$s or higher. You are running WordPress %3$s.',
			$plugin_name,
			$wp_min,
			$wp_version
		);
	}

	if ( ! empty( $errors ) ) {
		wp_affiliate_disclosure_store_compatibility_errors( $errors );
		return false;
	}

	return true;
}
endif;

if ( ! function_exists( 'wp_affiliate_disclosure_store_compatibility_errors' ) ) :
/**
 * Store errors and register deferred admin feedback.
 *
 * @param array $errors Requirement error messages.
 */
function wp_affiliate_disclosure_store_compatibility_errors( $errors ) {
	$GLOBALS['wp_affiliate_disclosure_compatibility_errors'] = $errors;

	add_action( 'admin_notices', 'wp_affiliate_disclosure_compatibility_notice' );
	add_action( 'admin_init', 'wp_affiliate_disclosure_compatibility_deactivate' );
}
endif;

if ( ! function_exists( 'wp_affiliate_disclosure_compatibility_notice' ) ) :
/**
 * Display requirement errors in wp-admin.
 */
function wp_affiliate_disclosure_compatibility_notice() {
	if ( empty( $GLOBALS['wp_affiliate_disclosure_compatibility_errors'] ) ) {
		return;
	}

	$errors = $GLOBALS['wp_affiliate_disclosure_compatibility_errors'];
	?>
	<div class="notice notice-error">
		<p><strong><?php esc_html_e( 'WP Affiliate Disclosure - Requirements Not Met', 'wp-affiliate-disclosure' ); ?></strong></p>
		<ul style="list-style: disc; padding-left: 20px;">
			<?php foreach ( $errors as $error ) : ?>
				<li><?php echo esc_html( $error ); ?></li>
			<?php endforeach; ?>
		</ul>
		<p><?php esc_html_e( 'The plugin has been deactivated. Please update your environment and reactivate WP Affiliate Disclosure.', 'wp-affiliate-disclosure' ); ?></p>
	</div>
	<?php
}
endif;

if ( ! function_exists( 'wp_affiliate_disclosure_compatibility_deactivate' ) ) :
/**
 * Deactivate the plugin after a failed compatibility check.
 */
function wp_affiliate_disclosure_compatibility_deactivate() {
	if ( empty( $GLOBALS['wp_affiliate_disclosure_compatibility_errors'] ) ) {
		return;
	}

	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugin_file = plugin_basename( __FILE__ );
	$plugin_file = str_replace( 'compatibility-check.php', 'functions.php', $plugin_file );

	if ( is_plugin_active( $plugin_file ) ) {
		deactivate_plugins( $plugin_file );

		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}
}
endif;
