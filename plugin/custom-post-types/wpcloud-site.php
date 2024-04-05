<?php
/**
 * WP Cloud Site.
 *
 * @package wpcloud-dashboard
 */

declare( strict_types = 1 );

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
		'label'               => __( 'Sites', 'wpcloud' ),
		'description'         => __( 'Sites', 'wpcloud' ),
		'labels'              => $labels,
		//
		// 'can_export'          => true,
		'capability_type'     => 'post',
		// 'exclude_from_search' => false,
		'has_archive'         => true,
		// 'hierarchical'        => false,
		'public'              => true,
		// 'publicly_queryable'  => false,
		'rest_base'           => 'wpcloud/sites',
		"show_in_rest"        => true,
		'show_ui'             => false,
		// 'show_in_admin_bar'   => false,
		'show_in_menu'        => false,
		// 'show_in_nav_menus'   => false,
		'taxonomies'          => array( 'category', 'tag' ),
	);

	// Register the custom post type
	register_post_type( 'wpcloud_site', $args );
}

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

function wpcloud_on_rest_prepare_site( WP_REST_Response $response, WP_Post $post ): WP_REST_Response {
	$wpcloud_site_id = get_post_meta( $post->ID, 'wpcloud_site_id', true );
	if ( empty( $wpcloud_site_id ) ) {
		return $response;
	}

	$wpcloud_site_id = intval( $wpcloud_site_id );

	$result = wpcloud_client_site_details( $wpcloud_site_id, true );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	$response->data['wpcloud'] = array(
		'php_version'     => $result->php_version,
		'data_center'     => $result->extra->server_pool->geo_affinity,
		'domain_name'     => $result->domain_name,
		'wpcloud_site_id' => $wpcloud_site_id,
	);

	return $response;
}
add_filter( 'rest_prepare_wpcloud_site', 'wpcloud_on_rest_prepare_site', 10, 2 );
