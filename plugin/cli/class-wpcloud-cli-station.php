<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli.php';

/**
 * WP Cloud CLI Station
 */
class WPCloud_CLI_Station extends WPCloud_CLI {

	/**
	 * Setup the Station site.
	 *
	 * ## OPTIONS
	 *
	 * [--internal=<internal>]
	 * : Setup the site for an internal Automattic Station.
	 *
	 * [--site-name=<site-name>]
	 * : The name of the site. If not provided, the site name will be the client name.
	 *
	 * [--site-logo=<site-logo>]
	 * : The URL of the site logo. If not provided, the site logo will default to the wp cloud logo.
	 *
	 * ## EXAMPLES
	 * wp cloud station setup --internal
	 *
	 * @param array $args       The arguments.
	 * @param array $switches The switches.
	 */
	public function setup( $args, $switches = array() ) {

		// Setup the mu hosting plugins.
		$this->symlink_hosting( 'wpcloud-station.php' );

		switch ( $switches['internal'] ?? 'client' ) {
			case 'atomic-team':
				break;
			case 'a8c':
				$this->symlink_hosting( 'a8c-station.php' );
				// No break, include client setup.
			case 'client':
				$this->symlink_hosting( 'client-station.php' );
				break;
			default:
				WP_CLI::error( 'Please provide a valid internal switch.' );
		}

		// Setup the site name.
		$site_name = $switches['site-name'] ?? wpcloud_get_client_name();
		$this->log( 'Setting up the site name: ' . $site_name );
		update_option( 'blogname', $site_name );

		// Setup the site logo.
		$attachment_id = $this->add_logo_attachment( $switches['site-logo'] ?? '' );
		if ( is_wp_error( $attachment_id ) ) {
			$this->log( '%y' . $attachment_id->get_error_message() );
		}
		set_theme_mod( 'custom_logo', $attachment_id );

		// Add the core category.
		$wpcloud_core_cat = get_category_by_slug( WPCLOUD_CATEGORY_CORE );
		if ( ! $wpcloud_core_cat ) {
			wpcloud_setup_categories();
		}

		// Create add site page.
		$core_pages = array(
			'add-site' => array(
				'post_title'    => 'Add Site',
				'post_content'  => '<!-- wp:pattern {"slug":"wpcloud-station/form-add-site"} /-->',
				'post_category' => array( wpcloud_core_cat->term_id, get_category_by_slug( WPCLOUD_CATEGORY_PRIVATE )->term_id ),
			),
		);
		$query      = new WP_Query(
			array(
				'cat'           => $wpcloud_core_cat->term_id,
				'post_type'     => 'any',
				'post_name__in' => array_keys( $core_pages ),
			)
		);
		$post_names = array_map( fn( $post ) => $post->post_name, $query->get_posts() );

		foreach ( $core_pages as $page_name => $args ) {
			if ( ! in_array( $page_name, $post_names, true ) ) {
				$this->log( 'Creating "' . $page_name . '" page...' );
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
					$this->log( '%y' . $page_id->get_error_message() );
				}
			}
		}

		// Configure permalinks.
		global $wp_rewrite;

		$permalink_structure = '/%postname%/';
		update_option( 'permalink_structure', $permalink_structure );
		$wp_rewrite->set_permalink_structure( $permalink_structure );
		flush_rewrite_rules();

		$this->log( '%GStation setup complete.' );
	}

	/**
	 * Symlink the hosting plugin.
	 *
	 * @param string $filename The filename to symlink.
	 */
	private function symlink_hosting( $filename ) {
		$mu_hosting_plugins = plugin_dir_path( __DIR__ ) . 'hosting';
		$mu_hosting_plugin  = trailingslashit( $mu_hosting_plugins ) . $filename;
		if ( ! file_exists( $mu_hosting_plugin ) ) {
			WP_CLI::error( 'The hosting plugin file does not exist: ' . $mu_hosting_plugin );
		}
		$mu_hosting_link = WPMU_PLUGIN_DIR . '/' . $filename;
		if ( ! file_exists( $mu_hosting_link ) ) {
			$this->log( 'Symlinking ' . $filename );
			symlink( $mu_hosting_plugin, $mu_hosting_link );
		} else {
			$this->log( 'Symlink already exists for ' . $filename );
		}
	}

	/**
	 * Set the site logo.
	 *
	 * @param string $image_url The image URL.
	 * @return WP_Error|int
	 */
	private function add_logo_attachment( string $image_url ): WP_Error|int {
		if ( $image_url ) {
			// Try downloading the logo.
			return media_sideload_image( $image_url, 0, null, 'id' );
		}
		$logo_path        = plugin_dir_path( __DIR__ ) . 'assets/images/wpcloud.svg';
		$upload_dir       = wp_upload_dir();
		$logo_path_upload = trailingslashit( $upload_dir['basedir'] ) . 'wpcloud.svg';
		copy( $logo_path, $logo_path_upload );

		$attachment = array(
			'post_mime_type' => 'image/png',
			'post_title'     => 'WP Cloud Logo',
			'post_content'   => ' ',
			'post_status'    => 'inherit',
		);

		$attachment_id = wp_insert_attachment( $attachment, 'wpcloud_logo.svg', 0, false, false );
		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attach_data = wp_generate_attachment_metadata( $attachment_id, $logo_path_upload );
		if ( ! wp_update_attachment_metadata( $attachment_id, $attach_data ) ) {
			return new WP_Error( 'wpcloud-cli', 'Failed to update attachment metadata' );
		}

		return $attachment_id;
	}
}
