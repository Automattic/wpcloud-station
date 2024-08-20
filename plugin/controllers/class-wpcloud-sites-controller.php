<?php
/**
 * WP Cloud Sites Controller.
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

if ( ! class_exists( 'WPCLOUD_Sites_Controller' ) ) {

	/**
	 * Class WPCLOUD_Webhook_Controller.
	 */
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
		 * The post type.
		 *
		 * @var string
		 */
		protected $post_type = 'wpcloud_site';


		protected function rootPathArgs(): array {
			return array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				// 'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				'allow_batch' => false,
				'schema'      => array( $this, 'get_public_item_schema' ),
			);
		}

		protected function sitePathArgs(): array {
			return array(
				'args'        => array(
					'id' => array(
						'description' => __( 'Unique identifier for the site.' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					// 'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				'allow_batch' => false,
			);
		}

		/**
		 * Register the routes.
		 */
		public function register_routes() {
			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base,
				$this->rootPathArgs()
			);

			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . '/(?P<id>[\d]+)',
				$this->sitePathArgs()
			);

			// @TODO: Remove this route once Gutenberg is updated to use the new routes.
			register_rest_route(
				'wp/v2',
				'/wpcloud_site',
				$this->rootPathArgs()
			);
		}

		/**
		 * Create a site.
		 *
		 * @param WP_REST_Request $request The request object.
		 *
		 * @return WP_REST_Response
		 */
		public function create_item( $request ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Not implemented',
				),
				501
			);
		}

		/**
		 * Get a site.
		 *
		 * @param WP_REST_Request $request The request object.
		 *
		 * @return WP_REST_Response
		 */
		public function get_item( $request ) {
			$post = $this->get_post( $request );
			if ( is_wp_error( $post ) ) {
				return $post;
			}

			$data = $this->prepare_item_for_response( $post, $request );
			return rest_ensure_response( $data );
		}

		/**
		 * Update a site.
		 *
		 * @param WP_REST_Request $request The request object.
		 *
		 * @return WP_REST_Response
		 */
		public function update_item( $request ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Not implemented',
				),
				501
			);
		}

		/**
		 * Delete a site.
		 *
		 * @param WP_REST_Request $request The request object.
		 *
		 * @return WP_REST_Response
		 */
		public function delete_item( $request ) {
			$post = $this->get_post( $request );
			if ( is_wp_error( $post ) ) {
				return $post;
			}

			$result = wp_delete_post( $post->ID, true );

			if ( is_wp_error( $result ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => 'Error deleting site.',
					),
					400
				);
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => 'Delete site request succeeded.',
				),
				200
			);
		}

		/**
		 * Get the sites.
		 *
		 * @param WP_REST_Request $request The request object.
		 *
		 * @return WP_REST_Response
		 */
		public function get_items( $request ) {

			$parameter_mappings = array(
				'owner'         => 'author__in',
				'owner_exclude' => 'author__not_in',
				'exclude'       => 'post__not_in',
				'include'       => 'post__in',
				'menu_order'    => 'menu_order',
				'offset'        => 'offset',
				'order'         => 'order',
				'orderby'       => 'orderby',
				'page'          => 'paged',
				'search'        => 's',
				'slug'          => 'post_name__in',
				'status'        => 'post_status',
			);

			foreach ( $parameter_mappings as $api_param => $wp_param ) {
				if ( isset( $request[ $api_param ] ) ) {
					$args[ $wp_param ] = $request[ $api_param ];
				}
			}

			if ( ! isset( $args['post_status'] ) ) {
				$args['post_status'] = 'any';
			}

			$query_args              = apply_filters( 'wpcloud_rest_sites_query_args', $args, $request );
			$query_args['post_type'] = $this->post_type;

			if ( ! current_user_can( 'manage_options' ) ) {
				$query_args['author__in'] = array( get_current_user_id() );
			}

			$posts        = array();
			$posts_query  = new WP_Query();
			$query_result = $posts_query->query( $query_args );
			foreach ( $query_result as $post ) {
				$data    = $this->prepare_item_for_response( $post, $request );
				$posts[] = $this->prepare_response_for_collection( $data );
			}

			$page        = (int) ( $query_args['paged'] ?? 1 );
			$total_posts = $posts_query->found_posts;

			if ( $total_posts < 1 && $page > 1 ) {
				// Out-of-bounds, run the query again without LIMIT for total count.
				unset( $query_args['paged'] );

				$count_query = new WP_Query();
				$count_query->query( $query_args );
				$total_posts = $count_query->found_posts;
			}

			$max_pages = (int) ceil( $total_posts / (int) $posts_query->query_vars['posts_per_page'] );

			if ( $page > $max_pages && $total_posts > 0 ) {
				return new WP_Error(
					'rest_sites_invalid_page_number',
					__( 'The page number requested is larger than the number of sites available.' ),
					array( 'status' => 400 )
				);
			}

			$response = rest_ensure_response( $posts );
			$response->header( 'X-WP-Total', (int) $total_posts );
			$response->header( 'X-WP-TotalPages', (int) $max_pages );

			$request_params = $request->get_query_params();
			$collection_url = rest_url( rest_get_route_for_post_type_items( $this->post_type ) );
			$base           = add_query_arg( urlencode_deep( $request_params ), $collection_url );

			if ( $page > 1 ) {
				$prev_page = $page - 1;

				if ( $prev_page > $max_pages ) {
					$prev_page = $max_pages;
				}

				$prev_link = add_query_arg( 'page', $prev_page, $base );
				$response->link_header( 'prev', $prev_link );
			}
			if ( $max_pages > $page ) {
				$next_page = $page + 1;
				$next_link = add_query_arg( 'page', $next_page, $base );

				$response->link_header( 'next', $next_link );
			}

			return $response;
		}

		/**
		 * Prepare a single site output for response.
		 *
		 * @param WP_Post         $item The post object.
		 * @param WP_REST_Request $request Request object.
		 *
		 * @return WP_REST_Response
		 */
		public function prepare_item_for_response( $item, $request ) {
			$post = $item;

			$GLOBALS['post'] = $post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

			$data = array(
				'id'     => $post->ID,
				'name'   => get_the_title( $post ),
				'owner'  => get_the_author_meta( 'user_nicename', $post->post_author ),
				'date'   => $post->post_date,
				'status' => ( 'draft' === $post->post_status ? 'provisioning' : 'active' ),
			);

			$wpcloud_site_id = get_post_meta( $post->ID, 'wpcloud_site_id', true );
			if ( empty( $wpcloud_site_id ) ) {
				$data['status'] = 'unknown';
			} else {
				$wpcloud_site = wpcloud_client_site_details( (int) $wpcloud_site_id, true );
				if ( is_wp_error( $wpcloud_site ) ) {
					$data['status'] = 'error';
					$data['error']  = $wpcloud_site->get_error_message();
				} else {
					$data = array_merge(
						$data,
						array(
							'wpcloud_site_id' => $wpcloud_site_id,
							'data_center'     => $wpcloud_site->extra->server_pool->geo_affinity,
							'php_version'     => $wpcloud_site->php_version,
							'primary_domain'  => $wpcloud_site->domain_name,
							'cache_prefix'    => $wpcloud_site->cache_prefix,
							'db_charset'      => $wpcloud_site->db_charset,
							'db_collate'      => $wpcloud_site->db_collate,
							'wp_admin_user'   => $wpcloud_site->wp_admin_user,
							'static_file_404' => $wpcloud_site->static_file_404,
							'wp_admin_email'  => $wpcloud_site->wp_admin_email,
							'wp_version'      => $wpcloud_site->wp_version,
						)
					);
				}
			}
			$response = rest_ensure_response( $data );
			return apply_filters( 'wpcloud_rest_prepare_site', $response, $post, $request );
		}

		/**
		 * Get items permissions check.
		 *
		 * @param WP_REST_Request $request The request object.
		 *
		 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
		 */
		public function get_items_permissions_check( $request ) {
			$check = $this->user_access_check();
			// we only need to check for an error here.
			if ( is_wp_error( $check ) ) {
				return $check;
			}
			return true;
		}

		/**
		 * Check user for site permissions.
		 *
		 * @param WP_REST_Request $request The request object.
		 *
		 * @return true|WP_Error
		 */
		public function get_item_permissions_check( $request ): true|WP_Error {
			$post = $this->get_post( $request );
			if ( is_wp_error( $post ) ) {
				return $post;
			}
			$check = $this->user_access_check();
			if ( is_wp_error( $check ) || true === $check ) {
				return $check;
			}

			if ( get_current_user_id() !== (int) $post->post_author ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'Unauthorized request.', 'wpcloud' ), rest_authorization_required_code() );
			}
			return true;
		}

		/**
		 * Check permissions for the current request.
		 *
		 * @return bool|WP_Error True if the requester has manage site capabilities. False if logged in but with out manage site capabilities, WP_Error if not logged in.
		 */
		protected function user_access_check(): bool|WP_Error {
			if ( ! is_user_logged_in() ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'Unauthorized request', 'wpcloud' ), rest_authorization_required_code() );
			}
			if ( current_user_can( WPCLOUD_CAN_MANAGE_SITES ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Get a post.
		 *
		 * @param WP_REST_Request $request The request object.
		 * @return WP_Post|WP_Error
		 */
		protected function get_post( WP_REST_Request $request ): WP_Post|WP_Error {
			$error = new WP_Error(
				'rest_post_invalid_id',
				__( 'Invalid post ID.' ),
				array( 'status' => 404 )
			);

			$id = (int) $request->get_param( 'id' );

			if ( $id <= 0 ) {
				return $error;
			}

			$post = get_post( (int) $id );
			if ( empty( $post ) || empty( $post->ID ) || $this->post_type !== $post->post_type ) {
				return $error;
			}

			return $post;
		}
	}
}
