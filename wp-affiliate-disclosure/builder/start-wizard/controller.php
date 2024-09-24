<?php
/**
 * Getting Started Wizard Controller Class
 *
 * @author 		MojofyWP
 * @package 	builder/start-wizard
 * 
 */

if ( !class_exists('WPADC_Start_Wizard') ) :

class WPADC_Start_Wizard {

	/**
	 * Class instance
	 *
	 * @access private
	 * @var object
	 */
	private static $_instance = null;

	/**
	 * model
	 *
	 * @access private
	 * @var object
	 */
	private $_model = null;

	/**
	 * hook prefix
	 *
	 * @access private
	 * @var string
	 */
	private $_hook_prefix = null;

	/**
	 * Get class instance
	 *
	 * @access public
	 * @return object
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new WPADC_Start_Wizard();

		return self::$_instance;
	}

	/**
	 * Get Model
	 *
	 * @access public
	 * @return WPADC_Start_Wizard_Model
	 */
	public function get_model() {
		return $this->_model;
	}
	
	/**
	 * Class Constructor
	 * @param array
	 * @access private
	 */
    function __construct( $args = array() ) {

		$this->_hook_prefix = wpadc()->plugin_hook() . 'start_wizard/';
		$this->_model 		= new WPADC_Start_Wizard_Model();
	}
	
	/**
	 * render wizard
	 *
	 * @access public
	 * @return string
	 */
	public function render() {

		$data = wpadcb_get_start_wizard_data();
		$view = new WPADC_Start_Wizard_View();

		ob_start();
		?>
		<div id="wpadcb-start-wizard">
			<?php echo $view->render_wizard( array(
				'current_step' => ( isset( $data ) && isset( $data['current_step'] ) ? $data['current_step'] : 1 ),
				'steps' => $this->_model->get_steps(),
				'values' => $data
			) ); ?>
		</div>
		<?php
		$html = ob_get_clean();
		
		return apply_filters( $this->_hook_prefix . 'render' , ( !empty( $html ) ? $html : '' ) , $this );
	}

	/**
	 * trigger wizard form submit & next step
	 *
	 * @access public
	 * @return string
	 */
	public function wizard_submit() {

		$current_step = ( isset( $_POST['wpadcb_startw_step'] ) ? intval( $_POST['wpadcb_startw_step'] ) : 0 );
		$values = ( isset( $_POST['wpadcb_first_item'] ) ? $_POST['wpadcb_first_item'] : false );
		$new_step = $current_step+1;

		if ( isset( $_POST['_wpadcb_start_wizard_nonce'] ) && !empty( $_POST['_wpadcb_start_wizard_nonce'] ) && wp_verify_nonce( $_POST['_wpadcb_start_wizard_nonce'] , 'wpadcb_start_wizard' ) ) {

			$current_step_str = ( $current_step + '' );
			$new_data = array( 'current_step' => $new_step );

			switch( $current_step_str ) {
				case '2':
					$new_data['rule_name'] = ( !empty( $values ) && isset( $values['rule_name'] ) && !empty( $values['rule_name'] ) ? esc_attr( $values['rule_name'] ) : esc_html__( 'Affiliate Disclosure Statement', WPADC_SLUG ) );
					break;
				case '3':
					$new_data['disclosure_statement'] = ( !empty( $values ) && isset( $values['disclosure_statement'] ) && !empty( $values['disclosure_statement'] ) ? $values['disclosure_statement'] : '' );
					break;
				case '4':
					$new_data['statement_position'] = ( !empty( $values ) && isset( $values['statement_position'] ) && !empty( $values['statement_position'] ) ? esc_attr( $values['statement_position'] ) : '' );
					break;
				case '5':
					$new_data['post_type'] = ( !empty( $values ) && isset( $values['post_type'] ) && !empty( $values['post_type'] ) ? esc_attr( $values['post_type'] ) : '' );
					break;
				case '6':
					$new_data['condition'] = ( !empty( $values ) && isset( $values['condition'] ) && !empty( $values['condition'] ) ? esc_attr( $values['condition'] ) : '' );
					$new_data['ids'] = ( !empty( $values ) && isset( $values['ids'] ) && !empty( $values['ids'] ) ? esc_attr( $values['ids'] ) : '' );
					$new_data['taxonomies'] = ( !empty( $values ) && isset( $values['taxonomies'] ) && !empty( $values['taxonomies'] ) ? esc_attr( $values['taxonomies'] ) : '' );
					break;
			}
			
			$this->_model->update_wizard_option( $new_data );	

			if ( $current_step_str == '6' ) {
				$this->_model->create_rule();
			} // end - $current_step_str
		}

		$view = new WPADC_Start_Wizard_View();
		echo apply_filters( $this->_hook_prefix . 'wizard_submit' , $view->render_wizard( array(
			'current_step' => ( $new_step > 6 ? 6 : $new_step ),
			'steps' => $this->_model->get_steps(),
			'values' => wpadcb_get_start_wizard_data()
		) ) , $this );
		
	}

	/**
	 * trigger wizard previous step
	 *
	 * @access public
	 * @return string
	 */
	public function wizard_back() {

		$current_step = ( isset( $_POST['wpadcb_startw_step'] ) ? intval( $_POST['wpadcb_startw_step'] ) : 0 );
		$new_step = ( $current_step > 1 ? $current_step-1 : 1 );

		if ( isset( $_POST['_wpadcb_start_wizard_nonce'] ) && !empty( $_POST['_wpadcb_start_wizard_nonce'] ) && wp_verify_nonce( $_POST['_wpadcb_start_wizard_nonce'] , 'wpadcb_start_wizard' ) ) {
			$this->_model->update_wizard_option( array( 'current_step' => $new_step ) );	
		}	

		$view = new WPADC_Start_Wizard_View();
		echo apply_filters( $this->_hook_prefix . 'wizard_back' , $view->render_wizard( array(
			'current_step' => ( $new_step > 6 ? 6 : $new_step ),
			'steps' => $this->_model->get_steps(),
			'values' => wpadcb_get_start_wizard_data()
		) ) , $this );
	}

	/**
	 * trigger close wizard
	 *
	 * @access public
	 * @return string
	 */
	public function wizard_close() {

		if ( isset( $_POST['_wpadcb_start_wizard_nonce'] ) && !empty( $_POST['_wpadcb_start_wizard_nonce'] ) && wp_verify_nonce( $_POST['_wpadcb_start_wizard_nonce'] , 'wpadcb_start_wizard' ) ) {
			$this->_model->update_wizard_option( array( 'wizard_status' => 'skipped' ) );	
		}

		echo apply_filters( $this->_hook_prefix . 'wizard_close' , 'closed' , $this );
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

} // end - class WPADC_Start_Wizard

endif; // end - !class_exists('WPADC_Start_Wizard')