<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli.php';

/**
 * WP Cloud CLI Site SSH User
 */
class WPCloud_CLI_Site_SSH_User extends WPCloud_CLI_Site {

	/**
	 * The user name.
	 *
	 * @var string
	 */
	protected $user;

	/**
	 * Add a new SSH user.
	 *
	 * @param array $args The arguments.
	 * @param array $options The options.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The site id.
	 *
	 * <user>
	 * : The user name.
	 *
	 * [--pass=<pass>]
	 * : The password for the user.
	 *
	 * [--pub_key=<pub_key>]
	 * : The public key for the user.
	 */
	public function add( $args, $options ) {
		$this->set_site_id( $args );
		$this->set_user( $args );

		$key = $options['pub_key'] ?? '';
		if ( isset( $options['pass'] ) ) {
			$pass = $options['pass'];
		} else {
			$pass = null;
		}

		$this->api()
			->ssh_user_add( $this->site_id, $this->user, $key, $pass )
			->log( success: 'SSH user added' );
	}

	/**
	 * Remove an SSH user.
	 *
	 * @param array $args The arguments.
	 */
	public function remove( $args ) {
		$this->set_site_id( $args );
		$this->set_user( $args );

		$this->api()
			->ssh_user_remove( $this->site_id, $this->user )
			->log( success: 'SSH user removed' );
	}

	/**
	 * List all SSH users.
	 *
	 * @param array $args The arguments.
	 * @param array $switches The switches.
	 */
	public function list( $args, $switches = array() ) {
		$this->set_site_id( $args );
		$this->api()->ssh_user_list( $this->site_id )->log();
	}

	/**
	 * Get the site meta.
	 *
	 * @param array $args The arguments.
	 */
	protected function set_user( $args ) {
		$this->user = $args[1] ?? '';

		if ( ! $this->user ) {
			WP_CLI::error( 'Please provide a user' );
		}
	}
}
