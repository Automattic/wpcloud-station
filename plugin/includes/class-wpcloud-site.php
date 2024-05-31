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
	 * @return WP_Post|WP_Error
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

		return get_post( $post_id );
	}

	public static function get_detail_options(): array {
		return array(
			'site_name' => __('Site Name'), // only used locally
			'site_owner_id' => __( 'Site Owner ID' ), // only used locally
			'domain_name' => __( 'Domain Name' ),
			'site_alias' => __( 'Site Alias' ),
			'wp_admin_email' => __( 'Admin Email' ),
			'wp_admin_user'	=> __( 'Admin User' ),
			'smtp_pass'	=> __( 'SMTP Password' ),
			'geo_affinity' => __( 'Geo Affinity' ),
			'data_center' => __( 'Data Center'),  // We advertise this as data center but it maps to geo_affinity
			'ip_addresses' => __( 'IP Addresses' ),
			'wp_version' => __( 'WP Version' ),
			'php_version' => __( 'PHP Version' ),
			'static_file_404' => __( 'Static File 404' ),
			'db_pass' => __( 'DB Password' ),
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
}
