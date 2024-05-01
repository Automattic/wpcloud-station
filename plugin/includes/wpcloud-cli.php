<?php

require_once __DIR__ . '/wpcloud-client.php';

class WPCloud_CLI {
	protected static function log( string $message ):void {
		WP_CLI::log(WP_CLI::colorize( $message . '%n' ));
	}

	protected static function log_response( array|string|bool $result, int|null $padding = null ): null  {
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
		$padding = $padding ?: max( array_map( 'strlen', array_keys( $result ) ) ) + 1;

		foreach ( $result as $key => $value ) {

			if ( is_array( $value ) && ! array_is_list( $value ) ) {
				return self::log_response( $value, $padding );
			}

			if ( str_contains( $key, 'wpcom' ) || is_object( $value ) ) {
				continue;
			}

			if ( is_int($key) ) {
				$key = '-';
			}
			else {
				$key .= ':';
			}
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}
			self::log( sprintf( "%%_%s  %%G%s", str_pad($key, $padding, " ", STR_PAD_LEFT), $value ) );
		}
		return null;
	}

	protected static function log_result( mixed $result , string $message = '' ):void {
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
		self::log_response(json_decode( json_encode( $result ), true ));
	}

	protected static function human_filesize($bytes, $dec = 2): string {
		$size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$factor = floor((strlen($bytes) - 1) / 3);
		if ($factor == 0) $dec = 0;

		return sprintf("%.{$dec}f %s", $bytes / (1024 ** $factor), $size[$factor]);
	}
}

class WPCLoud_CLI_Job extends WPCloud_CLI {
	public function __invoke($args) {
		$job_id = $args[0] ?? 0;
		if ( ! $job_id ) {
			WP_CLI::error( 'Please provide a job id.' );
		}

		$result = wpcloud_client_job_status( $job_id );
		self::log_result( $result );
	}
}

class WPCloud_CLI_Site extends WPCloud_CLI {

	protected $site_id;

	public function list($args) {
		$sites = wpcloud_client_site_list();
		if ( is_wp_error( $sites ) ) {
			WP_CLI::error( $sites->get_error_message() );
		}

		$site_list = array_map( function( $site ) {
			return [
				'id' => $site->atomic_site_id,
				'domain' => $site->domain_name,
				'created' => $site->created,
				'space_used' => self::human_filesize( $site->space_used ),
			];
		}, $sites );

		WP_CLI\Utils\format_items( 'table', $site_list, [ 'id', 'domain', 'created', 'space_used' ] );
	}

	public function get( $args ) {
		$this->set_site_id( $args );
		$result = wpcloud_client_site_details( $this->site_id, true );
		self::log_result( $result );
	}

	public function delete($args) {
		$this->set_site_id($args);
		WP_CLI::confirm( 'Are you sure you want to delete this site?' );
		$result = wpcloud_client_site_delete( $this->site_id );
		self::log_result( $result, 'Site deleted' );
	}

	public function domains($args) {
		$this->set_site_id($args);
		$result = wpcloud_client_site_domain_alias_list( $this->site_id );
		self::log_result( $result );
	}

	public function phpmyadmin($args) {
		$this->set_site_id($args);
		$result = wpcloud_client_site_phpmyadmin_url( $this->site_id );
		self::log_result( $result );
	}

	public function software($args, $actions = array() ) {
		$this->set_site_id($args);

		$software = array();
		foreach ( $actions as $action => $package_list ) {
			$packages = explode( ',', $package_list );
			foreach( $packages as $package ) {
				$software[$package] = $action;
			}
		}
		$result = wpcloud_client_site_manage_software( $this->site_id, $software );
		self::log_result( $result );
	}

	protected function set_site_id($args) {
		$this->site_id = $args[0] ?? 0;

		if ( ! is_numeric( $this->site_id ) ) {
			$sites = wpcloud_client_site_list();
			$site =  array_filter( $sites, function( $site ) {
				return $site->domain_name === $this->site_id;
			} );

			if ( empty($site) ) {
				WP_CLI::error( 'Site not found.' );
			}
			$site = reset($site);
			$this->site_id = $site->atomic_site_id;
		}

		if ( ! $this->site_id ) {
			WP_CLI::error( 'Please provide a site id.' );
		}
	}
}

class WPCloud_CLI_Site_Domain extends WPCloud_CLI_Site {

	protected $domain_name;

	public function get( $args, $switches = array() ) {
		$this->set_site_id($args);
		$details = wpcloud_client_site_details( $this->site_id );
		if ( is_wp_error( $details ) ) {
			WP_CLI::error( $details->get_error_message() );
		}

		self::log( $details->domain_name );
	}

	public function add( $args ) {
		$this->set_site_id($args);
		$this->set_domain_name($args);

		$result = wpcloud_client_site_domain_alias_add( $this->site_id, $this->domain_name );
		self::log_result( $result, "Domain added" );
	}

	public function remove($args) {
		$this->set_site_id($args);
		$this->set_domain_name($args);

		$result = wpcloud_client_site_domain_alias_remove( $this->site_id, $this->domain_name );
		self::log_result( $result, "Domain deleted" );
	}

	public function make_primary($args, $switches = array() ) {
		$this->set_site_id($args);
		$this->set_domain_name($args);
		$keep = $switches['keep'] ?? false;

		$result = wpcloud_client_site_domain_primary_set( $this->site_id, $this->domain_name, $keep );
		self::log_result( $result, "Primary domain set" );
	}

	public function ip($args) {
		self::log_result( wpcloud_client_site_ip_addresses($args[ 0 ] ?? '' ) );
	}

	public function validate($args) {
		$this->set_site_id($args);
		$this->set_domain_name($args);
		$result = wpcloud_client_domain_validate( $this->site_id, $this->domain_name );
		self::log_result( $result );
	}

	public function retry_ssl($args) {
		$this->set_site_id($args);
		$this->set_domain_name($args);
		$result = wpcloud_client_site_ssl_retry( $this->site_id, $this->domain_name );
		self::log_result( $result, 'SSL retry initiated' );
	}

	protected function set_domain_name($args) {
		$this->domain_name = $args[1] ?? '';

		if ( ! $this->domain_name ) {
			WP_CLI::error( 'Please provide a domain' );
		}
	}
}

class WPCloud_CLI_Site_SSH_User extends WPCloud_CLI_Site {

	protected $user;

	public function add($args, $options ) {
		$this->set_site_id($args);
		$this->set_user($args);

		$key = $options['pub_key'] ?? '';
		if ( isset( $options['pass'] ) ) {
			$pass = $options['pass'];
		} else {
			$pass = null;
		}

		$result = wpcloud_client_ssh_user_add( $this->site_id, $this->user, $key, $pass);
		self::log_result( $result, 'SSH user added' );

	}

	public function remove( $args ) {
		$this->set_site_id($args);
		$this->set_user($args);

		$result = wpcloud_client_ssh_user_remove( $this->site_id, $this->user );
		self::log_result( $result, 'SSH user removed' );
	}

	public function list( $args ) {
		$this->set_site_id($args);
		self::log_result( wpcloud_client_ssh_user_list( $this->site_id ) );
	}

	protected function set_user($args) {
		$this->user = $args[1] ?? '';

		if ( ! $this->user ) {
			WP_CLI::error( 'Please provide a user' );
		}
	}
}

class WPCloud_CLI_Client extends WPCloud_CLI {

	public function get() {
	 $options = get_option( 'wpcloud_settings' );
	 $root_options = array();
	 foreach ( $options as $key => $value ) {

		 $root_options[preg_replace('/^wpcloud_/','',$key)] = $value;
	 }
	 self::log_result( $root_options );

	}

	public function set( $args ) {
		$key = $args[0] ?? '';
		$value = $args[1] ?? '';

		if ( ! $key ) {
			WP_CLI::error( 'Please provide a key' );
		}

		$options = get_option( 'wpcloud_settings' );

		if ( "wpcloud_api_key" === $key && isset( $options[ 'wpcloud_api_key' ] ) ) {
			WP_CLI::confirm( 'Are you sure you want to change the API key?' );
		}

		if ( "wpcloud_client" ===  $key && isset( $options[ 'wpcloud_client' ] ) ) {
			WP_CLI::confirm( 'Are you sure you want to change the client?' );
		}

		$available_options = [
			'api_key',
			'client',
			'domain',
			'default_theme',
		];

		if ( ! in_array( $key, $available_options ) ) {
			WP_CLI::error( 'Invalid option' );
		}

		$key = preg_replace('/^(wpcloud_)?/','wpcloud_', $key);

		if ( ! $value ) {
			unset($options[$key]);
		} else {
			$options[$key] = $value;
		}
		if ( ! update_option( 'wpcloud_settings', $options ) ) {
			WP_CLI::error( 'Failed to update option' );
		}

		WP_CLI::success( 'Option updated' );
	}

	public function ip() {
		self::log_result( wpcloud_client_site_ip_addresses() );
	}

	public function available() {
		self::log( '%GPHP Versions:');
		self::log_result( wpcloud_client_php_versions_available() );
		self::log( '%GData centers:');
		self::log_result( wpcloud_client_datacenters_available() );
	}
}

add_action( 'cli_init', function( ) {
	WP_CLI::add_command( 'cloud job' , 'WPCloud_CLI_Job');
	WP_CLI::add_command( 'cloud site' , 'WPCloud_CLI_Site');
	WP_CLI::add_command( 'cloud site domain' , 'WPCloud_CLI_Site_Domain');
	WP_CLI::add_command( 'cloud site ssh-user' , 'WPCloud_CLI_Site_SSH_User');
	WP_CLI::add_command( 'cloud client' , 'WPCloud_CLI_Client');
} );