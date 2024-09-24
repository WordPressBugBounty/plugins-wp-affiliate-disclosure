<?php
/**
 * Settings Page Model Class
 *
 * @author 		MojofyWP
 * @package 	builder/settings-page
 * 
 */

if ( !class_exists('WPADC_Builder_Settings_Model') ) :

class WPADC_Builder_Settings_Model {

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
		$this->_hook_prefix = wpadc()->plugin_hook() . 'builder_settings/model/';
		
    }

   	/**
	 * Retrieve all items
	 *
	 * @access public
	 * @return array
	 */
	public function get_all_items() {

		$items = array();

		$query = new WP_Query( array(
				'post_type' => 'wpadc',
				'post_status' => 'publish',
				'paged' => 1,
				'posts_per_page' => 9999,
				'meta_key' => wpadc()->plugin_meta_prefix() . 'priority',
				'orderby' => wpadc()->plugin_meta_prefix() . 'priority',
				'order' => 'ASC',
			) );

		if ($query->have_posts()) : 
			while ($query->have_posts()) : $query->the_post();
				$item_id = get_the_ID();
				$priority = wpadcb_get_meta( array( 'id' => $item_id, 'key' => 'priority', 'default' => 1 ) );
				$items[ $item_id ] = array( 'id' => $item_id, 'priority' => intval( $priority ) );
			endwhile; 
		endif;

		wp_reset_postdata();

		// re-arrange items based on priority
		if ( !empty( $items ) ) {
			usort( $items , array(&$this, 'sort_by_priority') );
		}

		return apply_filters( $this->_hook_prefix . 'get_all_items' , $items , $this );
	}

	/**
	 * sort by priority
	 *
	 * @access public
	 * @return string
	 */
	public function sort_by_priority( $a, $b ) {
		if ($a['priority'] == $b['priority']) {
            return 0;
    	}
    	return ($a['priority'] < $b['priority']) ? -1 : 1;
	}

	/**
	 * Retrieve total items
	 *
	 * @access public
	 * @return array
	 */
	public function get_total_items( $per_page = 9999 ) {

		$items = get_posts( array(
				'post_type' => 'wpadc',
				'post_status' => 'publish',
				'paged' => 1,
				'posts_per_page' => $per_page,
			) );

		return apply_filters( $this->_hook_prefix . 'get_total_items' , ( $items && !empty( $items ) ? count( $items ) : 0 ) , $this );
	}

	/**
	 * Get Values
	 *
	 * @access public
	 * @return array
	 */
	public function get_values( $item_id = 0 ) {

		$values = array();

		// Get title
		$values['title'] = get_the_title( $item_id );

		// Get meta values
		$default = wpadcb_default_meta();
		$numbers_meta = wpadcb_numbers_meta();
		$selectables_meta = wpadcb_selectable_meta();

		if ( !empty( $default ) && is_array( $default ) ) {
			foreach ( $default as $key => $d_value ) {
				if ( in_array( $key, $numbers_meta ) ) {
					$values[ $key ] = wpadcb_get_meta( array( 'id' => $item_id , 'key' => $key , 'default' => $d_value , 'esc' => null ) );
				} else if ( in_array( $key, $selectables_meta ) ) {
					$values[ $key ] = wpadcb_get_meta( array( 'id' => $item_id , 'key' => $key , 'default' => $d_value , 'esc' => null ) );
					// use default value if it's empty
					if ( isset( $values[ $key ] ) && empty( $values[ $key ] ) )
						$values[ $key ] = $d_value;
				} else {
					$values[ $key ] = wpadcb_get_meta( array( 'id' => $item_id , 'key' => $key , 'default' => $d_value , 'esc' => 'attr' ) );
				}
			}
		}

		return apply_filters( $this->_hook_prefix . 'get_values' , $values , $item_id , $this );
	}

	/**
	 * Create new
	 *
	 * @access public
	 */
	public function create_new( $values = array() ) {

		$id = null;
		$meta_prefix = wpadc()->plugin_meta_prefix();

		if ( isset( $values['title'] ) ) {

			$id = wp_insert_post( array(
					'post_title' => esc_attr( $values['title'] ),
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

						if ( $is_checkbox ) {
							if ( !empty( $d_value ) && $d_value == 'on' ) {
								update_post_meta( $id , $meta_prefix . $key , 'on' );
							} else {
								update_post_meta( $id , $meta_prefix . $key , 0 );
							}
						} else {
							update_post_meta( $id , $meta_prefix . $key , $d_value );
						} // end - is_checkbox
					} // end - foreach
				} // end -$default

			}

		} // end - $values[title]

		return apply_filters( $this->_hook_prefix . 'update_settings' , $id , $values , $this );
	}

	/**
	 * update settings
	 *
	 * @access public
	 */
	public function update_settings( $id = 0 , $values = array() ) {

		$status = true;
		$meta_prefix = wpadc()->plugin_meta_prefix();

		// Get type
		$default = wpadcb_default_meta();
		$checkboxes = wpadcb_checkbox_meta();

		// update meta
		if ( !empty( $default ) && is_array( $default ) ) {
			foreach ( $default as $key => $d_value ) {
				$is_checkbox = ( !empty( $checkboxes ) && in_array( $key , $checkboxes ) ? true : false );

				if ( $is_checkbox ) {
					if ( !empty( $values[ $key ] ) && $values[ $key ] == 'on' ) {
						update_post_meta( $id , $meta_prefix . $key , 'on' );
					} else {
						update_post_meta( $id , $meta_prefix . $key , 0 );
					}
				} else {
					if ( isset( $values[ $key ] ) ) {
						update_post_meta( $id , $meta_prefix . $key , $values[ $key ] );
					} else {
						update_post_meta( $id , $meta_prefix . $key , '' );
					}
				} // end - is_checkbox
			}
		}

		// update title 
		if ( isset( $values['title'] ) ) {
			$post_title = esc_attr( $values['title'] );
			wp_update_post( array( 'ID' => $id , 'post_title' => $post_title ) );
		}

		return apply_filters( $this->_hook_prefix . 'update_settings' , $status , $id , $values , $this );
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

} // end - class WPADC_Builder_Settings_Model

endif; // end - !class_exists('WPADC_Builder_Settings_Model')