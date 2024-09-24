<?php
/**
 * Settings Page View Class
 *
 * @author 		MojofyWP
 * @package 	builder/settings-page
 * 
 */

if ( !class_exists('WPADC_Builder_Settings_View') ) :

class WPADC_Builder_Settings_View {

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
		$this->_hook_prefix = wpadc()->plugin_hook() . 'builder_settings/view/';


    }

	/**
	 * sample render
	 *
	 * @access public
	 */
	public function render_header( $subtitle = '' ) {

		ob_start();
		?>
		<h1 class="wpadc-builder-title">
			<?php if ( !empty( $subtitle ) ) : ?>
				<span class="subtitle"><?php echo esc_attr( $subtitle ); ?></span>
			<?php endif; ?>
		</h1>
		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'render_header' , ( !empty( $html ) ? $html : '' ) , $subtitle , $this );
	}


	/**
	 * Render Overview
	 *
	 * @access public
	 */
	public function render_overview( $args = array() ) {
		ob_start();
		?>
		<div class="wpadcb-create-item">
			<button 
				class="wpadcb-button-success wpadcb-new-item"
				data-action-nonce="<?php echo wp_create_nonce("wpdcb_user_action"); ?>"	
			><i class="fa fa-plus-circle"></i><?php _e( 'Add New Rule' , WPADC_SLUG ); ?></button>
		</div>
		<div class="wpadcb-items-wrapper">
			<?php echo $this->render_overview_table( $args ); ?>
		</div>
		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'render_overview' , ( !empty( $html ) ? $html : '' ) , $args , $this );
	}

	/**
	 * Render Overview Table
	 *
	 * @access public
	 */
	public function render_overview_table( $args = array() ) {

		$defaults = array(
			'items' => array(),
		);

		$instance = wp_parse_args( $args, $defaults );
		extract( $instance );	

		ob_start();
		?>
		<table id="wpadcb-items-list" class="wpadcb-table">
			<thead>
				<td class="wpadcb-trow-name"><?php _e( 'Name' , WPADC_SLUG ); ?></td>
				<td class="wpadcb-trow-shortcode"><?php _e( 'Priority' , WPADC_SLUG ); ?></td>
				<td class="wpadcb-trow-actions"><?php _e( 'Actions' , WPADC_SLUG ); ?></td>
			</thead>
			<tbody>
			<?php if ( !empty( $items ) ) : ?>
				<?php foreach ( $items as $item ) { 
					$priority = wpadcb_get_meta( array( 'id' => $item['id'], 'key' => 'priority', 'default' => '1' ) );
					?>
					<tr>
						<td class="wpadcb-trow-name"><?php echo get_the_title( $item['id'] ); ?></td>
						<td class="wpadcb-trow-priority">
							<?php echo $priority; ?>
						</td>
						<td class="wpadcb-trow-actions">
							<a href="<?php echo wpadcb_options_page_url( array( 'view' => 'edit' , 'id' => $item['id'] ) ); ?>" class="wpadcb-button-info"><i class="fa fa-edit"></i><?php _e( 'Edit' , WPADC_SLUG ); ?></a>
							<button class="wpadcb-button-danger wpadcb-open-selectedmodal" data-modal-id="#wpadcb-delete-confirm-<?php echo $item['id']; ?>" data-modal-type="small"><i class="fa fa-trash-o"></i><?php _e( 'Delete' , WPADC_SLUG ); ?></button>
							<?php echo $this->delete_confirmation( $item['id'] ); ?>
						</td>
					</tr>
				<?php } // end - foreach ?>
			<?php else : ?>
				<tr>
					<td colspan="3"><?php _e( 'No rule(s) added yet.' , WPADC_SLUG ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table><!-- .wpadcb-table -->
		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'render_overview' , ( !empty( $html ) ? $html : '' ) , $args , $this );
	}

	/**
	 * Render Edit
	 *
	 * @access public
	 */
	public function render_edit( $args = array() ) {

		$defaults = array(
			'id' => 0,
			'values' => array(),
		);

		$instance = wp_parse_args( $args, $defaults );
		extract( $instance );	

		ob_start();
		?>
		<div class="wpadcb-edit-wrapper">
			<div class="wpadcb-back-overview">
				<a href="<?php echo wpadcb_options_page_url(); ?>" class="wpadcb-button-success"><i class="fa fa-angle-double-left"></i><?php _e( 'Go back' , WPADC_SLUG ); ?></a>
			</div>
			<form id="wpadcb-edit-form-<?php echo $id; ?>" class="wpadcb-edit-form" method="post">

				<?php
					// load form
					try {
						require_once wpadc()->plugin_path( 'builder/settings-page/views/form.php' );
					} catch ( Exception $e ){
						echo '<br><br>View Form Error';			
					}
				?>

				<!-- Hidden field -->
				<input type="hidden" name="wpadcb_item_id" value="<?php echo $id; ?>" />
				<input type="hidden" name="action" value="wpadcb-update-settings" />
				<?php 
					wp_nonce_field( 'wpadcb_update_settings' , '_wpadcb_update_settings_nonce' ); 
				?>
				<?php 
					wp_nonce_field( 'wpdcb_user_action' , '_wpdcb_user_action_nonce' ); 
				?>

			</form><!-- .wpadcb-edit-form -->
		</div><!-- .wpadcb-edit-wrapper -->
		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'render_edit' , ( !empty( $html ) ? $html : '' ) , $args , $this );
	}

	/**
	 * render wizard message
	 *
	 * @access public
	 */
	public function render_wizard_message() {
		ob_start();
		?>
		<div style="padding: 20px;">
			<div class="wpadcb-message-info">
				<div class="wpadcb-message-icon"><i class="fa fa-exclamation-circle"></i></div>
				<div class="wpadcb-message-excerpt">
					<p><?php echo esc_html__( "Need Help? " , WPADC_SLUG ); ?><a href="<?php echo wpadcb_options_page_url( array( 'view' => 'getting-started' ) ); ?>" class="wpadcb-startw-action" data-action-type="reset" data-action-nonce="<?php echo wp_create_nonce( 'wpadcb_start_wizard' ); ?>"><?php echo esc_html__( "Click here to start the configuration wizard." , WPADC_SLUG ); ?></a></p>
				</div>
			</div><!-- .wpadcb-message-info -->
		</div>
		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'render_wizard_message' , ( !empty( $html ) ? $html : '' ) , $args , $this );
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
		return apply_filters( $this->_hook_prefix . 'input_name' , ( !empty( $name ) ? 'wpadcb_item[' . $name . ']' : '' ) , $name , $this );
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
	 * Add Modal Container
	 *
	 * @access public
	 */
	public function modal_container() {
		ob_start();
		?>
		<div id="wpadcb-main-modal" class="zoom-anim-dialog mfp-hide"></div>
		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'modal_container' , ( !empty( $html ) ? $html : '' ) , $this );
	}

	/**
	 * Add new wizard
	 *
	 * @access public
	 */
	public function add_new_wizard() {

		ob_start();
		?>
		<div class="wpadcb-addnew-container">
			<h2 class="wpadcb-modal-headline"><?php _e( 'Add New Rule' , WPADC_SLUG ); ?></h2>

			<div class="wpadcb-addnew-fields">
				<form class="wpadcb-addnew-form" method="post">
					<div class="wpadcb-form-section">
						<div class="wpadcb-form-control">
							<label class="wpadcb-input-label" for="wpadcb-new-item-title"><?php _e( 'Enter a name below' , WPADC_SLUG ); ?></label>
							<input id="wpadcb-new-item-title" name="wpadcb_new_item[title]" type="text" class="wpadcb-input-text" value="" placeholder="<?php _e( '( e.g. Apple Orange Banana )' , WPADC_SLUG ); ?>">
						</div><!-- .wpadcb-form-control -->
					</div><!-- .wpadcb-form-section -->
					<input type="hidden" name="action" value="wpadcb-create-new" />
					<?php wp_nonce_field( 'wpadcb_create_item' , '_wpadcb_create_item_nonce' ); ?>
					<?php wp_nonce_field( 'wpdcb_user_action' , '_wpdcb_user_action_nonce' ); ?>
				</form><!-- .wpadcb-addnew-form -->
			</div><!-- .wpadcb-addnew-fields -->

			<div class="wpadcb-modal-actions">
				<button class="wpadcb-modal-cancel-btn wpadcb-close-modal"><?php _e( 'Cancel' , WPADC_SLUG ); ?></button>
				<button class="wpadcb-create-item wpadcb-modal-action-primary"><?php _e( 'Create' , WPADC_SLUG ); ?></button>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'add_new_wizard' , ( !empty( $html ) ? $html : '' ) , $this );
	}

	/**
	 * Render delete confirmation modal
	 *
	 * @access public
	 */
	public function delete_confirmation( $id = '' ) {

		ob_start();
		?>
		<div id="wpadcb-delete-confirm-<?php echo $id; ?>" class="wpadcb-delete-confirm zoom-anim-dialog mfp-hide">
			<div class="wpadcb-modal-container">
				<div class="wpadcb-modal-delete">
					<h3 class="wpadcb-modal-headline"><?php echo sprintf( __( 'Are you sure you want to delete "%s"?' , WPADC_SLUG ) , get_the_title( $id ) ); ?></h3>
					<h4 class="wpadcb-modal-subheadline"><?php _e( 'You will not be able to recover it' , WPADC_SLUG ); ?></h4>
				</div><!-- .wpadcb-modal-delete -->
				<div class="wpadcb-modal-actions">
					<button class="wpadcb-modal-cancel-btn wpadcb-close-selectedmodal" data-modal-id="wpadcb-delete-confirm-<?php echo $id; ?>"><?php _e( 'Cancel' , WPADC_SLUG ); ?></button>
					<button class="wpadcb-delete-item wpadcb-modal-action-danger" 
						data-item-id="<?php echo $id; ?>"
						data-action-nonce="<?php echo wp_create_nonce("wpdcb_user_action"); ?>"
						><?php _e( 'Delete' , WPADC_SLUG ); ?></button>
				</div>
			</div><!-- .wpadcb-modal-container -->
		</div><!-- #wpadcb-delete-confirm- -->
		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'delete_confirmation' , ( !empty( $html ) ? $html : '' ) , $id , $this );
	}

	/**
	 * render upgrade now message
	 *
	 * @access public
	 */
	public function render_upgrade_msg( $args = array() ) {

		$defaults = array(
			'sample' => true,
		);

		$instance = wp_parse_args( $args, $defaults );
		extract( $instance );	

		ob_start();
		?>
		<div class="wpadcb-upgrade-modal">
			<h3 class="wpadcb-upgrade-heading"><?php _e( 'You reached the limits of your plan!', WPADC_SLUG ); ?></h3>
			<div class="wpadcb-upgrade-message">
				<?php echo sprintf( __( 'On the %s plan, you can only have one rule active at a time. To create more rules, please consider to upgrade your plan to %s.' , WPADC_SLUG ), '<strong>'.__( 'Free', WPADC_SLUG ).'</strong>', '<strong>'.__( 'Premium', WPADC_SLUG ).'</strong>' ); ?>
			</div>
			<div>
				<a href="<?php echo admin_url( 'admin.php?page=wpadc-builder-pricing' ); ?>" class="wpadcb-upgrade-button"><?php _e( 'Upgrade Now', WPADC_SLUG ); ?></a>
			</div>
			<a href="#cancel" class="wpadcb-upgrade-cancel wpadcb-close-modal"><?php _e( "Nah, I'll stick with just one rule for now", WPADC_SLUG ); ?></a>
		</div>
		<?php
		$html = ob_get_clean();

		return apply_filters( $this->_hook_prefix . 'render_upgrade_msg' , ( !empty( $html ) ? $html : '' ) , $args , $this );
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

} // end - class WPADC_Builder_Settings_View

endif; // end - !class_exists('WPADC_Builder_Settings_View')