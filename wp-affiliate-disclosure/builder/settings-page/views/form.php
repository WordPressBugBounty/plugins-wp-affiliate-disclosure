<?php

global  $wp_affiliate_disclosure_fs ;
/**
 * Edit form layout
 *
 * @author 		MojofyWP
 * @package 	builder/settings-page/views
 * 
 */
$post_types = get_post_types( array(
    'public'             => true,
    'publicly_queryable' => true,
), 'objects', 'or' );
?>
<!-- General Settings -->
<div id="wpadcb-form-general" class="wpadcb-form-section">

	<h3 class="wpadcb-form-section-title"><?php 
_e( 'General', WPADC_SLUG );
?></h3>

	<div class="wpadcb-form-control">
		<label class="wpadcb-input-label" for="<?php 
echo  $this->input_id( 'title' ) ;
?>"><?php 
_e( 'Rule Name', WPADC_SLUG );
?></label>
		<input<?php 
echo  $this->attributes( 'title' ) ;
?> type="text" class="wpadcb-input-text" value="<?php 
echo  $this->get_value( 'title', $values ) ;
?>">
	</div><!-- .wpadcb-form-control -->

</div><!-- .wpadcb-form-section -->

<!-- statement Settings -->
<div id="wpadcb-form-statement" class="wpadcb-form-section">

	<h3 class="wpadcb-form-section-title"><?php 
_e( 'Disclosure Statement', WPADC_SLUG );
?></h3>

	<div class="wpadcb-form-control">
		<?php 
$disclosure_statement = wpadcb_get_meta( array(
    'id'      => $id,
    'key'     => 'disclosure_statement',
    'default' => esc_html__( 'This post contains affiliate links.', WPADC_SLUG ),
) );
wp_editor( $disclosure_statement, $this->input_id( 'disclosure_statement' ), array(
    'textarea_name' => $this->input_name( 'disclosure_statement' ),
    'textarea_rows' => 6,
) );
?> 
	</div><!-- .wpadcb-form-control -->

	<div class="wpadcb-form-control wpadcb-input-type-multioptselector">
		<label class="wpadcb-input-label" for=<?php 
echo  $this->input_id( 'statement_position' ) ;
?>>
			<?php 
_e( 'Show Statement At', WPADC_SLUG );
?>
		</label>
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

if ( $wp_affiliate_disclosure_fs->is__premium_only() && $wp_affiliate_disclosure_fs->can_use_premium_code() ) {
    ?>
			<button class="wpadcb-multioptselector-btn<?php 
    echo  $this->multi_option_selected( $values, 'statement_position', 'shortcode' ) ;
    ?>" data-multioptselector-value="shortcode"><?php 
    _e( 'Shortcode', WPADC_SLUG );
    ?></button>
			<button class="wpadcb-multioptselector-btn<?php 
    echo  $this->multi_option_selected( $values, 'statement_position', 'widget' ) ;
    ?>" data-multioptselector-value="widget"><?php 
    _e( 'Widget', WPADC_SLUG );
    ?></button>
			<?php 
} else {
    ?>
			<div class="wpadcb-feature-disabled" data-powertip="<?php 
    _e( 'Only Available in Premium Version', WPADC_SLUG );
    ?>"><?php 
    _e( 'Shortcode', WPADC_SLUG );
    ?></div>
			<div class="wpadcb-feature-disabled" data-powertip="<?php 
    _e( 'Only Available in Premium Version', WPADC_SLUG );
    ?>"><?php 
    _e( 'Widget', WPADC_SLUG );
    ?></div>
			<?php 
}

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

	<?php 
?>

</div><!-- .wpadcb-form-section -->

<!-- Condition Settings -->
<div id="wpadcb-form-condition" class="wpadcb-form-section">

	<h3 class="wpadcb-form-section-title"><?php 
_e( 'Conditions', WPADC_SLUG );
?></h3>
	<?php 
?>
	<div class="wpadcb-form-control wpadcb-input-type-optselector">
		<label class="wpadcb-input-label" for=<?php 
echo  $this->input_id( 'post_type' ) ;
?>>
			<?php 
_e( 'Selected Post Type', WPADC_SLUG );
?>
		</label>
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

	<div class="wpadcb-form-control wpadcb-input-type-optselector">
		<label class="wpadcb-input-label" for=<?php 
echo  $this->input_id( 'condition' ) ;
?>>
			<?php 
_e( 'Post Type Condition', WPADC_SLUG );
?>
		</label>
		<div class="wpadcb-optselector-options">
			<button class="wpadcb-optselector-btn<?php 
echo  $this->option_selected( $values, 'condition', 'none' ) ;
?>" data-optselector-value="none"><?php 
_e( 'Show on All', WPADC_SLUG );
?></button>
			<button class="wpadcb-optselector-btn<?php 
echo  $this->option_selected( $values, 'condition', 'taxonomy' ) ;
?>" data-optselector-value="taxonomy"><?php 
_e( 'Only Show on Selected Taxonomies (categories / tags )', WPADC_SLUG );
?></button>
			<button class="wpadcb-optselector-btn<?php 
echo  $this->option_selected( $values, 'condition', 'ids' ) ;
?>" data-optselector-value="ids"><?php 
_e( 'Only Show on Selected Post(s)', WPADC_SLUG );
?></button>
		</div>
		<input<?php 
echo  $this->attributes( 'condition' ) ;
?> type="hidden" value="<?php 
echo  $this->get_value( 'condition', $values ) ;
?>" class="wpadcb-optselector-input" />
	</div><!-- .wpadcb-form-control -->

	<div class="wpadcb-form-control" <?php 
echo  $this->show_if( 'condition', 'taxonomy', 'opt_selected' ) ;
?>>
		<label class="wpadcb-input-label" for=<?php 
echo  $this->input_id( 'taxonomies' ) ;
?>><?php 
_e( 'Please insert taxonomy slug(s) below: ', WPADC_SLUG );
?></label>
		<input<?php 
echo  $this->attributes( 'taxonomies' ) ;
?> type="text" class="wpadcb-input-text" placeholder="<?php 
_e( 'Each taxonomy slug must be separated by comma - Ex: slug-1,slug-2,slug-3', WPADC_SLUG );
?>" value="<?php 
echo  $this->get_value( 'taxonomies', $values ) ;
?>">

		<div style="padding: 25px 15px;">
			<div class="wpadcb-message-success">
				<div class="wpadcb-message-icon"><i class="fa fa-exclamation-circle"></i></div>
				<h4 class="wpadcb-message-title"><?php 
_e( 'Need help locating the correct slug?', WPADC_SLUG );
?></h4>
				<div class="wpadcb-message-excerpt">
					<p><?php 
_e( "If you need to find the slug of a category, simply go to Posts > Categories, and you should see the 'slug column' in the category table.", WPADC_SLUG );
?></p>
					<img src="<?php 
echo  wpadc()->plugin_url( "assets/img/help/" ) ;
?>taxonomy_slug.jpg" />
				</div>
			</div><!-- .wpadcb-message-success -->
		</div>

	</div><!-- .wpadcb-form-control -->

	<div class="wpadcb-form-control" <?php 
echo  $this->show_if( 'condition', 'ids', 'opt_selected' ) ;
?>>
		<label class="wpadcb-input-label" for=<?php 
echo  $this->input_id( 'ids' ) ;
?>><?php 
_e( 'Please insert post ID(s) below:', WPADC_SLUG );
?></label>
		<input<?php 
echo  $this->attributes( 'ids' ) ;
?> type="text" class="wpadcb-input-text" placeholder="<?php 
_e( 'Each Post ID must be separated by comma - Ex: 1,2,3', WPADC_SLUG );
?>" value="<?php 
echo  $this->get_value( 'ids', $values ) ;
?>">

		<div style="padding: 25px 15px;">
			<div class="wpadcb-message-success">
				<div class="wpadcb-message-icon"><i class="fa fa-exclamation-circle"></i></div>
				<h4 class="wpadcb-message-title"><?php 
_e( 'Need help locating the post ID?', WPADC_SLUG );
?></h4>
				<div class="wpadcb-message-excerpt">
					<p><?php 
_e( "In the edit post screen, look at the URL in your web browser. The post ID is the number in the URL.", WPADC_SLUG );
?></p>
					<img src="<?php 
echo  wpadc()->plugin_url( "assets/img/help/" ) ;
?>post_id.jpg" />
				</div>
			</div><!-- .wpadcb-message-success -->
		</div>

	</div><!-- .wpadcb-form-control -->

	<?php 

if ( $wp_affiliate_disclosure_fs->is__premium_only() && $wp_affiliate_disclosure_fs->can_use_premium_code() ) {
    ?>
		<div class="wpadcb-form-control wpadcb-input-type-optselector">
			<label class="wpadcb-input-label" for=<?php 
    echo  $this->input_id( 'advanced_filter' ) ;
    ?>>
				<?php 
    _e( 'Advanced Filtering', WPADC_SLUG );
    ?>
			</label>
			<div class="wpadcb-optselector-options">
				<button class="wpadcb-optselector-btn<?php 
    echo  $this->option_selected( $values, 'advanced_filter', 'none' ) ;
    ?>" data-optselector-value="none"><?php 
    _e( 'Disabled', WPADC_SLUG );
    ?></button>
				<button class="wpadcb-optselector-btn<?php 
    echo  $this->option_selected( $values, 'advanced_filter', 'exclude_taxonomy' ) ;
    ?>" data-optselector-value="exclude_taxonomy"><?php 
    _e( 'Exclude Selected Taxonomies (categories / tags )', WPADC_SLUG );
    ?></button>
				<button class="wpadcb-optselector-btn<?php 
    echo  $this->option_selected( $values, 'advanced_filter', 'exclude_ids' ) ;
    ?>" data-optselector-value="exclude_ids"><?php 
    _e( 'Exclude Selected Post(s)', WPADC_SLUG );
    ?></button>
			</div>
			<input<?php 
    echo  $this->attributes( 'advanced_filter' ) ;
    ?> type="hidden" value="<?php 
    echo  $this->get_value( 'advanced_filter', $values ) ;
    ?>" class="wpadcb-optselector-input" />
		</div><!-- .wpadcb-form-control -->

		<div class="wpadcb-form-control" <?php 
    echo  $this->show_if( 'advanced_filter', 'exclude_taxonomy', 'opt_selected' ) ;
    ?>>
			<label class="wpadcb-input-label" for=<?php 
    echo  $this->input_id( 'exclude_taxonomies' ) ;
    ?>><?php 
    _e( 'Please insert taxonomy slug(s) below: ', WPADC_SLUG );
    ?></label>
			<input<?php 
    echo  $this->attributes( 'exclude_taxonomies' ) ;
    ?> type="text" class="wpadcb-input-text" placeholder="<?php 
    _e( 'Each taxonomy slug must be separated by comma - Ex: slug-1,slug-2,slug-3', WPADC_SLUG );
    ?>" value="<?php 
    echo  $this->get_value( 'exclude_taxonomies', $values ) ;
    ?>">

			<div style="padding: 25px 15px;">
				<div class="wpadcb-message-success">
					<div class="wpadcb-message-icon"><i class="fa fa-exclamation-circle"></i></div>
					<h4 class="wpadcb-message-title"><?php 
    _e( 'Need help locating the correct slug?', WPADC_SLUG );
    ?></h4>
					<div class="wpadcb-message-excerpt">
						<p><?php 
    _e( "If you need to find the slug of a category, simply go to Posts > Categories, and you should see the 'slug column' in the category table.", WPADC_SLUG );
    ?></p>
						<img src="<?php 
    echo  wpadc()->plugin_url( "assets/img/help/" ) ;
    ?>taxonomy_slug.jpg" />
					</div>
				</div><!-- .wpadcb-message-success -->
			</div>

		</div><!-- .wpadcb-form-control -->

		<div class="wpadcb-form-control" <?php 
    echo  $this->show_if( 'advanced_filter', 'exclude_ids', 'opt_selected' ) ;
    ?>>
			<label class="wpadcb-input-label" for=<?php 
    echo  $this->input_id( 'excludes_ids' ) ;
    ?>><?php 
    _e( 'Please insert post ID(s) below:', WPADC_SLUG );
    ?></label>
			<input<?php 
    echo  $this->attributes( 'excludes_ids' ) ;
    ?> type="text" class="wpadcb-input-text" placeholder="<?php 
    _e( 'Each Post ID must be separated by comma - Ex: 1,2,3', WPADC_SLUG );
    ?>" value="<?php 
    echo  $this->get_value( 'excludes_ids', $values ) ;
    ?>">

			<div style="padding: 25px 15px;">
				<div class="wpadcb-message-success">
					<div class="wpadcb-message-icon"><i class="fa fa-exclamation-circle"></i></div>
					<h4 class="wpadcb-message-title"><?php 
    _e( 'Need help locating the post ID?', WPADC_SLUG );
    ?></h4>
					<div class="wpadcb-message-excerpt">
						<p><?php 
    _e( "In the edit post screen, look at the URL in your web browser. The post ID is the number in the URL.", WPADC_SLUG );
    ?></p>
						<img src="<?php 
    echo  wpadc()->plugin_url( "assets/img/help/" ) ;
    ?>post_id.jpg" />
					</div>
				</div><!-- .wpadcb-message-success -->
			</div>

		</div><!-- .wpadcb-form-control -->

		<?php 
} else {
    ?>
		<div class="wpadcb-form-control wpadcb-input-type-optselector">
			<label class="wpadcb-input-label" for=<?php 
    echo  $this->input_id( 'advanced_filter' ) ;
    ?>>
				<?php 
    _e( 'Advanced Filtering', WPADC_SLUG );
    ?>
			</label>
			<div class="wpadcb-optselector-options">
				<div class="wpadcb-feature-disabled" data-powertip="<?php 
    _e( 'Only Available in Premium Version', WPADC_SLUG );
    ?>"><?php 
    _e( 'Disabled', WPADC_SLUG );
    ?></div>
				<div class="wpadcb-feature-disabled" data-powertip="<?php 
    _e( 'Only Available in Premium Version', WPADC_SLUG );
    ?>"><?php 
    _e( 'Exclude Selected Taxonomies (categories / tags )', WPADC_SLUG );
    ?></div>
				<div class="wpadcb-feature-disabled" data-powertip="<?php 
    _e( 'Only Available in Premium Version', WPADC_SLUG );
    ?>"><?php 
    _e( 'Exclude Selected Post(s)', WPADC_SLUG );
    ?></div>
			</div>
			<input<?php 
    echo  $this->attributes( 'advanced_filter' ) ;
    ?> type="hidden" value="<?php 
    echo  $this->get_value( 'advanced_filter', $values ) ;
    ?>" class="wpadcb-optselector-input" />
		</div><!-- .wpadcb-form-control -->
	<?php 
}

?>

</div><!-- .wpadcb-form-section -->

<!-- Priority Settings -->
<div id="wpadcb-form-priority" class="wpadcb-form-section">

	<h3 class="wpadcb-form-section-title"><?php 
_e( 'Priority', WPADC_SLUG );
?></h3>

	<div class="wpadcb-form-control">
		<label class="wpadcb-input-label" for=<?php 
echo  $this->input_id( 'priority' ) ;
?>><?php 
_e( 'The lower the number, the higher the priority', WPADC_SLUG );
?></label>
		<input<?php 
echo  $this->attributes( 'priority' ) ;
?> type="number" class="wpadcb-input-number" value="<?php 
echo  $this->get_value( 'priority', $values ) ;
?>">
	</div><!-- .wpadcb-form-control -->

</div><!-- .wpadcb-form-section -->

<!-- Save Button -->
<div id="wpadcb-form-save" class="wpadcb-form-section">
	<button type="submit" class="wpadcb-button-info wpadcb-save-changes"><?php 
_e( 'Save Changes', WPADC_SLUG );
?></button>
</div><!-- .wpadcb-form-section -->