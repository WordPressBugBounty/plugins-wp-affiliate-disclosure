<?php
/**
 * Builder Controller Class
 *
 * @author 		MojofyWP
 * @package 	builder/builder
 * 
 */

if ( !class_exists('WPADC_Builder') ) :

class WPADC_Builder {

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
			self::$_instance = new WPADC_Builder();

		return self::$_instance;
	}

	/**
	 * Get Model
	 *
	 * @access public
	 * @return WPADC_Builder_Model
	 */
	public function get_model() {
		return $this->_model;
	}
	
	/**
	 * Class Constructor
	 * @access private
	 */
    function __construct() {

		$this->_hook_prefix = wpadc()->plugin_hook() . 'builder/';

    	// setup variables
    	$this->_model = new WPADC_Builder_Model();

		// register wpadc as custom post type
		add_action( 'init', array( &$this->_model, 'register_wpadc' ), 1 );

		// add wpadc shortcode
		add_shortcode( 'wpadc' , array(&$this, 'wpadc_sc') );

		// add filter to the_content
		add_filter( 'the_content', array(&$this, 'add_statement_before_post'), 5 );
		add_filter( 'the_content', array(&$this, 'add_statement_after_post'), 8 );
    }

	/**
	 * Render wpadc Shortcode
	 *
	 * @access public
	 */
	public function wpadc_sc( $atts ) {

		extract( shortcode_atts( array(
			'id' => ''
		), $atts , 'wpadc' ) );

		$component = new WPADC_Disclosure_Statement();

		ob_start();
		?>
		<div id="wp-affiliate-disclosure<?php echo ( !empty( $id ) ? '-' . esc_attr( $id ) : '' ); ?>" class="wpadc_sc">
			<?php echo $component->render( 'shortcode' ); ?>
		</div>
		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'wpadc_sc' , ( !empty( $html ) ? $html : '' ) , $atts , $this );
		
	}

	/**
	 * add disclosure statement before content
	 *
	 * @access public
	 * @return string
	 */
	public function add_statement_before_post( $content ) {
		if ( ! ( is_home() || is_archive() || is_admin() ) ) {
			$component = new WPADC_Disclosure_Statement();
			$statement = $component->render( 'before-content' );
		} // end is_home
		return ( !empty( $statement ) ? $statement : '' ) . $content;
	}

	/**
	 * add disclosure statement after content
	 *
	 * @access public
	 * @return string
	 */
	public function add_statement_after_post( $content ) {
		if ( ! ( is_home() || is_archive() || is_admin() ) ) {
			$component = new WPADC_Disclosure_Statement();
			$statement = $component->render( 'after-content' );
		} // end - is_home
		return $content . ( !empty( $statement ) ? $statement : '' );
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

} // end - class WPADC_Builder

WPADC_Builder::get_instance();

endif; // end - !class_exists('WPADC_Builder')