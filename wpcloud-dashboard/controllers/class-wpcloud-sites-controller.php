<?php

if ( ! class_exists( 'WPCLOUD_Sites_Controller' ) ) {

	class WPCLOUD_Sites_Controller extends WP_REST_Controller {

		/**
		 * The namespace.
		 *
		 * @var string
		 */
		protected $namespace = 'wpcloud/v1';
		/**
		 * Rest base for the current object.
		 *
		 * @var string
		 */
		protected $rest_base = 'sites';

		/**
		 * Register the routes.
		 */
		public function register_routes() {
			register_rest_route(
				$this->namespace, '/' . $this->rest_base,
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_sites' ),
						'permission_callback' => array( $this, 'manage_sites_permission_check' ),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'create_site' ),
						'permission_callback' => array( $this, 'manage_sites_permission_check' ),
					),
				))
			;

			register_rest_route(
				$this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)',
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_site' ),
						'permission_callback' => array( $this, 'manage_sites_permission_check' ),
					),
				)
			);
		}

		/**
		 * Validate that a user has the manage sites permission.
		 *
		 * @param WP_REST_Request $request
		 *
		 * @return WP_Error|WP_REST_Response
		 */
		public function manage_sites_permission_check( $request ) {
			return current_user_can( WPCLOUD_CAN_MANAGE_SITES );
		}

		/**
		 * Get a collection of sites.
		 *
		 * @param WP_REST_Request $request
		 *
		 * @return WP_Error|WP_REST_Response
		 */
		public function get_sites( $request ) {
			require_once  plugin_dir_path( __FILE__ ) . '../includes/wpcloud-client.php';

			$sites = wpcloud_client_site_list( 'wp_version', 'php_version', 'space_quota', 'db_file_size', 'static_file_404', 'suspended' );
			if ( is_wp_error( $sites) ) {
				return $sites;
			}

			return new WP_REST_Response( $sites, WP_Http::OK );
		}

		/**
		 * Get site details.
		 *
		 * @param WP_REST_Request $request
		 *
		 * @return WP_Error|WP_REST_Response
		 */
		public function get_site( $request ) {
			require_once  plugin_dir_path( __FILE__ ) . '../includes/wpcloud-client.php';

			$params = $request->get_params();
			$atomic_site_id = intval( $params['id'] );

			$site_details = wpcloud_client_site_details( $atomic_site_id );
			if ( is_wp_error( $site_details ) ) {
				return $site_details;
			}

			return new WP_REST_Response( $site_details, WP_Http::OK );
		}

		/**
		 * Create a new site.
		 *
		 * @param WP_REST_Request $request
		 *
		 * @return WP_Error|WP_REST_Response
		 */
		public function create_site( $request ) {
			return new WP_REST_Response( null, WP_Http::NOT_IMPLEMENTED );
		}
	}

}
