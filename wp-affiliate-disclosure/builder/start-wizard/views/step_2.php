<?php global $wp_affiliate_disclosure_fs;
/**
 * step 2 layout
 *
 * @author 		MojofyWP
 * @package 	builder/start-wizard/views
 * 
 */

?>
<div class="wpadcb-startw-step-wrapper">
    
    <h2 class="wpadcb-startw-step-heading"><?php echo esc_html__( 'Name Your Rule', WPADC_SLUG ); ?></h2>

    <div class="wpadcb-startw-step-desc">
        <?php echo esc_html__( "Give your rule a name - this is for your reference only.", WPADC_SLUG ); ?>
    </div><!-- .wpadcb-startw-step-desc -->

    <form id="wpadcb-startw-step-form" class="wpadcb-startw-step-form" method="post">

        <div class="wpadcb-form-control">
            <input<?php echo $this->attributes( 'rule_name' ); ?> type="text" class="wpadcb-input-text" value="<?php echo $this->get_value( 'rule_name' , $values ); ?>">
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
