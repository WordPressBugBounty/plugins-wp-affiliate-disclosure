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
			'selected' => '',
			'style_class' => '',
			'wrapper_id' => '',
			'customize_appearance' => 0,
			'style_preset' => '',
			'content_parent_selector' => '.entry-content',
		);

		$instance = wp_parse_args( $args, $defaults );

		$classes = array( 'wpadc-wrapper-class', 'wpadc-disclosure' );

		if ( ! empty( $instance['selected'] ) ) {
			$classes[] = 'wpadc-selected-' . sanitize_html_class( $instance['selected'] );
		}

		if ( ! empty( $instance['id'] ) ) {
			$classes[] = 'wpadc-rule-' . absint( $instance['id'] );
		}

		// style_class wins over the rule's stored preset (shortcode override).
		// Preset class only applied when appearance customization is explicitly enabled.
		$preset_class              = '';
		$customize_appearance_on   = ( 'on' === $instance['customize_appearance'] );
		if ( ! empty( $instance['style_class'] ) ) {
			$preset_class = sanitize_html_class( $instance['style_class'] );
		} elseif ( $customize_appearance_on && ! empty( $instance['style_preset'] ) ) {
			$preset = sanitize_key( $instance['style_preset'] );
			if ( in_array( $preset, array( 'minimal', 'boxed', 'banner', 'inline' ), true ) ) {
				$preset_class = 'wpadc-preset-' . $preset;
			}
		}
		if ( ! empty( $preset_class ) ) {
			$classes[] = $preset_class;
		}

		$class_attr = implode( ' ', array_filter( $classes ) );

		// Build the id attribute.
		// Priority 1: explicit wrapper_id from the legacy [wpadc id="X"] shortcode.
		// Priority 2: legacy #wpadc-wrapper compat — emit on the first wrapper per
		//             request only, so single-disclosure pages keep backward-compat
		//             without producing duplicate-id HTML on multi-disclosure pages.
		//             Filterable so advanced users can opt out.
		static $legacy_id_emitted = false;

		$id_attr = '';
		if ( ! empty( $instance['wrapper_id'] ) ) {
			$id_attr = ' id="wp-affiliate-disclosure-' . esc_attr( sanitize_html_class( $instance['wrapper_id'] ) ) . '"';
		} elseif ( ! $legacy_id_emitted && apply_filters( 'wpadc_emit_legacy_wrapper_id', true ) ) {
			$id_attr          = ' id="wpadc-wrapper"';
			$legacy_id_emitted = true;
		}

		$body = ! empty( $instance['disclosure_statement'] ) ? wpautop( $instance['disclosure_statement'] ) : '';

		// For after-paragraph positions, attach data attributes so JS can reposition
		// using the configured parent selector (handles non-standard themes).
		$data_attrs = '';
		$p_index_map = array( 'after_p1' => 1, 'after_p2' => 2, 'after_p3' => 3 );
		if ( ! empty( $instance['selected'] ) && isset( $p_index_map[ $instance['selected'] ] ) ) {
			$data_attrs .= ' data-paragraph-index="' . $p_index_map[ $instance['selected'] ] . '"';
			$parent_sel  = ! empty( $instance['content_parent_selector'] ) ? $instance['content_parent_selector'] : '.entry-content';
			$data_attrs .= ' data-parent-selector="' . esc_attr( $parent_sel ) . '"';
		}

		ob_start();
		?>
		<div<?php echo $id_attr; ?><?php echo $data_attrs; ?> class="<?php echo esc_attr( $class_attr ); ?>"><?php echo $body; ?></div>
		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'render_statement' , ( !empty( $html ) ? $html : '' ) , $args , $this );
	}

	/* END
	------------------------------------------------------------------- */

} // end - class WPADC_Disclosure_Statement_View

endif; // end - !class_exists('WPADC_Disclosure_Statement_View')