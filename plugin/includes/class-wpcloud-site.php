<?php
/**
 * WP Cloud Site.
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

class WPCLOUD_Site {

	const LINKABLE_DETAIL_KEYS = array(
		'domain_name',
		'wp_admin_url',
		'phpmyadmin_url'
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

	/**
	 * Create a new WPCLOUD_Site.
	 *
	 * example options:
	 * $options = [
	 * 	'site_name' => 'example',
	 * 	'php_version' => '8.4',
	 * 	'data_center' => 'cda',
	 * 	'site_owner_id' => 1,
	 * 	'admin_pass' => 'password123',
	 * ];
	 *
	 * @param array $options
	 * @return WPCLOUD_Site|WP_Error
	 */

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
		$post_name = $options['site_name'];

		if ( isset( $options[ 'admin_pass' ] ) && $options[ 'admin_pass' ] ) {
			add_filter( 'wpcloud_site_create_data', function( $data ) use ( $options ) {
				$data[ 'admin_pass' ] = $options[ 'admin_pass' ];
				return $data;
			} );
		}

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
					'initial_domain' => $domain,
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
			$data_center_cities = wpcloud_client_data_center_cities();
			$this->details[ 'geo_affinity' ] = $wpcloud_site->extra->server_pool->geo_affinity;
			$this->details[ 'data_center' ]  =$data_center_cities[ $this->details[ 'geo_affinity' ] ];
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
			wpcloud_backfill();
		}
		return array_map( self::class . '::from_post', $results->posts );
	}

	public static function get_detail_options(): array {
		return array(
			'site_name' => __('Site Name'), // only used locally
			'site_owner_id' => __( 'Site Owner ID' ), // only used locally
			'domain_name' => __( 'Domain Name' ),
			'domain_alias' => __( 'Domain Alias' ),
			'wp_admin_email' => __( 'Admin Email' ),
			'wp_admin_user'	=> __( 'Admin User' ),
			'smtp_pass'	=> __( 'SMTP Password' ),
			'geo_affinity' => __( 'Geo Affinity' ),
			'data_center' => __( 'Data Center'),  // We advertise this as data center but it maps to geo_affinity
			'ip_addresses' => __( 'IP Addresses' ),
			'wp_version' => __( 'WP Version' ),
			'php_version' => __( 'PHP Version' ),
			'static_file_404' => __( 'Static File 404' ),
			'db_password' => __( 'DB Password' ),
			'db_charset' => __( 'DB Charset' ),
			'db_collate' => __( 'DB Collate' ),
			'cache_prefix' => __( 'Cache Prefix' ),
			'chroot_path' => __( 'Chroot Path' ),
			'chroot_ssh_path' => __( 'Chroot SSH Path' ),
			'site_api_key' => __( 'Site API Key' ),
			'atomic_site_id' => __( 'Atomic Site ID' ),
			'atomic_client_id' => __( 'Atomic Client ID' ),
			'server_pool_id' => __( 'Server Pool ID' ),
			'phpmyadmin_url' => __( 'phpMyAdmin URL' ),
			'ssl_info' => __( 'SSL Info' ),
			'wp_admin_url' => __( 'WP Admin URL' ),
			'site_ssh_user' => __( 'Site SSH User' ),
		);
	}

	public static function get_linkable_detail_options(): array {
		return array_intersect_key( self::get_detail_options(),  array_flip( self::LINKABLE_DETAIL_KEYS ) );
	}

	public static function refresh_linkable_detail($post_id, $detail): string {
		// If the detail is unknown let the filter `wpcloud_refresh_link` handle it.
		if ( ! in_array( $detail, self::LINKABLE_DETAIL_KEYS ) ) {
			return '';
		}

		switch($detail) {
			case 'phpmyadmin_url':
				$site_id = get_post_meta( $post_id, 'wpcloud_site_id', true );
				$phpmyadmin_url = wpcloud_client_site_phpmyadmin_url( (int) $site_id );
				if ( is_wp_error( $phpmyadmin_url ) ) {
					error_log( 'Error fetching phpMyAdmin URL: ' . $phpmyadmin_url->get_error_message() );
					return '';
				}
				return $phpmyadmin_url;
		}
		return '';
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
