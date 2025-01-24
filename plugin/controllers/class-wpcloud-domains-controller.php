<?php
/**
 * WP Cloud Domains Controller.
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

if ( ! class_exists( 'WPCLOUD_Domains_Controller' ) ) {

	/**
	 * Class WPCLOUD_Webhook_Controller.
	 */
	class WPCLOUD_Domains_Controller extends WP_REST_Controller {

		/**
		 * The namespace.
		 *
		 * @var string
		 */
		protected $namespace = 'wpcloud-station/v1';

		/**
		 * Rest base for the current object.
		 *
		 * @var string
		 */
		protected $rest_base = '/domains';


		/**
		 * Register the routes.
		 */
		public function register_routes() {
			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . '/ssl-status',
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_ssl_status' ),
						'permission_callback' => array( $this, 'get_permissions_check' ),
					),
				)
			);

			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . '/txt-verification',
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_txt_verification' ),
						'permission_callback' => array( $this, 'get_permissions_check' ),
					),
				)
			);
		}

		/**
		 * Get the SSL status for a domain.
		 *
		 * @param WP_REST_Request $request The request object.
		 *
		 * @return WP_REST_Response
		 */
		public function get_ssl_status( $request ) {
			$params = $request->get_params();
			$domain = $params['domain'];

			$ssl_valid = WPCLOUD_Site::is_domain_ssl_valid( $domain );

			if ( is_wp_error( $ssl_valid ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => $ssl_valid->get_error_message(),
					),
					500
				);
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'valid'   => $ssl_valid,
				),
				200
			);
		}

		/**
		 * Get the TXT verification for a domain.
		 *
		 * @param WP_REST_Request $request The request object.
		 *
		 * @return WP_REST_Response
		 */
		public function get_txt_verification( $request ) {
			$params = $request->get_params();
			$domain = $params['domain'] ?? '';

			$verification = wpcloud_client_domain_verification_record( $domain );

			if ( is_wp_error( $verification ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => $verification->get_error_message(),
					),
					500
				);
			}

			return new WP_REST_Response(
				array(
					'success'      => true,
					'verification' => $verification,
					'domain'  => $domain,
				),
				200
			);
		}

		/**
		 * Check permissions for the current request.
		 *
		 * @param WP_REST_Request $request The request object.
		 *
		 * @return bool|WP_Error
		 */
		public function get_permissions_check( $request ) {
			if ( ! is_user_logged_in() ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You are not currently logged in.', 'wpcloud' ), rest_authorization_required_code() );
			}
			return true;
		}
	}
}
