<?php global $wp_affiliate_disclosure_fs;
/**
 * step 5 layout
 *
 * @author 		MojofyWP
 * @package 	builder/start-wizard/views
 * 
 */

?>
<div class="wpadcb-startw-step-wrapper">
    
    <h2 class="wpadcb-startw-step-heading"><?php echo esc_html__( 'Select a Condition', WPADC_SLUG ); ?></h2>

    <div class="wpadcb-startw-step-desc">
        <?php echo esc_html__( "You can choose to show your affiliate disclosure statement on all posts, or filter based on specific categories / tags, or specify to display only on certain posts", WPADC_SLUG ); ?>
    </div><!-- .wpadcb-startw-step-desc -->

    <form id="wpadcb-startw-step-form" class="wpadcb-startw-step-form" method="post">

        <div class="wpadcb-form-control wpadcb-input-type-optselector">
            <div class="wpadcb-optselector-options">
                <button class="wpadcb-optselector-btn<?php echo $this->option_selected( $values, 'condition' , 'none' ); ?>" data-optselector-value="none"><?php _e( 'Show on All' , WPADC_SLUG ); ?></button>
                <button class="wpadcb-optselector-btn<?php echo $this->option_selected( $values, 'condition' , 'taxonomy' ); ?>" data-optselector-value="taxonomy"><?php _e( 'Only Show on Selected Taxonomies (categories / tags )' , WPADC_SLUG ); ?></button>
                <button class="wpadcb-optselector-btn<?php echo $this->option_selected( $values, 'condition' , 'ids' ); ?>" data-optselector-value="ids"><?php _e( 'Only Show on Selected Post(s)' , WPADC_SLUG ); ?></button>
            </div>
            <input<?php echo $this->attributes( 'condition' ); ?> type="hidden" value="<?php echo $this->get_value( 'condition' , $values ); ?>" class="wpadcb-optselector-input" />
        </div><!-- .wpadcb-form-control -->

        <div class="wpadcb-form-control" <?php echo $this->show_if( 'condition' , 'taxonomy' , 'opt_selected' ); ?> style="margin-top: 15px">
            <label class="wpadcb-input-label" for=<?php echo $this->input_id( 'taxonomies' ); ?>><?php _e( 'Please insert taxonomy slug(s) below: ' , WPADC_SLUG ); ?></label>
            <input<?php echo $this->attributes( 'taxonomies' ); ?> type="text" class="wpadcb-input-text" value="<?php echo $this->get_value( 'taxonomies' , $values ); ?>">

            <div class="wpadcb-form-desc"><em><?php _e( 'Each slug must be separated by comma - Ex: slug-1,slug-2,slug-3' , WPADC_SLUG ); ?></em></div>

            <div style="padding: 25px 15px;">
                <div class="wpadcb-message-success">
                    <div class="wpadcb-message-icon"><i class="fa fa-exclamation-circle"></i></div>
                    <h4 class="wpadcb-message-title"><?php _e( 'Need help locating the correct slug?' , WPADC_SLUG ); ?></h4>
                    <div class="wpadcb-message-excerpt">
                        <p><?php _e( "If you need to find the slug of a category, simply go to Posts > Categories, and you should see the 'slug column' in the category table." , WPADC_SLUG ); ?></p>
                        <img src="<?php echo wpadc()->plugin_url("assets/img/help/"); ?>taxonomy_slug.jpg" />
                    </div>
                </div><!-- .wpadcb-message-success -->
            </div>

        </div><!-- .wpadcb-form-control -->

        <div class="wpadcb-form-control" <?php echo $this->show_if( 'condition' , 'ids' , 'opt_selected' ); ?> style="margin-top: 15px">
            <label class="wpadcb-input-label" for=<?php echo $this->input_id( 'ids' ); ?>><?php _e( 'Please insert post ID(s) below:' , WPADC_SLUG ); ?></label>
            <input<?php echo $this->attributes( 'ids' ); ?> type="text" class="wpadcb-input-text" value="<?php echo $this->get_value( 'ids' , $values ); ?>">

            <div class="wpadcb-form-desc"><em><?php _e( 'Each Post ID must be separated by comma - Ex: 1,2,3' , WPADC_SLUG ); ?></em></div>

            <div style="padding: 25px 15px;">
                <div class="wpadcb-message-success">
                    <div class="wpadcb-message-icon"><i class="fa fa-exclamation-circle"></i></div>
                    <h4 class="wpadcb-message-title"><?php _e( 'Need help locating the post ID?' , WPADC_SLUG ); ?></h4>
                    <div class="wpadcb-message-excerpt">
                        <p><?php _e( "In the edit post screen, look at the URL in your web browser. The post ID is the number in the URL." , WPADC_SLUG ); ?></p>
                        <img src="<?php echo wpadc()->plugin_url("assets/img/help/"); ?>post_id.jpg" />
                    </div>
                </div><!-- .wpadcb-message-success -->
            </div>

        </div><!-- .wpadcb-form-control -->

        <!-- Hidden field -->
        <input type="hidden" name="wpadcb_startw_step" value="<?php echo $current_step; ?>" />
        <?php 
            wp_nonce_field( 'wpadcb_start_wizard' , '_wpadcb_start_wizard_nonce' ); 
        ?>

    </form><!-- .wpadcb-startw-step-form -->

    <div class="wpadcb-startw-step-actions">
        <button class="wpadcb-button-passive wpadcb-startw-action" data-action-type="prev"><i class="fa fa-long-arrow-left"></i><?php echo esc_html__( 'Previous', WPADC_SLUG ); ?></button>
        <button class="wpadcb-button-success wpadcb-startw-action" data-action-type="done"><i class="fa fa-check"></i><?php echo esc_html__( 'Done', WPADC_SLUG ); ?></button>
    </div><!-- .wpadcb-startw-step-actions -->

</div><!-- .wpadcb-startw-step-wrapper -->

