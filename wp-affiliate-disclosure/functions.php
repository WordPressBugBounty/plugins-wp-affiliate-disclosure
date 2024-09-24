<?php /*
Plugin Name: WP Affiliate Disclosure
Version: 1.2.8
Plugin URI: https://www.mojofywp.com/wp-affiliate-disclosure
Description: Automatically add a customizable, FTC-compliant disclosure statement across your WordPress website based on the rule(s) you define.
Author: MojofyWP
Author URI: https://www.mojofywp.com

WordPress - 
Requires at least: 4.9.8
Tested up to: 6.3.2
Stable tag: 1.2.8

Text Domain: wp-affiliate-disclosure
Domain Path: /langCopyright 2012 - 2023 Smashing Advantage Enterprise.

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
if(!defined('WPADC_VERSION')) define( 'WPADC_VERSION', '1.2.8' );

/**
 * Plugin path
 **/
if(!defined('WPADC_PATH')) define( 'WPADC_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Plugin url
 **/
if(!defined('WPADC_URL')) define( 'WPADC_URL', plugin_dir_url( __FILE__ ) );


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

endif;