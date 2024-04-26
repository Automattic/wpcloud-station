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
require_once plugin_dir_path( __FILE__ ) . 'custom-post-types/wpcloud-site.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/demo-mode.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcloud-site.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/wpcloud-client.php';
require_once plugin_dir_path( __FILE__ ) . 'blocks-init.php';

if ( ! is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'assets/js/build/index.asset.php';
	add_action( 'wp_enqueue_scripts', function():void {
		wp_enqueue_script( 'wpcloud', plugin_dir_url( __FILE__ ) . 'assets/js/build/index.js', array('wp-hooks' ) );
	} );
}

/**
 * Set up the plugin's capabilities.
 */
function wpcloud_add_capabilities(): void {
	$role = get_role( 'administrator' );
	$role->add_cap( WPCLOUD_CAN_MANAGE_SITES );
}

/**
 * Set up ACL for WP Cloud specific pages
 */
function wpcloud_setup_acl(): void {
	// Verify that the private category exists
	wp_insert_term( 'WP Cloud Private Page', 'category', array(
    'description' => 'Private category for WP Cloud specific pages.',
    'slug' => WPCLOUD_PRIVATE_CATEGORY,
	) );

	// Allow adding categories to pages
	register_taxonomy_for_object_type('category', 'page');

	add_filter('template_redirect', 'wpcloud_verify_logged_in');
}

/**
 * Verify logged in for WP Cloud specific pages
 */
function wpcloud_verify_logged_in(): void {
	if ( is_admin() ) {
		return;
	}

	$categories = array_reduce(get_the_category(), function($categories, $category) {
		$categories[] = $category->slug;
		return $categories;
	}, []);

	$is_wpcloud_site_archive = is_post_type_archive( 'wpcloud_site' );
	$is_wpcloud_private_page = array_search( WPCLOUD_PRIVATE_CATEGORY, $categories ) !== false;

	if ( $is_wpcloud_site_archive || $is_wpcloud_private_page ) {
		if ( ! is_user_logged_in() ) {
			global $wp;
			$url = add_query_arg( array('ref' => $wp->request ), '/login' );
			wp_redirect( $url );
   		exit();
		} else {
			error_log('User is logged in and can view the page.');
		}
	}
}

/**
 * Initialize the plugin.
 */
function wpcloud_init(): void {
	wpcloud_add_capabilities();
	wpcloud_register_site_post_type();
	wpcloud_setup_acl();
}
add_action( 'init', 'wpcloud_init' );

if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'admin/init.php';
}


/*
* Data center labels. These are used to display the data center in the UI.
* Call wpcloud_client_datacenters_available() to get actual data centers available
*/
function wpcloud_client_data_center_cities(): array  {
	return 	array(
		'ams'           => __( 'Amsterdam, NL' ),
		'bur'           => __( 'Los Angeles, CA' ),
		'dca'           => __( 'Washington, D.C., USA' ),
		'dfw'           => __( 'Dallas, TX, USA' ),
	);
}
