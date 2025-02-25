<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

 // phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
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
	 * [--internal]
	 * : Setup the site for an internal Automattic Station.
	 *
	 * [--site-name=<site-name>]
	 * : The name of the site. If not provided, the site name will be the client name.
	 *
	 * [--site-logo=<site-logo>]
	 * : The URL of the site logo. If not provided, the site logo will default to the wp cloud logo.
	 *
	 * [--sync-users]
	 * : Sync the WPCOM users from the Atomic Persistent Data.
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
		$internal   = $switches['internal'] ?? false;
		$sync_users = $switches['sync-users'] ?? false;

		$station_type = $this->get_station_type( $internal );

		switch ( $station_type ) {
			case 'atomic-team':
				break;
			case 'a8c':
				if ( $sync_users ) {
					$this->add_wpcom_users();
				}
				$this->symlink_hosting( 'a8c-station.php' );
				// No break, include client setup.
			case 'client':
				$this->symlink_hosting( 'client-station.php' );
				WP_CLI::runcommand( 'config set DISALLOW_FILE_EDIT true --raw' );
				WP_CLI::runcommand( 'config set DISALLOW_FILE_MODS true --raw' );
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
				'post_category' => array( $wpcloud_core_cat->term_id, get_category_by_slug( WPCLOUD_CATEGORY_PRIVATE )->term_id ),
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
	 * List WPCOM users.
	 *
	 * ## EXAMPLES
	 * wp cloud station users
	 *
	 * @param array $args The arguments.
	 * @param array $switches The switches.
	 */
	public function users( $args, $switches = array() ) {
		$apd = new Atomic_Persistent_Data();
		if ( ! isset( $apd->WPCOM_USERS ) ) {
			$this->log( '%yNo WPCOM users found.' );
		}

		$wpcom_users = (array) json_decode( $apd->WPCOM_USERS );
		foreach ( $wpcom_users as $wpcom_user ) {
			$this->log( $wpcom_user );
		}
	}

	/**
	 * Sync WPCOM users.
	 *
	 * ## OPTIONS
	 *
	 * [--keep-as=<role>]
	 * : Keep missing users but assign to the provided role. If not provided, missing users will be deleted.
	 *
	 * [--assign=<user>]
	 * : Assign missing users to the provided user. Defaults to a8cwpcloud.
	 *
	 * ## EXAMPLES
	 *
	 * wp cloud station users_sync
	 *
	 * @param array $args The arguments.
	 * @param array $switches The switches.
	 */
	public function users_sync( $args, $switches = array() ) {
		$apd = new Atomic_Persistent_Data();
		if ( ! isset( $apd->WPCOM_USERS ) ) {
			$this->log( '%yNo WPCOM users found.' );
		}

		$wpcom_users = (array) json_decode( $apd->WPCOM_USERS );
		foreach ( $wpcom_users as $wpcom_user ) {
			$this->add_user( $wpcom_user );
		}

		$users          = get_users();
		$existing_users = array_map( fn( $user ) => $user->user_email, $users );
		$missing_users  = array_diff( $wpcom_users, $existing_users );

		if ( empty( $missing_users ) ) {
			$this->log( '%GNo extra users found on the site.' );
			return;
		}

		if ( $switches['keep-as'] ) {
			$new_role = $switches['keep-as'];
			$this->log( "Keeping missing users as $new_role:" );
			foreach ( $missing_users as $missing_user ) {
				$user = get_user_by( 'email', $missing_user );
				$user->set_role( $new_role );
				$this->log( "%G$missing_user" );
			}
			return;
		}

		$assign = $switches['assign'] ?? 'a8cwpcloud';
		$this->log( "The following users will be deleted (posts assigned to $assign):" );
		foreach ( $missing_users as $missing_user ) {
			$this->log( "%y$missing_user" );
		}
		WP_CLI::confirm( 'Are you sure you want to delete these users?' );
		$this->log( "Assigning missing users to $assign:" );
		foreach ( $missing_users as $missing_user ) {
			$user    = get_user_by( 'email', $missing_user );
			$user_id = $user->ID;
			$posts   = get_posts( array( 'author' => $user_id ) );
			foreach ( $posts as $post ) {
				wp_update_post(
					array(
						'ID'          => $post->ID,
						'post_author' => $assign,
					)
				);
			}
			wp_delete_user( $user_id );

			$this->log( "%G$missing_user" );
		}
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
			'post_mime_type' => 'image/svg+xml',
			'post_title'     => 'WP Cloud Logo',
			'post_content'   => ' ',
			'post_status'    => 'inherit',
		);

		$attachment_id = wp_insert_attachment( $attachment, $logo_path_upload, 0 );
		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}
		return $attachment_id;
	}

	/**
	 * Get the station type.
	 *
	 * @param bool $internal_flag The internal flag.
	 * @return string
	 */
	private function get_station_type( bool $internal_flag ): string {

		if ( $internal_flag ) {
			return 'a8c';
		}

		$apd           = new Atomic_Persistent_Data();
		$internal_type = $apd->WP_CLOUD_STATION_INTERNAL;
		if ( $internal_type ) {
			return $internal_type;
		}

		return 'client';
	}

	/**
	 * Add the WPCOM users.
	 */
	private function add_wpcom_users(): void {
		$this->log( 'Adding WPCOM users...' );
		$apd = new Atomic_Persistent_Data();
		if ( ! isset( $apd->WPCOM_USERS ) ) {
			$this->log( '%yNo WPCOM users found.' );
		}

		$wpcom_users = (array) json_decode( $apd->WPCOM_USERS );
		foreach ( $wpcom_users as $wpcom_user ) {
			$user = get_user_by( 'email', $wpcom_user );
			if ( ! $user ) {
				// Create a new user.
				$user_id = wp_create_user( $wpcom_user, wp_generate_password(), $wpcom_user );
				if ( is_wp_error( $user_id ) ) {
					$this->log( '%y' . $user_id->get_error_message() );
				} else {
					$user = get_user_by( 'id', $user_id );
					$user->set_role( 'administrator' );

					$this->log( 'Added user: ' . $wpcom_user );
				}
			}
		}
	}
}
