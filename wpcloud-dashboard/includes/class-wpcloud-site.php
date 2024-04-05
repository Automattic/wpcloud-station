<?php
/**
 * WP Cloud Site .
 *
 * @package wpcloud-dashboard
 */
declare( strict_types = 1 );

require_once  plugin_dir_path( __FILE__ ) . 'wpcloud-client.php';

class WPCLOUD_Site {

	const WPCLOUD_DETAIL_KEYS = [
    'atomic_site_id',
    'domain_name',
    'server_pool_id',
    'atomic_client_id',
		'chroot_path',
		'chroot_ssh_path',
    'cache_prefix',
    'db_charset',
    'db_collate',
		'db_password',
    'php_version',
    'site_api_key',
    'wp_admin_email',
    'wp_admin_user',
    'wp_version',
    'static_file_404',
    'smtp_pass'
	];

	private static $initial_status = 'draft';

	public int $id;
	public string $name;
	public string $php_version;
	public string $data_center;
	public string $status;
	public int $owner_id;
	public string $domain;
	public int $wpcloud_site_id;
	public array $details;

	public function __construct(string $domain = '') {
		$this->id = 0;
		$this->name = '';
		$this->php_version = '';
		$this->data_center = '';
		$this->status = '';
		$this->owner_id = 0;
		$this->wpcloud_site_id = 0;
		$this->details = array();

		if ( ! $domain ) {
			$this->domain = self::get_default_domain();
		} else {
			$this->domain = $domain;
		}
	}

	public static function get_default_domain(string $sub_domain = ''): string {
		$settings = get_option( 'wpcloud_settings' );
		$domain = $settings['wpcloud_domain'] ?? '';
		if ( $sub_domain && $domain ) {
			$domain = $sub_domain . '.' . $domain;
		}
		return $domain;
	}

	public static function register_post_type(): void {
			register_post_type( 'wpcloud_site',
				array(
				'labels'	=> array(
					'name'			=> __( 'Sites', 'wpcloud' ),
					'singular_name'	=> __( 'Site', 'wpcloud' ),
					'add_new' => 'Add New Site',
				),
				'public'	=> true,
				'has_archive' => true,
				'show_in_rest' => true,
				'show_in_ ui' => false,
				'show_in_menu' => false,
				'taxonomies' => array( 'category', 'tag' ),
			)
		);
	}

	/**
	 * Create a new WPCLOUD_Site from a WP_Post object.
	 * @param WP_Post $post
	 * @return WPCLOUD_Site
	 */
	public static function from_post( WP_Post $post ): self {
		$site = new self();
		$site->id = $post->ID;
		$site->name = $post->post_title;
		$site->php_version = get_post_meta( $post->ID, 'php_version', true );
		$site->data_center = get_post_meta( $post->ID, 'data_center', true );
		$site->status = $post->post_status;
		$site->owner_id = intval( $post->post_author );
		$site->domain = get_post_meta( $post->ID, 'domain', true );
		$site->wpcloud_site_id = intval( get_post_meta( $post->ID, 'wpcloud_site_id', true ) );
		return $site;
	}

	/**
	 * @return mixed
	 */
	public function set_client_details(array $detail_keys = array() ): mixed {
		$wpcloud_site = wpcloud_client_site_details( $this->wpcloud_site_id );
		if ( is_wp_error( $wpcloud_site ) ) {
			return $wpcloud_site;
		}
		$detail_keys = array_unique( array_merge( $detail_keys, self::WPCLOUD_DETAIL_KEYS ) );

		$this->details = array_intersect_key( (array) $wpcloud_site, array_flip( $detail_keys ) );

		return true;
	}

	public function delete( ): mixed {
		$wpcloud_id = get_post_meta( $this->id, 'wpcloud_id', true );

		if ( ! $wpcloud_id ) {
			return new WP_Error( 'not_found', __( 'WP Cloud site id not found.' ) );
		}

		$result = wpcloud_client_site_delete( intval( $wpcloud_id ) );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		error_log( 'Result from wpcloud_client_site_delete: ' . print_r( $result, true ) );

		return true;
	}

	public static function create(string $name, string $php_version, string $data_center, ?int $owner_id): mixed {
		// Check if the user is allowed to create a site.
		if ( ! $owner_id) {
			$owner_id = get_current_user_id();
		}

		$should_create = apply_filters( WPCLOUD_SHOULD_CREATE_SITE, true, $owner_id );
		if ( ! $should_create ) {
			return new WP_Error( 'forbidden', __( 'Site creation is disabled.' ) );
		}

		$pattern = "/^(?!-)(?!.*--)[A-Za-z0-9-]{1,63}(?<!-)$/";
		if ( ! preg_match( $pattern, $name ) ) {
			return new WP_Error( 'forbidden', __( 'Invalid site name.' ) );
		}

		$domain = self::get_default_domain( $name );
		$admin =  get_user_by( 'id', $owner_id );

		$data = array(
			'php_version' => $php_version,
		);

		if ( $data_center != 'NA' ) {
			$data['data_center'] = $data_center;
		}

		$result = wpcloud_client_site_create(
			domain: $domain,
			admin_user: $admin->user_login,
			admin_email: $admin->user_email,
			data: $data
		);

		error_log( 'Result from wpcloud_client_site_create: ' . print_r( $result, true ) );

		if ( is_wp_error( $result ) ) {
			error_log( 'Error creating site: ' . $result->get_error_message() );
			return $result;
		}

		$result = (array) $result;
		$site_id = self::create_post( owner_id: $owner_id, wpcloud_id: $result[ 'atomic_site_id' ], domain: $domain, data: $data );

		if ( is_wp_error( $site_id ) ) {
			error_log(  'Error creating site post: ' . $site_id->get_error_message() );
			return $site_id;
		}

		do_action( WPCLOUD_ACTION_SITE_CREATED, $site_id, $owner_id, 'wp-admin' );
		return self::from_post( get_post( $site_id ) );
	}

	private static function test() {
		error_log( 'Test' );
	}

	private static function create_post(int $owner_id, int $wpcloud_id, string $domain, array $data ): mixed {
		self::test();
 		$status = apply_filters( WPCLOUD_INITIAL_SITE_STATUS, self::$initial_status );
		$post_details = array(
			'post_type' => 'wpcloud_site',
			'post_title' => $domain,
			'post_status' => $status,
			'comment_status'=> 'closed',
			'author' => $owner_id,
		);

			// Create the site CPT and set the meta data.
		$site_id = wp_insert_post( $post_details );
		if ( is_wp_error( $site_id ) ) {
			return $site_id;
		}

		update_post_meta( $site_id, 'wpcloud_site_id', $wpcloud_id );
		//We only really need these for the initial creation so we can show the user what they just created.
		update_post_meta( $site_id, 'php_version', $data[ 'php_version' ] ?? '' );
		update_post_meta( $site_id, 'data_center', $data[ 'data_center' ] ?? '' );

		return $site_id;
	}

	public static function find( int $site_id ): mixed {
		$site = get_post( $site_id );
		if ( ! $site ) {
			return new WP_Error( 'not_found', __( 'Site not found.' ) );
		}
		return self::from_post( $site );
	}

	public static function find_all(string $owner_id, array $query = array(), bool $backfill_from_host = false ): mixed {
		$defaults = array(
			'post_type' => 'wpcloud_site',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
			'author' => $owner_id,
		);

		$query = wp_parse_args( $query, $defaults );

		$results = new WP_Query( $query );
		if ( is_wp_error( $results ) ) {
			return $results;
		}
		if ( $backfill_from_host ) {
			self::backfill_from_host( $results->posts );
		}
		return array_map( self::class . '::from_post', $results->posts );
	}

	private static function backfill_from_host(array $local_sites): void {
		$remote_sites = self::fetch_all();
		if ( is_wp_error( $remote_sites ) ) {
			return;
		}

		$remote_ids = array_map( fn($remote_site) => $remote_site->atomic_site_id ,$remote_sites );
		error_log( 'Remote ids: ' . print_r( $remote_ids, true ) );
		$local_ids = array_map( function( $site ) {
			$wpcloud_id = get_post_meta( $site->ID, 'wpcloud_id', true );
			return $wpcloud_id ? intval( $wpcloud_id ) : 0;
		}, $local_sites );

		$missing_ids = array_diff( $remote_ids, $local_ids );
		$missing_sites = array_filter( $remote_sites, fn( $site ) => in_array( $site->atomic_site_id, $missing_ids ) );

		$owner_id = get_current_user_id();

		error_log( 'Missing ids: ' . print_r( $missing_ids, true ) );
		foreach ( $missing_sites as $site ) {
			$site_id = self::create_post( $owner_id, intval($site->atomic_site_id), $site->domain_name, array() );
			if ( is_wp_error( $site_id ) ) {
				error_log( 'Error creating site post: ' . $site_id->get_error_message() );
				continue;
			}
			wp_set_post_tags( $site_id, array( 'backfill' ), true );

			do_action( WPCLOUD_ACTION_SITE_CREATED, $site_id, $owner_id, 'wp-admin' );
		}
	}

	private static function fetch_all(): mixed {
		$sites = wpcloud_client_site_list();
		if ( is_wp_error( $sites ) ) {
			error_log( 'Error fetching sites: ' . $sites->get_error_message() );
			return $sites;
		}
		return $sites;
		//return array_reduce( $sites, fn( $indexed, $site ) => $indexed + [ $site->atomic_site_id => (array) $site ], array() );
	}
}