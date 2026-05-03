<?php
/**
 * Namespaced v1.3 plugin services.
 *
 * @package WP_Affiliate_Disclosure
 */

declare(strict_types=1);

namespace WPADC;

use WPADC\Admin\ConfigurationNotices;
use WPADC\Admin\HelpPage;
use WPADC\API\InfoController;
use WPADC\API\RulesController;
use WPADC\API\SettingsController;
use WPADC\Styling\CssGenerator;

/**
 * Coordinates new namespaced services while legacy loading remains in place.
 */
final class Plugin {

	/**
	 * Register WordPress hooks for v1.3 services.
	 */
	public function init(): void {
		add_action( 'rest_api_init', array( $this, 'register_api' ) );
		add_action( 'current_screen', array( $this, 'register_help_tabs' ) );

		// v1.4 — CSS generator for styling presets and per-rule overrides.
		( new CssGenerator() )->register();

		// v1.4 — Admin configuration notices (no rules / no conditions).
		if ( is_admin() ) {
			( new ConfigurationNotices() )->register();
		}
	}

	/**
	 * Register REST API controllers.
	 */
	public function register_api(): void {
		( new SettingsController() )->register_routes();
		( new RulesController() )->register_routes();
		( new InfoController() )->register_routes();
	}

	/**
	 * Register contextual help tabs on plugin admin screens.
	 */
	public function register_help_tabs(): void {
		( new HelpPage() )->register_contextual_help();
	}
}
