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
					array(
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => array( $this, 'delete_site' ),
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
			$result = wpcloud_client_site_list( 'wp_version', 'php_version', 'space_quota', 'db_file_size', 'static_file_404', 'suspended' );
			if ( is_wp_error( $result) ) {
				return $result;
			}

			return new WP_REST_Response( $result, WP_Http::OK );
		}

		/**
		 * Get site details.
		 *
		 * @param WP_REST_Request $request
		 *
		 * @return WP_Error|WP_REST_Response
		 */
		public function get_site( $request ) {
			$params = $request->get_params();
			$wpcloud_site_id = intval( $params['id'] );

			$extra = false;
			if ( isset( $params['extra'] ) ) {
				$extra = (bool) $params['extra'];
			}

			$result = wpcloud_client_site_details( $wpcloud_site_id, $extra );
			if ( is_wp_error( $result ) ) {
				return $result;
			}

			return new WP_REST_Response( $result, WP_Http::OK );
		}

		/**
		 * Create a new site.
		 *
		 * @param WP_REST_Request $request
		 *
		 * @return WP_Error|WP_REST_Response
		 */
		public function create_site( $request ) {
			$params      = $request->get_params();
			$domain      = $params['domain'];
			$admin_user  = $params['admin_user'];
			$admin_email = $params['admin_email'];

			$result = wpcloud_client_site_create( $domain, $admin_user, $admin_email, $params );
			if ( is_wp_error( $result ) ) {
				return $result;
			}

			return new WP_REST_Response( $result, WP_Http::CREATED );
		}

		/**
		 * Delete site.
		 *
		 * @param WP_REST_Request $request
		 *
		 * @return WP_Error|WP_REST_Response
		 */
		public function delete_site( $request ) {
			$params = $request->get_params();
			$wpcloud_site_id = intval( $params['id'] );

			$result = wpcloud_client_site_delete( $wpcloud_site_id );
			if ( is_wp_error( $result ) ) {
				return $result;
			}

			return new WP_REST_Response( $result, WP_Http::OK );
		}
	}

}
