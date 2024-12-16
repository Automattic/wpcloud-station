<?php
/**
 * WP Cloud Metrics Controller
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

if ( ! class_exists( 'WPCLOUD_Metrics_Controller' ) ) {

	/**
	 * Class WPCLOUD_Metrics_Controller
	 */
	class WPCLOUD_Metrics_Controller extends WP_REST_Controller {

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
		protected $rest_base = '/metrics';

		/**
		 * Register the routes for the objects of the controller.
		 */
		public function register_routes() {
			register_rest_route(
				$this->namespace,
				$this->rest_base . '/site/(?P<id>[\d]+)',
				array(
					'args' => array(
						'id' => array(
							'description' => esc_html__( 'Unique identifier for the site.', 'wpcloud' ),
							'type'        => 'integer',
						),
					),
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_site_metric' ),
					'permission_callback' => array( $this, 'user_access_check' ),
				)
			);
		}

		public function get_site_metric( WP_REST_Request $request ): WP_REST_Response {
			$params = $request->get_params();
			$site_id = $params['id'];

			$site = WPCLOUD_Site::get_site( $site_id );

			if ( ! $site ) {
				return new WP_Error( 'rest_site_not_found', esc_html__( 'Site not found', 'wpcloud' ), array( 'status' => 404 ) );
			}

			$metrics = new WPCLOUD_Metrics( site: $site_id, type: 'server' );

			$metrics->filter( $params );

			return new WP_REST_Response( $metrics, 200 );
		}


		/**
		 * Check permissions for the current request.
		 *
		 * @return bool|WP_Error True if the requester has manage site capabilities. False if logged in but with out manage site capabilities, WP_Error if not logged in.
		 */
		protected function user_access_check(): bool|WP_Error {
			if ( is_user_logged_in() && current_user_can( WPCLOUD_CAN_MANAGE_SITES ) ) {
				return true;
			}
			return new WP_Error( 'rest_forbidden', esc_html__( 'Unauthorized request', 'wpcloud' ), rest_authorization_required_code() );
		}
	}
}
