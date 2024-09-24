<?php global $wp_affiliate_disclosure_fs;
/**
 * step 3 layout
 *
 * @author 		MojofyWP
 * @package 	builder/start-wizard/views
 * 
 */

?>
<div class="wpadcb-startw-step-wrapper">
    
    <h2 class="wpadcb-startw-step-heading"><?php echo esc_html__( 'Insert Your Disclosure Statement', WPADC_SLUG ); ?></h2>

    <div class="wpadcb-startw-step-desc">
        <?php echo esc_html__( "You can add links, images, as well as HTML elements into the disclosure statement", WPADC_SLUG ); ?>
    </div><!-- .wpadcb-startw-step-desc -->

    <form id="wpadcb-startw-step-form" class="wpadcb-startw-step-form" method="post">

        <div class="wpadcb-form-control wpadcb-has-editor">
            <?php 
                wp_editor( $values['disclosure_statement'], $this->input_id( 'disclosure_statement' ), array(
                        'textarea_name' => $this->input_name( 'disclosure_statement' ),
                        'textarea_rows' => 6
                    ) ); 
            ?> 
        </div><!-- .wpadcb-form-control -->

        <!-- Hidden field -->
        <input type="hidden" name="wpadcb_startw_step" value="<?php echo $current_step; ?>" />
        <?php 
            wp_nonce_field( 'wpadcb_start_wizard' , '_wpadcb_start_wizard_nonce' ); 
        ?>

    </form><!-- .wpadcb-startw-step-form -->

    <div class="wpadcb-startw-step-actions">
        <button class="wpadcb-button-passive wpadcb-startw-action" data-action-type="prev"><i class="fa fa-long-arrow-left"></i><?php echo esc_html__( 'Previous', WPADC_SLUG ); ?></button>
        <button class="wpadcb-button-info right-icon wpadcb-startw-action" data-action-type="next"><?php echo esc_html__( 'Next', WPADC_SLUG ); ?><i class="fa fa-long-arrow-right"></i></button>
    </div><!-- .wpadcb-startw-step-actions -->

</div><!-- .wpadcb-startw-step-wrapper -->

