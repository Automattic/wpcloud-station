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

		public static function with_post_details( $wpcloud_site ) {
			$post = wpcloud_lookup_post_by_site_id( $wpcloud_site->atomic_site_id );
			if ( ! empty( $post ) ) {
				$wpcloud_site->post_id     = $post->ID;
				$wpcloud_site->post_status = ( 'draft' === $post->post_status ) ? 'provisioning' : 'active';
			}

			$wpcloud_site->wpcloud_site_id = $wpcloud_site->atomic_site_id;

			// unset( $wpcloud_site->atomic_site_id );
			// unset( $wpcloud_site->wpcom_blog_id );
			// unset( $wpcloud_site->atomic_client_id );

			return $wpcloud_site;
		}

		/**
		 * Get a collection of sites.
		 *
		 * @param WP_REST_Request $request
		 *
		 * @return WP_Error|WP_REST_Response
		 */
		public function get_sites( $request ) {
			$results = wpcloud_client_site_list( 'wp_version', 'php_version', 'space_quota', 'db_file_size', 'static_file_404', 'suspended' );
			if ( is_wp_error( $results ) ) {
				return $results;
			}

			$results = array_map( self::class . '::with_post_details', $results );

			return new WP_REST_Response( $results, WP_Http::OK );
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

			$result = self::with_post_details( $result );

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
			$php_version = $params['php_version'];
			$data_center = $params['data_center'];
			$owner_id    = get_current_user_id();

			$post_data   = array(
				'post_title' => $domain,
				'post_type' => 'wpcloud_site',
				'post_status' => 'draft',
				'post_author' => $owner_id,
			);

			if ( ! empty( $php_version ) ) {
				$data['meta_input']['php_version'] = $php_version;
			}
			if ( 'No Preference' !== $data_center ) {
				$data['meta_input']['geo_affinity'] = $data_center;
			}

			$post = wp_insert_post( $post_data );
			if ( is_wp_error( $post ) ) {
				error_log( 'Error creating site post: ' . $post->get_error_message() );
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
			$post_id = intval( $params['id'] );

			$result = wp_delete_post( $id, true );
			if ( is_wp_error( $result ) ) {
				return $result;
			}

			return new WP_REST_Response( null, WP_Http::NO_CONTENT );
		}
	}

}
