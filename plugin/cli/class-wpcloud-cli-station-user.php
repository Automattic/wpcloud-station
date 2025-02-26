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
class WPCloud_CLI_Station_User extends WPCloud_CLI_Station {
		/**
		 * List WPCOM users.
		 *
		 * ## EXAMPLES
		 * wp cloud station user list
		 *
		 * @param array $args The arguments.
		 * @param array $switches The switches.
		 */
	public function list( $args, $switches = array() ) {
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
	 * @subcommand sync
	 *
	 * wp cloud station user sync
	 *
	 * @param array $args The arguments.
	 * @param array $switches The switches.
	 */
	public function sync( $args, $switches = array() ) {
		$apd = new Atomic_Persistent_Data();
		if ( ! isset( $apd->WPCOM_USERS ) ) {
			$this->log( '%yNo WPCOM users found.' );
		}

		$wpcom_users = $this->get_wpcom_users();

		$this->add_users( $wpcom_users );

		$users          = get_users();
		$existing_users = array_map( fn( $user ) => $user->user_email, $users );
		$missing_users  = array_diff( $existing_users, $wpcom_users );

		if ( empty( $missing_users ) ) {
			$this->log( '%GNo excluded users found on the site.' );
			return;
		}

		if ( $switches['keep-as'] ?? '' ) {
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
}
