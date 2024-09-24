<?php

/**
 * Help Page Functions
 *
 * @author 		MojofyWP
 * @package 	includes
 *
 */
/* ------------------------------------------------------------------------------- */

if ( !function_exists( 'wpadc_add_help_page' ) ) {
    /**
     * Add Help page
     *
     */
    function wpadc_add_help_page()
    {
        add_submenu_page(
            'wpadc-builder',
            // parent slug
            __( 'WP Affiliate Disclosure - Help & Support', WPADC_SLUG ),
            // page_title
            __( 'Help', WPADC_SLUG ),
            // menu_title
            'manage_options',
            // capability
            'wpadc-help',
            // menu_slug
            'wpadc_render_help_page'
        );
    }
    
    add_action( 'admin_menu', 'wpadc_add_help_page', 20 );
}

/* ------------------------------------------------------------------------------- */
if ( !function_exists( 'wpadc_render_help_page' ) ) {
    /**
     * Render shortcode generator page
     *
     * @return string
     */
    function wpadc_render_help_page()
    {
        global  $wp_affiliate_disclosure_fs ;
        $version = WPADC_VERSION;
        $active_tab = 'tutorials';
        // get current tab
        if ( isset( $_GET['tab'] ) ) {
            switch ( $_GET['tab'] ) {
                case 'resources':
                    $active_tab = 'resources';
                    break;
            }
        }
        ob_start();
        ?>
<div id="wpadc-help-page" class="wrap about-wrap">

	<h1 class="wpadc-help-header">Welcome to WP Affiliate Disclosure v<?php 
        echo  $version ;
        echo  ( $wp_affiliate_disclosure_fs->can_use_premium_code() ? ' <small>PREMIUM Version</small>' : '' ) ;
        ?></h1>

	<div class="about-text">
		Congratulations! Now you can add a customizable, FTC-compliant disclosure statement that will automatically display across your WordPress website based on the rule(s) you define.
	</div>

	<div class="wp-badge wpadc-help-logo">Version <?php 
        echo  $version ;
        ?></div>

	<h2 class="nav-tab-wrapper">
		<a href="<?php 
        echo  esc_url( admin_url( 'admin.php' ) . '?page=wpadc-builder&page=wpadc-help' ) ;
        ?>" class="nav-tab<?php 
        echo  ( $active_tab == 'tutorials' ? ' nav-tab-active' : '' ) ;
        ?>">Tutorials</a>
		<a href="<?php 
        echo  esc_url( admin_url( 'admin.php?page=wpadc-builder-contact' ) ) ;
        ?>" class="nav-tab">Help & Support</a>
		<a href="<?php 
        echo  esc_url( admin_url( 'admin.php' ) . '?page=wpadc-builder&page=wpadc-help&tab=resources' ) ;
        ?>" class="nav-tab<?php 
        echo  ( $active_tab == 'resources' ? ' nav-tab-active' : '' ) ;
        ?>">Resources</a>
	</h2>

	<?php 
        switch ( $active_tab ) {
            case 'resources':
                ?>
			<div class="wpadc-help-tab">

				<h3>Resources</h3>

				<p>Here are a few resources available that we believe would help you to get around this plugin:</p>

				<ul>
					<li><a href="https://www.mojofywp.com/" target="_blank" rel="nofollow">Official Website</a></li>
					<li><a href="https://www.mojofywp.com/wp-affiliate-disclosure" target="_blank" rel="nofollow">About the plugin</a></li>
					<li><a href="https://www.mojofywp.com/wp-affiliate-disclosure/demo" target="_blank" rel="nofollow">Plugin demo Page</a></li>
					<li><a href="<?php 
                echo  esc_url( admin_url( 'admin.php?page=wpadc-builder-contact' ) ) ;
                ?>">Help & Support</a></li>
					<li><a href="https://www.facebook.com/mojofywp/" target="_blank" rel="nofollow">Facebook Page</a></li>
				</ul>
				
			</div><!-- .wpadc-help-tab -->
			<?php 
                break;
            case 'tutorials':
            default:
                ?>
			<div class="wpadc-help-tab">

				<h3>Tutorials</h3>

				<?php 
                // load tutorial
                try {
                    require_once wpadc()->plugin_path( 'includes/help/free_version.php' );
                } catch ( Exception $e ) {
                    echo  '<br><br>View Form Error' ;
                }
                ?>

			</div><!-- .wpadc-help-tab -->
			<?php 
                break;
        }
        ?>

</div><!-- .wpadc-about-page -->
<?php 
        $html = ob_get_clean();
        echo  apply_filters( 'wpadc_render_help_page', ( !empty($html) ? $html : '' ) ) ;
    }

}