<?php

global  $wp_affiliate_disclosure_fs ;
/**
 * step 4 layout
 *
 * @author 		MojofyWP
 * @package 	builder/start-wizard/views
 * 
 */
?>
<div class="wpadcb-startw-step-wrapper">
    
    <h2 class="wpadcb-startw-step-heading"><?php 
echo  esc_html__( 'Where to Display Your Disclosure Statement.', WPADC_SLUG ) ;
?></h2>

    <div class="wpadcb-startw-step-desc">
        <?php 
echo  esc_html__( "Please select one (or multiple) location to display your affiliate disclosure statement.", WPADC_SLUG ) ;
?>
    </div><!-- .wpadcb-startw-step-desc -->

    <form id="wpadcb-startw-step-form" class="wpadcb-startw-step-form" method="post">

        <div class="wpadcb-form-control wpadcb-input-type-multioptselector">
            <div class="wpadcb-multioptselector-options">
                <button class="wpadcb-multioptselector-btn<?php 
echo  $this->multi_option_selected( $values, 'statement_position', 'before-content' ) ;
?>" data-multioptselector-value="before-content"><?php 
_e( 'Before Post Content', WPADC_SLUG );
?></button>
                <button class="wpadcb-multioptselector-btn<?php 
echo  $this->multi_option_selected( $values, 'statement_position', 'after-content' ) ;
?>" data-multioptselector-value="after-content"><?php 
_e( 'After Post Content', WPADC_SLUG );
?></button>
            <?php 
?>
            </div>
            <div class="wpadcb-form-desc"><em><?php 
_e( '*selection of multiple options is allowed', WPADC_SLUG );
?></em></div>
            <input<?php 
echo  $this->attributes( 'statement_position' ) ;
?> type="hidden" value="<?php 
echo  $this->get_value( 'statement_position', $values ) ;
?>" class="wpadcb-multioptselector-input" />
        </div><!-- .wpadcb-form-control -->

        <!-- Hidden field -->
        <input type="hidden" name="wpadcb_startw_step" value="<?php 
echo  $current_step ;
?>" />
        <?php 
wp_nonce_field( 'wpadcb_start_wizard', '_wpadcb_start_wizard_nonce' );
?>

    </form><!-- .wpadcb-startw-step-form -->

    <div class="wpadcb-startw-step-actions">
        <button class="wpadcb-button-passive wpadcb-startw-action" data-action-type="prev"><i class="fa fa-long-arrow-left"></i><?php 
echo  esc_html__( 'Previous', WPADC_SLUG ) ;
?></button>
        <button class="wpadcb-button-info right-icon wpadcb-startw-action" data-action-type="next"><?php 
echo  esc_html__( 'Next', WPADC_SLUG ) ;
?><i class="fa fa-long-arrow-right"></i></button>
    </div><!-- .wpadcb-startw-step-actions -->

</div><!-- .wpadcb-startw-step-wrapper -->

