=== WP Affiliate Disclosure ===

Contributors: mojofywp, freemius
Requires at least: 5.8
Requires PHP: 7.4
Tested up to: 6.7.1
Stable tag: 1.4.0
Tags: affiliate, disclosure, affiliate disclosure, affiliate disclosure statement, disclosure statement, FTC-compliant disclosure, affiliate disclaimer, affiliate disclaimer statement, FTC, FTC disclosure statement
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically add a customizable, FTC-compliant disclosure statement across your WordPress website based on the rule(s) you define.

== Description ==

Live Demo: [https://www.mojofywp.com/wp-affiliate-disclosure/demo](https://www.mojofywp.com/wp-affiliate-disclosure/demo "WP Affiliate Disclosure in Action")

If you're an affiliate marketer, blogger, or website owner, and you're promoting products of companies from US, UK, or Canada, the FTC (Federal Trade Commission) requires you to tell your visitors that you will be compensated if they purchase something through your affiliate link(s).

However, adding the disclosure statement manually into each posts would be a hassle – But worries no more...

WP Affiliate Disclosure allows you to add a customizable, FTC-compliant disclosure statement that will automatically display across your WordPress website based on the rule(s) you define.

**Main features:**

* Show the disclosure statement at the beginning and/or the end of a post.
* Easily customize statement content, including adding in links, images, as well as HTML elements.
* Only show the disclosure statement on certain posts.
* Only show the disclosure statement based on its taxonomies ( categories / tags )
* Works on custom post type as well

Live Demo: [https://www.mojofywp.com/wp-affiliate-disclosure/demo](https://www.mojofywp.com/wp-affiliate-disclosure/demo "WP Affiliate Disclosure in Action")

== Screenshots ==
1. Admin: Settings page
2. Admin: Rules settings page
3. Frontend: WP Affiliate Disclosure in action 1.
4. Frontend: WP Affiliate Disclosure in action 2.
5. Frontend: WP Affiliate Disclosure in action 3.

= Installation =

Once you have installed, you just need to activate the plugin to enable it.

= Configuration =

WP Affiliate Disclosure will add a new menu called "WP Affiliate Disclosure" in your admin dashboard. From here you can configure all plugin settings there.

== Installation ==

1. Unzip the downloaded zip file.
2. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `WP Affiliate Disclosure` from Plugins page

== Frequently Asked Questions ==

= My disclosure is not showing on any posts =

Check that you have a published rule, the display position is enabled, the post type and conditions match the post you are viewing, and any page or CDN caches have been cleared. Some custom themes and builder templates bypass WordPress's standard content filter; in those cases, use shortcode placement or test with a default WordPress theme.

= My disclosure shows on the wrong pages =

Review the rule conditions. Use taxonomy slugs for category or tag targeting, post IDs for individual posts, and priority ordering when multiple rules can match the same post.

= Can I change the disclosure statement? =

Yes. You can.

= Can I add links into the statement? =

Yes. You can.

= Can I add images into the statement? =

Yes. You can.

= Can I limit to which post the statement is shown? =

Yes. You can.

= Can I specify which post the disclosure statement should display? =

Yes. You can.

= Can I specify only posts with a certain categories that should show the disclosure statement? =

Yes. You can.

= Is the plugin compatible with all WordPress themes? =

WP Affiliate Disclosure is compatible with most of the themes that follow wordpress coding practices and standards. However, since every wordpress themes has its own coding structure, it's pretty difficult to promise that this plugin will absolutely work with just any themes in the market.

= Is the plugin translation ready? =

Yes, absolutely! The plugin comes with a PO file that you can use to translate WP Affiliate Disclosure to any desired language.

== Changelog ==

= 1.4.0 =

* New: Visual styling controls in the rule editor — pick from 4 presets (Minimal, Boxed, Banner, Inline) or customize colors, borders, padding, and margin without writing CSS.
* New: After-paragraph placement — show the disclosure after paragraph 1, 2, or 3.
* New: One-click disclosure templates for Amazon Associates and General FTC compliance (short and long variants).
* New: `[affiliate_disclosure]` shortcode alias with `style`, `template`, `variant`, `text`, and `rule` attributes.
* New: REST API CRUD endpoints — POST/PUT/DELETE `/wpadc/v1/rules` and `PUT /wpadc/v1/rules/reorder` for programmatic rule management.
* New: Admin notices when a rule has no targeting conditions or no rules exist (per-user dismissible).
* Fixed: Disclosure no longer injected in widgets, REST output, excerpts, or featured-image processing — main-loop guard added to all `the_content` callbacks.
* Fixed: Autoloader failure now shows a helpful admin notice instead of a white screen.
* Compatibility: `id="wpadc-wrapper"` is now emitted on the **first** disclosure wrapper per page only — preserving backward-compatible CSS for sites with a single disclosure per page. Pages with multiple disclosures (e.g. both before- and after-content placements active) will only see the id on the first wrapper; use `.wpadc-wrapper-class` or `.wpadc-disclosure` to target all wrappers. Advanced users can remove the id entirely by returning `false` from the `wpadc_emit_legacy_wrapper_id` filter.

== Upgrade Notice ==

= 1.4.0 =
v1.4 ships visual styling, after-paragraph placement, disclosure templates, and full REST API CRUD. Compatibility: `id="wpadc-wrapper"` is preserved on the first disclosure wrapper per page. Sites with multiple disclosures on a single page should migrate custom CSS to `.wpadc-wrapper-class` or `.wpadc-disclosure`.

= 1.3.0 =

* Improved: Refreshed in-plugin Help and FAQ content.
* Under the hood: Modernized the build system for future development.
* Under the hood: Added namespaced service scaffolding and REST API foundations.
* Updated: Now requires WordPress 5.8+ and PHP 7.4+.
