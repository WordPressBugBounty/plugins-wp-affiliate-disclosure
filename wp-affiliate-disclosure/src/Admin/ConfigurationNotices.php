<?php
/**
 * Admin notices for common configuration issues.
 *
 * @package WP_Affiliate_Disclosure
 */

declare(strict_types=1);

namespace WPADC\Admin;

use WP_Query;

/**
 * Detects empty rule list and rules without conditions; emits dismissible
 * admin notices stored per-user via user_meta.
 */
final class ConfigurationNotices {

	const DISMISSAL_META_KEY = 'wpadc_notice_dismissed';

	/**
	 * Notices to render this request (id => [message, level]).
	 *
	 * @var array
	 */
	private $notices = array();

	/**
	 * Hook the class.
	 */
	public function register(): void {
		add_action( 'admin_init', array( $this, 'check_for_issues' ) );
		add_action( 'admin_notices', array( $this, 'render_notices' ) );
		add_action( 'wp_ajax_wpadc_dismiss_notice', array( $this, 'ajax_dismiss' ) );
	}

	/**
	 * Detect issues and queue notices.
	 */
	public function check_for_issues(): void {
		// Only on plugin admin pages or all admin? Keep light — only on our pages + dashboard.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$rules = $this->get_rules();

		// (a) Zero rules → info notice.
		if ( empty( $rules ) ) {
			if ( ! $this->is_dismissed( 'no_rules' ) ) {
				$this->notices['no_rules'] = array(
					'message' => __( 'WP Affiliate Disclosure: No active disclosure rules found. Create a rule to start displaying disclosures.', 'wp-affiliate-disclosure' ),
					'level'   => 'info',
				);
			}
			return;
		}

		// (b) Per-rule "no conditions" check.
		foreach ( $rules as $rule ) {
			$key = 'no_conditions_' . $rule->ID;
			if ( $this->has_no_conditions( $rule ) ) {
				if ( ! $this->is_dismissed( $key ) ) {
					$this->notices[ $key ] = array(
						'message' => sprintf(
							/* translators: %s: rule name */
							__( 'WP Affiliate Disclosure: Rule "%s" has no targeting conditions and won\'t display on any posts.', 'wp-affiliate-disclosure' ),
							esc_html( $rule->post_title )
						),
						'level'   => 'warning',
					);
				}
			} else {
				// Auto-clear: if previously dismissed but conditions are now set, drop the flag.
				$this->clear_dismissal( $key );
			}
		}
	}

	/**
	 * Render queued notices.
	 */
	public function render_notices(): void {
		if ( empty( $this->notices ) ) {
			return;
		}
		$nonce = wp_create_nonce( 'wpadc_dismiss_notice' );
		foreach ( $this->notices as $id => $n ) {
			$class = 'notice notice-' . esc_attr( $n['level'] ) . ' is-dismissible';
			printf(
				'<div class="%s" data-wpadc-notice="%s" data-wpadc-nonce="%s"><p>%s</p></div>',
				esc_attr( $class ),
				esc_attr( $id ),
				esc_attr( $nonce ),
				esc_html( $n['message'] )
			);
		}
		// Inline dismissal JS (small, only loads when notices present).
		?>
		<script>
		(function($){
			$(document).on('click', '.notice[data-wpadc-notice] .notice-dismiss', function(){
				var $n = $(this).closest('.notice');
				$.post(ajaxurl, {
					action: 'wpadc_dismiss_notice',
					notice_id: $n.data('wpadc-notice'),
					_wpnonce: $n.data('wpadc-nonce')
				});
			});
		})(jQuery);
		</script>
		<?php
	}

	/**
	 * AJAX handler: append notice id to current user's dismissal meta.
	 */
	public function ajax_dismiss(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
		}
		$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'wpadc_dismiss_notice' ) ) {
			wp_send_json_error( array( 'message' => 'bad_nonce' ), 400 );
		}
		$notice_id = isset( $_POST['notice_id'] ) ? sanitize_key( wp_unslash( $_POST['notice_id'] ) ) : '';
		if ( '' === $notice_id ) {
			wp_send_json_error( array( 'message' => 'no_id' ), 400 );
		}
		$this->add_dismissal( $notice_id );
		wp_send_json_success();
	}

	/* ------------------------------------------------------------------ */
	/* Helpers                                                            */
	/* ------------------------------------------------------------------ */

	/**
	 * Get all published wpadc rules.
	 *
	 * @return \WP_Post[]
	 */
	private function get_rules(): array {
		$q = new WP_Query( array(
			'post_type'      => 'wpadc',
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'no_found_rows'  => true,
		) );
		return is_array( $q->posts ) ? $q->posts : array();
	}

	/**
	 * Whether a rule has no targeting conditions.
	 */
	private function has_no_conditions( $rule ): bool {
		$cond = (string) get_post_meta( $rule->ID, '_wpadc_condition', true );

		// condition=none (or unset): the controller's else branch renders on all matching post types — valid config.
		if ( '' === $cond || 'none' === $cond ) {
			return false;
		}

		if ( 'ids' === $cond ) {
			$ids = (string) get_post_meta( $rule->ID, '_wpadc_ids', true );
			return '' === trim( $ids );
		}

		if ( 'taxonomy' === $cond ) {
			$taxes = (string) get_post_meta( $rule->ID, '_wpadc_taxonomies', true );
			return '' === trim( $taxes );
		}

		return false;
	}

	/**
	 * Check dismissal state.
	 */
	private function is_dismissed( string $notice_id ): bool {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return false;
		}
		$dismissed = get_user_meta( $user_id, self::DISMISSAL_META_KEY, true );
		if ( ! is_array( $dismissed ) ) {
			$dismissed = array();
		}
		return in_array( $notice_id, $dismissed, true );
	}

	private function add_dismissal( string $notice_id ): void {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return;
		}
		$dismissed = get_user_meta( $user_id, self::DISMISSAL_META_KEY, true );
		if ( ! is_array( $dismissed ) ) {
			$dismissed = array();
		}
		if ( ! in_array( $notice_id, $dismissed, true ) ) {
			$dismissed[] = $notice_id;
			update_user_meta( $user_id, self::DISMISSAL_META_KEY, $dismissed );
		}
	}

	/**
	 * Auto-clear: when a "no_conditions_X" notice was dismissed but conditions
	 * are now present, drop the flag so the notice can re-fire later.
	 */
	private function clear_dismissal( string $notice_id ): void {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return;
		}
		$dismissed = get_user_meta( $user_id, self::DISMISSAL_META_KEY, true );
		if ( ! is_array( $dismissed ) || ! in_array( $notice_id, $dismissed, true ) ) {
			return;
		}
		$dismissed = array_values( array_diff( $dismissed, array( $notice_id ) ) );
		update_user_meta( $user_id, self::DISMISSAL_META_KEY, $dismissed );
	}
}
