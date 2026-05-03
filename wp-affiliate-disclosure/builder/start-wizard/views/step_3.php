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

        <?php
        $wpadcb_templates = class_exists( '\\WPADC\\Templates\\TemplateLibrary' ) ? \WPADC\Templates\TemplateLibrary::get_all() : array();
        if ( ! empty( $wpadcb_templates ) ) :
        ?>
        <style>
        .wpadcb-template-cards { display:flex; gap:12px; flex-wrap:wrap; margin:8px 0 12px; }
        .wpadcb-template-card { flex:1 1 220px; padding:12px; border:1px solid #ddd; border-radius:6px; background:#fff; text-align:center; }
        .wpadcb-template-card-icon { font-size:24px; margin-bottom:6px; color:#2271b1; }
        .wpadcb-template-card-name { font-weight:600; margin-bottom:4px; }
        .wpadcb-template-card-desc { font-size:12px; color:#555; margin-bottom:8px; min-height:32px; }
        .wpadcb-template-variant-picker { margin-top:8px; padding-top:8px; border-top:1px solid #eee; font-size:13px; }
        .wpadcb-template-variant-picker label { display:block; text-align:left; margin:4px 0; }
        .wpadcb-template-variant-picker button { margin:6px 4px 0 0; }
        </style>
        <div class="wpadcb-form-control wpadcb-template-library">
            <label class="wpadcb-input-label"><?php _e( 'Start from a template:', WPADC_SLUG ); ?></label>
            <div class="wpadcb-template-cards">
                <?php foreach ( $wpadcb_templates as $tpl ) : ?>
                    <div class="wpadcb-template-card" data-template-id="<?php echo esc_attr( $tpl['id'] ); ?>"
                        data-short-text="<?php echo esc_attr( $tpl['short_text'] ); ?>"
                        data-long-text="<?php echo esc_attr( $tpl['long_text'] ); ?>"
                        data-target-editor="<?php echo esc_attr( $this->input_id( 'disclosure_statement' ) ); ?>">
                        <div class="wpadcb-template-card-icon"><span class="dashicons <?php echo esc_attr( $tpl['icon'] ); ?>"></span></div>
                        <div class="wpadcb-template-card-name"><?php echo esc_html( $tpl['name'] ); ?></div>
                        <div class="wpadcb-template-card-desc"><?php echo esc_html( $tpl['description'] ); ?></div>
                        <button type="button" class="wpadcb-button-info wpadcb-template-use-btn"><?php _e( 'Use This', WPADC_SLUG ); ?></button>
                        <div class="wpadcb-template-variant-picker" style="display:none;">
                            <label><input type="radio" name="wpadcb-template-variant-<?php echo esc_attr( $tpl['id'] ); ?>" value="short" checked /> <?php _e( 'Short', WPADC_SLUG ); ?></label>
                            <label><input type="radio" name="wpadcb-template-variant-<?php echo esc_attr( $tpl['id'] ); ?>" value="long" /> <?php _e( 'Long', WPADC_SLUG ); ?></label>
                            <button type="button" class="wpadcb-button-info wpadcb-template-insert-btn"><?php _e( 'Insert', WPADC_SLUG ); ?></button>
                            <button type="button" class="wpadcb-button-info wpadcb-template-cancel-btn"><?php _e( 'Cancel', WPADC_SLUG ); ?></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <input<?php echo $this->attributes( 'template_id' ); ?> type="hidden" value="<?php echo $this->get_value( 'template_id', $values ); ?>" />
            <input<?php echo $this->attributes( 'template_variant' ); ?> type="hidden" value="<?php echo $this->get_value( 'template_variant', $values ); ?>" />
        </div>
        <?php endif; ?>

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

