<?php

require_once plugin_dir_path( __FILE__ ) . 'class-wpcloud-site.php';

/**
 * Set up the plugin's capabilities.
 */
function wpcloud_add_capabilities() {
	$role = get_role( 'administrator' );
	$role->add_cap( WPCLOUD_CAN_MANAGE_SITES );
}

/**
 * Initialize the plugin.
 */
function wpcloud_init() {
	wpcloud_add_capabilities();
	WPCloud_Site::register_post_type();
}
add_action( 'init', 'wpcloud_init' );


if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'admin/init.php';
}