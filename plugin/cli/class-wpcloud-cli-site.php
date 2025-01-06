<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli.php';

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
	 * ## OPTIONS
	 *
	 * [--source=<all|remote|local|remote-only>]
	 * : list sites from all contexts, remote , remote only, or local.
	 * remote-only will list sites that are only on the remote server.
	 * default: local
	 *
	 * [--limit=<limit>]
	 * : Limit the number of remote sites to list.
	 *
	 * [--after=<after>]
	 * : List remote sites after a specific site id.
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *  wp cloud site list --context=all
	 *
	 * @param array $args     The arguments.
	 * @param array $switches The switches.
	 */
	public function list( $args, $switches = array() ) {
		$limit   = $switches['limit'] ?? 0;
		$after   = $switches['after'] ?? '';
		$context = $switches['source'] ?? '';

		switch ( $context ) {
			case 'all':
				self::log( '%GRemote sites' );
				$remote_total = $this->list_remote( $limit, $after );
				self::log( '%GLocal sites' );
				$local_total = $this->list_local();
				self::log( '%gTotal remote sites: ' . $remote_total );
				self::log( '%gTotal local sites: ' . $local_total );
				break;
			case 'remote':
				$total = $this->list_remote( $limit, $after );
				self::log( '%gTotal remote sites: ' . $total );
				break;
			case 'remote-only':
				$total = $this->list_remote( $limit, $after, true );
				self::log( '%gTotal remote only sites: ' . $total );
				break;
			default:
				$total = $this->list_local( $switches['col'] ?? '' );
				self::log( '%gTotal local sites: ' . $total );
				break;
		}
	}

	/**
	 * List all remote sites.
	 *
	 * @param int  $limit The limit.
	 * @param int  $after The after.
	 * @param bool $remote_only Whether to list only remote sites.
	 */
	private function list_remote( $limit, $after, $remote_only = false ): int {
		$sites = $this->api()->site_list( $limit, $after )->result;

		if ( is_wp_error( $sites ) ) {
			WP_CLI::error( $sites->get_error_message() );
		}

		if ( $remote_only ) {
			$sites = array_filter(
				$sites,
				function ( $site ) {
					return ! WPCLOUD_Site::get_by_id( $site->atomic_site_id );
				}
			);
		}

		$site_list = array_map(
			function ( $site ) {
				return array(
					'id'         => $site->atomic_site_id,
					'domain'     => $site->domain_name,
					'created'    => $site->created,
					'space_used' => WPCLOUD_Site::readable_size( (float) $site->space_used ),
				);
			},
			$sites
		);

		WP_CLI\Utils\format_items( 'table', $site_list, array( 'id', 'domain', 'created', 'space_used' ) );
		return count( $sites );
	}

	/**
	 * List all local sites.
	 *
	 * @param string $column The column to list.
	 */
	private function list_local( $column = '' ): int {
		$sites = get_posts(
			array(
				'post_type'      => 'wpcloud_site',
				'posts_per_page' => -1,
				'post_status'    => 'any',
			)
		);

		if ( $column ) {
			$sites = array_map(
				function ( $site ) use ( $column ) {
					return $site->$column;
				},
				$sites
			);
			self::log_result( implode( ' ', $sites ) );
			return count( $sites );
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
		return count( $site_list );
	}

	/**
	 * Get a site.
	 *
	 * @param array $args The arguments.
	 */
	public function get( array $args ): void {
		$this->set_site_id( $args );
		$result     = $this->api()->site_details( $this->site_id, true )->result;
		$local_site = WPCLOUD_Site::get_by_id( $this->site_id );

		self::log( '%GLocal site:' );
		if ( $local_site ) {
			self::log_result( $local_site );
		} else {
			self::log( '%YWARNING: Site not found locally' );
		}
		echo "\n";

		self::log( '%GSite details:' );
		self::log_result( $result );
	}

	/**
	 * Import a site.
	 *
	 * @param array $args The arguments.
	 * @param array $switches The switches.
	 */
	public function import( array $args, array $switches ): void {

		$owner_slug = $args[0] ?? '';
		$owner      = get_user_by( 'slug', $owner_slug );
		if ( ! $owner ) {
			WP_CLI::error( "Owner not found: slug=`$owner_slug`" );
			return;
		}

		if ( isset( $switches['all'] ) ) {
			$sites = $this->api()->site_list()->result;
			if ( is_wp_error( $sites ) ) {
				WP_CLI::error( $sites->get_error_message() );
				return;
			}
			// Skip sites that already exist locally.
			$sites = array_filter(
				$sites,
				function ( $site ) {
					return ! WPCLOUD_Site::get_by_id( (int) $site->atomic_site_id );
				}
			);

			$total = count( $sites );

			self::log(
				sprintf(
					_n( 'There is %s site to import', 'There are %s sites to import', $total, 'wpcloud_station'), // phpcs:ignore
					$total
				)
			);
			WP_CLI::confirm( 'Are you sure you want to import?' );
			$progress = \WP_CLI\Utils\make_progress_bar( 'Importing', $total );
			foreach ( $sites as $site ) {
				$this->import_site( (int) $site->atomic_site_id, $owner );
				$progress->tick();
			}
			$progress->finish();
			return;
		}

		$site_id = $args[1] ?? 0;
		if ( ! $site_id ) {
			WP_CLI::error( 'Please provide a wpcloud site id.' );
			return;
		}
		$this->import_site( $site_id, $owner );
		WP_CLI::success( 'Site imported' );
	}

	/**
	 * Import a site.
	 *
	 * @param int     $site_id The site ID.
	 * @param WP_User $owner   The owner.
	 */
	private function import_site( int $site_id, WP_User $owner ): bool {
		$result = WPCLOUD_Site::import( $site_id, $owner );
		if ( is_wp_error( $result ) ) {
			WP_CLI::warning( $result->get_error_message() );
			return false;
		}
		if ( ! $result ) {
			WP_CLI::warning( "Site $site_id already exists locally" );
			return false;
		}
		return true;
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
			$result = $this->api()->site_delete( $site_id )->result;
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
	 * ## OPTIONS
	 *
	 * --name=<name>
	 * : Site name
	 *
	 * --email=<email>
	 * : Email address of the site owner.
	 *
	 * --pass=<pass>
	 * : Admin password.
	 *
	 * [--create-user]
	 * : Create a user if it does not exist.
	 *
	 * [--dc=<dc>]
	 * : Datacenter
	 *
	 * [--php=<php>]
	 * : PHP version
	 *
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *  wp cloud site create --name=example.com --email=admin@example.com --pass=*******
	 *
	 * @param array $switches The switches.
	 */
	public function create( $args, $switches ) {
		$name  = $switches['name'] ?? '';
		$email = $switches['email'] ?? '';
		$pass  = $switches['pass'] ?? '';
		if ( ! $name || ! $email || ! $pass ) {
			WP_CLI::error( 'Please provide a name, email and password' );
		}

		$dc = $switches['dc'] ?? '';
		if ( $dc ) {
			$datacenters = $this->api()->data_centers_available()->result;
			if ( is_wp_error( $datacenters ) ) {
				WP_CLI::error( $datacenters->get_error_message() );
			}
			if ( ! in_array( $dc, (array) $datacenters, true ) ) {
				WP_CLI::error( 'Invalid datacenter' );
			}
		}

		$php = $switches['php'] ?? '';
		if ( $php ) {
			$php_versions = $this->api()->php_versions_available()->result;
			if ( ! in_array( $php, $php_versions, true ) ) {
				WP_CLI::error( 'Invalid PHP version' );
			}
		}

		$user = get_user_by( 'email', $email );
		$create_user = $switches['create-user'] ?? false;
		if ( ! $user && $create_user ) {
			$user_id = wp_create_user( $email, $pass, $email );
			if ( is_wp_error( $user_id ) ) {
				WP_CLI::error( $user_id->get_error_message() );
			}
			$user = get_user_by( 'id', $user_id );
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
		self::log_success( $result, 'Site created' );
	}

	/**
	 * Get the site meta.
	 *
	 * @param array $args The arguments.
	 */
	public function domains( $args ) {
		$this->set_site_id( $args );
		$result = $this->api()->site_domain_alias_list( $this->site_id )->log();
	}

	/**
	 * Get the site meta.
	 *
	 * @param array $args The arguments.
	 */
	public function phpmyadmin( $args ) {
		$this->set_site_id( $args );
		$result = $this->api()->site_phpmyadmin_url( $this->site_id )->log();
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
		$result = $this->api()->site_manage_software( $this->site_id, $software )->log();
	}

	/**
	 * Get site logs
	 *
	 * @param array $args The arguments.
	 * @param array $switches The switches.
	 */
	public function logs( $args, $switches = array() ) {
		$this->set_site_id( $args );
		$error = $switches['error'] ?? false;
		$file  = $switches['file'] ?? false;

		$log_type = $error ? 'site_error_logs' : 'site_logs';
		if ( $file ) {
			$log_file = WPCLOUD_Site::prepare_log_file( $log_type, $this->site_id );

			WP_CLI::success( $log_file );
			return;
		}

		$result = $this->api()->$log_type( $this->site_id, null, null )->result;

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
		$this->site_id = self::get_site_id( $this->api(), $args[0] ?? 0 );
	}

	/**
	 * Get site id
	 *
	 * @param WPCLOUD_CLI_Api $api The wpcloud api.
	 * @param string|int      $site_id The site ID.
	 */
	public static function get_site_id( WPCLOUD_CLI_Api $api, string|int $site_id = 0 ): null|int {

		// Check the local sites first.
		$site_cpt = null;
		if ( ! is_numeric( $site_id ) ) {
			$query = new WP_Query(
				array(
					'post_type'   => 'wpcloud_site',
					'title'       => $site_id,
					'post_status' => 'any',
					'numberposts' => 1,
				)
			);

			$site_cpt = $query->have_posts() ? $query->posts[0] : null;
		} else {
			$site_cpt = get_post( $site_id );
		}

		if ( $site_cpt && ! is_wp_error( $site_cpt ) ) {
			$site_id = (int) get_post_meta( $site_cpt->ID, 'wpcloud_site_id', true );

			if ( ! $site_id ) {
				WP_CLI::error( sprintf( 'Local site %s is missing a wp cloud site id', $site_cpt->post_title ) );
				return null;
			}
			return $site_id;
		}

		if ( ! is_numeric( $site_id ) ) {
			$sites = $api->site_list()->result;
			$site  = array_filter(
				$sites,
				function ( $site ) use ( $site_id ) {
					return $site_id === $site->domain_name;
				}
			);

			if ( empty( $site ) ) {
				WP_CLI::error( 'Site not found.' );
			}
			$site    = reset( $site );
			$site_id = (int) $site->atomic_site_id;
		}

		if ( ! $site_id ) {
			WP_CLI::error( 'Please provide a site id.' );
			return null;
		}
		return $site_id;
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
			'meta_query'  => array( // phpcs:ignore
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
