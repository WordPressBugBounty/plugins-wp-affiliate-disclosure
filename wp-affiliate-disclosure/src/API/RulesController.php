<?php
/**
 * REST API controller for disclosure rules.
 *
 * @package WP_Affiliate_Disclosure
 */

declare(strict_types=1);

namespace WPADC\API;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WPADC\Styling\ColorSanitizer;

/**
 * Handles full CRUD on /wpadc/v1/rules.
 */
final class RulesController extends WP_REST_Controller {

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
	protected $rest_base = 'rules';

	/**
	 * Allowed placement values.
	 *
	 * @var string[]
	 */
	private $valid_placements = array(
		'before-content', 'after-content',
		'after_p1', 'after_p2', 'after_p3',
		'shortcode', 'widget',
	);

	/**
	 * Premium-only placement values.
	 *
	 * @var string[]
	 */
	private $premium_placements = array( 'shortcode', 'widget' );

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
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_rule' ),
					'permission_callback' => array( $this, 'permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/reorder',
			array(
				array(
					'methods'             => 'PUT',
					'callback'            => array( $this, 'reorder_rules' ),
					'permission_callback' => array( $this, 'permissions_check' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'permissions_check' ),
					'args'                => array(
						'id' => array(
							'description'       => __( 'Disclosure rule ID.', 'wp-affiliate-disclosure' ),
							'type'              => 'integer',
							'sanitize_callback' => 'absint',
							'validate_callback' => 'rest_validate_request_arg',
							'required'          => true,
						),
					),
				),
				array(
					'methods'             => 'PUT',
					'callback'            => array( $this, 'update_rule' ),
					'permission_callback' => array( $this, 'permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_rule' ),
					'permission_callback' => array( $this, 'permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Capability check (also returns 401 vs 403 differentiated by WP core).
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error
	 */
	public function permissions_check( $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You must be logged in to manage disclosure rules.', 'wp-affiliate-disclosure' ),
				array( 'status' => 401 )
			);
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to manage disclosure rules.', 'wp-affiliate-disclosure' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}

	/* ------------------------------------------------------------------ */
	/* Read endpoints                                                     */
	/* ------------------------------------------------------------------ */

	/**
	 * List rules.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_items( $request ): WP_REST_Response {
		$page     = max( 1, (int) $request->get_param( 'page' ) );
		$per_page = min( 100, max( 1, (int) $request->get_param( 'per_page' ) ) );
		$status   = (string) $request->get_param( 'status' );
		$status   = '' === $status ? 'publish' : $status;

		$query = new WP_Query(
			array(
				'post_type'      => 'wpadc',
				'post_status'    => $status,
				'paged'          => $page,
				'posts_per_page' => $per_page,
				'meta_key'       => $this->meta_key( 'priority' ),
				'orderby'        => 'meta_value_num',
				'order'          => 'ASC',
			)
		);

		$rules = array();

		foreach ( $query->posts as $post ) {
			if ( $post instanceof WP_Post ) {
				$rules[] = $this->prepare_rule( $post );
			}
		}

		$response = new WP_REST_Response( $rules, 200 );
		$response->header( 'X-WP-Total', (int) $query->found_posts );
		$response->header( 'X-WP-TotalPages', (int) $query->max_num_pages );

		return $response;
	}

	/**
	 * Get one rule.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_item( $request ) {
		$post = get_post( (int) $request->get_param( 'id' ) );

		if ( ! $post instanceof WP_Post || 'wpadc' !== $post->post_type ) {
			return $this->not_found_error();
		}

		return new WP_REST_Response( $this->prepare_rule( $post ), 200 );
	}

	/* ------------------------------------------------------------------ */
	/* Create / Update / Delete / Reorder                                 */
	/* ------------------------------------------------------------------ */

	/**
	 * Create a rule.
	 */
	public function create_rule( $request ) {
		$body = $this->get_json_body( $request );

		// Required fields.
		if ( empty( $body['title'] ) || ! is_string( $body['title'] ) ) {
			return $this->invalid_param_error( array( 'title' => __( 'Title is required.', 'wp-affiliate-disclosure' ) ) );
		}
		if ( ! isset( $body['statement'] ) ) {
			$body['statement'] = '';
		}

		// Validate full payload.
		$errors = $this->validate_payload( $body, true );
		if ( ! empty( $errors ) ) {
			return $this->invalid_param_error( $errors );
		}

		// Free-tier checks.
		$tier_error = $this->check_free_tier_limits( $body, true );
		if ( $tier_error ) {
			return $tier_error;
		}

		$model = $this->get_settings_model();
		if ( ! $model ) {
			return new WP_Error( 'wpadc_model_unavailable', __( 'Rule storage is unavailable.', 'wp-affiliate-disclosure' ), array( 'status' => 500 ) );
		}

		// Create using existing model (writes defaults).
		$id = $model->create_new( array( 'title' => sanitize_text_field( $body['title'] ) ) );
		if ( empty( $id ) ) {
			return new WP_Error( 'wpadc_create_failed', __( 'Failed to create rule.', 'wp-affiliate-disclosure' ), array( 'status' => 500 ) );
		}

		// Apply payload values via update_settings.
		$values = $this->payload_to_meta_values( $body, array() );
		$values['title'] = sanitize_text_field( $body['title'] );
		$model->update_settings( (int) $id, $values );

		// Set post_status if requested.
		if ( ! empty( $body['status'] ) && in_array( $body['status'], array( 'publish', 'draft' ), true ) ) {
			wp_update_post( array( 'ID' => (int) $id, 'post_status' => $body['status'] ) );
		}

		delete_transient( 'wpadc_custom_css' );

		$post = get_post( (int) $id );
		return new WP_REST_Response( $this->prepare_rule( $post ), 201 );
	}

	/**
	 * Update a rule.
	 */
	public function update_rule( $request ) {
		$id   = (int) $request->get_param( 'id' );
		$post = get_post( $id );
		if ( ! $post instanceof WP_Post || 'wpadc' !== $post->post_type ) {
			return $this->not_found_error();
		}

		$body = $this->get_json_body( $request );

		$errors = $this->validate_payload( $body, false );
		if ( ! empty( $errors ) ) {
			return $this->invalid_param_error( $errors );
		}

		$tier_error = $this->check_free_tier_limits( $body, false );
		if ( $tier_error ) {
			return $tier_error;
		}

		$model = $this->get_settings_model();
		if ( ! $model ) {
			return new WP_Error( 'wpadc_model_unavailable', __( 'Rule storage is unavailable.', 'wp-affiliate-disclosure' ), array( 'status' => 500 ) );
		}

		$current_values = $model->get_values( $id );

		// Shallow-merge payload into current values.
		$merged = $this->payload_to_meta_values( $body, $current_values );

		// Title (top-level field).
		if ( isset( $body['title'] ) ) {
			$merged['title'] = sanitize_text_field( $body['title'] );
		} else {
			$merged['title'] = $current_values['title'] ?? $post->post_title;
		}

		$model->update_settings( $id, $merged );

		// Status.
		if ( isset( $body['status'] ) && in_array( $body['status'], array( 'publish', 'draft' ), true ) ) {
			wp_update_post( array( 'ID' => $id, 'post_status' => $body['status'] ) );
		}

		delete_transient( 'wpadc_custom_css' );

		$post = get_post( $id );
		return new WP_REST_Response( $this->prepare_rule( $post ), 200 );
	}

	/**
	 * Delete a rule.
	 */
	public function delete_rule( $request ) {
		$id   = (int) $request->get_param( 'id' );
		$post = get_post( $id );
		if ( ! $post instanceof WP_Post || 'wpadc' !== $post->post_type ) {
			return $this->not_found_error();
		}

		$payload = $this->prepare_rule( $post );

		$result = wp_delete_post( $id, true );
		if ( ! $result ) {
			return new WP_Error( 'wpadc_delete_failed', __( 'Failed to delete rule.', 'wp-affiliate-disclosure' ), array( 'status' => 500 ) );
		}

		delete_transient( 'wpadc_custom_css' );

		return new WP_REST_Response( $payload, 200 );
	}

	/**
	 * Reorder rules.
	 */
	public function reorder_rules( $request ) {
		$body = $this->get_json_body( $request );

		if ( empty( $body['order'] ) || ! is_array( $body['order'] ) ) {
			return $this->invalid_param_error( array( 'order' => __( 'Missing or invalid order array.', 'wp-affiliate-disclosure' ) ) );
		}

		// Two-pass validation: verify ALL ids are wpadc posts before any write.
		$validated = array();
		foreach ( $body['order'] as $idx => $entry ) {
			if ( ! is_array( $entry ) || ! isset( $entry['id'], $entry['priority'] ) ) {
				return $this->invalid_param_error( array(
					'order' => sprintf( __( 'Entry at index %d must include id and priority.', 'wp-affiliate-disclosure' ), $idx ),
				) );
			}
			$rid = (int) $entry['id'];
			$pri = (int) $entry['priority'];
			if ( $rid <= 0 ) {
				return $this->invalid_param_error( array( 'order' => __( 'Invalid id in order array.', 'wp-affiliate-disclosure' ) ) );
			}
			$post = get_post( $rid );
			if ( ! $post instanceof WP_Post || 'wpadc' !== $post->post_type ) {
				return $this->invalid_param_error( array(
					'order' => sprintf( __( 'Post %d is not a disclosure rule.', 'wp-affiliate-disclosure' ), $rid ),
				) );
			}
			$validated[] = array( 'id' => $rid, 'priority' => $pri );
		}

		// Second pass — apply.
		$updated_rules = array();
		foreach ( $validated as $entry ) {
			update_post_meta( $entry['id'], $this->meta_key( 'priority' ), $entry['priority'] );
			$updated_rules[] = array( 'id' => $entry['id'], 'priority' => $entry['priority'] );
		}

		delete_transient( 'wpadc_custom_css' );

		return new WP_REST_Response(
			array(
				'updated' => count( $updated_rules ),
				'rules'   => $updated_rules,
			),
			200
		);
	}

	/* ------------------------------------------------------------------ */
	/* Validation                                                         */
	/* ------------------------------------------------------------------ */

	/**
	 * Validate the payload against schema. Returns array of {field => message}.
	 *
	 * @param array $body      JSON payload.
	 * @param bool  $is_create True for create requests (some fields required).
	 * @return array
	 */
	private function validate_payload( $body, $is_create ): array {
		$errors = array();

		if ( isset( $body['placement'] ) ) {
			if ( ! is_array( $body['placement'] ) ) {
				$errors['placement'] = __( 'placement must be an array of strings.', 'wp-affiliate-disclosure' );
			} else {
				foreach ( $body['placement'] as $p ) {
					if ( ! in_array( $p, $this->valid_placements, true ) ) {
						$errors['placement'] = sprintf(
							__( 'Invalid placement value. Must be one of: %s.', 'wp-affiliate-disclosure' ),
							implode( ', ', $this->valid_placements )
						);
						break;
					}
				}
			}
		}

		if ( isset( $body['post_types'] ) ) {
			if ( ! is_array( $body['post_types'] ) ) {
				$errors['post_types'] = __( 'post_types must be an array.', 'wp-affiliate-disclosure' );
			} else {
				foreach ( $body['post_types'] as $pt ) {
					if ( ! is_string( $pt ) || '' === $pt ) {
						$errors['post_types'] = __( 'Invalid post type value.', 'wp-affiliate-disclosure' );
						break;
					}
				}
			}
		}

		if ( isset( $body['condition'] ) && ! in_array( $body['condition'], array( 'none', 'ids', 'taxonomy' ), true ) ) {
			$errors['condition'] = __( 'condition must be one of: none, ids, taxonomy.', 'wp-affiliate-disclosure' );
		}

		if ( isset( $body['advanced_filter'] ) && ! in_array( $body['advanced_filter'], array( 'none', 'exclude_ids', 'exclude_taxonomy' ), true ) ) {
			$errors['advanced_filter'] = __( 'advanced_filter must be one of: none, exclude_ids, exclude_taxonomy.', 'wp-affiliate-disclosure' );
		}

		if ( isset( $body['priority'] ) && ( ! is_numeric( $body['priority'] ) || (int) $body['priority'] < 1 ) ) {
			$errors['priority'] = __( 'priority must be a positive integer.', 'wp-affiliate-disclosure' );
		}

		if ( isset( $body['status'] ) && ! in_array( $body['status'], array( 'publish', 'draft' ), true ) ) {
			$errors['status'] = __( 'status must be "publish" or "draft".', 'wp-affiliate-disclosure' );
		}

		if ( isset( $body['template_id'] ) && '' !== $body['template_id'] && ! in_array( $body['template_id'], array( 'amazon', 'ftc' ), true ) ) {
			$errors['template_id'] = __( 'template_id must be one of: amazon, ftc.', 'wp-affiliate-disclosure' );
		}

		if ( isset( $body['template_variant'] ) && '' !== $body['template_variant'] && ! in_array( $body['template_variant'], array( 'short', 'long' ), true ) ) {
			$errors['template_variant'] = __( 'template_variant must be one of: short, long.', 'wp-affiliate-disclosure' );
		}

		// style sub-validation.
		if ( isset( $body['style'] ) ) {
			if ( ! is_array( $body['style'] ) ) {
				$errors['style'] = __( 'style must be an object.', 'wp-affiliate-disclosure' );
			} else {
				$style = $body['style'];
				if ( isset( $style['preset'] ) && '' !== $style['preset'] && ! in_array( $style['preset'], array( 'minimal', 'boxed', 'banner', 'inline', 'custom' ), true ) ) {
					$errors['style.preset'] = __( 'Invalid preset value.', 'wp-affiliate-disclosure' );
				}
				if ( isset( $style['bg_color'] ) && '' !== $style['bg_color'] ) {
					$sanitized = ColorSanitizer::sanitize( $style['bg_color'] );
					if ( '' === $sanitized ) {
						$errors['style.bg_color'] = __( "Invalid color. Expected hex color (e.g., #ff0000) or the literal token 'transparent'.", 'wp-affiliate-disclosure' );
					}
				}
				foreach ( array( 'text_color', 'border_color' ) as $ck ) {
					if ( isset( $style[ $ck ] ) && '' !== $style[ $ck ] ) {
						$hex = sanitize_hex_color( $style[ $ck ] );
						if ( ! is_string( $hex ) ) {
							$errors[ 'style.' . $ck ] = __( 'Invalid hex color.', 'wp-affiliate-disclosure' );
						}
					}
				}
				if ( isset( $style['border_style'] ) && '' !== $style['border_style'] && ! in_array( $style['border_style'], array( 'solid', 'dashed', 'dotted', 'none' ), true ) ) {
					$errors['style.border_style'] = __( 'Invalid border style.', 'wp-affiliate-disclosure' );
				}
			}
		}

		return $errors;
	}

	/**
	 * Free-tier feature gating.
	 *
	 * @param array $body      Payload.
	 * @param bool  $is_create True if this is a create.
	 * @return WP_Error|null
	 */
	private function check_free_tier_limits( $body, $is_create ) {
		$is_premium = $this->user_can_use_premium();
		if ( $is_premium ) {
			return null;
		}

		// 1-rule limit on create.
		if ( $is_create ) {
			$model = $this->get_settings_model();
			if ( $model ) {
				$existing = (int) $model->get_total_items();
				if ( $existing >= 1 ) {
					return new WP_Error(
						'wpadc_rule_limit',
						__( 'Free version is limited to 1 disclosure rule. Upgrade to Pro for unlimited rules.', 'wp-affiliate-disclosure' ),
						array( 'status' => 403 )
					);
				}
			}
		}

		// Premium placement values.
		if ( isset( $body['placement'] ) && is_array( $body['placement'] ) ) {
			foreach ( $body['placement'] as $p ) {
				if ( in_array( $p, $this->premium_placements, true ) ) {
					return new WP_Error(
						'wpadc_premium_required',
						__( 'Shortcode and Widget placements require the premium version.', 'wp-affiliate-disclosure' ),
						array( 'status' => 403 )
					);
				}
			}
		}

		// Multiple post types.
		if ( isset( $body['post_types'] ) && is_array( $body['post_types'] ) && count( $body['post_types'] ) > 1 ) {
			return new WP_Error(
				'wpadc_premium_required',
				__( 'Targeting multiple post types requires the premium version.', 'wp-affiliate-disclosure' ),
				array( 'status' => 403 )
			);
		}

		// Advanced filter.
		if ( isset( $body['advanced_filter'] ) && 'none' !== $body['advanced_filter'] && '' !== $body['advanced_filter'] ) {
			return new WP_Error(
				'wpadc_premium_required',
				__( 'Advanced filtering requires the premium version.', 'wp-affiliate-disclosure' ),
				array( 'status' => 403 )
			);
		}

		return null;
	}

	/* ------------------------------------------------------------------ */
	/* Payload <-> Meta translation                                       */
	/* ------------------------------------------------------------------ */

	/**
	 * Convert REST payload into the values array consumed by update_settings().
	 * Performs shallow merge for style; replaces arrays wholesale.
	 *
	 * @param array $body            Payload from REST.
	 * @param array $current_values  Existing get_values() output (or empty for create).
	 * @return array
	 */
	private function payload_to_meta_values( $body, $current_values ): array {
		$values = is_array( $current_values ) ? $current_values : array();

		if ( isset( $body['statement'] ) ) {
			$values['disclosure_statement'] = wp_kses_post( $body['statement'] );
		}

		if ( isset( $body['placement'] ) && is_array( $body['placement'] ) ) {
			$values['statement_position'] = implode( ',', array_map( 'sanitize_key', $body['placement'] ) );
		}

		if ( isset( $body['post_types'] ) && is_array( $body['post_types'] ) ) {
			$values['post_type'] = implode( ',', array_map( 'sanitize_key', $body['post_types'] ) );
		}

		if ( isset( $body['condition'] ) ) {
			$values['condition'] = sanitize_key( $body['condition'] );
		}

		if ( isset( $body['condition_targets'] ) && is_array( $body['condition_targets'] ) ) {
			$cond = isset( $values['condition'] ) ? $values['condition'] : ( $body['condition'] ?? 'none' );
			$csv  = implode( ',', array_map( function ( $v ) { return is_int( $v ) || ctype_digit( (string) $v ) ? (string) (int) $v : sanitize_text_field( (string) $v ); }, $body['condition_targets'] ) );
			if ( 'ids' === $cond ) {
				$values['ids'] = $csv;
				$values['taxonomies'] = '';
			} elseif ( 'taxonomy' === $cond ) {
				$values['taxonomies'] = $csv;
				$values['ids'] = '';
			}
		}

		if ( isset( $body['advanced_filter'] ) ) {
			$values['advanced_filter'] = sanitize_key( $body['advanced_filter'] );
		}

		if ( isset( $body['advanced_filter_targets'] ) && is_array( $body['advanced_filter_targets'] ) ) {
			$af  = isset( $values['advanced_filter'] ) ? $values['advanced_filter'] : ( $body['advanced_filter'] ?? 'none' );
			$csv = implode( ',', array_map( function ( $v ) { return is_int( $v ) || ctype_digit( (string) $v ) ? (string) (int) $v : sanitize_text_field( (string) $v ); }, $body['advanced_filter_targets'] ) );
			if ( 'exclude_ids' === $af ) {
				$values['excludes_ids'] = $csv;
				$values['exclude_taxonomies'] = '';
			} elseif ( 'exclude_taxonomy' === $af ) {
				$values['exclude_taxonomies'] = $csv;
				$values['excludes_ids'] = '';
			}
		}

		if ( isset( $body['priority'] ) ) {
			$values['priority'] = max( 1, (int) $body['priority'] );
		}

		if ( isset( $body['template_id'] ) ) {
			$values['template_id'] = sanitize_key( $body['template_id'] );
		}
		if ( isset( $body['template_variant'] ) ) {
			$values['template_variant'] = sanitize_key( $body['template_variant'] );
		}

		// style: shallow merge.
		if ( isset( $body['style'] ) && is_array( $body['style'] ) ) {
			foreach ( $body['style'] as $k => $v ) {
				$values[ 'style_' . $k ] = $v;
			}
		}

		return $values;
	}

	/* ------------------------------------------------------------------ */
	/* Response shaping                                                   */
	/* ------------------------------------------------------------------ */

	/**
	 * Prepare a rule response.
	 */
	private function prepare_rule( WP_Post $post ): array {
		$priority = get_post_meta( $post->ID, $this->meta_key( 'priority' ), true );
		$condition = (string) get_post_meta( $post->ID, $this->meta_key( 'condition' ), true );
		$condition = '' === $condition ? 'none' : $condition;
		$adv_filter = (string) get_post_meta( $post->ID, $this->meta_key( 'advanced_filter' ), true );

		// condition_targets resolution.
		$condition_targets = array();
		if ( 'ids' === $condition ) {
			$condition_targets = $this->parse_csv_meta( $post->ID, 'ids', array() );
			$condition_targets = array_map( 'intval', $condition_targets );
		} elseif ( 'taxonomy' === $condition ) {
			$condition_targets = $this->parse_csv_meta( $post->ID, 'taxonomies', array() );
		}

		$adv_filter_targets = array();
		if ( 'exclude_ids' === $adv_filter ) {
			$adv_filter_targets = $this->parse_csv_meta( $post->ID, 'excludes_ids', array() );
			$adv_filter_targets = array_map( 'intval', $adv_filter_targets );
		} elseif ( 'exclude_taxonomy' === $adv_filter ) {
			$adv_filter_targets = $this->parse_csv_meta( $post->ID, 'exclude_taxonomies', array() );
		}

		return array(
			'id'                       => (int) $post->ID,
			'title'                    => $post->post_title,
			'status'                   => $post->post_status,
			'statement'                => (string) get_post_meta( $post->ID, $this->meta_key( 'disclosure_statement' ), true ),
			'placement'                => $this->parse_csv_meta( $post->ID, 'statement_position', array( 'before-content' ) ),
			'position'                 => $this->parse_csv_meta( $post->ID, 'statement_position', array( 'before-content' ) ), // legacy alias
			'post_types'               => $this->parse_csv_meta( $post->ID, 'post_type', array( 'post' ) ),
			'condition'                => $condition,
			'condition_targets'        => $condition_targets,
			'advanced_filter'          => '' === $adv_filter ? 'none' : $adv_filter,
			'advanced_filter_targets'  => $adv_filter_targets,
			'style'                    => array(
				'preset'        => (string) get_post_meta( $post->ID, $this->meta_key( 'style_preset' ), true ),
				'bg_color'      => (string) get_post_meta( $post->ID, $this->meta_key( 'style_bg_color' ), true ),
				'text_color'    => (string) get_post_meta( $post->ID, $this->meta_key( 'style_text_color' ), true ),
				'border_color'  => (string) get_post_meta( $post->ID, $this->meta_key( 'style_border_color' ), true ),
				'border_style'  => (string) get_post_meta( $post->ID, $this->meta_key( 'style_border_style' ), true ),
				'border_width'  => (int) get_post_meta( $post->ID, $this->meta_key( 'style_border_width' ), true ),
				'border_radius' => (int) get_post_meta( $post->ID, $this->meta_key( 'style_border_radius' ), true ),
				'padding_y'     => (int) get_post_meta( $post->ID, $this->meta_key( 'style_padding_y' ), true ),
				'padding_x'     => (int) get_post_meta( $post->ID, $this->meta_key( 'style_padding_x' ), true ),
				'margin_y'      => (int) get_post_meta( $post->ID, $this->meta_key( 'style_margin_y' ), true ),
				'margin_x'      => (int) get_post_meta( $post->ID, $this->meta_key( 'style_margin_x' ), true ),
			),
			'template_id'              => (string) get_post_meta( $post->ID, $this->meta_key( 'template_id' ), true ),
			'template_variant'         => (string) get_post_meta( $post->ID, $this->meta_key( 'template_variant' ), true ),
			'priority'                 => '' === $priority ? 1 : (int) $priority,
			'date_created'             => mysql_to_rfc3339( $post->post_date ),
			'date_modified'            => mysql_to_rfc3339( $post->post_modified ),
		);
	}

	/* ------------------------------------------------------------------ */
	/* Helpers                                                            */
	/* ------------------------------------------------------------------ */

	/**
	 * Get JSON body (REST infra normally parses this; fallback for raw input).
	 */
	private function get_json_body( $request ): array {
		$body = $request->get_json_params();
		if ( ! is_array( $body ) ) {
			$body = $request->get_params();
		}
		return is_array( $body ) ? $body : array();
	}

	/**
	 * Build a 400 response with the spec's params shape.
	 *
	 * @param array $params Field => message map.
	 * @return WP_Error
	 */
	private function invalid_param_error( array $params ): WP_Error {
		return new WP_Error(
			'rest_invalid_param',
			sprintf( __( 'Invalid parameter(s): %s', 'wp-affiliate-disclosure' ), implode( ', ', array_keys( $params ) ) ),
			array(
				'status' => 400,
				'params' => $params,
			)
		);
	}

	/**
	 * Build a 404 response.
	 */
	private function not_found_error(): WP_Error {
		return new WP_Error(
			'wpadc_rule_not_found',
			__( 'No disclosure rule found with that ID.', 'wp-affiliate-disclosure' ),
			array( 'status' => 404 )
		);
	}

	/**
	 * Acquire the existing builder settings model.
	 *
	 * @return \WPADC_Builder_Settings_Model|null
	 */
	private function get_settings_model() {
		if ( ! class_exists( 'WPADC_Builder_Settings_Model' ) ) {
			$path = defined( 'WPADC_PATH' ) ? WPADC_PATH . 'builder/settings-page/model.php' : '';
			if ( $path && file_exists( $path ) ) {
				require_once $path;
			}
		}
		if ( class_exists( 'WPADC_Builder_Settings_Model' ) ) {
			return new \WPADC_Builder_Settings_Model();
		}
		return null;
	}

	/**
	 * Whether the current installation can use premium code.
	 */
	private function user_can_use_premium(): bool {
		global $wp_affiliate_disclosure_fs;
		if ( is_object( $wp_affiliate_disclosure_fs ) && method_exists( $wp_affiliate_disclosure_fs, 'can_use_premium_code' ) ) {
			return (bool) $wp_affiliate_disclosure_fs->can_use_premium_code();
		}
		return false;
	}

	/**
	 * Get collection params.
	 */
	public function get_collection_params(): array {
		return array(
			'status' => array(
				'description'       => __( 'Rule post status.', 'wp-affiliate-disclosure' ),
				'type'              => 'string',
				'default'           => 'publish',
				'enum'              => array( 'publish', 'draft', 'any' ),
				'sanitize_callback' => 'sanitize_key',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'per_page' => array(
				'description'       => __( 'Maximum number of rules to return.', 'wp-affiliate-disclosure' ),
				'type'              => 'integer',
				'default'           => 20,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'page' => array(
				'description'       => __( 'Current page of results.', 'wp-affiliate-disclosure' ),
				'type'              => 'integer',
				'default'           => 1,
				'minimum'           => 1,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
	}

	/**
	 * Public schema.
	 */
	public function get_item_schema(): array {
		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'wpadc_rule',
			'type'       => 'object',
			'properties' => array(
				'id'                       => array( 'type' => 'integer' ),
				'title'                    => array( 'type' => 'string' ),
				'status'                   => array( 'type' => 'string' ),
				'statement'                => array( 'type' => 'string' ),
				'placement'                => array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
				'post_types'               => array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
				'condition'                => array( 'type' => 'string' ),
				'condition_targets'        => array( 'type' => 'array' ),
				'advanced_filter'          => array( 'type' => 'string' ),
				'advanced_filter_targets'  => array( 'type' => 'array' ),
				'style'                    => array( 'type' => 'object' ),
				'template_id'              => array( 'type' => 'string' ),
				'template_variant'         => array( 'type' => 'string' ),
				'priority'                 => array( 'type' => 'integer' ),
				'date_created'             => array( 'type' => 'string' ),
				'date_modified'            => array( 'type' => 'string' ),
			),
		);
	}

	/**
	 * Parse comma-separated rule meta.
	 */
	private function parse_csv_meta( int $post_id, string $key, array $default ): array {
		$value = (string) get_post_meta( $post_id, $this->meta_key( $key ), true );

		if ( '' === trim( $value ) ) {
			return $default;
		}

		$items = array_filter(
			array_map(
				'trim',
				explode( ',', $value )
			)
		);

		return array_values( $items );
	}

	/**
	 * Build a prefixed post meta key.
	 */
	private function meta_key( string $key ): string {
		return '_wpadc_' . $key;
	}
}
