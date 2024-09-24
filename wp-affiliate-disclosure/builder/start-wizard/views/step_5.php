<?php

global  $wp_affiliate_disclosure_fs ;
/**
 * step 5 layout
 *
 * @author 		MojofyWP
 * @package 	builder/start-wizard/views
 * 
 */
$post_types = get_post_types( array(
    'public'             => true,
    'publicly_queryable' => true,
), 'objects', 'or' );
?>
<div class="wpadcb-startw-step-wrapper">
    
    <h2 class="wpadcb-startw-step-heading"><?php 
echo  esc_html__( 'Select a Post Type', WPADC_SLUG ) ;
?></h2>

    <div class="wpadcb-startw-step-desc">
        <?php 
echo  esc_html__( "Please select which post to show your disclosure statement.", WPADC_SLUG ) ;
?>
    </div><!-- .wpadcb-startw-step-desc -->

    <form id="wpadcb-startw-step-form" class="wpadcb-startw-step-form" method="post">

        <?php 
?>
        <div class="wpadcb-form-control wpadcb-input-type-optselector">
            <div class="wpadcb-optselector-options">
                <?php 
foreach ( $post_types as $post_type ) {
    ?>
                    <button class="wpadcb-optselector-btn<?php 
    echo  $this->option_selected( $values, 'post_type', $post_type->name ) ;
    ?>" data-optselector-value="<?php 
    echo  $post_type->name ;
    ?>"><?php 
    echo  $post_type->label ;
    ?></button>
                <?php 
}
// end - foreach
?>
            </div>
            <input<?php 
echo  $this->attributes( 'post_type' ) ;
?> type="hidden" value="<?php 
echo  $this->get_value( 'post_type', $values ) ;
?>" class="wpadcb-optselector-input" />
        </div><!-- .wpadcb-form-control -->
        <?php 
?>

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

