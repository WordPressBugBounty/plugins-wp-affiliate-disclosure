<?php
/**
 * Builder Helper Functions
 *
 * @author 		MojofyWP
 * @package 	builder
 *
 */

/* ------------------------------------------------------------------------------- */

if ( ! function_exists('wpadcb_sample_func') ) :
/**
 * Sample
 *
 * @return string
 */
function wpadcb_sample_func() {
	
	return apply_filters( 'wpadcb_sample_func' , ( !empty( $output ) ? $output : '' ) );
}
endif;

/* ------------------------------------------------------------------------------- */

if ( ! function_exists('wpadcb_default_meta') ) :
/**
 * Lists of default meta value for affiliate disclosure rule
 *
 * @return array
 */
function wpadcb_default_meta() {

	$values = array(
		'disclosure_statement' => esc_html__( 'This post contains affiliate links.', WPADC_SLUG ),
		'statement_position' => 'before-content,after-content',
		'post_type' => 'post',
		'condition' => 'none',
		'ids' => '',
		'taxonomies' => '',
		'advanced_filter' => 'none',
		'exclude_taxonomies' => '',
		'excludes_ids' => '',
		'priority' => 1
	);

	return apply_filters( 'wpadcb_default_meta' , $values );
}
endif;

/* ------------------------------------------------------------------------------- */

if ( ! function_exists('wpadcb_selectable_meta') ) :
/**
 * Retrieve a list of selectables in meta settings
 *
 * @return array
 */
function wpadcb_selectable_meta() {

	$selectables = array(
			'post_type',
			'condition',
			'advanced_filter'
		);
	
	return apply_filters( 'wpadcb_selectable_meta' , $selectables );
}
endif;

/* ------------------------------------------------------------------------------- */

if ( ! function_exists('wpadcb_checkbox_meta') ) :
/**
 * Retrieve a list of checkboxes in meta settings
 *
 * @return array
 */
function wpadcb_checkbox_meta() {

	$checkboxes = array(
            'show_sample',
		);
	
	return apply_filters( 'wpadcb_checkbox_meta' , $checkboxes );
}
endif;

/* ------------------------------------------------------------------------------- */

if ( ! function_exists('wpadcb_numbers_meta') ) :
/**
 * Retrieve a list of numbers in meta settings
 *
 * @return array
 */
function wpadcb_numbers_meta() {

	$numbers = array(
			'show_sample',
		);
	
	return apply_filters( 'wpadcb_numbers_meta' , $numbers );
}
endif;

/* ------------------------------------------------------------------------------- */

if ( ! function_exists('wpadcb_options_page_url') ) :
/**
 * get option page url
 *
 * @return string
 */
function wpadcb_options_page_url( $args = array() ) {
	
	$defaults = array(
		'page' => 'wpadc-builder', 
		'view' => 'overview',
	);

	$instance = wp_parse_args( $args, $defaults );
	extract( $instance );

	$url 	= admin_url( 'admin.php' );
	$count	= 1;
	
	if ( !empty( $instance ) && is_array( $instance ) ) {
		foreach ( $instance as $key => $value ) {
			if ( !empty( $value ) ) {
				$url .= ( $count == 1 ? '?' : '&' ) . $key . '=' . $value;
				$count++;
			}				
		}
	}

	return apply_filters( 'wpadcb_options_page_url' , esc_url( $url ) , $instance );
}
endif;

/* ------------------------------------------------------------------------------- */

if ( ! function_exists('wpadcb_get_meta') ) :
/**
 * Get meta value
 *
 * @param array $args = array (
 * 		@type int id - post ID
 * 		@type string key - meta key
 * 		@type mixed default - default value
 * 		@type bool single - whether to return only single result
 * 		@type string prefix - meta key prefix
 * )
 * @return mixed
 */
function wpadcb_get_meta( $args = array() ) {

	$defaults = array(
		'id' => null, 
		'key' => null,
		'default' => '',
		'single' => true,
		'prefix' => wpadc()->plugin_meta_prefix(),
		'esc' => null,
	);

	$instance = wp_parse_args( $args, $defaults );
	extract( $instance );

	if ( is_null( $id ) || is_null( $key ) )
		return;

	$value = get_post_meta( $id , $prefix . $key , $single );

	if ( isset( $value ) )
		$return = $value;
	else
		$return = $default;

	if ( !is_null( $esc ) ) {
		if ( $esc == 'attr' )
			$return = esc_attr( $return ); 
		elseif ( $esc == 'url' )
			$return = esc_url( $return ); 
	}

	return apply_filters( 'wpadcb_get_meta' , $return , $instance );
}
endif;

/* ------------------------------------------------------------------------------- */

if ( ! function_exists('wpadcb_run_start_wizard') ) :
/**
 * check whether to run start wizard
 *
 * @return string
 */
function wpadcb_run_start_wizard() {

	$run = true;
	$items = get_posts( array(
		'post_type' => 'wpadc',
		'post_status' => 'publish',
		'paged' => 1,
		'posts_per_page' => 1,
	) ); // get rules
	$total_rules = ( $items && !empty( $items ) ? count( $items ) : 0 ); // get total rules in the system
	$wizard_status = get_option( wpadc()->plugin_hook() . 'start_wizard', array() );

	if ( $total_rules > 0 || 
		( !empty( $wizard_status ) && 
			( is_array( $wizard_status ) && isset( $wizard_status['wizard_status'] ) && ( $wizard_status['wizard_status'] == 'skipped' || $wizard_status['wizard_status'] == 'completed' ) ) 
		) ) {
		$run = false;
	}
	
	return apply_filters( 'wpadcb_run_start_wizard' , $run );
}
endif;

/* ------------------------------------------------------------------------------- */

if ( ! function_exists('wpadcb_start_wizard_default_data') ) :
/**
 * Get start wizard default data
 *
 * @return string
 */
function wpadcb_start_wizard_default_data() {
	return apply_filters( 'wpadcb_start_wizard_default_data' , array(
		'current_step' => 1,
		'wizard_status' => 'start',
		'rule_name' => esc_html__( 'Affiliate Disclosure Statement', WPADC_SLUG ),
		'disclosure_statement' => esc_html__( 'This post contains affiliate links.', WPADC_SLUG ),
		'statement_position' => 'before-content',
		'post_type' => 'post',
		'condition' => 'none',
		'ids' => '',
		'taxonomies' => '',
		'advanced_filter' => 'none',
		'priority' => 1
	) );
}
endif;
	
/* ------------------------------------------------------------------------------- */

if ( ! function_exists('wpadcb_get_start_wizard_data') ) :
/**
 * Get start wizard data
 *
 * @return string
 */
function wpadcb_get_start_wizard_data() {
	$default_data = wpadcb_start_wizard_default_data();
	$wizard_data = get_option( wpadc()->plugin_hook() . 'start_wizard', array() );

	if ( !isset( $wizard_data ) || empty( $wizard_data ) || !is_array( $wizard_data ) )
		$wizard_data = array();

	return apply_filters( 'wpadcb_get_start_wizard_data' , wp_parse_args( $wizard_data, $default_data ) );
}
endif;

/* ------------------------------------------------------------------------------- */

if ( ! function_exists('wpadcb_reset_start_wizard_data') ) :
/**
 * Reset start wizard data
 *
 * @return string
 */
function wpadcb_reset_start_wizard_data() {
	update_option( wpadc()->plugin_hook() . 'start_wizard', wpadcb_start_wizard_default_data() );
}
endif;