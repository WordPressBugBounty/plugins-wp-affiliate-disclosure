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

		// add wpadc shortcode (v1.4 unified handler)
		add_shortcode( 'wpadc' , array(&$this, 'render_shortcode') );

		// v1.4 — register human-readable alias only if no other plugin already owns it.
		if ( ! shortcode_exists( 'affiliate_disclosure' ) ) {
			add_shortcode( 'affiliate_disclosure' , array(&$this, 'render_shortcode') );
		}

		// add filter to the_content
		add_filter( 'the_content', array(&$this, 'add_statement_before_post'), 5 );
		add_filter( 'the_content', array(&$this, 'add_statement_after_post'), 8 );
		// v1.4 — after-paragraph injection runs at priority 11 (one tick after wpautop priority 10).
		add_filter( 'the_content', array(&$this, 'insert_after_paragraph_filter'), 11 );
    }

	/**
	 * v1.4 — Unified shortcode handler for [wpadc] and [affiliate_disclosure].
	 *
	 * Supported attributes (apply to BOTH shortcodes):
	 *   - style    : minimal|boxed|banner|inline (silently ignored if invalid)
	 *   - template : amazon|ftc (silently ignored if not in TemplateLibrary)
	 *   - variant  : short|long (default short)
	 *   - text     : custom override text (wp_kses_post; wins over template)
	 *   - rule     : numeric wpadc post id (0 = first match)
	 *   - id       : [wpadc] only — wrapper HTML id (legacy v1.3 behavior)
	 *
	 * @access public
	 * @param array  $atts          Shortcode attributes.
	 * @param string $content       Shortcode inner content (unused).
	 * @param string $shortcode_tag The actual tag used (wpadc | affiliate_disclosure).
	 * @return string
	 */
	public function render_shortcode( $atts = array(), $content = '', $shortcode_tag = '' ) {

		$atts = shortcode_atts( array(
			'style'    => '',
			'template' => '',
			'variant'  => 'short',
			'text'     => '',
			'rule'     => 0,
			'id'       => '',
		), $atts , $shortcode_tag ? $shortcode_tag : 'affiliate_disclosure' );

		$rule_id = absint( $atts['rule'] );

		// 1. Resolve text: text > template > rule's stored text.
		$override_text = '';
		if ( ! empty( $atts['text'] ) ) {
			$override_text = wp_kses_post( $atts['text'] );
		} elseif ( ! empty( $atts['template'] ) ) {
			$template_id = sanitize_key( $atts['template'] );
			$variant     = in_array( $atts['variant'], array( 'short', 'long' ), true ) ? $atts['variant'] : 'short';
			if ( class_exists( '\\WPADC\\Templates\\TemplateLibrary' ) ) {
				$override_text = \WPADC\Templates\TemplateLibrary::get_text( $template_id, $variant );
			}
		}

		// 2. Resolve style class.
		$style_class  = '';
		$valid_styles = array( 'minimal', 'boxed', 'banner', 'inline' );
		if ( ! empty( $atts['style'] ) ) {
			$style = sanitize_key( $atts['style'] );
			if ( in_array( $style, $valid_styles, true ) ) {
				$style_class = 'wpadc-preset-' . $style;
			}
		}

		// 3. Legacy wrapper id — only honored on [wpadc].
		$wrapper_id = '';
		if ( 'wpadc' === $shortcode_tag && ! empty( $atts['id'] ) ) {
			$wrapper_id = sanitize_html_class( $atts['id'] );
		}

		$component = new WPADC_Disclosure_Statement();
		$html = $component->render( 'shortcode', array(
			'rule_id'             => $rule_id,
			'override_text'       => $override_text,
			'style_class'         => $style_class,
			'wrapper_id'          => $wrapper_id,
			'skip_position_check' => true,
		) );

		return apply_filters( $this->_hook_prefix . 'render_shortcode' , ( !empty( $html ) ? $html : '' ) , $atts , $shortcode_tag , $this );
	}

	/**
	 * Guard for the_content callbacks — only inject disclosures inside the
	 * main loop on singular front-end content. Prevents disclosures from
	 * leaking into widgets, REST output, excerpts, and featured-image
	 * processing where the_content may run outside the loop.
	 *
	 * @access private
	 * @return bool
	 */
	private function should_inject_disclosure() {
		if ( is_admin() ) {
			return false;
		}
		if ( ! function_exists( 'in_the_loop' ) || ! in_the_loop() ) {
			return false;
		}
		if ( ! is_main_query() ) {
			return false;
		}
		if ( is_home() || is_archive() ) {
			return false;
		}
		if ( ! is_singular() ) {
			return false;
		}
		return true;
	}

	/**
	 * add disclosure statement before content
	 *
	 * @access public
	 * @return string
	 */
	public function add_statement_before_post( $content ) {
		if ( ! $this->should_inject_disclosure() ) {
			return $content;
		}
		$component = new WPADC_Disclosure_Statement();
		$statement = $component->render( 'before-content' );
		return ( !empty( $statement ) ? $statement : '' ) . $content;
	}

	/**
	 * add disclosure statement after content
	 *
	 * @access public
	 * @return string
	 */
	public function add_statement_after_post( $content ) {
		if ( ! $this->should_inject_disclosure() ) {
			return $content;
		}
		$component = new WPADC_Disclosure_Statement();
		$statement = $component->render( 'after-content' );
		return $content . ( !empty( $statement ) ? $statement : '' );
	}

	/**
	 * v1.4 — Inject disclosure after Nth paragraph for matching rules.
	 *
	 * Runs at the_content priority 11 (after wpautop). Iterates each rule's
	 * statement_position CSV and inserts after the requested paragraph.
	 *
	 * @access public
	 * @param string $content Post content (already wpautop'd).
	 * @return string
	 */
	public function insert_after_paragraph_filter( $content ) {
		if ( ! $this->should_inject_disclosure() ) {
			return $content;
		}

		$component      = new WPADC_Disclosure_Statement();
		$placement_to_n = array(
			'after_p1' => 1,
			'after_p2' => 2,
			'after_p3' => 3,
		);

		// Collect first, then insert in descending order so earlier insertions
		// don't shift paragraph offsets for later ones.
		$insertions = array();
		foreach ( $placement_to_n as $position => $n ) {
			$disclosure = $component->render( $position );
			if ( ! empty( $disclosure ) ) {
				$insertions[ $n ] = $disclosure;
			}
		}

		krsort( $insertions );
		foreach ( $insertions as $n => $disclosure ) {
			$content = $this->insert_after_paragraph( $content, $disclosure, $n );
		}

		return $content;
	}

	/**
	 * Insert disclosure HTML after the Nth paragraph in content.
	 *
	 * @access private
	 * @param string $content    Post content (already wpautop'd).
	 * @param string $disclosure HTML to insert.
	 * @param int    $n          Paragraph number (1, 2, or 3).
	 * @return string
	 */
	private function insert_after_paragraph( $content, $disclosure, $n ) {
		// Split content into text fragments and HTML tags so we can track
		// block-element nesting depth. Only top-level </p> tags (depth === 0)
		// count as real content paragraphs — this prevents paragraphs inside
		// injected disclosure wrappers (already added at earlier filter priorities)
		// from being counted.
		$parts = preg_split( '/(<[^>]+>)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE );

		$block_open  = '/^<(div|article|section|aside|nav|header|footer|figure|blockquote|details|ul|ol|table)\b[^>]*>$/i';
		$block_close = '/^<\/(div|article|section|aside|nav|header|footer|figure|blockquote|details|ul|ol|table)>$/i';

		$depth    = 0;
		$p_count  = 0;
		$out      = '';
		$inserted = false;

		foreach ( $parts as $part ) {
			if ( preg_match( $block_open, $part ) ) {
				$depth++;
			} elseif ( preg_match( $block_close, $part ) ) {
				$depth = max( 0, $depth - 1 );
			}

			$out .= $part;

			if ( ! $inserted && $depth === 0 && strtolower( $part ) === '</p>' ) {
				$p_count++;
				if ( $p_count >= $n ) {
					$out     .= $disclosure;
					$inserted = true;
				}
			}
		}

		if ( ! $inserted ) {
			$out .= $disclosure;
		}

		return $out;
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