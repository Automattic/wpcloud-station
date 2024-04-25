<?php
/**
 * WP Cloud Site.
 *
 * @package wpcloud-dashboard
 */

declare( strict_types = 1 );

class WPCLOUD_Site {

	const DETAIL_KEYS = array(
		'site_name', // only used locally
		'site_owner_id', // only used locally
		'domain_name',
		'wp_admin_email',
		'wp_admin_user',
		'smtp_pass',
		'geo_affinity',
		'data_center',  // We advertise this as data center but it maps to geo_affinity
		'ip_addresses',
		'wp_version',
		'php_version',
		'static_file_404',
		'db_password',
		'db_charset',
		'db_collate',
		'cache_prefix',
		'chroot_path',
		'chroot_ssh_path',
		'site_api_key',
		'atomic_site_id',
		'atomic_client_id',
		'server_pool_id',
		'phpmyadmin_url',
		'ssl_info',

	);

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
	public string $error_message;

	public function __construct() {
		$this->id              = 0;
		$this->name            = '';
		$this->php_version     = '';
		$this->data_center     = '';
		$this->status          = '';
		$this->owner_id        = 0;
		$this->wpcloud_site_id = 0;
		$this->details         = array();
		$this->error_message   = '';
	}

	public static function create(array $options): mixed {
		$author = get_user_by( 'id', $options[ 'site_owner_id' ] ?? 0 );

		if ( ! $author ) {
			return new WP_Error( 'invalid_user', __( 'Invalid user.', 'wpcloud' ) );
		}

		$can_create = apply_filters( 'wpcloud_can_create_site', true, $author, $options );
		if ( ! $can_create ) {
			return new WP_Error( 'unauthorized', __( 'You are not authorized to create a site.', 'wpcloud' ) );
		}

		$domain = wpcloud_site_get_default_domain( $options['site_name'] );
		$php_version = $options['php_version'];
		$data_center = $options['data_center'];
		$post_name = str_replace( '.', '-', $domain );

		$post_id = wp_insert_post(
			array(
				'post_title' => $domain,
				'post_name' => $post_name,
				'post_type' => 'wpcloud_site',
				'post_status' => 'draft',
				'post_author' => $author->ID,
				'meta_input' => array(
					'php_version' => $php_version,
					'data_center' => $data_center,
					'site_name' => $options[ 'site_name' ],
				)
			)
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		return self::from_post( get_post( $post_id ) );
	}

	/**
	 * Create a new WPCLOUD_Site from a WP_Post object.
	 * @param WP_Post $post
	 * @return WPCLOUD_Site
	 */
	public static function from_post( WP_Post $post, bool $fetch_details = false ): self {
		$site = new self();
		$site->id = $post->ID;
		$site->name = $post->post_title;
		$site->php_version = get_post_meta( $post->ID, 'php_version', true );
		$site->data_center = get_post_meta( $post->ID, 'data_center', true );
		$site->status = $post->post_status;
		$site->owner_id = intval( $post->post_author );
		$site->domain = $post->post_title;
		$site->wpcloud_site_id = intval( get_post_meta( $post->ID, 'wpcloud_site_id', true ) );
		$site->error_message = get_post_meta( $post->ID, 'wpcloud_site_error', true );

		if ( $fetch_details && $site->wpcloud_site_id ) {
			$site->set_client_details();
		}
		return $site;
	}

	/**
	 * @return mixed
	 */
	public function set_client_details(array $detail_keys = array() ): mixed {
		$wpcloud_site = wpcloud_client_site_details( $this->wpcloud_site_id, true );
		if ( is_wp_error( $wpcloud_site ) ) {
			return $wpcloud_site;
		}
		$detail_keys = array_unique( array_merge( $detail_keys, self::DETAIL_KEYS ) );

		$this->details = array_intersect_key( (array) $wpcloud_site, array_flip( $detail_keys ) );

		if ( array_search('geo_affinity', $detail_keys) !== false ) {
			$this->details[ 'geo_affinity' ] = $wpcloud_site->extra->server_pool->geo_affinity;
			$this->details[ 'data_center' ]  = WPCLOUD_DATA_CENTERS[ $this->details[ 'geo_affinity' ] ];
		}

		$ips = wpcloud_client_domain_ip_addresses( $this->wpcloud_site_id, $this->domain );

		if ( is_wp_error( $ips ) ) {
			error_log( 'Error while fetching WP Cloud Site IP addresses: ' . $ips->get_error_message() );
			return $ips;
		} else {
				$this->details[ 'ip_addresses' ] = $ips->suggested ?? $ips->ips;
		}

		return true;
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
			// 'author' => $owner_id,
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
		$local_ids = array_map( function( $site ) {
			$wpcloud_id = get_post_meta( $site->ID, 'wpcloud_id', true );
			return $wpcloud_id ? intval( $wpcloud_id ) : 0;
		}, $local_sites );

		$missing_ids = array_diff( $remote_ids, $local_ids );
		$missing_sites = array_filter( $remote_sites, fn( $site ) => in_array( $site->atomic_site_id, $missing_ids ) );

		$owner_id = get_current_user_id();

		// remove the create site action
		remove_action( 'save_post_wpcloud_site', 'wpcloud_on_create_site', 10, 3 );

		foreach ( $missing_sites as $site ) {

			$post = wp_insert_post(
				array(
					'post_title'  => $site->domain_name,
					'post_type'   => 'wpcloud_site',
					'post_status' => self::$initial_status,
					'post_author' => $owner_id,
				)
			);
			if ( is_wp_error( $post ) ) {
				error_log( 'Error creating site post: ' . $post->get_error_message() );
				continue;
			}
			wp_set_post_tags( $post, array( 'backfill' ), true );
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
