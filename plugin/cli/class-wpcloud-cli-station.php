<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

 // phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli.php';
require_once plugin_dir_path( __DIR__ ) . '/includes/class-wpcloud-station.php';

/**
 * WP Cloud CLI Station
 */
abstract class WPCloud_CLI_Station extends WPCloud_CLI {

	/**
	 * The station.
	 *
	 * @var WP_Cloud_Station
	 */
	protected WPCloud_Station $station;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->station = new WPCloud_Station();
	}

	/**
	 * Add the WPCOM users.
	 *
	 * @param array  $emails The emails.
	 * @param string $role The role.
	 */
	protected function add_users( array $emails, string $role = 'administrator' ): void {
		if ( empty( $emails ) ) {
			return;
		}
		foreach ( $emails as $email ) {
			$user = get_user_by( 'email', $email );
			if ( ! $user ) {
				// Create a new user.
				$user_id = wp_create_user( $email, wp_generate_password(), $email );
				if ( is_wp_error( $user_id ) ) {
					$this->log( '%y' . $user_id->get_error_message() );
				} else {
					$user = get_user_by( 'id', $user_id );
					$user->set_role( $role );

					$this->log( "Added $role $email." );
				}
			}
		}
	}
}
