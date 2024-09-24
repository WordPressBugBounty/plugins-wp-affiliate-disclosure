<?php
/**
 * Getting Started Wizard Model Class
 *
 * @author 		MojofyWP
 * @package 	builder/start-wizard
 * 
 */

if ( !class_exists('WPADC_Start_Wizard_Model') ) :

class WPADC_Start_Wizard_Model {

	/**
	 * instance
	 *
	 * @access private
	 * @var array
	 */
	private $instance = null;

	/**
	 * Hook prefix
	 *
	 * @access private
	 * @var string
	 */
	private $_hook_prefix = null;

	/**
	 * Get instance
	 *
	 * @access public
	 * @return array
	 */
	public function get_instance() {
		return $this->instance;
	}

	/**
	 * Class Constructor
	 *
	 * @access private
	 */
    function __construct() {
		
		// setup variables
		$this->_hook_prefix = wpadc()->plugin_hook() . 'start_wizard/model/';
		
	}
	
	/**
	 * get all steps
	 *
	 * @access public
	 * @return array
	 */
	public function get_steps() {
		return apply_filters( $this->_hook_prefix . 'get_steps' , array(
			'step-1' => array(
				'id' => 1,
				'label' => esc_html__( 'Welcome', WPADC_SLUG )
			),
			'step-2' => array(
				'id' => 2,
				'label' => esc_html__( 'Name Your Rule', WPADC_SLUG )
			),
			'step-3' => array(
				'id' => 3,
				'label' => esc_html__( 'Disclosure Statement', WPADC_SLUG )
			),
			'step-4' => array(
				'id' => 4,
				'label' => esc_html__( 'Select a Location', WPADC_SLUG )
			),
			'step-5' => array(
				'id' => 5,
				'label' => esc_html__( 'Select a Post Type', WPADC_SLUG )
			),
			'step-6' => array(
				'id' => 6,
				'label' => esc_html__( 'Select a Condition', WPADC_SLUG )
			),
		) , $this );
	}

	/**
	 * update wizard option
	 *
	 * @access public
	 * @return string
	 */
	public function update_wizard_option( $new = array() ) {
		$old_data = wpadcb_get_start_wizard_data();
		if ( !empty( $new ) )
			update_option( wpadc()->plugin_hook() . 'start_wizard', wp_parse_args( $new, $old_data ) );		
	}

	/**
	 * Create rule
	 *
	 * @access public
	 */
	public function create_rule() {
		$values = wpadcb_get_start_wizard_data();

		$id = null;
		$meta_prefix = wpadc()->plugin_meta_prefix();

		if ( isset( $values['rule_name'] ) ) {

			$id = wp_insert_post( array(
					'post_title' => esc_attr( $values['rule_name'] ),
					'post_type' => 'wpadc',
					'post_status' => 'publish'
				) );

			if ( !empty( $id ) && $id > 0 ) {

				$default = wpadcb_default_meta();
				$checkboxes = wpadcb_checkbox_meta();

				// update meta
				if ( !empty( $default ) && is_array( $default ) ) {
					foreach ( $default as $key => $d_value ) {
						$is_checkbox = ( !empty( $checkboxes ) && in_array( $key , $checkboxes ) ? true : false );
						$current_value = ( isset( $values[$key] ) ? $values[$key] : $d_value );

						if ( $is_checkbox ) {
							if ( !empty( $current_value ) && $current_value == 'on' ) {
								update_post_meta( $id , $meta_prefix . $key , 'on' );
							} else {
								update_post_meta( $id , $meta_prefix . $key , 0 );
							}
						} else {
							update_post_meta( $id , $meta_prefix . $key , $current_value );
						} // end - is_checkbox
					} // end - foreach
				} // end -$default

				// update option to completed
				$this->update_wizard_option( array( 'wizard_status' => 'completed', 'post_id' => $id ) );
			} // end - $id

		} // end - $values[rule_name]
		
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

} // end - class WPADC_Start_Wizard_Model

endif; // end - !class_exists('WPADC_Start_Wizard_Model')