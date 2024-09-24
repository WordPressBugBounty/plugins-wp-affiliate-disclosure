<?php
/**
 * Getting Started Wizard View Class
 *
 * @author 		MojofyWP
 * @package 	builder/start-wizard
 * 
 */

if ( !class_exists('WPADC_Start_Wizard_View') ) :

class WPADC_Start_Wizard_View {

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
		$this->_hook_prefix = wpadc()->plugin_hook() . 'start_wizard/view/';


	}
	
	/**
	 * Render wizard
	 *
	 * @access public
	 */
	public function render_wizard( $args = array() ) {

		$defaults = array(
			'current_step' => 1,
			'steps' => array(),
			'values' => array()
		);

		$instance = wp_parse_args( $args, $defaults );
		extract( $instance );	

		global $wp_affiliate_disclosure_fs;

		if ( $current_step > 6 )
			$current_step = 6;

		ob_start();
		?>
		<div class="wpadcb-startw-wrapper">
			<?php echo $this->render_wizard_steps( $args ); ?>
			<div class="wpadcb-startw-content">
			<?php
				// load form
				try {
					require_once wpadc()->plugin_path( 'builder/start-wizard/views/step_'.$current_step.'.php' );
				} catch ( Exception $e ){
					echo '<br><br>View Form Error';			
				}
			?>
			</div>
			<div class="wpadcb-startw-exit-wrapper">
				<button class="wpadcb-button-passive wpadcb-startw-action" data-action-type="close"><i class="fa fa-remove"></i><?php echo esc_html__( 'Close Wizard', WPADC_SLUG ); ?></button>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'render_wizard' , ( !empty( $html ) ? $html : '' ) , $args , $this );
	}

	/**
	 * render steps
	 *
	 * @access public
	 */
	public function render_wizard_steps( $args = array() ) {

		$defaults = array(
			'current_step' => 1,
			'steps' => array()
		);

		$instance = wp_parse_args( $args, $defaults );
		extract( $instance );	

		if ( $current_step > 6 )
			$current_step = 6;

		ob_start();
		if ( !empty( $steps ) ) :
		?>
		<ul class="wpadcb-startw-stepnav">
		<?php 
		foreach ($steps as $step) {

			?>
			<li class="wpadcb-startw-stepnav-item<?php echo ( $step['id'] == $current_step ? ' active' : '' ); ?>">
				<span class="wpadcb-startw-stepnav-num"><?php echo $step['id']; ?></span>
				<span class="wpadcb-startw-stepnav-label"><?php echo $step['label']; ?></span>
			</li>
			<?php
		} ?>
		</ul><!-- .wpadcb-startw-stepnav -->
		<?php
		endif;
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'render_wizard_steps' , ( !empty( $html ) ? $html : '' ) , $args , $this );
	}

	/**
	 * render attributes
	 *
	 * @access public
	 */
	public function attributes( $name = '' ) {

		$attributes = '';
		$attributes .= ' id="'.$this->input_id( $name ).'"';
		$attributes .= ' name="'.$this->input_name( $name ).'"';

		return apply_filters( $this->_hook_prefix . 'attributes' , $attributes , $name , $this );
	}

	/**
	 * render input id
	 *
	 * @access public
	 */
	public function input_id( $name = '' ) {
		return apply_filters( $this->_hook_prefix . 'input_id' , ( !empty( $name ) ? 'wpadcb-field-' . $name : '' ) , $name , $this );
	}

	/**
	 * render input name
	 *
	 * @access public
	 */
	public function input_name( $name = '' ) {
		return apply_filters( $this->_hook_prefix . 'input_name' , ( !empty( $name ) ? 'wpadcb_first_item[' . $name . ']' : '' ) , $name , $this );
	}

	/**
	 * Get value
	 *
	 * @access public
	 */
	public function get_value( $name = '' , $value = array() , $esc = 'attr' ) {

		$return = '';

		if ( !empty( $value[ $name ] ) ) {
			if ( $esc == 'attr' ) {
				$return = esc_attr( $value[ $name ] );
			} elseif ( $esc == 'url' ) {
				$return = esc_url( $value[ $name ] );
			} else {
				$return = $value[ $name ];
			}
		}

		return apply_filters( $this->_hook_prefix . 'get_value' , $return , $name , $value , $esc , $this );
	}

	/**
	 * whether is selected
	 *
	 * @access public
	 */
	public function selected( $value = array() , $name = '' , $compare = '' ) {

		$return = '';

		if ( !empty( $value[ $name ] ) ) {
			$return = selected( $value[ $name ] , $compare , false );
		}

		return apply_filters( $this->_hook_prefix . 'selected' , $return , $name , $value , $compare , $this );
	}

	/**
	 * whether is option selected
	 *
	 * @access public
	 */
	public function option_selected( $value = array() , $name = '' , $compare = '' ) {

		$selected = false;

		if ( isset( $value[ $name ] ) && $value[ $name ] == $compare ) {
			$selected = true;
		}

		return apply_filters( $this->_hook_prefix . 'option_selected' , ( $selected ? ' wpadcb-optselector-active' : '' ) , $name , $value , $compare , $this );
	}

	/**
	 * whether is multi option selected
	 *
	 * @access public
	 */
	public function multi_option_selected( $value = array() , $name = '' , $compare = '' ) {

		$selected = false;

		if ( isset( $value[ $name ] ) && !empty( $value[ $name ] ) ) {
			$exploded = explode( ',' , $value[ $name ] );
			if ( !empty( $exploded ) && is_array( $exploded ) ) {
				foreach ($exploded as $item) {
					if ( $item == $compare )
						$selected = true;
				}
			}
		}

		return apply_filters( $this->_hook_prefix . 'option_selected' , ( $selected ? ' wpadcb-optselector-active' : '' ) , $name , $value , $compare , $this );
	}

	/**
	 * whether is checked
	 *
	 * @access public
	 */
	public function checked( $name = '' , $value = array() ) {

		$return = '';

		if ( !empty( $value[ $name ] ) && $value[ $name ] == 'on' ) {
			$return = 'checked="checked"';
		}

		return apply_filters( $this->_hook_prefix . 'selected' , $return , $name , $value , $this );
	}

	/**
	 * render show if selector
	 *
	 * @access public
	 */
	public function show_if( $name = '', $value = 'true' , $operator = '==' ) {
		return apply_filters( $this->_hook_prefix . 'show_if' , 'data-show-if="'.$this->input_id( $name ).'" data-show-if-value="'.$value.'" data-show-if-operator="'.$operator.'"' , $name , $value , $operator , $this );
	}

	/**
	 * sample render
	 *
	 * @access public
	 */
	public function sample_render( $args = array() ) {

		$defaults = array(
			'sample' => true,
		);

		$instance = wp_parse_args( $args, $defaults );
		extract( $instance );	

		ob_start();
		?>

		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'sample_render' , ( !empty( $html ) ? $html : '' ) , $args , $this );
	}

	/* END
	------------------------------------------------------------------- */

} // end - class WPADC_Start_Wizard_View

endif; // end - !class_exists('WPADC_Start_Wizard_View')