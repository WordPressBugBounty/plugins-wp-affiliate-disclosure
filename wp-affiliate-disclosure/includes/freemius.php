<?php

/**
 * Freemius Init Functions
 *
 * @author 		MojofyWP
 * @package 	includes
 *
 */
// Create a helper function for easy SDK access.
function wp_affiliate_disclosure_fs()
{
    global  $wp_affiliate_disclosure_fs ;
    
    if ( !isset( $wp_affiliate_disclosure_fs ) ) {
        // Activate multisite network integration.
        if ( !defined( 'WP_FS__PRODUCT_2803_MULTISITE' ) ) {
            define( 'WP_FS__PRODUCT_2803_MULTISITE', true );
        }
        // Include Freemius SDK.
        require_once WPADC_PATH . '/freemius/start.php';
        $wp_affiliate_disclosure_fs = fs_dynamic_init( array(
            'id'             => '2803',
            'slug'           => 'wp-affiliate-disclosure',
            'type'           => 'plugin',
            'public_key'     => 'pk_edcefd8745b43aea096decf73f8bf',
            'is_premium'     => false,
            'has_addons'     => false,
            'has_paid_plans' => true,
            'menu'           => array(
            'slug'    => 'wpadc-builder',
            'contact' => false,
            'support' => false,
        ),
            'is_live'        => true,
        ) );
    }
    
    return $wp_affiliate_disclosure_fs;
}

// Init Freemius.
wp_affiliate_disclosure_fs();
// Signal that SDK was initiated.
do_action( 'wp_affiliate_disclosure_fs_loaded' );