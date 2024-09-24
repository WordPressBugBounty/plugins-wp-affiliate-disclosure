<?php
/**
 * Disclosure Statement View Class
 *
 * @author 		MojofyWP
 * @package 	includes/disclosure_statement
 * 
 */

if ( !class_exists('WPADC_Disclosure_Statement_View') ) :

class WPADC_Disclosure_Statement_View {

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
		$this->_hook_prefix = wpadc()->plugin_hook() . 'disclosure_statement/view/';

    }

	/**
	 * render statement
	 *
	 * @access public
	 */
	public function render_statement( $args = array() ) {

		$defaults = array(
			'id' => '',
			'disclosure_statement' => '',
		);

		$instance = wp_parse_args( $args, $defaults );
		extract( $instance );	

		ob_start();
		?>
		<div id="wpadc-wrapper" class="wpadc-wrapper-class <?php echo ( !empty( $instance['selected'] ) ? ' wpadc-selected-' . $instance['selected'] : '' ) ?><?php echo ( !empty( $instance['id'] ) ? ' wpadc-rule-' . $instance['id'] : '' ) ?>"><?php echo ( !empty( $instance['disclosure_statement'] ) ? wpautop( $instance['disclosure_statement'] ) : '' ) ?></div>
		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'render_statement' , ( !empty( $html ) ? $html : '' ) , $args , $this );
	}

	/* END
	------------------------------------------------------------------- */

} // end - class WPADC_Disclosure_Statement_View

endif; // end - !class_exists('WPADC_Disclosure_Statement_View')