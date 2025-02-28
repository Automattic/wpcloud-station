<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

 // phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli-station.php';

/**
 * WP Cloud CLI Station
 */
class WPCloud_CLI_Station_Setup extends WPCloud_CLI_Station {

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
	 * [--set-debug]
	 * : Set the site to debug mode.
	 *
	 *
	 * ## EXAMPLES
	 * wp cloud station setup --internal
	 *
	 * @param array $args       The arguments.
	 * @param array $switches The switches.
	 */
	public function __invoke( $args, $switches = array() ) {

		// Setup the mu hosting plugins.
		$this->symlink_hosting( 'wpcloud-station.php' );
		$internal = $switches['internal'] ?? false;

		$station_type = $this->get_station_type( $internal );

		switch ( $station_type ) {
			case 'atomic-team':
				$this->unlink_hosting( 'a8c-station.php' );
				$this->unlink_hosting( 'client-station.php' );
				WP_CLI::runcommand( 'config set DISALLOW_FILE_EDIT false --raw' );
				WP_CLI::runcommand( 'config set DISALLOW_FILE_MODS false --raw' );
				break;
			case 'a8c':
				$wpcom_users = $this->station->wpcom_users;
				if ( empty( $wpcom_users ) ) {
					$this->log( '%YNo WPCOM users found.' );
				} else {
					$this->add_users( $wpcom_users );
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

		if ( $switches['set-debug'] ?? false ) {
			WP_CLI::runcommand( 'config set WP_DEBUG true --raw' );
			WP_CLI::runcommand( 'config set WP_DEBUG_LOG true --raw' );
			WP_CLI::runcommand( 'config set WP_DEBUG_DISPLAY false --raw' );
		}

		$errors = $this->station->setup( $switches );
		if ( ! empty( $errors ) ) {
			foreach ( $errors as $error ) {
				$this->log( '%r' . $error->get_error_message() );
			}
		}

		$this->log( '%GStation setup complete.' );
	}

	/**
	 * Symlink the hosting plugin.
	 *
	 * @param string $filename The filename to symlink.
	 * @param bool   $relink   Whether to relink the file.
	 */
	private function symlink_hosting( $filename, $relink = true ) {
		$mu_hosting_plugins = plugin_dir_path( __DIR__ ) . 'hosting';
		$mu_hosting_plugin  = trailingslashit( $mu_hosting_plugins ) . $filename;
		if ( ! file_exists( $mu_hosting_plugin ) ) {
			WP_CLI::error( 'The hosting plugin file does not exist: ' . $mu_hosting_plugin );
		}
		$mu_hosting_link = WPMU_PLUGIN_DIR . '/' . $filename;
		if ( file_exists( $mu_hosting_link ) ) {
			if ( ! $relink ) {
				$this->log( 'Symlink already exists for ' . $filename );
				return;
			}
			// Remove the existing symlink.
			wp_delete_file( $mu_hosting_link );
		}

		$this->log( 'Symlinking ' . $filename );
		symlink( $mu_hosting_plugin, $mu_hosting_link );
	}

	/**
	 * Unlink the hosting plugin.
	 *
	 * @param string $filename The filename to unlink.
	 */
	private function unlink_hosting( $filename ) {
		$mu_hosting_link = WPMU_PLUGIN_DIR . '/' . $filename;
		if ( file_exists( $mu_hosting_link ) ) {
			$this->log( 'Unlinking ' . $filename );
			wp_delete_file( $mu_hosting_link );
		}
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
		$internal_type = $this->station->WP_CLOUD_STATION_INTERNAL;
		if ( $internal_type ) {
			return $internal_type;
		}

		return 'client';
	}
}
