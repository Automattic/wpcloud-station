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
require_once plugin_dir_path( __FILE__ ) . 'custom-post-types/wpcloud-site.php';
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
	wpcloud_register_site_post_type();
}
add_action( 'init', 'wpcloud_init' );

if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'admin/init.php';
}
