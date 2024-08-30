<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

// phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound

require_once __DIR__ . '/wpcloud-client.php';
require_once __DIR__ . '/../admin/includes/wpcloud-headstart.php';

require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/class-theme-upgrader.php';

/**
 * WP Cloud CLI
 */
class WPCloud_CLI {
	/**
	 * Log a message.
	 *
	 * @param string $message The message to log.
	 * @return void
	 */
	protected static function log( string $message ): void {
		WP_CLI::log( WP_CLI::colorize( $message . '%n' ) );
	}

	/**
	 * Log a response.
	 *
	 * @param array|string|bool $result The result to log.
	 * @param int|null          $padding The padding for the log.
	 * @return mixed
	 */
	protected static function log_response( array|string|bool $result, int|null $padding = null ): mixed {
		if ( empty( $result ) ) {
			return null;
		}

		if ( is_scalar( $result ) ) {
			if ( is_bool( $result ) ) {
				$result = $result ? '%gtrue' : '%rfalse';
			}
			self::log( $result );
			return null;
		}

		$padding = $padding ? $padding : ( max( array_map( 'strlen', array_keys( $result ) ) ) + 1 );

		foreach ( $result as $key => $value ) {

			if ( is_array( $value ) && ! array_is_list( $value ) ) {
				return self::log_response( $value, $padding );
			}

			if ( str_contains( $key, 'wpcom' ) || is_object( $value ) ) {
				continue;
			}

			if ( is_int( $key ) ) {
				$key = '-';
			} else {
				$key .= ':';
			}
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}
			self::log( sprintf( '%%_%s  %%G%s', str_pad( $key, $padding, ' ', STR_PAD_LEFT ), $value ) );
		}
		return null;
	}

	/**
	 * Log a result.
	 *
	 * @param mixed  $result  The result to log.
	 * @param string $message The message to log.
	 * @return void
	 */
	protected static function log_result( mixed $result, string $message = '' ): void {
		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
		}
		if ( $message ) {
			WP_CLI::success( $message );
		}
		if ( is_string( $result ) ) {
			self::log( $result );
			return;
		}
		self::log_response( json_decode( json_encode( $result ), true ) );
	}
}

/**
 * WP Cloud CLI Job
 */
class WPCLoud_CLI_Job extends WPCloud_CLI {

	/**
	 * List all jobs.
	 *
	 * @param array $args     The arguments.
	 */
	public function __invoke( $args ) {
		$job_id = $args[0] ?? 0;
		if ( ! $job_id ) {
			WP_CLI::error( 'Please provide a job id.' );
		}

		$result = wpcloud_client_job_status( $job_id );
		self::log_result( $result );
	}
}

/**
 * WP Cloud CLI Site
 */
class WPCloud_CLI_Site extends WPCloud_CLI {

	/**
	 * The site ID.
	 *
	 * @var int
	 */
	protected $site_id;

	/**
	 * List all sites.
	 *
	 * @param array $args     The arguments.
	 * @param array $switches The switches.
	 */
	public function list( $args, $switches = array() ) {

		$show_remote = $switches['remote'] ?? false;

		if ( $show_remote ) {
			$sites = wpcloud_client_site_list();
			if ( is_wp_error( $sites ) ) {
				WP_CLI::error( $sites->get_error_message() );
			}
			if ( isset( $switches['col'] ) ) {
				$column = $switches['col'];
				if ( 'id' === $column ) {
					$column = 'atomic_site_id';
				}
				$sites = array_map(
					function ( $site ) use ( $column ) {
						return $site->$column;
					},
					$sites
				);
				self::log_result( implode( ' ', $sites ) );
				return;
			}
			$site_list = array_map(
				function ( $site ) {
					return array(
						'id'         => $site->atomic_site_id,
						'domain'     => $site->domain_name,
						'created'    => $site->created,
						'space_used' => WPCLOUD_Site::readable_size( $site->space_used ),
					);
				},
				$sites
			);

				WP_CLI\Utils\format_items( 'table', $site_list, array( 'id', 'domain', 'created', 'space_used' ) );
		} else {
			$sites = get_posts(
				array(
					'post_type'      => 'wpcloud_site',
					'posts_per_page' => -1,
					'post_status'    => 'any',
				)
			);

			if ( isset( $switches['col'] ) ) {
				$column = $switches['col'];
				$sites  = array_map(
					function ( $site ) use ( $column ) {
						return $site->$column;
					},
					$sites
				);
				self::log_result( implode( ' ', $sites ) );
				return;
			}

			$site_list = array_map(
				function ( $site ) {
					return array(
						'wpcloud id' => get_post_meta( $site->ID, 'wpcloud_site_id', true ),
						'id'         => $site->ID,
						'domain'     => $site->post_title,
						'created'    => $site->post_date,
						'status'     => $site->post_status,
					);
				},
				$sites
			);

			WP_CLI\Utils\format_items( 'table', $site_list, array( 'wpcloud id', 'id', 'domain', 'created', 'status' ) );
		}
	}

	/**
	 * Get a site.
	 *
	 * @param array $args The arguments.
	 */
	public function get( array $args ): void {
		$this->set_site_id( $args );
		$result = wpcloud_client_site_details( $this->site_id, true );
		self::log_result( $result );
	}

	/**
	 * Update a site.
	 *
	 * @param int  $site_id The site ID.
	 * @param bool $remote  Whether to update the remote site.
	 * @param bool $confirmed Whether to confirm the update.
	 */
	private function _delete( int $site_id, $remote = false, $confirmed = false ) { // phpcs:ignore
		$result = null;
		if ( ! $confirmed ) {
			WP_CLI::confirm( sprintf( 'Are you sure you want to delete the site %d ?', $site_id ) );
		}
		if ( $remote ) {
			$result = wpcloud_client_site_delete( $site_id );
		} else {
			$post   = $this->get_site_cpt( array( $site_id ) );
			$result = wp_delete_post( $post->ID, true );
		}
		if ( is_wp_error( $result ) ) {
			WP_CLI::warning( $result->get_error_message() );
			return;
		}

		self::log( sprintf( '%%gSite %d deleted', $site_id ) );
	}

	/**
	 * Delete a site.
	 *
	 * @param array $args     The arguments.
	 * @param array $switches The switches.
	 */
	public function delete( $args, $switches = array() ) {
		$remote    = $switches['remote'] ?? false;
		$confirmed = $switches['confirmed'] ?? false;

		$delete_count = 0;
		foreach ( $args as $site_id ) {

			// make sure to confirm after every 5th site.
			$pause_to_confirm = $confirmed && 0 === $delete_count % 5 && $delete_count > 0;
			if ( $pause_to_confirm ) {
				WP_CLI::confirm( sprintf( 'Are you sure you want to continue deleting %d more sites?', count( $args ) - $delete_count ) );
			}
			$this->_delete( $site_id, $remote, $confirmed );
			++$delete_count;
		}

		WP_CLI::success( $delete_count . ' ' . _n( 'site deleted', 'sites deleted', $delete_count ) );
	}

	/**
	 * Create a site.
	 *
	 * @param array $switches The switches.
	 */
	public function create( $switches ) {
		$name  = $switches['name'] ?? '';
		$email = $switches['email'] ?? '';
		$pass  = $switches['pass'] ?? '';
		if ( ! $name || ! $email || ! $pass ) {
			WP_CLI::error( 'Please provide a name, email and password' );
		}

		$dc = $switches['dc'] ?? '';
		if ( $dc ) {
			$datacenters = wpcloud_client_data_centers_available();
			if ( is_wp_error( $datacenters ) ) {
				WP_CLI::error( $datacenters->get_error_message() );
			}
			if ( ! in_array( $dc, (array) $datacenters, true ) ) {
				WP_CLI::error( 'Invalid datacenter' );
			}
		}

		$php = $switches['php'] ?? '';
		if ( $php ) {
			$php_versions = wpcloud_client_php_versions_available();
			if ( ! in_array( $php, $php_versions, true ) ) {
				WP_CLI::error( 'Invalid PHP version' );
			}
		}

		$user = get_user_by( 'email', $email );
		if ( ! $user && $switches['create-user'] ) {
			$user = wp_create_user( $email, $pass, $email );
		}

		if ( ! $user ) {
			WP_CLI::error( 'User not found. Add --create-user flag to create the user' );
		}

		$options = array(
			'site_owner_id' => $user->ID,
			'site_name'     => $name,
			'data_center'   => $dc,
			'php_version'   => $php,
			'admin_pass'    => $pass,
		);

		$result = WPCLoud_Site::create( $options );
		self::log_result( $result, 'Site created' );
	}

	/**
	 * Get the site meta.
	 *
	 * @param array $args The arguments.
	 */
	public function domains( $args ) {
		$this->set_site_id( $args );
		$result = wpcloud_client_site_domain_alias_list( $this->site_id );
		self::log_result( $result );
	}

	/**
	 * Get the site meta.
	 *
	 * @param array $args The arguments.
	 */
	public function phpmyadmin( $args ) {
		$this->set_site_id( $args );
		$result = wpcloud_client_site_phpmyadmin_url( $this->site_id );
		self::log_result( $result );
	}

	/**
	 * Get the site meta.
	 *
	 * @param array $args The arguments.
	 * @param array $actions The actions.
	 */
	public function software( $args, $actions = array() ) {
		$this->set_site_id( $args );

		$software = array();
		foreach ( $actions as $action => $package_list ) {
			$packages = explode( ',', $package_list );
			foreach ( $packages as $package ) {
				$software[ $package ] = $action;
			}
		}
		$result = wpcloud_client_site_manage_software( $this->site_id, $software );
		self::log_result( $result );
	}

	/**
	 * Get site logs
	 *
	 * @param array $args The arguments.
	 * @param array $switches The switches.
	 */
	public function logs( $args, $switches = array() ) {
		$this->set_site_id( $args );
		$type = $switches['type'] ?? 'site';
		$file = $switches['file'] ?? false;

		if ( $file ) {
			$log_file = WPCLOUD_Site::prepare_log_file( $type, $this->site_id );

			WP_CLI::success( $log_file );
			return;
		}

		$result = 'site' === $type ? wpcloud_client_site_logs( $this->site_id, null, null ) : wpcloud_client_site_error_logs( $this->site_id, null, null );
		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
			return;
		}

		self::log( 'Total logs: ' . $result->total_results );
		self::log( 'Next page: ' . $result->scroll_id );
		$logs = wp_json_encode( $result->logs, JSON_PRETTY_PRINT );
		WP_CLI::print_value( $logs );
	}


	/**
	 * Set site id
	 *
	 * @param array $args The arguments.
	 */
	protected function set_site_id( $args ) {
		$this->site_id = $args[0] ?? 0;

		// Check the local sites first.
		$site_cpt = null;
		if ( ! is_numeric( $this->site_id ) ) {
			$site_cpt = get_page_by_title( $this->site_id, OBJECT, 'wpcloud_site' );
		} else {
			$site_cpt = get_post( $this->site_id );
		}

		if ( $site_cpt && ! is_wp_error( $site_cpt ) ) {
			$this->site_id = get_post_meta( $site_cpt->ID, 'wpcloud_site_id', true );

			if ( ! $this->site_id ) {
				WP_CLI::error( sprintf( 'Local site %s is missing a wp cloud site id', $site_cpt->post_title ) );
			}
			return;
		}
		if ( ! is_numeric( $this->site_id ) ) {
			$sites = wpcloud_client_site_list();
			$site  = array_filter(
				$sites,
				function ( $site ) {
					return $site->domain_name === $this->site_id;
				}
			);

			if ( empty( $site ) ) {
				WP_CLI::error( 'Site not found.' );
			}
			$site          = reset( $site );
			$this->site_id = $site->atomic_site_id;
		}

		if ( ! $this->site_id ) {
			WP_CLI::error( 'Please provide a site id.' );
		}
	}

	/**
	 * Get the site cpt
	 *
	 * @param array $args The arguments.
	 */
	protected function get_site_cpt( $args ) {
		self::set_site_id( $args );
		$query = array(
			'post_type'   => 'wpcloud_site',
			'post_status' => 'any',
			'meta_query'  => array(
				array(
					'key'   => 'wpcloud_site_id',
					'value' => $this->site_id,
				),
			),
		);
		$site  = get_posts( $query );
		if ( empty( $site ) ) {
			WP_CLI::error( 'Site not found.' );
		}
		return reset( $site );
	}
}

/**
 * WP Cloud CLI Site Domain
 */
class WPCloud_CLI_Site_Domain extends WPCloud_CLI_Site {

	/**
	 * The domain name.
	 *
	 * @var string
	 */
	protected $domain_name;


	/**
	 * Get a domain.
	 *
	 * @param array $args The arguments.
	 */
	public function get( array $args ): void {
		$this->set_site_id( $args );
		$details = wpcloud_client_site_details( $this->site_id );
		if ( is_wp_error( $details ) ) {
			WP_CLI::error( $details->get_error_message() );
		}

		self::log( $details->domain_name );
	}

	/**
	 * Add a domain.
	 *
	 * @param array $args The arguments.
	 */
	public function add( $args ) {
		$this->set_site_id( $args );
		$this->set_domain_name( $args );

		$result = wpcloud_client_site_domain_alias_add( $this->site_id, $this->domain_name );
		self::log_result( $result, 'Domain added' );
	}

	/**
	 * Remove a domain.
	 *
	 * @param array $args The arguments.
	 */
	public function remove( $args ) {
		$this->set_site_id( $args );
		$this->set_domain_name( $args );

		$result = wpcloud_client_site_domain_alias_remove( $this->site_id, $this->domain_name );
		self::log_result( $result, 'Domain deleted' );
	}

	/**
	 * Make a domain primary.
	 *
	 * @param array $args The arguments.
	 * @param array $switches The switches.
	 */
	public function make_primary( $args, $switches = array() ) {
		$this->set_site_id( $args );
		$this->set_domain_name( $args );
		$keep = $switches['keep'] ?? false;

		$result = wpcloud_client_site_domain_primary_set( $this->site_id, $this->domain_name, $keep );
		self::log_result( $result, 'Primary domain set' );
	}

	/**
	 * Get the site meta.
	 *
	 * @param array $args The arguments.
	 */
	public function ip( $args ) {
		self::log_result( wpcloud_client_site_ip_addresses( $args[0] ?? '' ) );
	}

	/**
	 * Validate a domain.
	 *
	 * @param array $args The arguments.
	 */
	public function validate( $args ) {
		$this->set_site_id( $args );
		$this->set_domain_name( $args );
		$result = wpcloud_client_domain_validate( $this->site_id, $this->domain_name );
		self::log_result( $result );
	}

	/**
	 * Retry a domain.
	 *
	 * @param array $args The arguments.
	 */
	public function retry_ssl( $args ) {
		$this->set_site_id( $args );
		$this->set_domain_name( $args );
		$result = wpcloud_client_site_ssl_retry( $this->site_id, $this->domain_name );
		self::log_result( $result, 'SSL retry initiated' );
	}

	/**
	 * Get the site meta.
	 *
	 * @param array $args The arguments.
	 */
	protected function set_domain_name( $args ) {
		$this->domain_name = $args[1] ?? '';

		if ( ! $this->domain_name ) {
			WP_CLI::error( 'Please provide a domain' );
		}
	}
}

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

		$result = wpcloud_client_ssh_user_add( $this->site_id, uniqid( $this->user ), $key, $pass );
		self::log_result( $result, 'SSH user added' );
	}

	/**
	 * Remove an SSH user.
	 *
	 * @param array $args The arguments.
	 */
	public function remove( $args ) {
		$this->set_site_id( $args );
		$this->set_user( $args );

		$result = wpcloud_client_ssh_user_remove( $this->site_id, $this->user );
		self::log_result( $result, 'SSH user removed' );
	}

	/**
	 * List all SSH users.
	 *
	 * @param array $args The arguments.
	 * @param array $switches The switches.
	 */
	public function list( $args, $switches = array() ) {
		$this->set_site_id( $args );
		self::log_result( wpcloud_client_ssh_user_list( $this->site_id ) );
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

		if ( 'api_key' === $key && isset( $options['wpcloud_api_key'] ) ) {
			WP_CLI::confirm( 'Are you sure you want to change the API key?' );
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

		if ( ! in_array( $key, $available_options ) ) {
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
	 * Get the client meta.
	 */
	public function ip() {
		self::log_result( wpcloud_client_site_ip_addresses() );
	}

	/**
	 * Get the client meta.
	 */
	public function available() {
		self::log( '%GPHP Versions:' );
		self::log_result( wpcloud_client_php_versions_available() );
		self::log( '%GData centers:' );
		self::log_result( wpcloud_client_data_centers_available() );
	}

	/**
	 * Run the headstart.
	 *
	 * @param array $args The arguments.
	 * @param array $switches The switches.
	 */
	public function headstart( $args, $switches ) {
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
		$result       = wpcloud_client_test_status( 200, $test_message );
		if ( is_wp_error( $result ) ) {
			return WP_CLI::error( $result->get_error_message() );
		}
		WP_CLI::success( 'OK' );
	}
}

/**
 * WP Cloud CLI Client Meta
 */
class WPCloud_CLI_Client_Meta extends WPCloud_CLI {

	/**
	 * Get the client meta.
	 *
	 * @param array $args The arguments.
	 */
	public function get( $args ) {
		$key = $args[0] ?? '';
		if ( ! $key ) {
			WP_CLI::error( 'Please provide a key' );
		}
		self::log_result( wpcloud_client_get_client_meta( $key ) );
	}

	/**
	 * Set the client meta.
	 *
	 * @param array $args The arguments.
	 */
	public function set( $args ) {
		$key = $args[0] ?? '';
		if ( ! $key ) {
			WP_CLI::error( 'Please provide a key' );
		}

		$value = $args[1] ?? '';
		if ( ! $value ) {
			WP_CLI::error( 'Please provide a value' );
		}
		$result = wpcloud_client_set_client_meta( $key, $value );
		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
		}
		self::log( '%gOK' );
	}

	/**
	 * Remove the client meta.
	 *
	 * @param array $args The arguments.
	 */
	public function remove( $args ) {
		$key = $args[0] ?? '';
		if ( ! $key ) {
			WP_CLI::error( 'Please provide a key' );
		}

		$result = wpcloud_client_remove_client_meta( $key );
		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
		}
		self::log( '%gOK' );
	}
}

/**
 * WP Cloud CLI Skin
 */
class WPCloud_CLI_Skin extends WP_Upgrader_Skin {

	/**
	 * Feedback.
	 *
	 * @param string $str The string to log.
	 * @param mixed  ...$args The arguments.
	 * @return string
	 */
	public function feedback( $str, ...$args ) {
		WP_CLI::log( $str );
		return '';
	}

	/**
	 * Header.
	 *
	 * @return string
	 */
	public function header() {
		// Silence is golden.
		return '';
	}

	/**
	 * Footer.
	 *
	 * @return string
	 */
	public function footer() {
		// Silence is golden.
		return '';
	}
}

add_action(
	'cli_init',
	function () {
		WP_CLI::add_command( 'cloud job', 'WPCloud_CLI_Job' );
		WP_CLI::add_command( 'cloud site', 'WPCloud_CLI_Site' );
		WP_CLI::add_command( 'cloud site domain', 'WPCloud_CLI_Site_Domain' );
		WP_CLI::add_command( 'cloud site ssh-user', 'WPCloud_CLI_Site_SSH_User' );
		WP_CLI::add_command( 'cloud client', 'WPCloud_CLI_Client' );
		WP_CLI::add_command( 'cloud client meta', 'WPCloud_CLI_Client_Meta' );
	}
);
