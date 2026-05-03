<?php
/**
 * REST API controller for plugin settings.
 *
 * @package WP_Affiliate_Disclosure
 */

declare(strict_types=1);

namespace WPADC\API;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Handles /wpadc/v1/settings.
 */
final class SettingsController extends WP_REST_Controller {

	private const OPTION_KEY = 'wpadc_settings';

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
	protected $rest_base = 'settings';

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
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_items' ),
					'permission_callback' => array( $this, 'permissions_check' ),
					'args'                => $this->get_update_args(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check settings permissions.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool
	 */
	public function permissions_check( $request ): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get plugin settings.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_items( $request ): WP_REST_Response {
		return new WP_REST_Response( $this->prepare_settings( $this->get_stored_settings() ), 200 );
	}

	/**
	 * Update plugin settings.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_items( $request ) {
		$stored        = $this->get_stored_settings();
		$current       = wp_parse_args( $stored, $this->get_defaults() );
		$before_update = $current;
		$writable_keys = array( 'default_position', 'enable_shortcode', 'css_class_prefix' );
		$updated       = false;

		foreach ( $writable_keys as $key ) {
			if ( $request->has_param( $key ) ) {
				$current[ $key ] = $request->get_param( $key );
				$updated         = true;
			}
		}

		if ( ! $updated ) {
			$params = $request->get_json_params();

			if ( empty( $params ) ) {
				return new WP_Error(
					'wpadc_invalid_settings',
					__( 'No valid settings fields provided.', 'wp-affiliate-disclosure' ),
					array( 'status' => 400 )
				);
			}

			return new WP_REST_Response( $this->prepare_settings( $stored ), 200 );
		}

		if ( $current !== $before_update && false === update_option( self::OPTION_KEY, $current ) ) {
			return new WP_Error(
				'wpadc_settings_save_failed',
				__( 'Settings could not be saved.', 'wp-affiliate-disclosure' ),
				array( 'status' => 500 )
			);
		}

		return new WP_REST_Response( $this->prepare_settings( $current ), 200 );
	}

	/**
	 * Get update argument schema.
	 *
	 * @return array
	 */
	private function get_update_args(): array {
		return array(
			'default_position' => array(
				'description'       => __( 'Default disclosure position for new rules.', 'wp-affiliate-disclosure' ),
				'type'              => 'string',
				'enum'              => array( 'before-content', 'after-content', 'both' ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'enable_shortcode' => array(
				'description'       => __( 'Whether the disclosure shortcode is enabled.', 'wp-affiliate-disclosure' ),
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'css_class_prefix' => array(
				'description'       => __( 'CSS class prefix for disclosure wrappers.', 'wp-affiliate-disclosure' ),
				'type'              => 'string',
				'minLength'         => 1,
				'maxLength'         => 30,
				'pattern'           => '^[a-z][a-z0-9-]*$',
				'sanitize_callback' => array( $this, 'sanitize_css_class_prefix' ),
				'validate_callback' => 'rest_validate_request_arg',
			),
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
			'title'      => 'wpadc_settings',
			'type'       => 'object',
			'properties' => array(
				'version' => array(
					'type'     => 'string',
					'readonly' => true,
				),
				'default_position' => array(
					'type' => 'string',
					'enum' => array( 'before-content', 'after-content', 'both' ),
				),
				'enable_shortcode' => array(
					'type' => 'boolean',
				),
				'css_class_prefix' => array(
					'type'      => 'string',
					'minLength' => 1,
					'maxLength' => 30,
					'pattern'   => '^[a-z][a-z0-9-]*$',
				),
				'freemius_status' => array(
					'type'     => 'string',
					'enum'     => array( 'free', 'premium' ),
					'readonly' => true,
				),
			),
		);
	}

	/**
	 * Sanitize CSS class prefix.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	public function sanitize_css_class_prefix( $value ): string {
		return sanitize_html_class( strtolower( (string) $value ) );
	}

	/**
	 * Get stored settings.
	 *
	 * @return array
	 */
	private function get_stored_settings(): array {
		$settings = get_option( self::OPTION_KEY, array() );

		return is_array( $settings ) ? $settings : array();
	}

	/**
	 * Get default writable settings.
	 *
	 * @return array
	 */
	private function get_defaults(): array {
		return array(
			'default_position' => 'before-content',
			'enable_shortcode' => true,
			'css_class_prefix' => 'wpadc',
		);
	}

	/**
	 * Prepare response settings.
	 *
	 * @param array $settings Stored settings.
	 * @return array
	 */
	private function prepare_settings( array $settings ): array {
		$settings = wp_parse_args( $settings, $this->get_defaults() );

		$settings['version']         = defined( 'WPADC_VERSION' ) ? WPADC_VERSION : '';
		$settings['freemius_status'] = $this->get_freemius_status();

		return $settings;
	}

	/**
	 * Determine current Freemius plan state.
	 *
	 * @return string
	 */
	private function get_freemius_status(): string {
		if ( function_exists( 'wp_affiliate_disclosure_fs' ) && wp_affiliate_disclosure_fs()->can_use_premium_code() ) {
			return 'premium';
		}

		return 'free';
	}
}
