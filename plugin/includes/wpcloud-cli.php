<?php

require_once __DIR__ . '/wpcloud-client.php';
require_once __DIR__ . '/../admin/includes/wpcloud-headstart.php';

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

	public function list($args, $switches = array()) {

		$show_remote = $switches['remote'] ?? false;

		if ( $show_remote ) {
			$sites = wpcloud_client_site_list();
			if ( is_wp_error( $sites ) ) {
				WP_CLI::error( $sites->get_error_message() );
			}
			if ( isset( $switches['col'] ) ) {
				$column = $switches['col'];
				if ($column === 'id') {
					$column = 'atomic_site_id';
				}
				$sites = array_map( function( $site ) use ( $column ) {
					return $site->$column;
				}, $sites );
				self::log_result( implode( ' ', $sites) );
				return;
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
		} else {
			$sites = get_posts( [
				'post_type' => 'wpcloud_site',
				'posts_per_page' => -1,
				'post_status' => 'any',
			] );

			if ( isset( $switches['col'] ) ) {
				$column = $switches['col'];
				$sites = array_map( function( $site ) use ( $column ) {
					return $site->$column;
				}, $sites );
				self::log_result( implode( ' ', $sites) );
				return;
			}

			$site_list = array_map( function( $site ) {
				return [
					'wpcloud id' => get_post_meta( $site->ID, 'wpcloud_site_id', true ),
					'id' => $site->ID,
					'domain' => $site->post_title,
					'created' => $site->post_date,
					'status' => $site->post_status,
				];
			}, $sites );

			WP_CLI\Utils\format_items( 'table', $site_list, [ 'wpcloud id', 'id', 'domain', 'created', 'status' ] );
		}
	}

	public function get( $args ) {
		$this->set_site_id( $args );
		$result = wpcloud_client_site_details( $this->site_id, true );
		self::log_result( $result );
	}

	private function _delete(int $site_id, $remote = false, $confirmed = false ) {
		$result = null;
		if ( ! $confirmed ) {
			WP_CLI::confirm( sprintf(  'Are you sure you want to delete the site %d ?', $site_id ) );
		}
		if ( $remote ) {
			$result = wpcloud_client_site_delete( $site_id );
		} else {
			$post = $this->get_site_cpt( [ $site_id ] );
			$result = wp_delete_post( $post->ID, true );
		}
		if ( is_wp_error( $result ) ) {
			WP_CLI::warning( $result->get_error_message() );
			return;
		}

		self::log( sprintf("%%gSite %d deleted", $site_id ) );
	}

	public function delete($args, $switches = array()) {
		$remote = $switches['remote'] ?? false;
		$confirmed = $switches['confirmed'] ?? false;

		$delete_count = 0;
		foreach( $args as $site_id ) {

			// make sure to confirm after every 5th site
			$pause_to_confirm  = $confirmed && $delete_count % 5 === 0 && $delete_count > 0;
			if ( $pause_to_confirm ) {
				WP_CLI::confirm( sprintf( 'Are you sure you want to continue deleting %d more sites?', count($args) - $delete_count ) );
			}
			$this->_delete( $site_id, $remote, $confirmed );
			$delete_count++;
		}
		WP_CLI::success( _n( 'Site deleted', sprintf('%d sites deleted', $delete_count ), count($args) ));
	}

	public function create($args, $switches) {
		$name = $switches['name'] ?? '';
		$email = $switches['email'] ?? '';
		$pass = $switches['pass'] ?? '';
		if ( ! $name || ! $email || ! $pass ) {
			WP_CLI::error( 'Please provide a name, email and password' );
		}
		$user_name = $switches['user'] ?? $email;

		$dc = $switches['dc'] ?? '';
		if ( $dc ) {
			$datacenters = wpcloud_client_data_centers_available();
			if ( ! in_array( $dc, $datacenters ) ) {
				WP_CLI::error( 'Invalid datacenter' );
			}
		}

		$php = $switches['php'] ?? '';
		if ( $php ) {
		 $php_versions = wpcloud_client_php_versions_available();
		 if ( ! in_array( $php, $php_versions ) ) {
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
			'site_name' => $name,
			'data_center' => $dc,
			'php_version' => $php,
			'admin_pass' => $pass,
		);

		$result = WPCLoud_Site::create( $options );
		self::log_result( $result, 'Site created');
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

		// check the local sites first
		$site_cpt = null;
		if ( ! is_numeric( $this->site_id ) ) {
			$site_cpt = get_page_by_title( $this->site_id, OBJECT, 'wpcloud_site' );
		} else {
			$site_cpt = get_post( $this->site_id );
		}

		if ( $site_cpt && ! is_wp_error( $site_cpt ) ) {
			$this->site_id = get_post_meta( $site_cpt->ID, 'wpcloud_site_id', true );

			if ( ! $this->site_id ) {
				WP_CLI::error( sprintf( 'Local site %s is missing a wp cloud site id'. $site_cpt->post_title ) );
			}
			return;
		}
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

	protected function get_site_cpt( $args ) {
		self::set_site_id($args);
		$query = array(
			'post_type' => 'wpcloud_site',
			'post_status' => 'any',
			'meta_query' => array(
				array(
					'key' => 'wpcloud_site_id',
					'value' => $this->site_id,
				),
			),
		);
		$site = get_posts( $query );
		if ( empty( $site ) ) {
			WP_CLI::error( 'Site not found.' );
		}
		return reset( $site );
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

	public function list( $args, $switches = array()) {
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

	public function get($args) {
		$options = get_option( 'wpcloud_settings' );
		$root_options = array();
		foreach ( $options as $key => $value ) {

			$root_options[preg_replace('/^wpcloud_/','',$key)] = $value;
		}

		if ( isset( $args[0] ) ) {
			$key = $args[0];
			if ( isset( $root_options[$key] ) ) {
				self::log( $root_options[$key] );
				return;
			}
		} else {
			self::log_result( $root_options );
		}
	}

	public function set( $args ) {
		$key = $args[0] ?? '';
		$value = $args[1] ?? '';

		if ( ! $key ) {
			WP_CLI::error( 'Please provide a key' );
		}

		$options = get_option( 'wpcloud_settings' );

		if ( "api_key" === $key && isset( $options[ 'wpcloud_api_key' ] ) ) {
			WP_CLI::confirm( 'Are you sure you want to change the API key?' );
		}

		if ( "client" ===  $key && isset( $options[ 'wpcloud_client' ] ) ) {
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

		$key = preg_replace( '/^(wpcloud_)?/', 'wpcloud_', $key );

		if ( ! $value ) {
			unset( $options[$key] );
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
		self::log_result( wpcloud_client_data_centers_available() );
	}

	public function headstart($args, $switches ) {


		$client = $switches['client'] ?? '';
		$key = $switches['key'] ?? '';
		$force = $switches['force'] ?? false;

		$result = wpcloud_headstart( $client, $key, $force, new WPCloud_CLI_Skin());
		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
		}
		WP_CLI::success( 'Headstart installed' );
	}
}

class WPCloud_CLI_Skin extends WP_Upgrader_Skin {
	public function feedback($string, ...$args)
	{
		WP_CLI::log( $string );
		return '';
	}
	public function header()
	{
		// Silence is golden.
		return '';
	}

	public function footer()
	{
		// Silence is golden.
		return '';
	}
}

add_action( 'cli_init', function( ) {
	WP_CLI::add_command( 'cloud job' , 'WPCloud_CLI_Job');
	WP_CLI::add_command( 'cloud site' , 'WPCloud_CLI_Site');
	WP_CLI::add_command( 'cloud site domain' , 'WPCloud_CLI_Site_Domain');
	WP_CLI::add_command( 'cloud site ssh-user' , 'WPCloud_CLI_Site_SSH_User');
	WP_CLI::add_command( 'cloud client' , 'WPCloud_CLI_Client');
} );
