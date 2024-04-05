<?php
/**
 * Plugin instantiation.
 *
 * @package wpcloud-dashboard
 */

declare( strict_types = 1 );

/**
 * Requires
 */
require_once plugin_dir_path( __FILE__ ) . 'controllers/class-wpcloud-sites-controller.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcloud-site.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/wpcloud-client.php';

/**
 * Set up the plugin's capabilities.
 */
function wpcloud_add_capabilities(): void {
	$role = get_role( 'administrator' );
	$role->add_cap( WPCLOUD_CAN_MANAGE_SITES );
}

function register_controllers(): void {
	$sites_controller = new WPCLOUD_Sites_Controller();
	$sites_controller->register_routes();
}
add_action( 'rest_api_init', 'register_controllers' );

/**
 * Initialize the plugin.
 */
function wpcloud_init(): void {
	wpcloud_add_capabilities();
	WPCloud_Site::register_post_type();
}
add_action( 'init', 'wpcloud_init' );

function on_delete_site( int $site_id ): void {
	$site = WPCloud_Site::find( $site_id );
	if ( ! $site ) {
		return;
	}
	$result = $site->delete();
	if ( is_wp_error( $result ) ) {
		error_log( $result->get_error_message() );
	}
}
add_action( 'before_delete_post', 'on_delete_site', 10, 1 );

function on_rest_prepare_wpcloud_site( WP_REST_Response $response, WP_Post $post ): WP_REST_Response {
	$wpcloud_site_id = intval( get_post_meta( $post->ID, 'wpcloud_site_id', true ) );

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
add_filter( 'rest_prepare_wpcloud_site', 'on_rest_prepare_wpcloud_site', 10, 2 );

if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'admin/init.php';
}
