<?php


require_once plugin_dir_path( __FILE__ ) . './controllers/class-wpcloud-sites-controller.php';
require_once plugin_dir_path( __FILE__ ) . './includes/class-wpcloud-site.php';

/**
 * Set up the plugin's capabilities.
 */
function wpcloud_add_capabilities() {
	$role = get_role( 'administrator' );
	$role->add_cap( WPCLOUD_CAN_MANAGE_SITES );
}

function register_controllers() {
	$sites_controller = new WPCLOUD_Sites_Controller();
	$sites_controller->register_routes();
}
add_action( 'rest_api_init', 'register_controllers' );

/**
 * Initialize the plugin.
 */
function wpcloud_init() {
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

if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'admin/init.php';
}

// load the templates
/* Filter the single_template with our custom function*/
add_filter('single_template', 'load_wpcloud_site_template');

function load_wpcloud_site_template( $single ): string {

    global $post;
		global $wpcloud_site;

    /* Checks for single template by post type */
    if ( $post->post_type == 'wpcloud_site' ) {
			$wpcloud_site = WPCloud_Site::find( $post->ID );
			$wpcloud_site->set_client_details();
			return plugin_dir_path( __FILE__ ) . '/templates/single-wpcloud-site.php';
		}
		return $single;
}