<?php

global $wp_affiliate_disclosure_fs;
/**
 * Edit form layout
 *
 * @author 		MojofyWP
 * @package 	builder/settings-page/views
 *
 */
?>
<style>
/* v1.4 — template cards */
.wpadcb-template-cards { display:flex; gap:12px; flex-wrap:wrap; margin:8px 0 12px; }
.wpadcb-template-card { flex:1 1 220px; padding:12px; border:1px solid #ddd; border-radius:6px; background:#fff; text-align:center; }
.wpadcb-template-card-icon { font-size:24px; margin-bottom:6px; color:#2271b1; }
.wpadcb-template-card-name { font-weight:600; margin-bottom:4px; }
.wpadcb-template-card-desc { font-size:12px; color:#555; margin-bottom:8px; min-height:32px; }
.wpadcb-template-variant-picker { margin-top:8px; padding-top:8px; border-top:1px solid #eee; font-size:13px; }
.wpadcb-template-variant-picker label { display:block; text-align:left; margin:4px 0; }
.wpadcb-template-variant-picker button { margin:6px 4px 0 0; }
/* v1.4 — appearance section */
.wpadcb-appearance-section { padding:12px; border:1px solid #ddd; border-radius:6px; background:#fafafa; }
.wpadcb-preset-buttons { display:flex; gap:6px; flex-wrap:wrap; margin:6px 0 12px; }
.wpadcb-preset-btn { padding:6px 12px; border:1px solid #ccc; background:#fff; cursor:pointer; border-radius:4px; }
.wpadcb-preset-btn.wpadcb-active { background:#2271b1; color:#fff; border-color:#2271b1; }
.wpadcb-preset-btn[data-preset="custom"] { font-style:italic; opacity:0.7; cursor:default; }
.wpadcb-style-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px; }
.wpadcb-style-grid > div { position:relative; z-index:1; }
.wpadcb-style-grid > div:has(.wp-picker-active),
.wpadcb-style-grid .wp-picker-active { z-index:9999; }
.wpadcb-appearance-section .wp-picker-holder { z-index:9999 !important; }
/* Restore content-box for iris picker so its JS position calculations aren't thrown off by the global border-box rule */
.wpadcb-appearance-section .iris-picker,
.wpadcb-appearance-section .iris-picker *,
.wpadcb-appearance-section .iris-picker *::before,
.wpadcb-appearance-section .iris-picker *::after { box-sizing:content-box !important; }
.wpadcb-style-grid label { display:block; font-size:12px; font-weight:600; margin-bottom:4px; }
.wpadcb-style-preview-wrap { margin-top:12px; padding:12px; border:1px dashed #bbb; background:#fff; }
.wpadcb-style-preview-inner { transition: all 0.15s; }
/* Customize appearance toggle */
.wpadcb-appearance-toggle { display:flex; align-items:center; gap:8px; margin-bottom:10px; }
.wpadcb-appearance-toggle input[type="checkbox"] { width:16px; height:16px; margin:0; cursor:pointer; }
.wpadcb-appearance-toggle label { font-weight:600; font-size:13px; cursor:pointer; margin:0; }
/* Preset overwrite confirmation bar */
.wpadcb-preset-confirm { display:none; align-items:center; gap:12px; margin-top:8px; margin-bottom:12px; padding:8px 10px; background:#fff3cd; border:1px solid #ffc107; border-radius:4px; font-size:12px; line-height:1.4; }
.wpadcb-preset-confirm-msg { flex:1; margin-right:4px; }
.wpadcb-preset-confirm-yes { padding:4px 10px; background:#2271b1; color:#fff; border:none; border-radius:3px; cursor:pointer; font-size:12px; white-space:nowrap; }
.wpadcb-preset-confirm-cancel { padding:4px 10px; background:#fff; color:#444; border:1px solid #ccc; border-radius:3px; cursor:pointer; font-size:12px; white-space:nowrap; }
/* Slider + number input row */
.wpadcb-slider-wrap { display:flex; align-items:center; gap:8px; }
.wpadcb-slider-wrap input[type="range"] { flex:1; min-width:0; cursor:pointer; accent-color:#2271b1; }
.wpadcb-slider-wrap input[type="number"] { width:54px; flex-shrink:0; text-align:center; padding:2px 4px; }
</style>
<?php 
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
echo $this->input_id( 'title' );
?>"><?php 
_e( 'Rule Name', WPADC_SLUG );
?></label>
		<input<?php 
echo $this->attributes( 'title' );
?> type="text" class="wpadcb-input-text" value="<?php 
echo $this->get_value( 'title', $values );
?>">
	</div><!-- .wpadcb-form-control -->

</div><!-- .wpadcb-form-section -->

<!-- statement Settings -->
<div id="wpadcb-form-statement" class="wpadcb-form-section">

	<h3 class="wpadcb-form-section-title"><?php 
_e( 'Disclosure Statement', WPADC_SLUG );
?></h3>

	<?php 
// v1.4 — Disclosure templates (Amazon, FTC).
$wpadcb_templates = ( class_exists( '\\WPADC\\Templates\\TemplateLibrary' ) ? \WPADC\Templates\TemplateLibrary::get_all() : array() );
if ( !empty( $wpadcb_templates ) ) {
    ?>
	<div class="wpadcb-form-control wpadcb-template-library">
		<label class="wpadcb-input-label"><?php 
    _e( 'Start from a template:', WPADC_SLUG );
    ?></label>
		<div class="wpadcb-template-cards">
			<?php 
    foreach ( $wpadcb_templates as $tpl ) {
        ?>
				<div class="wpadcb-template-card" data-template-id="<?php 
        echo esc_attr( $tpl['id'] );
        ?>"
					data-short-text="<?php 
        echo esc_attr( $tpl['short_text'] );
        ?>"
					data-long-text="<?php 
        echo esc_attr( $tpl['long_text'] );
        ?>"
					data-target-editor="<?php 
        echo esc_attr( $this->input_id( 'disclosure_statement' ) );
        ?>">
					<div class="wpadcb-template-card-icon"><span class="dashicons <?php 
        echo esc_attr( $tpl['icon'] );
        ?>"></span></div>
					<div class="wpadcb-template-card-name"><?php 
        echo esc_html( $tpl['name'] );
        ?></div>
					<div class="wpadcb-template-card-desc"><?php 
        echo esc_html( $tpl['description'] );
        ?></div>
					<button type="button" class="wpadcb-button-info wpadcb-template-use-btn"><?php 
        _e( 'Use This', WPADC_SLUG );
        ?></button>
					<div class="wpadcb-template-variant-picker" style="display:none;">
						<label><input type="radio" name="wpadcb-template-variant-<?php 
        echo esc_attr( $tpl['id'] );
        ?>" value="short" checked /> <?php 
        _e( 'Short', WPADC_SLUG );
        ?></label>
						<label><input type="radio" name="wpadcb-template-variant-<?php 
        echo esc_attr( $tpl['id'] );
        ?>" value="long" /> <?php 
        _e( 'Long', WPADC_SLUG );
        ?></label>
						<button type="button" class="wpadcb-button-info wpadcb-template-insert-btn"><?php 
        _e( 'Insert', WPADC_SLUG );
        ?></button>
						<button type="button" class="wpadcb-button-info wpadcb-template-cancel-btn"><?php 
        _e( 'Cancel', WPADC_SLUG );
        ?></button>
					</div>
				</div>
			<?php 
    }
    ?>
		</div>
		<input<?php 
    echo $this->attributes( 'template_id' );
    ?> type="hidden" value="<?php 
    echo $this->get_value( 'template_id', $values );
    ?>" />
		<input<?php 
    echo $this->attributes( 'template_variant' );
    ?> type="hidden" value="<?php 
    echo $this->get_value( 'template_variant', $values );
    ?>" />
	</div>
	<?php 
}
?>

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

	<?php 
// v1.4 — Appearance section
$wpadcb_preset_slug = $this->get_value( 'style_preset', $values );
$wpadcb_customize_on = $this->get_value( 'customize_appearance', $values ) === 'on';
?>
	<div class="wpadcb-form-control wpadcb-appearance-section">
		<label class="wpadcb-input-label"><?php 
_e( 'Appearance', WPADC_SLUG );
?></label>

		<div class="wpadcb-appearance-toggle">
			<input<?php 
echo $this->attributes( 'customize_appearance' );
?> type="checkbox"<?php 
checked( $wpadcb_customize_on );
?> />
			<label for="<?php 
echo esc_attr( $this->input_id( 'customize_appearance' ) );
?>"><?php 
_e( 'Customize appearance', WPADC_SLUG );
?></label>
		</div>

		<div class="wpadcb-appearance-body"<?php 
echo ( $wpadcb_customize_on ? '' : ' style="display:none"' );
?>>

		<div class="wpadcb-preset-buttons">
			<button type="button" class="wpadcb-preset-btn<?php 
echo ( 'minimal' === $wpadcb_preset_slug ? ' wpadcb-active' : '' );
?>" data-preset="minimal"><?php 
_e( 'Minimal', WPADC_SLUG );
?></button>
			<button type="button" class="wpadcb-preset-btn<?php 
echo ( 'boxed' === $wpadcb_preset_slug ? ' wpadcb-active' : '' );
?>" data-preset="boxed"><?php 
_e( 'Boxed', WPADC_SLUG );
?></button>
			<button type="button" class="wpadcb-preset-btn<?php 
echo ( 'banner' === $wpadcb_preset_slug ? ' wpadcb-active' : '' );
?>" data-preset="banner"><?php 
_e( 'Banner', WPADC_SLUG );
?></button>
			<button type="button" class="wpadcb-preset-btn<?php 
echo ( 'inline' === $wpadcb_preset_slug ? ' wpadcb-active' : '' );
?>" data-preset="inline"><?php 
_e( 'Inline', WPADC_SLUG );
?></button>
			<button type="button" class="wpadcb-preset-btn<?php 
echo ( 'custom' === $wpadcb_preset_slug ? ' wpadcb-active' : '' );
?>" data-preset="custom"><?php 
_e( 'Custom', WPADC_SLUG );
?></button>
		</div>
		<div class="wpadcb-preset-confirm">
			<span class="wpadcb-preset-confirm-msg"></span>
			<button type="button" class="wpadcb-preset-confirm-yes"><?php 
_e( 'Yes, apply', WPADC_SLUG );
?></button>
			<button type="button" class="wpadcb-preset-confirm-cancel"><?php 
_e( 'Cancel', WPADC_SLUG );
?></button>
		</div>

		<input<?php 
echo $this->attributes( 'style_preset' );
?> type="hidden" value="<?php 
echo esc_attr( $wpadcb_preset_slug );
?>" />

		<div class="wpadcb-style-grid">
			<div>
				<label><?php 
_e( 'Background', WPADC_SLUG );
?></label>
				<input<?php 
echo $this->attributes( 'style_bg_color' );
?> type="text" class="wpadcb-style-control" value="<?php 
echo $this->get_value( 'style_bg_color', $values );
?>" />
			</div>
			<div>
				<label><?php 
_e( 'Text Color', WPADC_SLUG );
?></label>
				<input<?php 
echo $this->attributes( 'style_text_color' );
?> type="text" class="wpadcb-style-control" value="<?php 
echo $this->get_value( 'style_text_color', $values );
?>" />
			</div>
			<div>
				<label><?php 
_e( 'Border Color', WPADC_SLUG );
?></label>
				<input<?php 
echo $this->attributes( 'style_border_color' );
?> type="text" class="wpadcb-style-control" value="<?php 
echo $this->get_value( 'style_border_color', $values );
?>" />
			</div>
			<div>
				<label><?php 
_e( 'Border Style', WPADC_SLUG );
?></label>
				<select<?php 
echo $this->attributes( 'style_border_style' );
?> class="wpadcb-style-control">
					<?php 
$bs = $this->get_value( 'style_border_style', $values );
?>
					<option value="solid" <?php 
selected( $bs, 'solid' );
?>><?php 
_e( 'Solid', WPADC_SLUG );
?></option>
					<option value="dashed" <?php 
selected( $bs, 'dashed' );
?>><?php 
_e( 'Dashed', WPADC_SLUG );
?></option>
					<option value="dotted" <?php 
selected( $bs, 'dotted' );
?>><?php 
_e( 'Dotted', WPADC_SLUG );
?></option>
					<option value="none" <?php 
selected( $bs, 'none' );
?>><?php 
_e( 'None', WPADC_SLUG );
?></option>
				</select>
			</div>
			<div>
				<label><?php 
_e( 'Border Width (px)', WPADC_SLUG );
?></label>
				<div class="wpadcb-slider-wrap">
					<input type="range" class="wpadcb-style-slider" min="0" max="10" value="<?php 
echo (int) $this->get_value( 'style_border_width', $values );
?>" aria-hidden="true" tabindex="-1" />
					<input<?php 
echo $this->attributes( 'style_border_width' );
?> type="number" class="wpadcb-style-control" min="0" max="10" value="<?php 
echo $this->get_value( 'style_border_width', $values );
?>" />
				</div>
			</div>
			<div>
				<label><?php 
_e( 'Border Radius (px)', WPADC_SLUG );
?></label>
				<div class="wpadcb-slider-wrap">
					<input type="range" class="wpadcb-style-slider" min="0" max="50" value="<?php 
echo (int) $this->get_value( 'style_border_radius', $values );
?>" aria-hidden="true" tabindex="-1" />
					<input<?php 
echo $this->attributes( 'style_border_radius' );
?> type="number" class="wpadcb-style-control" min="0" max="50" value="<?php 
echo $this->get_value( 'style_border_radius', $values );
?>" />
				</div>
			</div>
			<div>
				<label><?php 
_e( 'Padding Vertical (px)', WPADC_SLUG );
?></label>
				<div class="wpadcb-slider-wrap">
					<input type="range" class="wpadcb-style-slider" min="0" max="60" value="<?php 
echo (int) $this->get_value( 'style_padding_y', $values );
?>" aria-hidden="true" tabindex="-1" />
					<input<?php 
echo $this->attributes( 'style_padding_y' );
?> type="number" class="wpadcb-style-control" min="0" max="60" value="<?php 
echo $this->get_value( 'style_padding_y', $values );
?>" />
				</div>
			</div>
			<div>
				<label><?php 
_e( 'Padding Horizontal (px)', WPADC_SLUG );
?></label>
				<div class="wpadcb-slider-wrap">
					<input type="range" class="wpadcb-style-slider" min="0" max="60" value="<?php 
echo (int) $this->get_value( 'style_padding_x', $values );
?>" aria-hidden="true" tabindex="-1" />
					<input<?php 
echo $this->attributes( 'style_padding_x' );
?> type="number" class="wpadcb-style-control" min="0" max="60" value="<?php 
echo $this->get_value( 'style_padding_x', $values );
?>" />
				</div>
			</div>
			<div>
				<label><?php 
_e( 'Margin Vertical (px)', WPADC_SLUG );
?></label>
				<div class="wpadcb-slider-wrap">
					<input type="range" class="wpadcb-style-slider" min="0" max="60" value="<?php 
echo (int) $this->get_value( 'style_margin_y', $values );
?>" aria-hidden="true" tabindex="-1" />
					<input<?php 
echo $this->attributes( 'style_margin_y' );
?> type="number" class="wpadcb-style-control" min="0" max="60" value="<?php 
echo $this->get_value( 'style_margin_y', $values );
?>" />
				</div>
			</div>
			<div>
				<label><?php 
_e( 'Margin Horizontal (px)', WPADC_SLUG );
?></label>
				<div class="wpadcb-slider-wrap">
					<input type="range" class="wpadcb-style-slider" min="0" max="60" value="<?php 
echo (int) $this->get_value( 'style_margin_x', $values );
?>" aria-hidden="true" tabindex="-1" />
					<input<?php 
echo $this->attributes( 'style_margin_x' );
?> type="number" class="wpadcb-style-control" min="0" max="60" value="<?php 
echo $this->get_value( 'style_margin_x', $values );
?>" />
				</div>
			</div>
		</div>

		<div class="wpadcb-style-preview-wrap">
			<label><?php 
_e( 'Preview:', WPADC_SLUG );
?></label>
			<div class="wpadcb-style-preview-inner"><?php 
_e( 'Your disclosure will look like this.', WPADC_SLUG );
?></div>
		</div>

		</div><!-- .wpadcb-appearance-body -->
	</div><!-- .wpadcb-appearance-section -->

	<div class="wpadcb-form-control wpadcb-input-type-multioptselector">
		<label class="wpadcb-input-label" for=<?php 
echo $this->input_id( 'statement_position' );
?>>
			<?php 
_e( 'Show Statement At', WPADC_SLUG );
?>
		</label>
		<div class="wpadcb-multioptselector-options">
			<button class="wpadcb-multioptselector-btn<?php 
echo $this->multi_option_selected( $values, 'statement_position', 'before-content' );
?>" data-multioptselector-value="before-content"><?php 
_e( 'Before Post Content', WPADC_SLUG );
?></button>
			<button class="wpadcb-multioptselector-btn<?php 
echo $this->multi_option_selected( $values, 'statement_position', 'after-content' );
?>" data-multioptselector-value="after-content"><?php 
_e( 'After Post Content', WPADC_SLUG );
?></button>
		<?php 
if ( $wp_affiliate_disclosure_fs->is__premium_only() && $wp_affiliate_disclosure_fs->can_use_premium_code() ) {
    ?>
			<button class="wpadcb-multioptselector-btn<?php 
    echo $this->multi_option_selected( $values, 'statement_position', 'shortcode' );
    ?>" data-multioptselector-value="shortcode"><?php 
    _e( 'Shortcode', WPADC_SLUG );
    ?></button>
			<button class="wpadcb-multioptselector-btn<?php 
    echo $this->multi_option_selected( $values, 'statement_position', 'widget' );
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
			<?php 
if ( $wp_affiliate_disclosure_fs->is__premium_only() && $wp_affiliate_disclosure_fs->can_use_premium_code() ) {
    ?>
			<button class="wpadcb-multioptselector-btn<?php 
    echo $this->multi_option_selected( $values, 'statement_position', 'after_p1' );
    ?>" data-multioptselector-value="after_p1"><?php 
    _e( 'After P1', WPADC_SLUG );
    ?></button>
			<button class="wpadcb-multioptselector-btn<?php 
    echo $this->multi_option_selected( $values, 'statement_position', 'after_p2' );
    ?>" data-multioptselector-value="after_p2"><?php 
    _e( 'After P2', WPADC_SLUG );
    ?></button>
			<button class="wpadcb-multioptselector-btn<?php 
    echo $this->multi_option_selected( $values, 'statement_position', 'after_p3' );
    ?>" data-multioptselector-value="after_p3"><?php 
    _e( 'After P3', WPADC_SLUG );
    ?></button>
			<?php 
} else {
    ?>
			<div class="wpadcb-feature-disabled" data-powertip="<?php 
    _e( 'Only Available in Premium Version', WPADC_SLUG );
    ?>"><?php 
    _e( 'After P1', WPADC_SLUG );
    ?></div>
			<div class="wpadcb-feature-disabled" data-powertip="<?php 
    _e( 'Only Available in Premium Version', WPADC_SLUG );
    ?>"><?php 
    _e( 'After P2', WPADC_SLUG );
    ?></div>
			<div class="wpadcb-feature-disabled" data-powertip="<?php 
    _e( 'Only Available in Premium Version', WPADC_SLUG );
    ?>"><?php 
    _e( 'After P3', WPADC_SLUG );
    ?></div>
			<?php 
}
?>
		</div>
		<div class="wpadcb-form-desc"><em><?php 
_e( '*selection of multiple options is allowed', WPADC_SLUG );
?></em></div>
		<div class="wpadcb-form-desc"><em><?php 
_e( 'If the post has fewer paragraphs than selected, the disclosure will appear after the last paragraph.', WPADC_SLUG );
?></em></div>
		<input<?php 
echo $this->attributes( 'statement_position' );
?> type="hidden" value="<?php 
echo $this->get_value( 'statement_position', $values );
?>" class="wpadcb-multioptselector-input" />
	</div><!-- .wpadcb-form-control -->

	<div class="wpadcb-form-control" <?php 
echo $this->show_if( 'statement_position', 'after_p', 'contains_reverse' );
?>>
		<label class="wpadcb-input-label" for="<?php 
echo $this->input_id( 'content_parent_selector' );
?>"><?php 
_e( 'Content Container Selector', WPADC_SLUG );
?></label>
		<input<?php 
echo $this->attributes( 'content_parent_selector' );
?> type="text" class="wpadcb-input-text" placeholder=".entry-content" value="<?php 
echo $this->get_value( 'content_parent_selector', $values );
?>">
		<div class="wpadcb-form-desc"><em><?php 
_e( 'CSS selector for the element that directly wraps your post paragraphs. Default: .entry-content — change only if your theme uses a different selector.', WPADC_SLUG );
?></em></div>
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
echo $this->input_id( 'post_type' );
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
    echo $this->option_selected( $values, 'post_type', $post_type->name );
    ?>" data-optselector-value="<?php 
    echo $post_type->name;
    ?>"><?php 
    echo $post_type->label;
    ?></button>
			<?php 
}
// end - foreach
?>
		</div>
		<input<?php 
echo $this->attributes( 'post_type' );
?> type="hidden" value="<?php 
echo $this->get_value( 'post_type', $values );
?>" class="wpadcb-optselector-input" />
	</div><!-- .wpadcb-form-control -->
	<?php 
?>

	<div class="wpadcb-form-control wpadcb-input-type-optselector">
		<label class="wpadcb-input-label" for=<?php 
echo $this->input_id( 'condition' );
?>>
			<?php 
_e( 'Post Type Condition', WPADC_SLUG );
?>
		</label>
		<div class="wpadcb-optselector-options">
			<button class="wpadcb-optselector-btn<?php 
echo $this->option_selected( $values, 'condition', 'none' );
?>" data-optselector-value="none"><?php 
_e( 'Show on All', WPADC_SLUG );
?></button>
			<button class="wpadcb-optselector-btn<?php 
echo $this->option_selected( $values, 'condition', 'taxonomy' );
?>" data-optselector-value="taxonomy"><?php 
_e( 'Only Show on Selected Taxonomies (categories / tags )', WPADC_SLUG );
?></button>
			<button class="wpadcb-optselector-btn<?php 
echo $this->option_selected( $values, 'condition', 'ids' );
?>" data-optselector-value="ids"><?php 
_e( 'Only Show on Selected Post(s)', WPADC_SLUG );
?></button>
		</div>
		<input<?php 
echo $this->attributes( 'condition' );
?> type="hidden" value="<?php 
echo $this->get_value( 'condition', $values );
?>" class="wpadcb-optselector-input" />
	</div><!-- .wpadcb-form-control -->

	<div class="wpadcb-form-control" <?php 
echo $this->show_if( 'condition', 'taxonomy', 'opt_selected' );
?>>
		<label class="wpadcb-input-label" for=<?php 
echo $this->input_id( 'taxonomies' );
?>><?php 
_e( 'Please insert taxonomy slug(s) below: ', WPADC_SLUG );
?></label>
		<input<?php 
echo $this->attributes( 'taxonomies' );
?> type="text" class="wpadcb-input-text" placeholder="<?php 
_e( 'Each taxonomy slug must be separated by comma - Ex: slug-1,slug-2,slug-3', WPADC_SLUG );
?>" value="<?php 
echo $this->get_value( 'taxonomies', $values );
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
echo wpadc()->plugin_url( "assets/img/help/" );
?>taxonomy_slug.jpg" />
				</div>
			</div><!-- .wpadcb-message-success -->
		</div>

	</div><!-- .wpadcb-form-control -->

	<div class="wpadcb-form-control" <?php 
echo $this->show_if( 'condition', 'ids', 'opt_selected' );
?>>
		<label class="wpadcb-input-label" for=<?php 
echo $this->input_id( 'ids' );
?>><?php 
_e( 'Please insert post ID(s) below:', WPADC_SLUG );
?></label>
		<input<?php 
echo $this->attributes( 'ids' );
?> type="text" class="wpadcb-input-text" placeholder="<?php 
_e( 'Each Post ID must be separated by comma - Ex: 1,2,3', WPADC_SLUG );
?>" value="<?php 
echo $this->get_value( 'ids', $values );
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
echo wpadc()->plugin_url( "assets/img/help/" );
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
    echo $this->input_id( 'advanced_filter' );
    ?>>
				<?php 
    _e( 'Advanced Filtering', WPADC_SLUG );
    ?>
			</label>
			<div class="wpadcb-optselector-options">
				<button class="wpadcb-optselector-btn<?php 
    echo $this->option_selected( $values, 'advanced_filter', 'none' );
    ?>" data-optselector-value="none"><?php 
    _e( 'Disabled', WPADC_SLUG );
    ?></button>
				<button class="wpadcb-optselector-btn<?php 
    echo $this->option_selected( $values, 'advanced_filter', 'exclude_taxonomy' );
    ?>" data-optselector-value="exclude_taxonomy"><?php 
    _e( 'Exclude Selected Taxonomies (categories / tags )', WPADC_SLUG );
    ?></button>
				<button class="wpadcb-optselector-btn<?php 
    echo $this->option_selected( $values, 'advanced_filter', 'exclude_ids' );
    ?>" data-optselector-value="exclude_ids"><?php 
    _e( 'Exclude Selected Post(s)', WPADC_SLUG );
    ?></button>
			</div>
			<input<?php 
    echo $this->attributes( 'advanced_filter' );
    ?> type="hidden" value="<?php 
    echo $this->get_value( 'advanced_filter', $values );
    ?>" class="wpadcb-optselector-input" />
		</div><!-- .wpadcb-form-control -->

		<div class="wpadcb-form-control" <?php 
    echo $this->show_if( 'advanced_filter', 'exclude_taxonomy', 'opt_selected' );
    ?>>
			<label class="wpadcb-input-label" for=<?php 
    echo $this->input_id( 'exclude_taxonomies' );
    ?>><?php 
    _e( 'Please insert taxonomy slug(s) below: ', WPADC_SLUG );
    ?></label>
			<input<?php 
    echo $this->attributes( 'exclude_taxonomies' );
    ?> type="text" class="wpadcb-input-text" placeholder="<?php 
    _e( 'Each taxonomy slug must be separated by comma - Ex: slug-1,slug-2,slug-3', WPADC_SLUG );
    ?>" value="<?php 
    echo $this->get_value( 'exclude_taxonomies', $values );
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
    echo wpadc()->plugin_url( "assets/img/help/" );
    ?>taxonomy_slug.jpg" />
					</div>
				</div><!-- .wpadcb-message-success -->
			</div>

		</div><!-- .wpadcb-form-control -->

		<div class="wpadcb-form-control" <?php 
    echo $this->show_if( 'advanced_filter', 'exclude_ids', 'opt_selected' );
    ?>>
			<label class="wpadcb-input-label" for=<?php 
    echo $this->input_id( 'excludes_ids' );
    ?>><?php 
    _e( 'Please insert post ID(s) below:', WPADC_SLUG );
    ?></label>
			<input<?php 
    echo $this->attributes( 'excludes_ids' );
    ?> type="text" class="wpadcb-input-text" placeholder="<?php 
    _e( 'Each Post ID must be separated by comma - Ex: 1,2,3', WPADC_SLUG );
    ?>" value="<?php 
    echo $this->get_value( 'excludes_ids', $values );
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
    echo wpadc()->plugin_url( "assets/img/help/" );
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
    echo $this->input_id( 'advanced_filter' );
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
    echo $this->attributes( 'advanced_filter' );
    ?> type="hidden" value="<?php 
    echo $this->get_value( 'advanced_filter', $values );
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
echo $this->input_id( 'priority' );
?>><?php 
_e( 'The lower the number, the higher the priority', WPADC_SLUG );
?></label>
		<input<?php 
echo $this->attributes( 'priority' );
?> type="number" class="wpadcb-input-number" value="<?php 
echo $this->get_value( 'priority', $values );
?>">
	</div><!-- .wpadcb-form-control -->

</div><!-- .wpadcb-form-section -->

<!-- Save Button -->
<div id="wpadcb-form-save" class="wpadcb-form-section">
	<button type="submit" class="wpadcb-button-info wpadcb-save-changes"><?php 
_e( 'Save Changes', WPADC_SLUG );
?></button>
</div><!-- .wpadcb-form-section -->