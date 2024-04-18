<?php
/**
 * WP Cloud Site.
 *
 * @package wpcloud-dashboard
 */

declare( strict_types = 1 );

/**
 * Register WP Cloud Site post type.
 */
function wpcloud_register_site_post_type(): void {
	$labels = array(
		'name'               => _x( 'Sites', 'post type general name', 'wpcloud' ),
		'singular_name'      => _x( 'Site', 'post type singular name', 'wpcloud' ),
		'menu_name'          => _x( 'Sites', 'admin menu', 'wpcloud' ),
		'name_admin_bar'     => _x( 'Site', 'add new on admin bar', 'wpcloud' ),
		'add_new'            => _x( 'Add New', 'site', 'wpcloud' ),
		'add_new_item'       => __( 'Add New Site', 'wpcloud' ),
		'new_item'           => __( 'New Site', 'wpcloud' ),
		'edit_item'          => __( 'Edit Site', 'wpcloud' ),
		'view_item'          => __( 'View Site', 'wpcloud' ),
		'all_items'          => __( 'All Sites', 'wpcloud' ),
		'search_items'       => __( 'Search Sites', 'wpcloud' ),
		'parent_item_colon'  => __( 'Parent Sites:', 'wpcloud' ),
		'not_found'          => __( 'No sites found.', 'wpcloud' ),
		'not_found_in_trash' => __( 'No sites found in Trash.', 'wpcloud' )
	  );

	// Set the custom post type args
	$args = array(
		'labels'	   => $labels,
		'public'	   => true,
		'has_archive'  => true,
		'rest_base'    => 'wpcloud/sites',
		'rewrite'      => array( 'slug' => 'sites' ),
		'show_in_rest' => true,
		'show_in_ui'   => false,
		'show_in_menu' => false,
		'supports'     => array( 'title', 'editor', 'comments', 'author', 'thumbnail', 'custom-fields' ),
		'taxonomies'   => array( 'category', 'tag' ),
	);

	// Register the custom post type
	register_post_type( 'wpcloud_site', $args );

	// register the post meta
	wpcloud_site_register_post_meta( 'wpcloud_site_id', 'integer' );
	wpcloud_site_register_post_meta( 'php_version', 'string' );
	wpcloud_site_register_post_meta( 'data_center', 'string' );
}

/**
 * Register post meta for WP Cloud Site post type.
 *
 * @param string $name The name of the post meta.
 * @param string $type The type of the post meta.
 */
function wpcloud_site_register_post_meta( string $name, string $type ) {
	register_post_meta(
		'wpcloud_site',
		$name,
		array(
			'show_in_rest'       => true,
			'single'             => true,
			'type'               => $type,
			'sanitize_callback'  => 'wp_kses_post',
		)
	);
}

/**
 * Get the default domain name for a WP Cloud site.
 *
 * @param string $domain Optional. The domain name to include as subdomain.
 *
 * @return string Domain with default domain added.
 */
function wpcloud_site_get_default_domain( string $domain = '' ): string {
	$settings       = get_option( 'wpcloud_settings' );
	$default_domain = $settings['wpcloud_domain'] ?? '';

	if ( $default_domain ) {
		$domain .= ".{$default_domain}";
	}

	return $domain;
}

/**
 * After WP Cloud Site post has been created, create the site through the API.
 *
 * @param integer $post_id The post ID.
 * @param WP_Post $post    The post.
 * @param boolean $update  True if update. False of create.
 */
function wpcloud_on_create_site( int $post_id, WP_Post $post, bool $update ): void {
	if ( $update ) {
		return;
	}
	if ( 'wpcloud_site' !== $post->post_type ) {
		return;
	}

	$data        = array();
	$author      = get_user_by( 'id', $post->post_author );
	$domain      = $post->post_title;
	$php_version = get_post_meta( $post_id, 'php_version', true );
	$data_center = get_post_meta( $post_id, 'data_center', true );
	$post_name   = str_replace( '.', '-', $domain );

	wp_update_post( array( 'ID' => $post_id, 'post_name' => $post_name ) );

	if ( ! empty( $php_version ) ) {
		$data['php_version'] = $php_version;
	}
	if ( ! empty( $data_center ) && 'No Preference' !== $data_center ) {
		$data['geo_affinity'] = $data_center;
	}

	$result = wpcloud_client_site_create( $domain, $author->user_login, $author->user_email, $data );

	if ( is_wp_error( $result ) ) {
		error_log( $result->get_error_message() );
		update_post_meta( $post_id, 'wpcloud_site_error', $result->get_error_message() );
		return;
	}

	update_post_meta( $post_id, 'wpcloud_site_id', $result->atomic_site_id );

	do_action( 'wpcloud_site_created', $post_id, $post, $result->atomic_site_id );
}
add_action( 'wp_after_insert_post', 'wpcloud_on_create_site', 20, 3 );

/**
 * Prepare WP Cloud Site data for REST response. Retrieve details from the API and add to the response.
 *
 * @param WP_REST_Response $response The response.
 * @param WP_Post          $post     The post.
 *
 * @return WP_REST_Response The response.
 */
function wpcloud_on_rest_prepare_site( WP_REST_Response $response, WP_Post $post ): WP_REST_Response {
	$response->data['status'] = ( 'draft' === $post->status ) ? 'provisioning' : 'active';

	unset( $response->data['content'] );
	unset( $response->data['guid'] );
	unset( $response->data['meta'] );
	unset( $response->data['template'] );
	unset( $response->data['title'] );

	$wpcloud_site_id = get_post_meta( $post->ID, 'wpcloud_site_id', true );
	if ( empty( $wpcloud_site_id ) ) {
		return $response;
	}

	$wpcloud_site_id = intval( $wpcloud_site_id );

	$wpcloud_site = wpcloud_client_site_details( $wpcloud_site_id, true );
	if ( is_wp_error( $wpcloud_site ) ) {
		return $response;
	}

	$response->data = array_merge(
		$response->data,
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
			'wp_admin_user'   => $wpcloud_site->wp_admin_user,
			'wp_version'      => $wpcloud_site->wp_version,
		)
	);

	return $response;
}
add_filter( 'rest_prepare_wpcloud_site', 'wpcloud_on_rest_prepare_site', 10, 2 );

/**
 * Always query both draft and published sites in REST query.
 *
 * @param array      $args    The request arguments.
 * @param WP_Request $request The request.
 *
 * @return array The arguments with draft and publish included.
 */
function wpcloud_site_rest_query( $args, $request ): array {
	$args['post_status'] = array( 'draft', 'publish' );

	return $args;
}
add_filter( 'rest_wpcloud_site_query', 'wpcloud_site_rest_query', 10, 2 );

/**
 * Prevent WP Cloud Site from trash. Must force delete.
 */
add_filter( 'rest_wpcloud_site_trashable', '__return_false' );

/**
 * On WP Cloud Site post delete, delete from API.
 *
 * @param integer $post_id The ID of the post being deleted.
 */
function wpcloud_on_delete_site( int $post_id ): void {
	$wpcloud_site_id = get_post_meta( $post_id, 'wpcloud_site_id', true );
	// Site doesn't have associated wpcloud site id, proceed.
	if ( empty( $wpcloud_site_id ) ) {
		return;
	}

	$wpcloud_site_id = intval( $wpcloud_site_id );

	$result = wpcloud_client_site_delete( $wpcloud_site_id );
	if ( is_wp_error( $result ) ) {
		error_log( 'Error while deleting WP Cloud Site: ' . print_r( $result, true ) );
	}
}
add_action( 'before_delete_post', 'wpcloud_on_delete_site', 10, 1 );

/**
 * Lookup `wpcloud_site` post by `wpcloud_site_id`.
 *
 * @param integer $wpcloud_site_id The WP Cloud Site ID.
 *
 * @return WP_Post|WP_Error The post. WP_Error on error.
 */
function wpcloud_lookup_post_by_site_id( int $wpcloud_site_id ): mixed {
	$query = new WP_Query(
		array(
			'post_type' => 'wpcloud_site',
			'meta_key' => 'wpcloud_site_id',
			'meta_value' => $wpcloud_site_id
		)
	);

	if ( ! $query->have_posts() ) {
		return null;
	}

	return $query->posts[0];
}

/**
 * Get a site detail.
 *
 * @param int|WP_Post $post The site post or ID.
 * @param string $key The detail key.
 *
 * @return mixed The detail value. WP_Error on error.
 */

function wpcloud_get_site_detail( int|WP_Post $post, string $key, ): mixed {
	if ( is_int( $post ) ) {
		$post = get_post( $post );
	}

	if ( ! $post ) {
		return null;
	}

	$wpcloud_site_id = get_post_meta( $post->ID, 'wpcloud_site_id', true );
	if ( empty( $wpcloud_site_id ) ) {
		return null;
	}

	$wpcloud_site_id = intval( $wpcloud_site_id );

	$result = wpcloud_client_site_details( $wpcloud_site_id, true );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	if ( ! isset( $result->$key ) ) {
		return null;
	}

	return $result->$key;
}
