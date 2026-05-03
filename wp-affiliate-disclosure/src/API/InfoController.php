<?php
/**
 * REST API diagnostics controller.
 *
 * @package WP_Affiliate_Disclosure
 */

declare(strict_types=1);

namespace WPADC\API;

use stdClass;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Handles /wpadc/v1/info.
 */
final class InfoController extends WP_REST_Controller {

	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wpadc/v1';

	/**
	 * REST base.
	 *
	 * @var string
	 */
	protected $rest_base = 'info';

	/**
	 * Register routes.
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check endpoint permissions.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool
	 */
	public function permissions_check( $request ): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Return diagnostic info.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_item( $request ): WP_REST_Response {
		global $wp_version;

		return new WP_REST_Response(
			array(
				'plugin_version'    => defined( 'WPADC_VERSION' ) ? WPADC_VERSION : '',
				'wp_version'        => (string) $wp_version,
				'php_version'       => PHP_VERSION,
				'active_rules_count'=> $this->get_active_rules_count(),
				'freemius_plan'     => $this->get_freemius_plan(),
				'namespace_loaded'  => defined( 'WPADC_AUTOLOADER_LOADED' ) && WPADC_AUTOLOADER_LOADED,
				'rest_namespace'    => $this->namespace,
			),
			200
		);
	}

	/**
	 * Get public schema.
	 *
	 * @return array
	 */
	public function get_item_schema(): array {
		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'wpadc_info',
			'type'       => 'object',
			'properties' => array(
				'plugin_version' => array(
					'type' => 'string',
				),
				'wp_version' => array(
					'type' => 'string',
				),
				'php_version' => array(
					'type' => 'string',
				),
				'active_rules_count' => array(
					'type' => 'integer',
				),
				'freemius_plan' => array(
					'type' => 'string',
				),
				'namespace_loaded' => array(
					'type' => 'boolean',
				),
				'rest_namespace' => array(
					'type' => 'string',
				),
			),
		);
	}

	/**
	 * Count published disclosure rules.
	 *
	 * @return int
	 */
	private function get_active_rules_count(): int {
		$count = wp_count_posts( 'wpadc' );

		if ( ! $count instanceof stdClass || ! isset( $count->publish ) ) {
			return 0;
		}

		return (int) $count->publish;
	}

	/**
	 * Determine Freemius plan.
	 *
	 * @return string
	 */
	private function get_freemius_plan(): string {
		if ( function_exists( 'wp_affiliate_disclosure_fs' ) && wp_affiliate_disclosure_fs()->can_use_premium_code() ) {
			return 'premium';
		}

		return 'free';
	}
}
