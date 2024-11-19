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
function wpcloud_headstart(  WP_Upgrader_Skin $headstart_skin = null, ): bool|WP_Error { // phpcs:ignore
	if ( ! $headstart_skin ) {
		$headstart_skin = new WPCloud_Quiet_Skin();
	}

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
	}

	if ( $installed ) {
		switch_theme( 'wpcloud-station' );
	} else {
		$headstart_skin->feedback( 'Failed to install theme.' );
		return new WP_Error( 'install_failed', 'Failed to install theme.' );
	}

	$headstart_skin->feedback( 'Theme enabled.' );
	wpcloud_set_default_logo();
	$headstart_skin->feedback( 'Default logo set.' );

	// Add the core category.
	$wpcloud_core_cat = get_category_by_slug( WPCLOUD_CATEGORY_CORE );
	if ( ! $wpcloud_core_cat ) {
		wpcloud_setup_categories();
	}

	$core_pages = array(
		'login'    => array(
			'post_title'    => 'Login',
			'post_content'  => '<!-- wp:pattern {"slug":"wpcloud-station/form-login"} /-->',
			'post_category' => array( $wpcloud_core_cat->term_id ),
		),
		'add-site' => array(
			'post_title'    => 'Add Site',
			'post_content'  => '<!-- wp:pattern {"slug":"wpcloud-station/form-add-site"} /-->',
			'post_category' => array( $wpcloud_core_cat->term_id, get_category_by_slug( WPCLOUD_CATEGORY_PRIVATE )->term_id ),
		),
	);

	$query = new WP_Query(
		array(
			'cat'           => $wpcloud_core_cat->term_id,
			'post_type'     => 'any',
			'post_name__in' => array_keys( $core_pages ),
		)
	);

	$post_names = array_map( fn( $post ) => $post->post_name, $query->get_posts() );

	foreach ( $core_pages as $page_name => $args ) {
		if ( ! in_array( $page_name, $post_names, true ) ) {
			$headstart_skin->feedback( 'Creating "' . $page_name . '" page...' );
			$args    = wp_parse_args(
				$args,
				array(
					'post_title'  => $page_name,
					'post_status' => 'publish',
					'post_type'   => 'page',
				)
			);
			$page_id = wp_insert_post( $args );
			if ( is_wp_error( $page_id ) ) {
				$headstart_skin->feedback( $page_id->get_error_message() );
			}
		}
	}

	// Configure permalinks.
	global $wp_rewrite;

	$permalink_structure = '/%postname%/';
	update_option( 'permalink_structure', $permalink_structure );
	$wp_rewrite->set_permalink_structure( $permalink_structure );
	flush_rewrite_rules();

	return true;
}


/**
 * Set the default logo.
 *
 * @param WP_Upgrader_Skin $skin The skin to use for the installation.
 * @return void
 */
function wpcloud_set_default_logo( ): void {
	/* Don't do anything if the custom logo is already set. */
	$current_logo_id = get_theme_mod( 'custom_logo', -1 );
	$current_logo    = wp_get_attachment_image( $current_logo_id );
	if ( ! empty( $current_logo ) ) {
		echo "Logo already set. Skipping...\n";
		return;
	}

	$logo_path_theme  = get_template_directory() . '/assets/img/wpcloud_logo.png';
	$upload_dir       = wp_upload_dir();
	$logo_path_upload = $upload_dir['basedir'] . '/wpcloud_logo.png';

	copy( $logo_path_theme, $logo_path_upload );
	$attachment = array(
		'post_mime_type' => 'image/png',
		'post_title'     => 'WP Cloud Logo',
		'post_content'   => ' ',
		'post_status'    => 'inherit',
	);

	$attach_id = wp_insert_attachment( $attachment, 'wpcloud_logo.png', 0, false, false );
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$attach_data = wp_generate_attachment_metadata( $attach_id, $logo_path_upload );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	set_theme_mod( 'custom_logo', $attach_id );
}
