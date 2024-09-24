<?php
/**
 * Builder Model Class
 *
 * @author 		MojofyWP
 * @package 	builder/builder
 * 
 */

if ( !class_exists('WPADC_Builder_Model') ) :

class WPADC_Builder_Model {

	/**
	 * Hook prefix
	 *
	 * @access private
	 * @var string
	 */
	private $_hook_prefix = null;

	/**
	 * Class Constructor
	 *
	 * @access private
	 */
    function __construct() {
		
		// setup variables
		$this->_hook_prefix = wpadc()->plugin_hook() . 'builder/model/';
		
    }

	/**
	 * Register post type
	 *
	 * @access public
	 */
	public function register_wpadc() {

		register_post_type( 'wpadc' , apply_filters( wpadc()->plugin_hook() . 'register_wpadc', array(
			'labels'             => array(
				'name'               => __( 'Affiliate Disclosure', WPADC_SLUG ),
				'singular_name'      => __( 'Affiliate Disclosure', WPADC_SLUG ),
				'menu_name'          => __( 'Affiliate Disclosure', WPADC_SLUG ),
				'all_items'          => __( 'Affiliate Disclosure', WPADC_SLUG ),
				'add_new'            => __( 'Add New Affiliate Disclosure', WPADC_SLUG ),
				'add_new_item'       => __( 'Add New Affiliate Disclosure', WPADC_SLUG ),
				'edit_item'          => __( 'Edit Affiliate Disclosure', WPADC_SLUG ),
				'edit'               => __( 'Edit', WPADC_SLUG ),
				'new_item'           => __( 'New Affiliate Disclosure', WPADC_SLUG ),
				'view_item'          => __( 'View Affiliate Disclosure', WPADC_SLUG ),
				'search_items'       => __( 'Search Affiliate Disclosure', WPADC_SLUG ),
				'not_found'          => __( 'No Affiliate Disclosure Found', WPADC_SLUG ),
				'not_found_in_trash' => __( 'No Affiliate Disclosure found in Trash', WPADC_SLUG ),
				'view'               => __( 'View Affiliate Disclosure', WPADC_SLUG )
			),
			'public'             => false,
			'show_ui'            => false,
			'capability_type'    => 'post',
			'hierarchical' => false,
			'rewrite' => false,
			'supports' => array( 'title' ), 
			'query_var' => false,
			'can_export' => true,
			'show_in_nav_menus' => false
		) ) );
		
	}

	/**
	 * sample function
	 *
	 * @access public
	 * @return string
	 */
	public function sample_func() {

		$output = '';

		return apply_filters( $this->_hook_prefix . 'sample_func' , $output , $this );
	}

	/* END
	------------------------------------------------------------------- */

} // end - class WPADC_Builder_Model

endif; // end - !class_exists('WPADC_Builder_Model')