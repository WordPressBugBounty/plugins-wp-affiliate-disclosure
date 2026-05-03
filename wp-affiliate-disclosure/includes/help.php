<?php
/**
 * Help Page Functions
 *
 * @author  MojofyWP
 * @package includes
 */

if ( ! function_exists( 'wpadc_add_help_page' ) ) :
/**
 * Add Help page.
 */
function wpadc_add_help_page() {
	add_submenu_page(
		'wpadc-builder',
		__( 'WP Affiliate Disclosure - Help & Support', WPADC_SLUG ),
		__( 'Help', WPADC_SLUG ),
		'manage_options',
		'wpadc-help',
		'wpadc_render_help_page'
	);
}
add_action( 'admin_menu', 'wpadc_add_help_page', 20 );
endif;

if ( ! function_exists( 'wpadc_help_tabs' ) ) :
/**
 * Get Help page tabs.
 *
 * @return array
 */
function wpadc_help_tabs() {
	return array(
		'getting-started' => __( 'Getting Started', WPADC_SLUG ),
		'faq'             => __( 'FAQ', WPADC_SLUG ),
		'troubleshooting' => __( 'Troubleshooting', WPADC_SLUG ),
		'resources'       => __( 'Resources', WPADC_SLUG ),
		'changelog'       => __( 'What\'s New', WPADC_SLUG ),
	);
}
endif;

if ( ! function_exists( 'wpadc_help_tab_url' ) ) :
/**
 * Build a Help page tab URL.
 *
 * @param string $tab   Tab slug.
 * @param string $guide Optional Getting Started guide slug.
 * @return string
 */
function wpadc_help_tab_url( $tab, $guide = '' ) {
	$args = array(
		'page' => 'wpadc-help',
		'tab'  => sanitize_key( $tab ),
	);

	if ( '' !== $guide ) {
		$args['guide'] = sanitize_key( $guide );
	}

	return esc_url( add_query_arg( $args, admin_url( 'admin.php' ) ) );
}
endif;

if ( ! function_exists( 'wpadc_help_gs_guides' ) ) :
/**
 * Get Getting Started guide registry.
 *
 * @return array Map of slug => array( label, summary, icon, renderer ).
 */
function wpadc_help_gs_guides() {
	return array(
		'quick-setup' => array(
			'label'    => __( 'Quick Setup', WPADC_SLUG ),
			'summary'  => __( 'Show a disclosure on all your posts in under 2 minutes.', WPADC_SLUG ),
			'icon'     => 'dashicons-admin-site-alt3',
			'renderer' => 'wpadc_render_help_gs_quick_setup',
		),
		'amazon' => array(
			'label'    => __( 'Amazon Associates', WPADC_SLUG ),
			'summary'  => __( 'Set up the required Amazon Associate disclosure in one click.', WPADC_SLUG ),
			'icon'     => 'dashicons-cart',
			'renderer' => 'wpadc_render_help_gs_amazon',
		),
		'ftc' => array(
			'label'    => __( 'FTC Compliance', WPADC_SLUG ),
			'summary'  => __( 'Add a generic FTC-compliant disclosure for any affiliate program.', WPADC_SLUG ),
			'icon'     => 'dashicons-shield-alt',
			'renderer' => 'wpadc_render_help_gs_ftc',
		),
		'style' => array(
			'label'    => __( 'Style It', WPADC_SLUG ),
			'summary'  => __( 'Apply a preset or pick custom colors and spacing.', WPADC_SLUG ),
			'icon'     => 'dashicons-art',
			'renderer' => 'wpadc_render_help_gs_style',
		),
		'placement' => array(
			'label'    => __( 'Precise Placement', WPADC_SLUG ),
			'summary'  => __( 'After-paragraph, shortcode, and inline placement options.', WPADC_SLUG ),
			'icon'     => 'dashicons-editor-insertmore',
			'renderer' => 'wpadc_render_help_gs_placement',
		),
	);
}
endif;

if ( ! function_exists( 'wpadc_help_faq_entries' ) ) :
/**
 * Get FAQ entries.
 *
 * @return array
 */
function wpadc_help_faq_entries() {
	return array(
		array(
			'id'       => 'disclosure-not-showing',
			'question' => 'My disclosure is not showing on any posts',
			'answer'   => '<p>Work through these checks in order.</p><ol><li>Confirm you have at least one saved disclosure rule.</li><li>Edit the rule and make sure the position includes Before Content or After Content.</li><li>Check that the selected post type and conditions match the post you are viewing.</li><li>Clear any page cache, object cache, CDN cache, or optimization plugin cache.</li><li>Temporarily test with a default WordPress theme to confirm the theme uses the standard <code>the_content</code> filter.</li></ol>',
			'tags'     => 'display visibility not working missing empty',
		),
		array(
			'id'       => 'disclosure-wrong-pages',
			'question' => 'My disclosure shows on the wrong pages',
			'answer'   => '<p>This usually means the rule conditions are broader than intended.</p><ol><li>Open the rule and review the Conditions section.</li><li>Use taxonomy targeting for category or tag based placement. Enter slugs, not display names.</li><li>Use selected post IDs when the disclosure should appear on individual posts only.</li><li>If multiple rules overlap, remember that the lowest priority number wins.</li></ol>',
			'tags'     => 'display conditions wrong pages targeting categories',
		),
		array(
			'id'       => 'category-targeting',
			'question' => 'How do I show disclosures on specific categories only?',
			'answer'   => '<p>Edit the rule, set the post type to Posts, choose taxonomy targeting, and enter the category slugs separated by commas. You can find category slugs under Posts > Categories in the Slug column.</p><p>Example: <code>reviews,sponsored,product-roundups</code></p>',
			'tags'     => 'categories targeting taxonomy slugs conditions',
		),
		array(
			'id'       => 'page-builder-compatibility',
			'question' => 'Can I use this plugin with page builders?',
			'answer'   => '<p>Yes, in most cases. WP Affiliate Disclosure inserts disclosures through WordPress content filters. Page builders that pass output through <code>the_content</code> should work normally.</p><p>If a builder template bypasses WordPress content filters, use the <code>[wpadc]</code> shortcode in the template or test with the builder support team where content filters are applied.</p>',
			'tags'     => 'elementor divi beaver builder compatibility shortcode',
		),
		array(
			'id'       => 'shortcode-not-working',
			'question' => 'My shortcode is not working',
			'answer'   => '<p>Use <code>[wpadc]</code> for the first matching rule or <code>[wpadc id="123"]</code> for a specific disclosure rule. Confirm the rule ID exists, the rule is published, and the Shortcode position is selected in the rule settings.</p>',
			'tags'     => 'shortcode manual placement id',
		),
		array(
			'id'       => 'find-post-id',
			'question' => 'How do I find a post ID?',
			'answer'   => '<p>In wp-admin, go to Posts or Pages, hover over the item, and look at the browser status bar. The number after <code>post=</code> is the post ID. You can also open the editor and read the ID from the URL.</p>',
			'tags'     => 'post id selected posts',
		),
		array(
			'id'       => 'taxonomy-slugs',
			'question' => 'Should I enter category names or slugs?',
			'answer'   => '<p>Enter slugs. For example, if the category name is Affiliate Reviews and the slug is <code>affiliate-reviews</code>, enter <code>affiliate-reviews</code>. Multiple slugs should be comma separated.</p>',
			'tags'     => 'taxonomy category tag slug slugs',
		),
		array(
			'id'       => 'only-one-disclosure',
			'question' => 'Why do I only see one disclosure when multiple rules match?',
			'answer'   => '<p>The plugin intentionally renders only the first matching rule for each position. This prevents duplicate legal notices. If multiple rules match the same post, the rule with the lowest priority number is used.</p>',
			'tags'     => 'priority multiple rules one disclosure',
		),
		array(
			'id'       => 'cache-after-update',
			'question' => 'Why did changes not appear after I saved a rule?',
			'answer'   => '<p>Most often this is caching. Clear your WordPress caching plugin, hosting cache, CDN cache, and browser cache. Then test in a private browser window while logged out.</p>',
			'tags'     => 'cache caching update saved changes',
		),
		array(
			'id'       => 'layout-breaks',
			'question' => 'The disclosure breaks my page layout',
			'answer'   => '<p>Check the disclosure statement for unclosed HTML tags, unsupported markup, or copied content from a visual editor. Try plain text first. If plain text works, add formatting back one piece at a time.</p>',
			'tags'     => 'layout css html broken styling',
		),
		array(
			'id'       => 'amazon-required-disclosure',
			'question' => 'What disclosure does Amazon require?',
			'answer'   => '<p>Amazon\'s Operating Agreement (Section 5) requires you to include this statement on your site: <em>"As an Amazon Associate I earn from qualifying purchases."</em></p><p>You can use the built-in Amazon Associates template to set this up in one click. Go to WP Affiliate Disclosure &rarr; Add New Rule &rarr; click <strong>Use This</strong> on the Amazon Associates template.</p>',
			'tags'     => 'amazon associates operating agreement template',
		),
		array(
			'id'       => 'ftc-required-disclosure',
			'question' => 'What does the FTC require for affiliate disclosures?',
			'answer'   => '<p>The FTC requires that you clearly and conspicuously disclose your affiliate relationships to readers. The disclosure should be placed near the affiliate links (not just in the footer) and should be easy to understand. There is no specific required language, but the disclosure must make clear that you may receive compensation.</p><p>Our General FTC template provides compliant language you can customize. For full details, see the FTC\'s <a href="https://www.ftc.gov/business-guidance/resources/disclosures-101-social-media-influencers" target="_blank" rel="noopener">Disclosures 101 for Social Media Influencers</a>.</p>',
			'tags'     => 'ftc compliance endorsement guides template',
		),
		array(
			'id'       => 'after-paragraph-placement',
			'question' => 'How does after-paragraph placement work?',
			'answer'   => '<p>After-paragraph placement inserts the disclosure after the Nth paragraph of your post content. Only direct child <code>&lt;p&gt;</code> elements of the content container are counted — paragraphs that appear inside other injected disclosures or block wrappers are ignored. If the post has fewer paragraphs than selected, the disclosure appears after the last paragraph. If the post has no paragraphs, it is appended to the end of the content.</p><p>By default the plugin targets paragraphs that are direct children of <code>.entry-content</code>. If your theme uses a different wrapper, set the <strong>Content Container Selector</strong> field in the rule settings.</p>',
			'tags'     => 'after paragraph placement insertion container selector entry-content theme',
		),
		array(
			'id'       => 'troubleshoot-disclosure-isnt-showing',
			'question' => 'My disclosure isn\'t showing',
			'answer'   => '<p>Run through this checklist:</p><ol><li>Is the rule published? Check the rule list.</li><li>Does the rule have targeting conditions that match your post?</li><li>If placement is set to <strong>Shortcode</strong>, did you actually paste <code>[wpadc]</code> or <code>[affiliate_disclosure]</code> into the content?</li><li>Is a caching plugin serving stale pages? Purge cache and reload.</li><li>Does your theme call <code>the_content</code> filter? Some custom templates bypass it.</li></ol>',
			'tags'     => 'troubleshoot not showing missing display',
		),
		array(
			'id'       => 'troubleshoot-plugin-broke-site',
			'question' => 'The plugin broke my site after updating',
			'answer'   => '<p>This is usually a PHP version or hosting issue:</p><ol><li>Confirm with your host that PHP 7.4+ is running.</li><li>Enable <code>WP_DEBUG_LOG</code> and check <code>wp-content/debug.log</code> for the actual error.</li><li>Try deactivating and reactivating the plugin.</li><li>Switch to a default theme to rule out theme conflicts.</li></ol>',
			'tags'     => 'broken site update fatal error white screen',
		),
		array(
			'id'       => 'troubleshoot-search-engine-indexed',
			'question' => 'Is my disclosure indexed by search engines?',
			'answer'   => '<p>Yes — the disclosure text is regular page content and is indexed normally. This is generally a good thing for FTC compliance signals. If you want to keep it out of Google search snippets specifically, you can wrap it in a <code>data-nosnippet</code> attribute via custom code, but the text will still be indexed.</p>',
			'tags'     => 'seo indexed search engines crawl',
		),
		array(
			'id'       => 'troubleshoot-featured-image-disappeared',
			'question' => 'My featured image disappeared',
			'answer'   => '<p>v1.4 includes a fix for a known issue where the disclosure filter could interfere with featured-image processing. Update to v1.4 or later. If the issue persists, test with a default theme like Twenty Twenty-Four to rule out theme conflicts.</p>',
			'tags'     => 'featured image missing thumbnail',
		),
	);
}
endif;

if ( ! function_exists( 'wpadc_render_help_page' ) ) :
/**
 * Render Help page.
 */
function wpadc_render_help_page() {
	global $wp_affiliate_disclosure_fs;

	$tabs       = wpadc_help_tabs();
	$active_tab = 'getting-started';

	if ( isset( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$requested_tab = sanitize_key( wp_unslash( $_GET['tab'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( isset( $tabs[ $requested_tab ] ) ) {
			$active_tab = $requested_tab;
		}
	}

	$is_premium = is_object( $wp_affiliate_disclosure_fs ) && $wp_affiliate_disclosure_fs->can_use_premium_code();

	ob_start();
	?>
	<div id="wpadc-help-page" class="wrap about-wrap">

		<h1 class="wpadc-help-header">
			<?php echo esc_html( sprintf( __( 'Welcome to WP Affiliate Disclosure v%s', WPADC_SLUG ), WPADC_VERSION ) ); ?>
			<?php if ( $is_premium ) : ?>
				<small><?php esc_html_e( 'Premium Version', WPADC_SLUG ); ?></small>
			<?php endif; ?>
		</h1>

		<div class="about-text">
			<?php esc_html_e( 'Automatically add FTC-compliant disclosure statements to your WordPress site. Set up rules once, and disclosures appear where you need them.', WPADC_SLUG ); ?>
		</div>

		<div class="wp-badge wpadc-help-logo"><?php echo esc_html( sprintf( __( 'Version %s', WPADC_SLUG ), WPADC_VERSION ) ); ?></div>

		<h2 class="nav-tab-wrapper">
			<?php foreach ( $tabs as $tab => $label ) : ?>
				<a href="<?php echo wpadc_help_tab_url( $tab ); ?>" class="nav-tab<?php echo $active_tab === $tab ? ' nav-tab-active' : ''; ?>">
					<?php echo esc_html( $label ); ?>
				</a>
			<?php endforeach; ?>
		</h2>

		<?php
		switch ( $active_tab ) {
			case 'faq':
				wpadc_render_help_faq_tab();
				break;
			case 'troubleshooting':
				wpadc_render_help_troubleshooting_tab();
				break;
			case 'resources':
				wpadc_render_help_resources_tab();
				break;
			case 'changelog':
				wpadc_render_help_changelog_tab();
				break;
			case 'getting-started':
			default:
				wpadc_render_help_getting_started_tab();
				break;
		}
		?>

	</div>
	<?php
	$html = ob_get_clean();

	echo apply_filters( 'wpadc_render_help_page', ( ! empty( $html ) ? $html : '' ) );
}
endif;

if ( ! function_exists( 'wpadc_render_help_getting_started_tab' ) ) :
/**
 * Render Getting Started tab — dispatches to hub or a guide.
 */
function wpadc_render_help_getting_started_tab() {
	static $wpadc_gs_styles_printed = false;

	if ( ! $wpadc_gs_styles_printed ) {
		$wpadc_gs_styles_printed = true;
		?>
		<style>
			/* shared figure / section helpers */
			.wpadc-gs-figure { margin: 12px 0 24px; }
			.wpadc-gs-figure img {
				display: block;
				max-width: 100%;
				height: auto;
				border: 1px solid #c3c4c7;
				border-radius: 4px;
				box-shadow: 0 1px 3px rgba(0,0,0,0.08);
			}
			.wpadc-gs-figure figcaption {
				margin-top: 6px;
				color: #50575e;
				font-size: 13px;
				font-style: italic;
			}
			.wpadc-gs-compare {
				display: flex;
				gap: 16px;
				flex-wrap: wrap;
				margin: 12px 0 24px;
			}
			.wpadc-gs-compare > .wpadc-gs-figure {
				flex: 1 1 360px;
				margin: 0;
			}
			.wpadc-gs-compare > .wpadc-gs-figure h5 {
				margin: 0 0 6px;
				font-size: 13px;
				text-transform: uppercase;
				letter-spacing: 0.04em;
				color: #50575e;
			}
			.wpadc-gs-section {
				padding: 16px 20px;
				margin-bottom: 16px;
				background: #fff;
				border: 1px solid #ddd;
				border-left: 4px solid #2271b1;
				border-radius: 3px;
			}
			.wpadc-gs-section > h4 { margin-top: 0; }
			.wpadc-gs-section ol,
			.wpadc-gs-section ul { margin-left: 22px; }

			/* hub card grid */
			.wpadc-gs-card-grid {
				display: flex;
				flex-wrap: wrap;
				gap: 16px;
				margin: 20px 0 28px;
			}
			.wpadc-gs-card {
				flex: 1 1 220px;
				display: flex;
				flex-direction: column;
				padding: 18px 16px 14px;
				background: #fff;
				border: 1px solid #ddd;
				border-radius: 4px;
				text-decoration: none;
				color: inherit;
				transition: border-color 0.15s, box-shadow 0.15s;
			}
			.wpadc-gs-card:hover,
			.wpadc-gs-card:focus {
				border-color: #2271b1;
				box-shadow: 0 2px 8px rgba(34,113,177,0.12);
				text-decoration: none;
				color: inherit;
				outline: none;
			}
			.wpadc-gs-card-icon {
				font-size: 20px;
				margin-bottom: 10px;
				color: #2271b1;
			}
			.wpadc-gs-card h4 {
				margin: 0 0 6px;
				font-size: 14px;
				font-weight: 600;
				color: #1d2327;
			}
			.wpadc-gs-card p {
				flex: 1;
				margin: 0 0 12px;
				font-size: 13px;
				color: #50575e;
				line-height: 1.5;
			}
			.wpadc-gs-card-cta {
				font-size: 13px;
				font-weight: 600;
				color: #2271b1;
			}

			/* guide back link */
			.wpadc-gs-back {
				display: inline-flex;
				align-items: center;
				gap: 4px;
				margin-bottom: 20px;
				font-size: 13px;
				color: #2271b1;
				text-decoration: none;
			}
			.wpadc-gs-back:hover { text-decoration: underline; }
			.wpadc-gs-figure-small {
				max-width: 600px;
				margin-right: auto;
			}
		</style>
		<?php
	}

	$guides      = wpadc_help_gs_guides();
	$guide_slug  = '';

	if ( isset( $_GET['guide'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$requested = sanitize_key( wp_unslash( $_GET['guide'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( array_key_exists( $requested, $guides ) ) {
			$guide_slug = $requested;
		}
	}

	if ( '' !== $guide_slug ) {
		$renderer = $guides[ $guide_slug ]['renderer'];
		if ( function_exists( $renderer ) ) {
			call_user_func( $renderer );
			return;
		}
	}

	wpadc_render_help_gs_hub();
}
endif;

if ( ! function_exists( 'wpadc_render_help_gs_back' ) ) :
/**
 * Output the "← Back to Getting Started" link.
 */
function wpadc_render_help_gs_back() {
	?>
	<a class="wpadc-gs-back" href="<?php echo wpadc_help_tab_url( 'getting-started' ); ?>">
		<span class="dashicons dashicons-arrow-left-alt2" style="font-size:16px;width:16px;height:16px;line-height:16px;"></span>
		<?php esc_html_e( 'Back to Getting Started', WPADC_SLUG ); ?>
	</a>
	<?php
}
endif;

if ( ! function_exists( 'wpadc_render_help_gs_hub' ) ) :
/**
 * Render the Getting Started hub — 5 task cards.
 */
function wpadc_render_help_gs_hub() {
	$guides = wpadc_help_gs_guides();
	?>
	<div class="wpadc-help-tab">
		<h3><?php esc_html_e( 'Getting Started', WPADC_SLUG ); ?></h3>
		<p><?php esc_html_e( 'Pick a guide below to get set up quickly. Each one focuses on a single goal and takes less than 5 minutes.', WPADC_SLUG ); ?></p>

		<div class="wpadc-gs-card-grid">
			<?php foreach ( $guides as $slug => $guide ) : ?>
				<a class="wpadc-gs-card" href="<?php echo wpadc_help_tab_url( 'getting-started', $slug ); ?>">
					<div class="wpadc-gs-card-icon">
						<span class="dashicons <?php echo esc_attr( $guide['icon'] ); ?>"></span>
					</div>
					<h4><?php echo esc_html( $guide['label'] ); ?></h4>
					<p><?php echo esc_html( $guide['summary'] ); ?></p>
					<span class="wpadc-gs-card-cta"><?php esc_html_e( 'Start &rarr;', WPADC_SLUG ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}
endif;

if ( ! function_exists( 'wpadc_render_help_gs_quick_setup' ) ) :
/**
 * Guide: Quick Setup — create a basic rule that shows on all posts.
 */
function wpadc_render_help_gs_quick_setup() {
	$img = wpadc()->plugin_url( 'assets/img/help/' );
	?>
	<div class="wpadc-help-tab">
		<?php wpadc_render_help_gs_back(); ?>
		<h3><?php esc_html_e( 'Quick Setup', WPADC_SLUG ); ?></h3>
		<p><?php esc_html_e( 'Follow these steps to show a disclosure on all your blog posts in under 2 minutes.', WPADC_SLUG ); ?></p>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '1. Open the plugin', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'In the WordPress admin sidebar, click WP Affiliate Disclosure to open the Overview screen. This is where every disclosure rule is listed.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure">
				<img src="<?php echo esc_url( $img . 'admin-overview.png' ); ?>" alt="<?php esc_attr_e( 'Overview screen with Add New Rule button', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'Click Add New Rule to begin.', WPADC_SLUG ); ?></figcaption>
			</figure>
		</div>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '2. Name and create the rule', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'Give the rule a clear internal name (e.g. "All Blog Posts"), then click Create. The name never appears on the frontend.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure wpadc-gs-figure-small">
				<img src="<?php echo esc_url( $img . 'add-rule-modal-named.png' ); ?>" alt="<?php esc_attr_e( 'Add New Rule modal with a name typed in', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'Name the rule, then click Create.', WPADC_SLUG ); ?></figcaption>
			</figure>
			<p><?php esc_html_e( 'When the confirmation appears, click Edit to open the rule settings.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure wpadc-gs-figure-small">
				<img src="<?php echo esc_url( $img . 'add-rule-success.png' ); ?>" alt="<?php esc_attr_e( 'Confirmation with Close and Edit buttons', WPADC_SLUG ); ?>" />
			</figure>
		</div>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '3. Write your disclosure text and choose placement', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'Type your disclosure in the Disclosure Statement editor. Plain text works best. Then click Before Post Content or After Post Content under Show Statement At.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure">
				<img src="<?php echo esc_url( $img . 'edit-section-statement-free.png' ); ?>" alt="<?php esc_attr_e( 'Disclosure Statement section with placement buttons', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'Write your text, then select a placement position.', WPADC_SLUG ); ?></figcaption>
			</figure>
		</div>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '4. Target the right posts and save', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'Scroll to Conditions. Set Post Type to Posts and leave the condition set to None to show on all posts. Click Save Settings.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure">
				<img src="<?php echo esc_url( $img . 'edit-section-condition-free.png' ); ?>" alt="<?php esc_attr_e( 'Conditions section with post type selector', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'None = show on all posts of the selected type.', WPADC_SLUG ); ?></figcaption>
			</figure>
			<p><?php esc_html_e( 'Open any published blog post. The disclosure appears automatically in the position you chose.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure">
				<img src="<?php echo esc_url( $img . 'frontend-before.png' ); ?>" alt="<?php esc_attr_e( 'Disclosure displayed before post content on the frontend', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'Before Content places the disclosure above the first paragraph.', WPADC_SLUG ); ?></figcaption>
			</figure>
		</div>
	</div>
	<?php
}
endif;

if ( ! function_exists( 'wpadc_render_help_gs_amazon' ) ) :
/**
 * Guide: Amazon Associates — set up the required disclosure in one click.
 */
function wpadc_render_help_gs_amazon() {
	$img = wpadc()->plugin_url( 'assets/img/help/' );
	?>
	<div class="wpadc-help-tab">
		<?php wpadc_render_help_gs_back(); ?>
		<h3><?php esc_html_e( 'Amazon Associates', WPADC_SLUG ); ?></h3>
		<p><?php esc_html_e( "Amazon's Operating Agreement (Section 5) requires every Associates site to include this statement:", WPADC_SLUG ); ?></p>
		<blockquote style="border-left:4px solid #2271b1;margin:0 0 16px;padding:8px 16px;background:#f6f7f7;font-style:italic;">
			<?php esc_html_e( '"As an Amazon Associate I earn from qualifying purchases."', WPADC_SLUG ); ?>
		</blockquote>
		<p><?php esc_html_e( 'The built-in Amazon Associates template inserts this text for you in one click.', WPADC_SLUG ); ?></p>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '1. Create a new rule', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'Go to WP Affiliate Disclosure &rarr; Overview, click Add New Rule, give it a name such as "Amazon Disclosure", then click Create and Edit.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure wpadc-gs-figure-small">
				<img src="<?php echo esc_url( $img . 'add-rule-modal-named.png' ); ?>" alt="<?php esc_attr_e( 'Add New Rule modal with name typed', WPADC_SLUG ); ?>" />
			</figure>
		</div>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '2. Insert the Amazon Associates template', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'In the rule editor, find the Start from a template row above the text editor. Click Use This on the Amazon Associates card.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure">
				<img src="<?php echo esc_url( $img . 'template-cards.png' ); ?>" alt="<?php esc_attr_e( 'Template cards row showing Amazon Associates and FTC options', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'Click Use This on the Amazon Associates card.', WPADC_SLUG ); ?></figcaption>
			</figure>
			<p><?php esc_html_e( 'Choose Short (the required one-line statement) or Long (adds context about what qualifying purchases means), then click Insert.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure wpadc-gs-figure-small">
				<img src="<?php echo esc_url( $img . 'template-amazon-variant.png' ); ?>" alt="<?php esc_attr_e( 'Amazon Associates card with Short and Long variant picker and Insert button', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'Short meets the minimum requirement. Long adds helpful context.', WPADC_SLUG ); ?></figcaption>
			</figure>
		</div>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '3. Choose placement and targeting', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'Under Show Statement At, select Before Post Content so the disclosure is visible at the top of every Amazon review post. In the Conditions section, set the post type and any targeting rules (e.g. a specific category for your Amazon reviews).', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure">
				<img src="<?php echo esc_url( $img . 'edit-section-condition-free.png' ); ?>" alt="<?php esc_attr_e( 'Conditions section showing post type and targeting options', WPADC_SLUG ); ?>" />
			</figure>
			<p><?php esc_html_e( 'Click Save Settings. Your Amazon disclosure is live.', WPADC_SLUG ); ?></p>
		</div>
	</div>
	<?php
}
endif;

if ( ! function_exists( 'wpadc_render_help_gs_ftc' ) ) :
/**
 * Guide: FTC Compliance — add a generic FTC disclosure using the built-in template.
 */
function wpadc_render_help_gs_ftc() {
	$img = wpadc()->plugin_url( 'assets/img/help/' );
	?>
	<div class="wpadc-help-tab">
		<?php wpadc_render_help_gs_back(); ?>
		<h3><?php esc_html_e( 'FTC Compliance', WPADC_SLUG ); ?></h3>
		<p><?php esc_html_e( 'The FTC requires that affiliate relationships be clearly disclosed to readers near the affiliate content — not buried in a footer. There is no required wording, but the disclosure must be clear and conspicuous.', WPADC_SLUG ); ?></p>
		<p><?php esc_html_e( 'The built-in General FTC template provides compliant language you can use as-is or customise.', WPADC_SLUG ); ?></p>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '1. Create a new rule', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'Go to WP Affiliate Disclosure &rarr; Overview, click Add New Rule, name it (e.g. "FTC Disclosure"), then click Create and Edit.', WPADC_SLUG ); ?></p>
		</div>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '2. Insert the General FTC template', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'In the rule editor, click Use This on the General FTC Compliance card in the Start from a template row.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure">
				<img src="<?php echo esc_url( $img . 'template-cards.png' ); ?>" alt="<?php esc_attr_e( 'Template cards row', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'Click Use This on the General FTC Compliance card.', WPADC_SLUG ); ?></figcaption>
			</figure>
			<p><?php esc_html_e( 'Choose Short for a one-sentence disclosure or Long for a paragraph that explains what compensation you may receive. Click Insert.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure wpadc-gs-figure-small">
				<img src="<?php echo esc_url( $img . 'template-ftc-variant.png' ); ?>" alt="<?php esc_attr_e( 'FTC template card with variant picker', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'Both variants are FTC-compliant; the Long version provides more reader context.', WPADC_SLUG ); ?></figcaption>
			</figure>
			<p><?php esc_html_e( 'The text is inserted into the editor. Edit it to match your site and programs before saving.', WPADC_SLUG ); ?></p>
		</div>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '3. Choose placement and targeting', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'Select Before Post Content under Show Statement At to keep the disclosure above the fold and visible before readers reach any affiliate links. Set targeting to match the posts where affiliate links appear.', WPADC_SLUG ); ?></p>
			<p><?php esc_html_e( 'Click Save Settings.', WPADC_SLUG ); ?></p>
		</div>
	</div>
	<?php
}
endif;

if ( ! function_exists( 'wpadc_render_help_gs_style' ) ) :
/**
 * Guide: Style It — presets and custom colors.
 */
function wpadc_render_help_gs_style() {
	$img = wpadc()->plugin_url( 'assets/img/help/' );
	?>
	<div class="wpadc-help-tab">
		<?php wpadc_render_help_gs_back(); ?>
		<h3><?php esc_html_e( 'Style It', WPADC_SLUG ); ?></h3>
		<p><?php esc_html_e( 'Every rule has its own Appearance section. By default no plugin styles are applied — your theme controls the look of the disclosure. Turn on the Customize appearance toggle to apply a preset or set custom colors and spacing.', WPADC_SLUG ); ?></p>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '1. Enable appearance customization', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'Open the rule editor and scroll to the Appearance section. By default the section is collapsed and no plugin styles are output — your existing CSS is untouched.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure wpadc-gs-figure-small">
				<img src="<?php echo esc_url( $img . 'style-customize-toggle-off.png' ); ?>" alt="<?php esc_attr_e( 'Appearance section with the Customize appearance checkbox unchecked', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'By default the toggle is off and no styles are injected.', WPADC_SLUG ); ?></figcaption>
			</figure>
			<p><?php esc_html_e( 'Check the Customize appearance checkbox to reveal the preset buttons and style fields. This is a per-rule setting — each rule can be styled independently.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure wpadc-gs-figure-small">
				<img src="<?php echo esc_url( $img . 'style-customize-toggle-on.png' ); ?>" alt="<?php esc_attr_e( 'Appearance section with the toggle checked and preset buttons visible', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'Checking the toggle reveals the preset buttons and all style fields.', WPADC_SLUG ); ?></figcaption>
			</figure>
		</div>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '2. Apply a preset', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'With the toggle on, click one of the five preset buttons to apply a complete set of styles at once:', WPADC_SLUG ); ?></p>
			<ul>
				<li><strong><?php esc_html_e( 'Minimal', WPADC_SLUG ); ?></strong> &mdash; <?php esc_html_e( 'Clean, subtle background, light border.', WPADC_SLUG ); ?></li>
				<li><strong><?php esc_html_e( 'Boxed', WPADC_SLUG ); ?></strong> &mdash; <?php esc_html_e( 'White box with a light border. Works with any theme.', WPADC_SLUG ); ?></li>
				<li><strong><?php esc_html_e( 'Banner', WPADC_SLUG ); ?></strong> &mdash; <?php esc_html_e( 'Colored background, high contrast — stands out clearly.', WPADC_SLUG ); ?></li>
				<li><strong><?php esc_html_e( 'Inline', WPADC_SLUG ); ?></strong> &mdash; <?php esc_html_e( 'Italic text that blends naturally with your content.', WPADC_SLUG ); ?></li>
				<li><strong><?php esc_html_e( 'Custom', WPADC_SLUG ); ?></strong> &mdash; <?php esc_html_e( 'Set automatically when you edit any individual field.', WPADC_SLUG ); ?></li>
			</ul>
			<div class="wpadc-gs-compare">
				<figure class="wpadc-gs-figure">
					<h5><?php esc_html_e( 'Toggle off — theme styles only', WPADC_SLUG ); ?></h5>
					<img src="<?php echo esc_url( $img . 'style-frontend-no-styles.png' ); ?>" alt="<?php esc_attr_e( 'Disclosure displayed with default theme styling, no plugin styles', WPADC_SLUG ); ?>" />
					<figcaption><?php esc_html_e( 'No styles injected — the disclosure inherits your theme\'s typography.', WPADC_SLUG ); ?></figcaption>
				</figure>
				<figure class="wpadc-gs-figure">
					<h5><?php esc_html_e( 'Boxed preset applied', WPADC_SLUG ); ?></h5>
					<img src="<?php echo esc_url( $img . 'style-frontend-boxed.png' ); ?>" alt="<?php esc_attr_e( 'Disclosure displayed with the Boxed preset — white box and light border', WPADC_SLUG ); ?>" />
					<figcaption><?php esc_html_e( 'The Boxed preset wraps the disclosure in a clean white card.', WPADC_SLUG ); ?></figcaption>
				</figure>
			</div>
		</div>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '3. Customise individual fields', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'Start from a preset and tweak it, or set every value yourself. The style grid lets you change background color, text color, border color, border style, border width, border radius, padding, and margin. Any change switches the preset to Custom.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure wpadc-gs-figure-small">
				<img src="<?php echo esc_url( $img . 'style-custom.png' ); ?>" alt="<?php esc_attr_e( 'Style grid showing color pickers and spacing sliders', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'Color pickers and sliders update the live preview instantly.', WPADC_SLUG ); ?></figcaption>
			</figure>
		</div>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( '4. Check the live preview and save', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'The Preview area at the bottom of the Appearance section shows exactly how the disclosure will look before you save. When you are happy, click Save Settings.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure wpadc-gs-figure-small">
				<img src="<?php echo esc_url( $img . 'style-preview.png' ); ?>" alt="<?php esc_attr_e( 'Live preview area showing a styled disclosure', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'The preview updates instantly as you change any field.', WPADC_SLUG ); ?></figcaption>
			</figure>
			<p><strong><?php esc_html_e( 'Note:', WPADC_SLUG ); ?></strong> <?php esc_html_e( 'If you turn the toggle back off after styling a rule, the saved colors and spacing are preserved in the database — re-enabling the toggle will restore them.', WPADC_SLUG ); ?></p>
		</div>
	</div>
	<?php
}
endif;

if ( ! function_exists( 'wpadc_render_help_gs_placement' ) ) :
/**
 * Guide: Precise Placement — after-paragraph, shortcode, and shortcode reference.
 */
function wpadc_render_help_gs_placement() {
	global $wp_affiliate_disclosure_fs;

	$is_premium = is_object( $wp_affiliate_disclosure_fs ) && $wp_affiliate_disclosure_fs->can_use_premium_code();
	$img        = wpadc()->plugin_url( 'assets/img/help/' );
	?>
	<div class="wpadc-help-tab">
		<?php wpadc_render_help_gs_back(); ?>
		<h3><?php esc_html_e( 'Precise Placement', WPADC_SLUG ); ?></h3>
		<p><?php esc_html_e( 'Beyond Before and After Content, v1.4 adds after-paragraph insertion and an enhanced shortcode. All placement options are set under Show Statement At in the rule editor.', WPADC_SLUG ); ?></p>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( 'After-paragraph placement', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'After P1, After P2, and After P3 insert the disclosure after the 1st, 2nd, or 3rd paragraph of your post content. Only direct child paragraphs of the content container are counted — paragraphs inside other injected disclosure blocks are ignored. If the post has fewer paragraphs than selected, the disclosure appears after the last paragraph.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure  wpadc-gs-figure-small">
				<img src="<?php echo esc_url( $img . 'placement-after-paragraph.png' ); ?>" alt="<?php esc_attr_e( 'Frontend post showing the disclosure correctly positioned after the first content paragraph', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'After P1 places the disclosure after the first real content paragraph, not inside other disclosure wrappers.', WPADC_SLUG ); ?></figcaption>
			</figure>
			<p><?php esc_html_e( 'When any After P position is selected, a Content Container Selector field appears below the placement buttons. The default value is .entry-content, which works with most themes. If your theme wraps post content in a different element (e.g. .post-content or .article-body), enter its CSS selector here. The plugin uses this selector both server-side and in JavaScript to find the correct paragraphs.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure  wpadc-gs-figure-small">
				<img src="<?php echo esc_url( $img . 'placement-content-container-selector.png' ); ?>" alt="<?php esc_attr_e( 'Show Statement At buttons with After P1 active and Content Container Selector field below', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'The Content Container Selector field appears when any After P position is active. The default .entry-content works for most themes.', WPADC_SLUG ); ?></figcaption>
			</figure>
		</div>

		<div class="wpadc-gs-section">
			<h4>
				<?php esc_html_e( 'Shortcode placement', WPADC_SLUG ); ?>
				<?php if ( ! $is_premium ) : ?>
					<small style="font-size:12px;background:#dba617;color:#fff;padding:2px 8px;border-radius:3px;vertical-align:middle;margin-left:6px;"><?php esc_html_e( 'Premium', WPADC_SLUG ); ?></small>
				<?php endif; ?>
			</h4>
			<p><?php esc_html_e( 'Select the Shortcode position in the rule editor to get a [wpadc] shortcode you can paste directly into post content. This places the disclosure exactly where you put it — useful when you want it next to a specific product or link.', WPADC_SLUG ); ?></p>
			<figure class="wpadc-gs-figure">
				<img src="<?php echo esc_url( $img . 'edit-section-statement-shortcode.png' ); ?>" alt="<?php esc_attr_e( 'Statement section with Shortcode selected and the shortcode field shown', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'Selecting Shortcode reveals the [wpadc] code. Copy and paste it anywhere in your content.', WPADC_SLUG ); ?></figcaption>
			</figure>
			<figure class="wpadc-gs-figure">
				<img src="<?php echo esc_url( $img . 'frontend-shortcode.png' ); ?>" alt="<?php esc_attr_e( 'Disclosure rendered inline in post content via shortcode', WPADC_SLUG ); ?>" />
				<figcaption><?php esc_html_e( 'The shortcode renders the disclosure inline at the exact position it was placed.', WPADC_SLUG ); ?></figcaption>
			</figure>
		</div>

		<div class="wpadc-gs-section">
			<h4><?php esc_html_e( 'Shortcode Reference', WPADC_SLUG ); ?></h4>
			<p><?php esc_html_e( 'The plugin registers two shortcodes that produce the same output. Both are available to free and premium users.', WPADC_SLUG ); ?></p>
			<pre style="background:#f5f5f5;padding:12px;border-radius:4px;overflow:auto;font-size:12px;line-height:1.5;">
<strong><?php esc_html_e( 'Basic Usage:', WPADC_SLUG ); ?></strong>
  [wpadc]                    <?php esc_html_e( '— Display your disclosure', WPADC_SLUG ); ?>
  [affiliate_disclosure]     <?php esc_html_e( '— Same as [wpadc] (alias)', WPADC_SLUG ); ?>

<strong><?php esc_html_e( 'Style Presets:', WPADC_SLUG ); ?></strong>
  [affiliate_disclosure style="minimal"]   <?php esc_html_e( '— Clean, subtle look', WPADC_SLUG ); ?>
  [affiliate_disclosure style="boxed"]     <?php esc_html_e( '— White box with border', WPADC_SLUG ); ?>
  [affiliate_disclosure style="banner"]    <?php esc_html_e( '— Colored banner, stands out', WPADC_SLUG ); ?>
  [affiliate_disclosure style="inline"]    <?php esc_html_e( '— Italic text, blends with content', WPADC_SLUG ); ?>

<strong><?php esc_html_e( 'Templates:', WPADC_SLUG ); ?></strong>
  [affiliate_disclosure template="amazon"]                  <?php esc_html_e( '— Amazon short text', WPADC_SLUG ); ?>
  [affiliate_disclosure template="amazon" variant="long"]   <?php esc_html_e( '— Amazon long text', WPADC_SLUG ); ?>
  [affiliate_disclosure template="ftc"]                     <?php esc_html_e( '— FTC short text', WPADC_SLUG ); ?>
  [affiliate_disclosure template="ftc" variant="long"]      <?php esc_html_e( '— FTC long text', WPADC_SLUG ); ?>

<strong><?php esc_html_e( 'Custom Text:', WPADC_SLUG ); ?></strong>
  [affiliate_disclosure text="Your custom disclosure text here."]

<strong><?php esc_html_e( 'Combine:', WPADC_SLUG ); ?></strong>
  [affiliate_disclosure style="boxed" template="amazon" variant="long"]

<strong><?php esc_html_e( 'Specific Rule (works on either shortcode):', WPADC_SLUG ); ?></strong>
  [affiliate_disclosure rule="42"]
  [wpadc rule="42"]

<strong><?php esc_html_e( 'Legacy [wpadc] wrapper id (preserved from v1.3):', WPADC_SLUG ); ?></strong>
  [wpadc id="my-box"]   <?php esc_html_e( '→ &lt;div id="wp-affiliate-disclosure-my-box" ...&gt;', WPADC_SLUG ); ?>
  <?php esc_html_e( 'Note: id is NOT a rule selector — use rule="..." for that.', WPADC_SLUG ); ?>
</pre>
		</div>
	</div>
	<?php
}
endif;

if ( ! function_exists( 'wpadc_render_help_faq_tab' ) ) :
/**
 * Render FAQ tab.
 */
function wpadc_render_help_faq_tab() {
	$faqs = wpadc_help_faq_entries();
	?>
	<div class="wpadc-help-tab">
		<h3><?php esc_html_e( 'Frequently Asked Questions', WPADC_SLUG ); ?></h3>

		<div class="wpadc-faq-search">
			<input type="search" id="wpadc-faq-filter" class="regular-text" placeholder="<?php esc_attr_e( 'Type to filter FAQs...', WPADC_SLUG ); ?>" autocomplete="off" />
			<span id="wpadc-faq-count"></span>
		</div>

		<div class="wpadc-faq-list">
			<?php foreach ( $faqs as $faq ) : ?>
				<div class="wpadc-faq-item" data-faq-search="<?php echo esc_attr( strtolower( $faq['question'] . ' ' . $faq['tags'] ) ); ?>">
					<button type="button" class="wpadc-faq-toggle" aria-expanded="false" aria-controls="wpadc-faq-answer-<?php echo esc_attr( $faq['id'] ); ?>">
						<span class="wpadc-faq-icon" aria-hidden="true">&rsaquo;</span>
						<?php echo esc_html( $faq['question'] ); ?>
					</button>
					<div class="wpadc-faq-answer" id="wpadc-faq-answer-<?php echo esc_attr( $faq['id'] ); ?>" hidden>
						<div class="wpadc-faq-answer-inner">
							<?php echo wp_kses_post( $faq['answer'] ); ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<p class="wpadc-faq-footer">
			<?php esc_html_e( 'Cannot find your answer?', WPADC_SLUG ); ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpadc-builder-contact' ) ); ?>"><?php esc_html_e( 'Contact Support', WPADC_SLUG ); ?></a>
		</p>
	</div>

	<script>
		jQuery( function( $ ) {
			var $items = $( '.wpadc-faq-item' );
			var $count = $( '#wpadc-faq-count' );

			function updateCount() {
				var visible = $items.filter( ':visible' ).length;
				$count.text( visible + ' of ' + $items.length + ' questions' );
			}

			$( '.wpadc-faq-toggle' ).on( 'click', function() {
				var $button = $( this );
				var expanded = $button.attr( 'aria-expanded' ) === 'true';
				$button.attr( 'aria-expanded', expanded ? 'false' : 'true' );
				$( '#' + $button.attr( 'aria-controls' ) ).prop( 'hidden', expanded );
			} );

			$( '#wpadc-faq-filter' ).on( 'input', function() {
				var query = $( this ).val().toLowerCase();

				$items.each( function() {
					var $item = $( this );
					var matches = $item.data( 'faq-search' ).indexOf( query ) !== -1;
					$item.toggleClass( 'wpadc-faq-hidden', ! matches );
				} );

				updateCount();
			} );

			updateCount();
		} );
	</script>
	<?php
}
endif;

if ( ! function_exists( 'wpadc_render_help_troubleshooting_tab' ) ) :
/**
 * Render Troubleshooting tab.
 */
function wpadc_render_help_troubleshooting_tab() {
	$sections = array(
		array(
			'title' => __( 'Disclosure Not Appearing', WPADC_SLUG ),
			'items' => array(
				__( 'Check that a published rule exists.', WPADC_SLUG ),
				__( 'Confirm the display position matches where you expect the disclosure.', WPADC_SLUG ),
				__( 'Confirm the rule post type and condition match the current post.', WPADC_SLUG ),
				__( 'Clear all page, hosting, CDN, and browser caches.', WPADC_SLUG ),
				__( 'Test with a default WordPress theme to confirm content filters are running.', WPADC_SLUG ),
			),
		),
		array(
			'title' => __( 'Disclosure Breaks Page Layout', WPADC_SLUG ),
			'items' => array(
				__( 'Remove copied formatting and test with plain text.', WPADC_SLUG ),
				__( 'Check for unclosed HTML tags in the disclosure statement.', WPADC_SLUG ),
				__( 'Switch display position to see whether the issue is theme-template specific.', WPADC_SLUG ),
			),
		),
		array(
			'title' => __( 'Plugin or Theme Conflicts', WPADC_SLUG ),
			'items' => array(
				__( 'Temporarily deactivate other plugins one at a time.', WPADC_SLUG ),
				__( 'Switch to a default theme and test again.', WPADC_SLUG ),
				__( 'If the issue only occurs with one builder or theme, use shortcode placement as a workaround.', WPADC_SLUG ),
			),
		),
	);
	?>
	<div class="wpadc-help-tab">
		<h3><?php esc_html_e( 'Troubleshooting', WPADC_SLUG ); ?></h3>
		<?php foreach ( $sections as $section ) : ?>
			<div class="wpadc-troubleshoot-section">
				<h4><?php echo esc_html( $section['title'] ); ?></h4>
				<ol>
					<?php foreach ( $section['items'] as $item ) : ?>
						<li><?php echo esc_html( $item ); ?></li>
					<?php endforeach; ?>
				</ol>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}
endif;

if ( ! function_exists( 'wpadc_render_help_resources_tab' ) ) :
/**
 * Render Resources tab.
 */
function wpadc_render_help_resources_tab() {
	?>
	<div class="wpadc-help-tab">
		<h3><?php esc_html_e( 'Resources', WPADC_SLUG ); ?></h3>
		<ul>
			<li><a href="https://www.mojofywp.com/" target="_blank" rel="nofollow noopener"><?php esc_html_e( 'Official Website', WPADC_SLUG ); ?></a></li>
			<li><a href="https://www.mojofywp.com/wp-affiliate-disclosure" target="_blank" rel="nofollow noopener"><?php esc_html_e( 'About the Plugin', WPADC_SLUG ); ?></a></li>
			<li><a href="https://www.mojofywp.com/wp-affiliate-disclosure/demo" target="_blank" rel="nofollow noopener"><?php esc_html_e( 'Plugin Demo', WPADC_SLUG ); ?></a></li>
			<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=wpadc-builder-contact' ) ); ?>"><?php esc_html_e( 'Help & Support', WPADC_SLUG ); ?></a></li>
		</ul>
	</div>
	<?php
}
endif;

if ( ! function_exists( 'wpadc_render_help_changelog_tab' ) ) :
/**
 * Render What's New tab.
 */
function wpadc_render_help_changelog_tab() {
	?>
	<div class="wpadc-help-tab">
		<h3><?php esc_html_e( 'What\'s New in 1.4.0', WPADC_SLUG ); ?></h3>
		<div class="wpadc-changelog-entry">
			<h4><?php esc_html_e( 'Appearance, Templates & Smarter Placement', WPADC_SLUG ); ?></h4>
			<ul>
				<li><span class="wpadc-changelog-tag wpadc-changelog-tag--new"><?php esc_html_e( 'New', WPADC_SLUG ); ?></span> <?php esc_html_e( 'Customize appearance toggle — a per-rule checkbox in the Appearance section. When off (the default), the plugin injects no styles so your existing theme CSS is fully preserved. Enable it to reveal the preset buttons and color/spacing fields. Previously saved values are retained when the toggle is turned off.', WPADC_SLUG ); ?></li>
				<li><span class="wpadc-changelog-tag wpadc-changelog-tag--new"><?php esc_html_e( 'New', WPADC_SLUG ); ?></span> <?php esc_html_e( 'Style presets per rule — choose Minimal, Boxed, Banner, or Inline, or set custom colors and spacing. A live admin preview shows changes before you save.', WPADC_SLUG ); ?></li>
				<li><span class="wpadc-changelog-tag wpadc-changelog-tag--new"><?php esc_html_e( 'New', WPADC_SLUG ); ?></span> <?php esc_html_e( 'Built-in disclosure templates: Amazon Associates and General FTC, each with short and long variants. Insert a template in one click from the rule editor.', WPADC_SLUG ); ?></li>
				<li><span class="wpadc-changelog-tag wpadc-changelog-tag--new"><?php esc_html_e( 'New', WPADC_SLUG ); ?></span> <?php esc_html_e( 'After-paragraph placement — insert the disclosure after the 1st, 2nd, or 3rd paragraph of your content. Falls back to end-of-content on shorter posts.', WPADC_SLUG ); ?></li>
				<li><span class="wpadc-changelog-tag wpadc-changelog-tag--new"><?php esc_html_e( 'New', WPADC_SLUG ); ?></span> <?php esc_html_e( 'New [affiliate_disclosure] shortcode alias with style, template, variant, and text attributes for inline control without editing a rule.', WPADC_SLUG ); ?></li>
				<li><span class="wpadc-changelog-tag wpadc-changelog-tag--fixed"><?php esc_html_e( 'Fixed', WPADC_SLUG ); ?></span> <?php esc_html_e( 'Disclosure injection no longer interferes with featured-image processing or other non-content filters.', WPADC_SLUG ); ?></li>
				<li><span class="wpadc-changelog-tag wpadc-changelog-tag--updated"><?php esc_html_e( 'Compatibility', WPADC_SLUG ); ?></span> <?php esc_html_e( 'id="wpadc-wrapper" is now emitted on the first disclosure wrapper per page only, preserving backward-compatible CSS for the common single-disclosure case. Pages with multiple disclosures (e.g. both Before and After Content active) will only see the id on the first wrapper — use .wpadc-wrapper-class or .wpadc-disclosure to target all wrappers reliably.', WPADC_SLUG ); ?></li>
			</ul>
		</div>

		<hr />

		<h3><?php esc_html_e( '1.3.0', WPADC_SLUG ); ?></h3>
		<div class="wpadc-changelog-entry">
			<h4><?php esc_html_e( 'Foundation Release', WPADC_SLUG ); ?></h4>
			<ul>
				<li><span class="wpadc-changelog-tag wpadc-changelog-tag--improved"><?php esc_html_e( 'Improved', WPADC_SLUG ); ?></span> <?php esc_html_e( 'Refreshed Help and FAQ content.', WPADC_SLUG ); ?></li>
				<li><span class="wpadc-changelog-tag wpadc-changelog-tag--internal"><?php esc_html_e( 'Internal', WPADC_SLUG ); ?></span> <?php esc_html_e( 'Modernized the build system for future development.', WPADC_SLUG ); ?></li>
				<li><span class="wpadc-changelog-tag wpadc-changelog-tag--internal"><?php esc_html_e( 'Internal', WPADC_SLUG ); ?></span> <?php esc_html_e( 'Added namespaced service scaffolding and REST API foundations.', WPADC_SLUG ); ?></li>
				<li><span class="wpadc-changelog-tag wpadc-changelog-tag--updated"><?php esc_html_e( 'Updated', WPADC_SLUG ); ?></span> <?php esc_html_e( 'Raised minimum requirements to WordPress 5.8 and PHP 7.4.', WPADC_SLUG ); ?></li>
			</ul>
		</div>
	</div>
	<?php
}
endif;
