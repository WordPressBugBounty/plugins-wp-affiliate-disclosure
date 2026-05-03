<?php
/**
 * Disclosure Statement Controller Class
 *
 * @author 		MojofyWP
 * @package 	includes/disclosure_statement
 * 
 */

if ( !class_exists('WPADC_Disclosure_Statement') ) :

class WPADC_Disclosure_Statement {

	/**
	 * instance
	 *
	 * @access private
	 * @var array
	 */
	private $_instance = null;

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
	 * Get instance
	 *
	 * @access public
	 * @return array
	 */
	public function get_instance() {
		return $this->_instance;
	}

	/**
	 * Get Model
	 *
	 * @access public
	 * @return WPADC_Disclosure_Statement_Model
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

		$this->_hook_prefix = wpadc()->plugin_hook() . 'disclosure_statement/';
		$defaults = $this->default_value();
		$defaults['hook_prefix'] = $this->_hook_prefix;

		$instance = apply_filters( $this->_hook_prefix . 'args' , wp_parse_args( $args, $defaults ) , $args , $defaults );

    	// setup variables
    	$this->_instance = $instance;
    	$this->_model = new WPADC_Disclosure_Statement_Model( $this->_instance );

    }

	/**
	 * default values
	 *
	 * @access public
	 */
	public function default_value() {
		return apply_filters( $this->_hook_prefix . 'default_value' , wpadcb_default_meta() );
	}

	/**
	 * Render
	 *
	 * @access public
	 * @param string $selected Position slug being rendered (before-content/after-content/after_p1/after_p2/after_p3/shortcode/widget).
	 * @param array  $args     Optional overrides:
	 *                          - rule_id (int)       Render this specific rule (verifies post_type === 'wpadc'). 0 = auto-match.
	 *                          - override_text (str) Replace the rule's stored disclosure text.
	 *                          - style_class (str)   Preset class to add (e.g. 'wpadc-preset-banner') — wins over rule's stored preset.
	 *                          - wrapper_id (str)    Legacy [wpadc id="X"] wrapper id.
	 *                          - skip_position_check (bool) When true (shortcode path), bypass position-match logic.
	 */
	public function render( $selected, $args = array() ) {
		global $post, $wp_affiliate_disclosure_fs;
		$view = new WPADC_Disclosure_Statement_View();
		$html = '';

		$args = wp_parse_args( $args, array(
			'rule_id' => 0,
			'override_text' => '',
			'style_class' => '',
			'wrapper_id' => '',
			'skip_position_check' => false,
		) );

		$rules = $this->_model->get_disclosure_rules( absint( $args['rule_id'] ) );

		// Shortcode / explicit-rule path: bypass position + post-type matching;
		// just render the rule with overrides applied. Returns first available.
		if ( ! empty( $args['skip_position_check'] ) ) {
			foreach ( $rules as $rule ) {
				$rule['selected']     = $selected;
				$rule['style_class']  = $args['style_class'];
				$rule['wrapper_id']   = $args['wrapper_id'];
				if ( ! empty( $args['override_text'] ) ) {
					$rule['disclosure_statement'] = $args['override_text'];
				}
				if ( ! empty( $rule['disclosure_statement'] ) ) {
					$html = $view->render_statement( $rule );
					return $html;
				}
			}
			// No rule matched but override_text was supplied → render synthetic.
			if ( ! empty( $args['override_text'] ) ) {
				$synthetic = array(
					'id'                   => 0,
					'disclosure_statement' => $args['override_text'],
					'selected'             => $selected,
					'style_class'          => $args['style_class'],
					'wrapper_id'           => $args['wrapper_id'],
				);
				return $view->render_statement( $synthetic );
			}
			return '';
		}

		foreach ( $rules as $rule ) {

			$rule['selected'] = $selected;
			$rule['style_class'] = $args['style_class'];
			$rule['wrapper_id'] = $args['wrapper_id'];
			if ( ! empty( $args['override_text'] ) ) {
				$rule['disclosure_statement'] = $args['override_text'];
			}
			$statement_position = ( !empty( $rule[ 'statement_position' ] ) ? explode(',',$rule[ 'statement_position' ]) : array() );

			// if position match
			if ( !empty( $statement_position ) && is_array( $statement_position ) && in_array( $selected, $statement_position ) && !empty( $rule['disclosure_statement'] ) ) {
				$post_types = ( !empty( $rule['post_type'] ) ? explode(',',$rule['post_type']) : false );

				if ( $post_types && !empty( $post_types ) && is_array( $post_types ) ) {
					foreach ($post_types as $post_type) {
						
						// if post type match
						if ( !empty( $post_type ) && is_singular( $post_type ) ) {

							// if condition match
							if ( !empty( $rule['condition'] ) && $rule['condition'] == 'ids' ) {
								// selected IDs
								if ( !empty( $rule['ids'] ) ) {
									$IDs = explode(',', $rule['ids'] );
									if ( !empty( $IDs ) && is_array( $IDs ) && in_array( $post->ID, $IDs ) ) {
										if ( empty( $html ) )
											$html = $view->render_statement( $rule );
									}
								} // end - $rule['ids']
							} else if ( !empty( $rule['condition'] ) && $rule['condition'] == 'taxonomy' ) {
								// taxonomy match
								$taxonomies = explode(',', $rule['taxonomies'] );
								if ( !empty( $taxonomies ) && is_array( $taxonomies ) ) {
									$temp_html = '';
									$all_terms = array();
									$post_type_terms = get_object_taxonomies( $post->post_type );
									if ( $post_type_terms && !empty( $post_type_terms ) && is_array( $post_type_terms ) ) {
										foreach ($post_type_terms as $post_type_term) {
											$terms = wp_get_post_terms( $post->ID, $post_type_term );
											if ( $terms && is_array( $terms ) ) {
												foreach ($terms as $term) {
													if ( $term->slug && in_array($term->slug, $taxonomies)  ) {
														$temp_html = $view->render_statement( $rule );
													}
												}

											} // end - $terms
										} // end - $post_type_term
									} // end - $post_type_terms

									if ( empty( $html ) ) {
										$html = $temp_html;
									}
								}
							} else {
								// if no condition
								if ( empty( $html ) )
									$html = $view->render_statement( $rule );
							} // end - condition

							// check advanced condition
							if ( $wp_affiliate_disclosure_fs->is__premium_only() && $wp_affiliate_disclosure_fs->can_use_premium_code() ) {

								if ( !empty( $rule['advanced_filter'] ) && $rule['advanced_filter'] == 'exclude_ids' ) {

									// if current post is in the exclude posts list - empty the html
									if ( !empty( $rule['excludes_ids'] ) ) {
										$excluded_IDs = explode(',', $rule['excludes_ids'] );
										if ( !empty( $excluded_IDs ) && is_array( $excluded_IDs ) && in_array( $post->ID, $excluded_IDs ) ) {
											if ( !empty( $html ) )
												$html = '';
										}
									} // end - $rule['excludes_ids']

								} else if ( !empty( $rule['advanced_filter'] ) && $rule['advanced_filter'] == 'exclude_taxonomy' ) {

									// if current post is in the exclude taxonomy list - empth the html
									$exclude_taxonomies = explode(',', $rule['exclude_taxonomies'] );
									if ( !empty( $exclude_taxonomies ) && is_array( $exclude_taxonomies ) ) {
										$tax_excluded = false;
										$all_terms = array();
										$post_type_terms = get_object_taxonomies( $post->post_type );
										if ( $post_type_terms && !empty( $post_type_terms ) && is_array( $post_type_terms ) ) {
											foreach ($post_type_terms as $post_type_term) {
												$terms = wp_get_post_terms( $post->ID, $post_type_term );
												if ( $terms && is_array( $terms ) ) {
													foreach ($terms as $term) {
														if ( $term->slug && in_array($term->slug, $exclude_taxonomies)  ) {
															$tax_excluded = true;
														}
													}

												} // end - $terms
											} // end - $post_type_term
										} // end - $post_type_terms

										if ( !empty( $html ) && $tax_excluded ) {
											$html = '';
										} // end - $html

									} // end - $exclude_taxonomies

								} // end - $rule['advanced_filter]

							} // end - $wp_affiliate_disclosure_fs

						} // end - $rule['post_type']

					}
				} // end - $post_types

			} // end - $rule[ $selected ]
		} // end $rules

		return $html;
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

} // end - class WPADC_Disclosure_Statement

endif; // end - !class_exists('WPADC_Disclosure_Statement')