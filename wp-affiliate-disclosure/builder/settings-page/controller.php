<?php
/**
 * Settings Page Controller Class
 *
 * @author 		MojofyWP
 * @package 	builder/settings-page
 * 
 */

if ( !class_exists('WPADC_Builder_Settings') ) :

class WPADC_Builder_Settings {

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
			self::$_instance = new WPADC_Builder_Settings();

		return self::$_instance;
	}

	/**
	 * Get Model
	 *
	 * @access public
	 * @return WPADC_Builder_Settings_Model
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

		$this->_hook_prefix = wpadc()->plugin_hook() . 'builder_settings/';
    	$this->_model 		= new WPADC_Builder_Settings_Model();

    	add_action( 'admin_menu', array(&$this, 'add_settings_page') , 15 );

    	// add new wizard
    	add_action( 'wp_ajax_wpadcb-add-new', array(&$this, 'add_new') );
    	add_action( 'wp_ajax_wpadcb-create-new', array(&$this, 'create_new') );

    	// remove item
		add_action( 'wp_ajax_wpadcb-delete-item', array(&$this, 'delete_item') );
		
    	// update overview list ajax listener
    	add_action( 'wp_ajax_wpadcb-update-overview', array(&$this, 'update_overview') );

		// update settings ajax listener
		add_action( 'wp_ajax_wpadcb-update-settings', array(&$this, 'update_settings') );

		// trigger start wizard actions
		add_action( 'wp_ajax_wpadcb-startw-submit', array(&$this, 'trigger_wizard_action') );
		add_action( 'wp_ajax_wpadcb-startw-back', array(&$this, 'trigger_wizard_action') );
		add_action( 'wp_ajax_wpadcb-startw-close', array(&$this, 'trigger_wizard_action') );
		add_action( 'wp_ajax_wpadcb-startw-reset', array(&$this, 'trigger_wizard_action') );
    }

   	/**
	 * Add Settings Page
	 *
	 * @access public
	 */
	public function add_settings_page() {

		add_menu_page( 		        	
			__( 'WP Affiliate Disclosure' , WPADC_SLUG ) , // page_title
			__( 'WP Affiliate Disclosure' , WPADC_SLUG ) , // menu_title
			'manage_options' , // capability
			'wpadc-builder', // menu_slug
			array(&$this, 'render_settings_page'), // callback function
			'dashicons-format-aside' // icon
		);
	}

	/**
	 * Render Settings Page
	 *
	 * @access public
	 * @return string
	 */
	public function render_settings_page() {

		if ( ! current_user_can( 'manage_options' ) )
			wp_die( __( 'Cheatin&#8217; uh?' ) );

		$view = new WPADC_Builder_Settings_View();

		$page = ( isset( $_GET['view'] ) ? esc_attr( $_GET['view'] ) : 'overview' );
		$reset_wizard = ( $page == 'getting-started' ? true : false ); // reset wizard process if the page is called manually

		// override if is to run start wizard
		if ( wpadcb_run_start_wizard() ) {
			$page = 'getting-started';
		} else {
			// if skipped, but pointed to getting-started - then redirect back to overview
			if ( $reset_wizard )
				$page = 'overview';
		} // end - run_start_wizard

		switch ( $page ) {
			case 'overview':
			default:
				$subtitle = __( 'Overview' , WPADC_SLUG );
				break;

			case 'getting-started':
				$subtitle = __( 'WP Affiliate Disclosure - Configuration Wizard' , WPADC_SLUG );
				break;

			case 'edit':
				$subtitle = __( 'Edit Rule' , WPADC_SLUG );
				break;
		}

		ob_start();
		?>
		<div id="wpadc-builder">
			<div class="wpadcb-logo-wrapper">
				<img src="<?php echo wpadc()->plugin_url('assets/img/logo.png'); ?>" class="wpadcb-logo" />
				<span><?php echo 'v' . WPADC_VERSION; ?></span>
			</div>
			<div class="wpadcb-main-wrapper">
				<?php echo $view->render_header( $subtitle ); ?>
				<div class="wpadcb-main-container">
				<?php
					switch ( $page ) {
						case 'overview':
						default:
							$items = $this->_model->get_all_items();
							echo $view->render_overview( array(
									'items' => $items
								) );
							
							// display wizard message
							if ( !wpadcb_run_start_wizard() && $this->_model->get_total_items(1) < 1 ) {
								echo $view->render_wizard_message();
							}
							break;
					
						case 'edit':
							$c_id = ( isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0 );
							echo $view->render_edit( array(
									'id' => $c_id,
									'values' => $this->_model->get_values( $c_id ),
								) );
							break;

						case 'getting-started':
							$start_wizard = WPADC_Start_Wizard::get_instance();
							echo $start_wizard->render();
							break;
					}
				?>
				</div><!-- .wpadcb-main-container -->
			</div><!-- .wpadcb-main-wrapper -->
		</div><!-- #wpadc-builder -->
		<?php echo $view->modal_container(); ?>
		<?php
		$html = ob_get_clean();

		echo apply_filters( $this->_hook_prefix . 'render_settings_page' , ( !empty( $html ) ? $html : '' ) , $this );
	}

	/**
	 * Add New Wizard
	 *
	 * @access public
	 * @return string
	 */
	public function add_new() {
		global $wp_affiliate_disclosure_fs;

		// check if user can perform this action
		$this->check_capability();

		$view = new WPADC_Builder_Settings_View();
		if ( $wp_affiliate_disclosure_fs->can_use_premium_code() ) {
			$output = $view->add_new_wizard();
		} else {
			$totals = $this->_model->get_total_items();
			$output = ( $totals > 0 ? $view->render_upgrade_msg() : $view->add_new_wizard() );
		}
		
      	if (defined('DOING_AJAX') && DOING_AJAX) {
        	echo apply_filters( $this->_hook_prefix . 'add_new' , $output , $this );
			exit;
      	} // end - DOING_AJAX

	}

	/**
	 * Create new
	 *
	 * @access public
	 * @return string
	 */
	public function create_new() {
		global $wp_affiliate_disclosure_fs;

		// check if user can perform this action
		$this->check_capability();

		$status = false;
		$id = 0;
		$values = ( isset( $_POST['wpadcb_new_item'] ) ? $_POST['wpadcb_new_item'] : array() );
		$totals = $this->_model->get_total_items();

		if ( $wp_affiliate_disclosure_fs->can_use_premium_code() ) {
			if ( isset( $_POST['_wpadcb_create_item_nonce'] ) && wp_verify_nonce( $_POST['_wpadcb_create_item_nonce'] , 'wpadcb_create_item' ) && !empty( $values ) ) {
				$id = $this->_model->create_new( $values );	
				if ( !empty( $id ) )
					$status = true;
			}	
		} else {
			if ( $totals < 1 ) {
				if ( isset( $_POST['_wpadcb_create_item_nonce'] ) && wp_verify_nonce( $_POST['_wpadcb_create_item_nonce'] , 'wpadcb_create_item' ) && !empty( $values ) ) {
					$id = $this->_model->create_new( $values );	
					if ( !empty( $id ) )
						$status = true;
				}
			} // end - $totals
		} // end - $wp_affiliate_disclosure_fs
		
		if ( $status ) {
			$output = 'success_|_';
			$output .= apply_filters( $this->_hook_prefix . 'create_new/success_msg' , '
				<div class="wpadcb-addnew-container">
					<h2 class="wpadcb-modal-headline">'.sprintf( __( '"%s" has been successfully created!' , WPADC_SLUG ) , get_the_title( $id ) ).'<span class="subheadline">'. __( 'Click the "Edit" button below to edit your item' , WPADC_SLUG ).'</span></h2>
					<div class="wpadcb-modal-actions">
						<a href="#close" class="wpadcb-modal-cancel-btn wpadcb-close-modal">'.__( 'Close' , WPADC_SLUG ).'</a>
						<a href="' . wpadcb_options_page_url( array( 'view' => 'edit' , 'id' => $id ) ) . '" class="wpadcb-modal-action-success">'.__( 'Edit' , WPADC_SLUG ).'</a>
					</div>
				</div>
				' , $id );
		} else {
			$output = 'error_|_';
			if ( empty( $_POST['wpadcb_new_item']['title'] ) ) {
				$output .= apply_filters( $this->_hook_prefix . 'create_new/error_msg' , __( 'Please insert a name.' , WPADC_SLUG ) );
			} else if ( $totals > 0 ) {
				$view = new WPADC_Builder_Settings_View();
				$output = 'success_|_';
				$output .= $view->render_upgrade_msg();
			} else {
				$output .= apply_filters( $this->_hook_prefix . 'create_new/error_msg' , __( 'Something just went wrong! Please try again.' , WPADC_SLUG ) );
			}	
		}

      	if (defined('DOING_AJAX') && DOING_AJAX) {
        	echo apply_filters( $this->_hook_prefix . 'create_new' , $output , $id , $this );
			exit;
      	} // end - DOING_AJAX

	}

	/**
	 * Update settings via ajax
	 *
	 * @access public
	 * @return string
	 */
	public function update_settings() {

		// check if user can perform this action
		$this->check_capability();

		$status = false;
		$output = '';
		$id = ( isset( $_POST['wpadcb_item_id'] ) ? intval( $_POST['wpadcb_item_id'] ) : 0 );
		$values = ( isset( $_POST['wpadcb_item'] ) ? $_POST['wpadcb_item'] : array() );

		if ( isset( $_POST['_wpadcb_update_settings_nonce'] ) && !empty( $_POST['_wpadcb_update_settings_nonce'] ) && wp_verify_nonce( $_POST['_wpadcb_update_settings_nonce'] , 'wpadcb_update_settings' ) && !empty( $values ) && !empty( $id ) ) {
			$status = $this->_model->update_settings( $id , $values );	
		}	

		if ( $status ) {
			$output .= 'success_|_';
			$output .= apply_filters( $this->_hook_prefix . 'update_settings/success_msg' , '
					<i class="fa fa-check-square-o"></i><span class="wpadcb-modal-msg">'.__( 'Settings Saved!' , WPADC_SLUG ).'</span>' , WPADC_SLUG , $id );
		} else {
			$output .= 'error_|_';
			$output .= apply_filters( $this->_hook_prefix . 'update_settings/error_msg' , __( 'Something just went wrong! Please try again.' , WPADC_SLUG ) , $id );
		}

      	if (defined('DOING_AJAX') && DOING_AJAX) {
        	echo apply_filters( $this->_hook_prefix . 'update_settings' , $output , $this );
			exit;
      	} // end - DOING_AJAX
	}

	/**
	 * Remove item
	 *
	 * @access public
	 * @return string
	 */
	public function delete_item() {

		// check if user can perform this action
		$this->check_capability();

		$status = false;
		
		if ( isset( $_POST['id'] ) ) {
			$c_id = intval( $_POST['id'] );
			$post = get_post( $c_id );

			if ( $post->post_type == 'wpadc' ) {
				$status = wp_delete_post( $c_id, true );
			}

		} // end - $_POST['id']

      	if (defined('DOING_AJAX') && DOING_AJAX) {
        	echo apply_filters( $this->_hook_prefix . 'delete_item' , ( $status ? 'success' : 'error' ) , $this );
			exit;
      	} // end - DOING_AJAX

	}

	/**
	 * update overview table via ajax
	 *
	 * @access public
	 * @return string
	 */
	public function update_overview() {
		$items = $this->_model->get_all_items();
		$view = new WPADC_Builder_Settings_View();
		if (defined('DOING_AJAX') && DOING_AJAX) {
			echo apply_filters( $this->_hook_prefix . 'update_overview' , $view->render_overview_table( array(
				'items' => $items
			) ) . ( !wpadcb_run_start_wizard() && $this->_model->get_total_items(1) < 1 ? $view->render_wizard_message() : '' ) , $this );
			exit;
		} // end - DOING_AJAX
	}

	/**
	 * trigger start wizard action
	 *
	 * @access public
	 */
	public function trigger_wizard_action() {
		if ( isset( $_POST['action'] ) ) {
			if (defined('DOING_AJAX') && DOING_AJAX) {
				$start_wizard = WPADC_Start_Wizard::get_instance();
				switch( $_POST['action'] ) {
					case 'wpadcb-startw-back':
						$start_wizard->wizard_back();
						break;
					case 'wpadcb-startw-submit':
						$start_wizard->wizard_submit();
						break;
					case 'wpadcb-startw-close':
						$start_wizard->wizard_close();
						break;
					case 'wpadcb-startw-reset':
						wpadcb_reset_start_wizard_data();
						break;
				}
				exit;
			} // end - DOING_AJAX
		} // end - $_POST['action']
	}	

	/**
	 * make sure only user with manage_options capability can perform actions
	 *
	 * @access public
	 * @return string
	 */
	public function check_capability() {

		$invalid = false;

		if ( !current_user_can( 'manage_options' ) ) {
			$invalid = true;
		}

		// do nonce check
		if ( !( isset( $_POST['_wpdcb_user_action_nonce'] ) && !empty( $_POST['_wpdcb_user_action_nonce'] ) ) || 
			! wp_verify_nonce( $_POST['_wpdcb_user_action_nonce'], 'wpdcb_user_action' ) ) {
			$invalid = true;
		}

		if ( $invalid ) {
			if (defined('DOING_AJAX') && DOING_AJAX) {
				echo 'error_|_' . apply_filters( $this->_hook_prefix . 'check_capability/error_msg' , __( 'You do not have sufficient permissions to perform this action.' , WPADC_SLUG ) , $this );
				exit;
			} // end - DOING_AJAX
		}

		// custom action hook
		do_action( $this->_hook_prefix . 'check_capability' , true , $this );
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

} // end - class WPADC_Builder_Settings

WPADC_Builder_Settings::get_instance();

endif; // end - !class_exists('WPADC_Builder_Settings')