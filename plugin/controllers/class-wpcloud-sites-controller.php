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
		protected $namespace = 'wpcloud-station/v1';

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


		/**
		 * Item schema.
		 *
		 * @return array The item schema.
		 */
		public function get_item_schema(): array {
			$schema = array(
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				'title'      => $this->post_type,
				'type'       => 'object',
				// Base properties for every Post.
				'properties' => array(
					'date'            => array(
						'description' => __( "The date the site was created, in the site's timezone." ),
						'type'        => array( 'string', 'null' ),
						'format'      => 'date-time',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'date_gmt'        => array(
						'description' => __( 'The date the site was created, as GMT.' ),
						'type'        => array( 'string', 'null' ),
						'format'      => 'date-time',
						'context'     => array( 'view', 'edit' ),
					),
					'id'              => array(
						'description' => __( 'Unique identifier for the site.' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'wpcloud_site_id' => array(
						'description' => __( 'Unique identifier for the site in WP Cloud.' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit' ),
						'readonly'    => true,
					),
					'link'            => array(
						'description' => __( 'URL to the site configuration page.' ),
						'type'        => 'string',
						'format'      => 'uri',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'status'          => array(
						'description' => __( 'A named status for the site.' ),
						'type'        => 'string',
						'enum'        => array( 'active', 'provisioning', 'unknown', 'error' ),
						'context'     => array( 'view', 'edit' ),
						'readonly'    => true,
					),
					'owner'           => array(
						'description' => __( 'The user who owns the site. Either id, login, or email' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'arg_options' => array(
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => array( $this, 'validate_owner' ),
						),
					),
					'site_name'       => array(
						'description' => __( 'Internal name for the site.' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'arg_options' => array(
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
					'php_version'     => array(
						'description' => __( 'The PHP version for the site.' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'arg_options' => array(
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => array( $this, 'validate_php_version' ),
						),
					),
					'data_center'     => array(
						'description' => __( 'The data center for the site.' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'arg_options' => array(
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => array( $this, 'validate_data_center' ),
						),
					),
					'site_details'    => array(
						'description' => __( 'Details about the site.' ),
						'type'        => 'object',
						'context'     => array( 'view', 'edit' ),
						'arg_options' => array(
							'sanitize_callback' => array( $this, 'sanitize_site_details' ),
							'validate_callback' => array( $this, 'validate_site_details' ),
						),
					),
				),
			);

			$mutable_details = WPCloud_Site::get_mutable_options();
			foreach ( $mutable_details as $key => $detail ) {
				$prop = array(
					'description' => $detail['hint'],
					'type'        => $detail['type'],
					'context'     => array( 'view', 'edit' ),
				);
				if ( isset( $detail['options'] ) ) {
					$prop['enum'] = $detail['options'];
				}
				$schema['properties']['site_details']['properties'][ $key ] = $prop;
			}

			$read_only_details = WPCloud_Site::get_read_only_fields();
			foreach ( $read_only_details as $key => $detail ) {
				$schema['properties']['site_details'][ $key ] = array(
					'context' => array( 'view' ),
				);
			}
			return $schema;
		}


		/**
		 * Get root path args.
		 *
		 * @return array The root path args.
		 */
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
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				'allow_batch' => false,
				'schema'      => array( $this, 'get_public_item_schema' ),
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
				array(
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
						'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					),
					array(
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => array( $this, 'delete_item' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
					),
					'allow_batch' => false,
					'schema'      => array( $this, 'get_public_item_schema' ),
				)
			);

			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . '/details/list',
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_site_details_list' ),
						'permission_callback' => '__return_true',
					),
					'schema' => array( $this, 'get_public_item_schema' ),
				),
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
		public function create_item( $request ): WP_REST_Response {
			$site_data = $request->get_params();

			$owner = $this->get_owner( $site_data['owner'] );
			if ( is_wp_error( $owner ) ) {
				return $owner;
			}
			$site_data['site_owner_id'] = $owner->ID;

			$post = WPCloud_Site::create( $site_data );
			if ( is_wp_error( $post ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => $post->get_error_message(),
					),
					400
				);
			}
			return $this->prepare_item_for_response( $post, $request );
		}

		/**
		 * Get a site.
		 *
		 * @param WP_REST_Request $request The request object.
		 *
		 * @return WP_REST_Response
		 */
		public function get_item( $request ): WP_REST_Response {
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
			$site_id      = (int) $request['id'];
			$site_details = $request->get_param( 'site_details' );
			if ( ! $site_details ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => 'No site details provided.',
					),
					400
				);
			}
			$data   = array( 'site_id' => $site_id );
			$errors = array();
			foreach ( $site_details as $key => $value ) {
				$result = WPCloud_Site::update_detail( array_merge( $data, array( $key => $value ) ) );
				if ( is_wp_error( $result ) ) {
					$errors[] = $result->get_error_message();
				}
			}

			if ( ! empty( $errors ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => 'Error updating site details.',
						'errors'  => $errors,
					),
					400
				);
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => 'Update site request succeeded.',
				),
				200
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
		 * Get the site details list.
		 *
		 * @param WP_REST_Request $request The request object.
		 *
		 * @return WP_REST_Response
		 */
		public function get_site_details_list( $request ): WP_REST_Response {
			$list = array();

			$mutable_details = WPCloud_Site::get_mutable_options();

			foreach ( $mutable_details as $key => $detail ) {
				$field = array(
					'field'   => $key,
					'type'    => $detail['type'],
					'context' => array( 'view', 'edit' ),
				);
				if ( isset( $detail['options'] ) ) {
					$field['options'] = $detail['options'];
				}
				$list[] = $field;

			}

			$read_only_details = WPCloud_Site::get_read_only_fields();
			foreach ( $read_only_details as $key => $detail ) {
				$list[] = array(
					'field'   => $key,
					'context' => array( 'view' ),
				);
			}

			return rest_ensure_response( $list );
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
		 * Validate the owner parameter.
		 *
		 * @param string          $owner_search The owner search term.
		 * @param WP_REST_Request $request The request object.
		 * @param string          $param The parameter name.
		 *
		 * @return true|WP_Error
		 */
		public function validate_owner( string $owner_search, WP_REST_Request $request, string $param ): true|WP_Error {

			if ( ! $owner_search ) {
				return true;
			}

			$owner = $this->get_owner( $owner_search );
			if ( is_wp_error( $owner ) ) {
				return $owner;
			}

			$can_manage_owner = current_user_can( WPCLOUD_CAN_MANAGE_SITES ) || get_current_user_id() === $owner->ID;
			$can_manage_owner = apply_filters( 'wpcloud_rest_can_manage_owner', $can_manage_owner, $owner, $request, $param );

			if ( ! $can_manage_owner ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'Unauthorized request.', 'wpcloud' ), rest_authorization_required_code() );
			}

			$args = $request->get_attributes()['args'][ $param ];
			return rest_validate_value_from_schema( $owner_search, $args, $param );
		}

		/**
		 * Validate the PHP version parameter.
		 *
		 * @param string          $php_version The PHP version.
		 * @param WP_REST_Request $request The request object.
		 * @param string          $param The parameter name.
		 *
		 * @return true|WP_Error
		 */
		public function validate_php_version( $php_version, $request, $param ): true|WP_Error {
			if ( ! $php_version ) {
				return true;
			}

			$valid_versions = wpcloud_client_php_versions_available( true );

			if ( is_wp_error( $valid_versions ) ) {
				return $valid_versions;
			}

			if ( ! array_key_exists( $php_version, (array) $valid_versions ) ) {
				return new WP_Error( 'rest_invalid_param', __( 'Invalid PHP version.' ), array( 'status' => 400 ) );
			}

			$args = $request->get_attributes()['args'][ $param ];
			return rest_validate_value_from_schema( $php_version, $args, $param );
		}

		/**
		 * Validate the data center parameter.
		 *
		 * @param string          $data_center The data center.
		 * @param WP_REST_Request $request The request object.
		 * @param string          $param The parameter name.
		 *
		 * @return true|WP_Error
		 */
		public function validate_data_center( $data_center, $request, $param ): true|WP_Error {
			if ( ! $data_center ) {
				return true;
			}

			$valid_data_centers = wpcloud_client_data_centers_available( true );
			if ( is_wp_error( $valid_data_centers ) ) {
				return $valid_data_centers;
			}

			if ( ! array_key_exists( $data_center, (array) $valid_data_centers ) ) {
				return new WP_Error( 'rest_invalid_param', __( 'Invalid data center.' ), array( 'status' => 400 ) );
			}

			$args = $request->get_attributes()['args'][ $param ];
			return rest_validate_value_from_schema( $data_center, $args, $param );
		}

		/**
		 * Validate the site details parameter.
		 *
		 * @param array           $site_details The site details.
		 * @param WP_REST_Request $request The request object.
		 * @param string          $param The parameter name.
		 *
		 * @return true|WP_Error
		 */
		public function validate_site_details( $site_details, $request, $param ): true|WP_Error {
			if ( ! $site_details ) {
				return true;
			}

			$mutable_details = WPCloud_Site::get_mutable_options();
			foreach ( $site_details as $key => $value ) {
				$mutable_detail = $mutable_details[ $key ] ?? false;
				if ( ! $mutable_detail ) {
					return new WP_Error( 'rest_invalid_param', __( 'Invalid site detail.' ), array( 'status' => 400 ) );
				}

				$options = $mutable_detail['options'] ?? false;
				if ( $options ) {
					$valid_value = array_key_exists( $value, (array) $options );
					$valid       = apply_filters( 'wpcloud_rest_validate_site_detail', $valid_value, $value, $key, $options, $request, $param );
					if ( ! $valid ) {
						return new WP_Error( 'rest_invalid_param', __( 'Invalid site detail value.' ), array( 'status' => 400 ) );
					}
				}
				return true;
			}

			$args = $request->get_attributes()['args'][ $param ];
			return rest_validate_value_from_schema( $site_details, $args, $param );
		}

		/**
		 * Sanitize the site details parameter.
		 *
		 * @param array $site_details The site details.
		 *
		 * @return array The sanitized site details.
		 */
		public function sanitize_site_details( array $site_details ): array {
			if ( ! $site_details ) {
				return array();
			}

			$sanitized = array();
			foreach ( $site_details as $key => $value ) {
				$sanitized[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
			}

			return $sanitized;
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

		/**
		 * Get the owner
		 *
		 * @param string $owner_search The owner search term.
		 *
		 * @return WP_User|WP_Error
		 */
		protected function get_owner( string $owner_search ): WP_User|WP_Error {
			$users = get_users(
				array(
					'search'         => $owner_search,
					'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' ),
				)
			);

			if ( count( $users ) !== 1 ) {
				return new WP_Error( 'rest_invalid_param', __( 'User not found' ), array( 'status' => 400 ) );
			}
			return $users[0];
		}
	}
}
