<?php // phpcs:disable Generic.Files.nameConventions.UpperCaseConstantName.NotFound
/**
 * WP Cloud Station Headstart.
 *
 * @package wpcloud-station
 * @subpackage headstart
 */

// phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound
declare( strict_types = 1 );

require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/class-theme-upgrader.php';

if ( ! defined( 'WP_CLOUD_STATION_REPO' ) ) {
	define( 'WP_CLOUD_STATION_REPO', 'https://github.com/automattic/wpcloud-station' );
}

/**
 * WP Cloud Skin for the upgrader.
 */
class WPCloud_Quiet_Skin extends WP_Upgrader_Skin {
	/**
	 * Stub
	 *
	 * @param string $message The string to output.
	 * @param mixed  ...$args The arguments to pass to the string.
	 */
	public function feedback( $message, ...$args ): string {
		// Silence is golden.
		return '';
	}

	/**
	 * Stub
	 */
	public function header(): string {
		// Silence is golden.
		return '';
	}

	/**
	 * Stub
	 */
	public function footer(): string {
		// Silence is golden.
		return '';
	}
}

/**
 * WP Cloud Debug Skin for the headstart.
 */
class WPCloud_Debug_Skin extends WPCloud_Quiet_Skin {

	/**
	 * Output the feedback.
	 *
	 * @param string $message The string to output.
	 * @param mixed  ...$args The arguments to pass to the string.
	 */
	public function feedback( $message, ...$args ): string {
		error_log( $message ); // phpcs:ignore
		return '';
	}
}

/**
 * Install the WP Cloud Station theme.
 *
 * @param WP_Upgrader_Skin $headstart_skin The skin to use for the installation.
 * @return true|WP_Error True if headstart succeeded, WP_Error on failure.
 */
function wpcloud_headstart(  WP_Upgrader_Skin $headstart_skin = new WPCloud_Quite_Skin() ): true|WP_Error { // phpcs:ignore

	// Install the demo theme.
	$available_themes = wp_get_themes();

	$installed = false;
	foreach ( $available_themes as $theme ) {

		if ( str_contains( $theme->get( 'Name' ), 'WP Cloud Station' ) ) {
			$installed = true;
			break;
		}
	}

	if ( ! $installed ) {
		$headstart_skin->feedback( 'Installing WP Cloud Station theme...' );
		$package   = WP_CLOUD_STATION_REPO . '/releases/latest/download/wpcloud-station-theme.zip ';
		$up_grader = new Theme_Upgrader( $headstart_skin );
		$installed = $up_grader->install( $package );

		if ( is_wp_error( $installed ) ) {
			$headstart_skin->feedback( $installed->get_error_message() );
		}

		if ( $installed ) {
			switch_theme( 'wpcloud-station-theme' );
		} else {
			$headstart_skin->feedback( 'Failed to install theme.' );
			return new WP_Error( 'install_failed', 'Failed to install theme.' );
		}
	}

	// Configure permalinks (will likely not work if running from wp cli).
	global $wp_rewrite;

	$permalink_structure = '/%postname%/';
	$wp_rewrite->set_permalink_structure( $permalink_structure );
	$wp_rewrite->flush_rules( true );

	// Add the private category.
	$wpcloud_private_cat = get_category_by_slug( WPCLOUD_PRIVATE_CATEGORY );
	if ( ! $wpcloud_private_cat ) {
		$wpcloud_private_cat = wp_insert_term(
			'WP Cloud Private Page',
			'category',
			array(
				'description' => 'Private category for WP Cloud specific pages.',
				'slug'        => WPCLOUD_PRIVATE_CATEGORY,
			)
		);
	}

	$core_pages = array(
		'login'    => array(
			'title'   => 'Login',
			'content' => '<!-- wp:pattern {"slug":"wpcloud-station/form-login"} /-->',
		),
		'add-site' => array(
			'title'   => 'Add Site',
			'content' => '<!-- wp:pattern {"slug":"wpcloud-station/form-add-site"} /-->',
		),
	);

	$query = new WP_Query(
		array(
			'cat'           => $wpcloud_private_cat->term_id,
			'post_type'     => 'any',
			'post_name__in' => array_keys( $core_pages ),
		)
	);

	$post_names = array_map( fn( $post ) => $post->post_name, $query->get_posts() );

	foreach ( $core_pages as $page_name => $args ) {
		if ( ! in_array( $page_name, $post_names, true ) ) {
			$headstart_skin->feedback( 'Creating "' . $args['title'] . '" page...' );
			$page_id = wpcloud_headstart_insert_page( array_merge( $args, array( 'name' => $page_name ) ) );
			if ( is_wp_error( $page_id ) ) {
				$headstart_skin->feedback( $page_id->get_error_message() );
			}
		}
	}

	return true;
}

/**
 * Create insert a page
 *
 * @param array $args The arguments for the core pages.
 * @return int|WP_Error The page ID or WP_Error on failure.
 */
function wpcloud_headstart_insert_page( array $args ): int|WP_Error {
	$post_args = array(
		'post_title'   => $args['title'] ?? '',
		'post_content' => $args['content'] ?? '',
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_name'    => $args['name'] ?? '',
	);

	$post_category = $args['category'] ?? array();

	$wpcloud_private_cat = get_category_by_slug( WPCLOUD_PRIVATE_CATEGORY );
	if ( $wpcloud_private_cat ) {
		$post_category[] = $wpcloud_private_cat->term_id;
	}
	$post_args['post_category'] = $post_category;
	return wp_insert_post( $post_args );
}

add_action(
	'update_option',
	function ( $option ) {
		if ( 'wpcloud_settings' === $option ) {
			wpcloud_headstart( new WPCloud_Debug_Skin() );
		}
	},
);
