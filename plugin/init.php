<?php
/**
 * Plugin instantiation.
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

/**
 * Requires
 */
require_once plugin_dir_path( __FILE__ ) . 'controllers/class-wpcloud-domains-controller.php';
require_once plugin_dir_path( __FILE__ ) . 'controllers/class-wpcloud-sites-controller.php';
require_once plugin_dir_path( __FILE__ ) . 'controllers/class-wpcloud-webhook-controller.php';
require_once plugin_dir_path( __FILE__ ) . 'controllers/class-wpcloud-metrics-controller.php';

require_once plugin_dir_path( __FILE__ ) . 'custom-post-types/wpcloud-site.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcloud-site.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/wpcloud-client.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcloud-metrics.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcloud-metric-data-view.php';
require_once plugin_dir_path( __FILE__ ) . 'blocks/init.php';
if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'admin/init.php';
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once plugin_dir_path( __FILE__ ) . '/cli/init.php';
}

if ( ! is_admin() ) {

	add_action(
		'wp_enqueue_scripts',
		function (): void {
			$script_info = require_once plugin_dir_path( __FILE__ ) . 'assets/js/build/index.asset.php';
			wp_enqueue_script(
				'wpcloud',
				plugin_dir_url( __FILE__ ) . 'assets/js/build/index.js',
				$script_info['dependencies'],
				$script_info['version'] ?? '1.0.0',
				true
			);

			wp_localize_script(
				'wpcloud',
				'wpcloudStationApi',
				array(
					'nonce' => wp_create_nonce( 'wpcloud_station_api' ),
				)
			);


			global $post;
			$site_id = wpcloud_get_site_id( $post );
			error_log('site id: ' . $site_id);
			if ( $site_id ) {
				wp_localize_script(
					'wpcloud',
					'wpcloudSite',
					array( 'id' => $site_id )
				);
			}

		}
	);
}

/**
 * Get the api key from the environment.
 */
function wpcloud_get_api_key_from_env(): string {
	$api_key = getenv( 'WP_CLOUD_API_KEY' );
	if ( defined( 'WP_CLOUD_API_KEY' ) ) {
		$api_key = WP_CLOUD_API_KEY;
	}

	$api_key = apply_filters( 'wpcloud_api_key', $api_key );
	return $api_key ? $api_key : '';
}

/**
 * Check if using environment settings.
 *
 * @param string $config_name The config name.
 * @return bool
 */
function wpcloud_using_env_settings( $config_name ): bool {
	$config = apply_filters( "wpcloud_{$config_name}", '' );
	return ! empty( $config );
}

/**
 * Set up the plugin's capabilities.
 */
function wpcloud_add_capabilities(): void {
	$role = get_role( 'administrator' );
	$role->add_cap( WPCLOUD_CAN_MANAGE_SITES );
}

/**
 * Register REST API controllers.
 */
function wpcloud_register_controllers(): void {
	$webhook_controller = new WPCLOUD_Webhook_Controller();
	$webhook_controller->register_routes();

	$domains_controller = new WPCLOUD_Domains_Controller();
	$domains_controller->register_routes();

	$metrics_controller = new WPCLOUD_Metrics_Controller();
	$metrics_controller->register_routes();
}
add_action( 'rest_api_init', 'wpcloud_register_controllers' );

/**
 * Set up categories for WP Cloud specific pages
 */
function wpcloud_setup_categories(): void {
	// Verify that the core pages categories exists.
	wp_insert_term(
		'WP Cloud Private Page',
		'category',
		array(
			'description' => 'Private category for WP Cloud specific pages.',
			'slug'        => WPCLOUD_CATEGORY_PRIVATE,
		)
	);

	wp_insert_term(
		'WP Cloud Core Page',
		'category',
		array(
			'description' => 'Core category for WP Cloud specific pages.',
			'slug'        => WPCLOUD_CATEGORY_CORE,
		)
	);

	// Allow adding categories to pages.
	register_taxonomy_for_object_type( 'category', 'page' );
}

/**
 * Verify logged in for WP Cloud specific pages
 */
function wpcloud_verify_logged_in(): void {
	if ( is_admin() ) {
		return;
	}

	$categories = array_reduce(
		get_the_category(),
		function ( $categories, $category ) {
			$categories[] = $category->slug;
			return $categories;
		},
		array()
	);

	$is_wpcloud_site_archive = is_post_type_archive( 'wpcloud_site' );
	$is_wpcloud_private_page = array_search( WPCLOUD_CATEGORY_PRIVATE, $categories, true ) !== false;

	if ( $is_wpcloud_site_archive || $is_wpcloud_private_page ) {
		if ( ! is_user_logged_in() ) {
			global $wp;
			$url = add_query_arg( array( 'ref' => $wp->request ), '/login' );
			wp_safe_redirect( $url );
			exit();
		}
	}
}

/**
 * Initialize the plugin.
 */
function wpcloud_init(): void {
	wpcloud_add_capabilities();
	wpcloud_register_site_post_type();
	wpcloud_setup_categories();

	// Set up ACL for Station pages.
	add_filter( 'template_redirect', 'wpcloud_verify_logged_in' );
}
add_action( 'init', 'wpcloud_init' );


/**
 * Add query vars for site views.
 *
 * @param array $vars The query vars.
 *
 * @return array
 */
function wpcloud_station_query_vars( $vars ) {
	array_push( $vars, 'site_name', 'view' );
	return $vars;
}
add_filter( 'query_vars', 'wpcloud_station_query_vars' );

/**
 * Update the template to include the site view if it exists.
 *
 * @param string $template The template.
 *
 * @return string The template.
 */
function wpcloud_station_template_include( $template ) {
	$view      = get_query_var( 'view', '' );
	$site_name = get_query_var( 'site_name', '' );

	if ( ! $site_name || ! $view ) {
		return $template;
	}

	$view = plugin_dir_path( __FILE__ ) . 'views/sites.php';

	if ( file_exists( $view ) ) {
		return $view;
	}
	return $template;
}
add_filter( 'template_include', 'wpcloud_station_template_include', 50 );

/**
 * Add site view rewrite rule.
 *
 * @param string $view The view.
 */
function wpcloud_station_register_site_view( $view ) {
	$query = sprintf( 'index.php?site_name=$matches[1]&view=%s', $view );
	$regex = sprintf( 'sites/([^/]+)/%s/?$', $view );
	add_rewrite_rule( $regex, $query, 'top' );
}
