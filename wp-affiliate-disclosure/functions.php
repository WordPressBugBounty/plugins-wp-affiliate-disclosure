<?php /*
Plugin Name: WP Affiliate Disclosure
Version: 1.4.0
Plugin URI: https://www.mojofywp.com/wp-affiliate-disclosure
Description: Automatically add a customizable, FTC-compliant disclosure statement across your WordPress website based on the rule(s) you define.
Author: MojofyWP
Author URI: https://www.mojofywp.com

WordPress - 
Requires at least: 5.8
Tested up to: 6.7.1
Requires PHP: 7.4
Stable tag: 1.4.0

Text Domain: wp-affiliate-disclosure
Domain Path: /langCopyright 2012 - 2026 Smashing Advantage Enterprise.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'compatibility-check.php';

if ( ! wp_affiliate_disclosure_check_requirements() ) {
	return;
}

/**
 * Auto deactivate the free version when activating the paid one
 **/
if ( function_exists( 'wp_affiliate_disclosure_fs' ) ) {
    wp_affiliate_disclosure_fs()->set_basename( false, __FILE__ );
    return;
}

/**
 * Plugin slug (for translation)
 **/

if(!defined('WPADC_SLUG')) define( 'WPADC_SLUG', 'wp-affiliate-disclosure' );

/**
 * Plugin version
 **/
if(!defined('WPADC_VERSION')) define( 'WPADC_VERSION', '1.4.0' );

/**
 * Plugin path
 **/
if(!defined('WPADC_PATH')) define( 'WPADC_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Plugin url
 **/
if(!defined('WPADC_URL')) define( 'WPADC_URL', plugin_dir_url( __FILE__ ) );

/**
 * Composer autoloader. Falls back to a small PSR-4 loader for source/zip installs.
 **/
$wp_affiliate_disclosure_autoloader = WPADC_PATH . 'vendor/autoload.php';

if ( file_exists( $wp_affiliate_disclosure_autoloader ) ) {
	$wp_affiliate_disclosure_autoloader_failed = false;
	try {
		require_once $wp_affiliate_disclosure_autoloader;
	} catch ( \Throwable $wpadc_autoloader_e ) {
		$wp_affiliate_disclosure_autoloader_failed = true;
		$wpadc_autoloader_msg = $wpadc_autoloader_e->getMessage();
		add_action( 'admin_notices', function () use ( $wpadc_autoloader_msg ) {
			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				esc_html( sprintf(
					/* translators: %s: error message */
					__( 'WP Affiliate Disclosure: Autoloader error — %s. Please deactivate and reinstall the plugin.', 'wp-affiliate-disclosure' ),
					$wpadc_autoloader_msg
				) )
			);
		} );
	}
	if ( ! defined( 'WPADC_COMPOSER_AUTOLOADER_LOADED' ) ) define( 'WPADC_COMPOSER_AUTOLOADER_LOADED', ! $wp_affiliate_disclosure_autoloader_failed );
	if ( ! defined( 'WPADC_AUTOLOADER_LOADED' ) ) define( 'WPADC_AUTOLOADER_LOADED', ! $wp_affiliate_disclosure_autoloader_failed );
} else {
	if ( ! defined( 'WPADC_COMPOSER_AUTOLOADER_LOADED' ) ) define( 'WPADC_COMPOSER_AUTOLOADER_LOADED', false );

	if ( ! function_exists( 'wp_affiliate_disclosure_autoload_namespaced' ) ) :
	/**
	 * Load WPADC namespaced classes when Composer's autoloader is unavailable.
	 *
	 * @param string $class Class name.
	 */
	function wp_affiliate_disclosure_autoload_namespaced( $class ) {
		$prefix = 'WPADC\\';

		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}

		$relative_class = substr( $class, strlen( $prefix ) );
		$file           = WPADC_PATH . 'src/' . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
	endif;

	spl_autoload_register( 'wp_affiliate_disclosure_autoload_namespaced' );

	if ( ! defined( 'WPADC_AUTOLOADER_LOADED' ) ) define( 'WPADC_AUTOLOADER_LOADED', true );
}


if ( !function_exists( 'wp_affiliate_disclosure_init' ) && ! function_exists( 'wp_affiliate_disclosure_fs' ) ) :

/**
 * Load plugin core class file
 */
require_once ( 'includes/freemius.php' );
require_once ( 'includes/class-wp-affiliate-disclosure.php' );
require_once ( 'includes/helpers.php' );

/**
 * Init WP Affiliate Disclosure core class
 *
 */
function wp_affiliate_disclosure_init() {

	global $wp_affiliate_disclosure;

	// Instantiate Plugin
	$wp_affiliate_disclosure = WPAffiliateDisclosure::get_instance();

	// Localization
	load_plugin_textdomain( WPADC_SLUG , false , dirname( plugin_basename( __FILE__ ) ) . '/lang' );

}

add_action( 'plugins_loaded' , 'wp_affiliate_disclosure_init' );

if ( WPADC_AUTOLOADER_LOADED && class_exists( '\WPADC\Plugin' ) ) :
if ( ! function_exists( 'wp_affiliate_disclosure_init_namespaced' ) ) :
/**
 * Boot namespaced v1.3 services.
 */
function wp_affiliate_disclosure_init_namespaced() {
	$plugin = new \WPADC\Plugin();
	$plugin->init();
}
endif;

add_action( 'plugins_loaded' , 'wp_affiliate_disclosure_init_namespaced', 5 );
endif;

endif;
