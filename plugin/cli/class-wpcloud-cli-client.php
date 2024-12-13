<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli.php';
require_once __DIR__ . '/class-wpcloud-cli-skin.php';
require_once __DIR__ . '/../admin/includes/wpcloud-headstart.php';

/**
 * WP Cloud CLI Client
 */
class WPCloud_CLI_Client extends WPCloud_CLI {

	/**
	 * Get the client settings.
	 *
	 * @param array $args The arguments.
	 */
	public function get( $args ) {
		$options      = get_option( 'wpcloud_settings' );
		$root_options = array();
		foreach ( $options as $key => $value ) {

			$root_options[ preg_replace( '/^wpcloud_/', '', $key ) ] = $value;
		}

		if ( isset( $args[0] ) ) {
			$key = $args[0];
			if ( isset( $root_options[ $key ] ) ) {
				self::log( $root_options[ $key ] );
				return;
			}
		} else {
			self::log_result( $root_options );
		}
	}

	/**
	 * Set the client settings.
	 *
	 * @param array $args The arguments.
	 */
	public function set( $args ) {
		$key   = $args[0] ?? '';
		$value = $args[1] ?? '';

		if ( ! $key ) {
			WP_CLI::error( 'Please provide a key' );
		}

		$options = get_option( 'wpcloud_settings' );

		// Only allow changing the API key if it's not set in the environment.
		if ( ! wpcloud_get_api_key_from_env() ) {
			if ( 'api_key' === $key && isset( $options['wpcloud_api_key'] ) ) {
				WP_CLI::confirm( 'Are you sure you want to change the API key?' );
			}
		}

		if ( 'client' === $key && isset( $options['wpcloud_client'] ) ) {
			WP_CLI::confirm( 'Are you sure you want to change the client?' );
		}

		$available_options = array(
			'api_key',
			'client',
			'domain',
			'default_theme',
		);

		if ( ! in_array( $key, $available_options, true ) ) {
			WP_CLI::error( 'Invalid option' );
		}

		$key = preg_replace( '/^(wpcloud_)?/', 'wpcloud_', $key );

		if ( ! $value ) {
			unset( $options[ $key ] );
		} else {
			$options[ $key ] = $value;
		}
		if ( ! update_option( 'wpcloud_settings', $options ) ) {
			WP_CLI::error( 'Failed to update option' );
		}

		WP_CLI::success( 'Option updated' );
	}

	/**
	 * Get the site ip addresses.
	 */
	public function ip() {
		$this->api()->site_ip_addresses()->log();
	}

	/**
	 * Get the available PHP versions and data centers.
	 */
	public function available() {
		self::log( '%GPHP Versions:' );
		$this->api()->php_versions_available()->log();

		self::log( '%GData centers:' );
		$this->api()->data_centers_available()->log();
	}

	/**
	 * Run the headstart.
	 *
	 * @param array $args The arguments.
	 * @param array $switches The switches.
	 */
	public function headstart( $args, $switches ) {
		if ( ! empty( $switches ) ) {
			$options  = get_option( 'wpcloud_settings', array() );
			$settings = array();

			if ( isset( $switches['client'] ) ) {
				if ( isset( $options['wpcloud_client'] ) ) {
					WP_CLI::confirm( 'Are you sure you want to change the client?' );
				}
				$settings['wpcloud_client'] = $switches['client'];
			}

			if ( isset( $switches['api_key'] ) ) {
				if ( wpcloud_get_api_key_from_env() ) {
					WP_CLI::error( 'Cannot set the API key when it is set in the environment' );
					exit( 1 );
				}
				if ( isset( $options['wpcloud_api_key'] ) ) {
					WP_CLI::confirm( 'Are you sure you want to change the API key?' );
				}
				$settings['wpcloud_api_key'] = $switches['api_key'];
			}
			if ( ! empty( $settings ) ) {
				update_option( 'wpcloud_settings', array_merge( $options, $settings ) );
			}
		}

		$result = wpcloud_headstart( new WPCloud_CLI_Skin() );

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
		}

		WP_CLI::success( 'Headstart installed' );
	}

	/**
	 * Test the status.
	 *
	 * @param array $args The arguments.
	 * @param array $switches The switches.
	 */
	public function status( $args, $switches ) {
		$test_message = $args[0] ?? 'OK';
		$this->api()->test_status( $test_message )->log( success: "got $test_message" );
	}
}
