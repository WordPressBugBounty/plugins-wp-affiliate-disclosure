<?php global $wp_affiliate_disclosure_fs;
/**
 * step 1 layout
 *
 * @author 		MojofyWP
 * @package 	builder/start-wizard/views
 * 
 */

?>
<div class="wpadcb-startw-step-wrapper">
    
    <h2 class="wpadcb-startw-step-heading"><?php echo esc_html__( 'Configure WP Affiliate Disclosure in a few steps', WPADC_SLUG ); ?></h2>

    <div class="wpadcb-startw-step-desc">
        <?php echo esc_html__( "Welcome to WP Affiliate Disclosure configuration wizard. In a few steps, we'll help you setup your affiliate disclosure statement, and show it across your website.", WPADC_SLUG ); ?>
    </div><!-- .wpadcb-startw-step-desc -->

    <form id="wpadcb-startw-step-form" class="wpadcb-startw-step-form" method="post">

        <!-- Hidden field -->
        <input type="hidden" name="wpadcb_startw_step" value="<?php echo $current_step; ?>" />
        <?php 
            wp_nonce_field( 'wpadcb_start_wizard' , '_wpadcb_start_wizard_nonce' ); 
        ?>

    </form><!-- .wpadcb-startw-step-form -->

    <div class="wpadcb-startw-step-actions text-right">
        <button class="wpadcb-button-info right-icon wpadcb-startw-action" data-action-type="next"><?php echo esc_html__( "Let's Get Started", WPADC_SLUG ); ?><i class="fa fa-long-arrow-right"></i></button>
    </div><!-- .wpadcb-startw-step-actions -->

</div><!-- .wpadcb-startw-step-wrapper -->
