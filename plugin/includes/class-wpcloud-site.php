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
			// 'db_pass' => __( 'DB Password' ),
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

			// These are read only meta fields
			'max_space_quota' => __( 'Max Space Quota' ),
			"space_used" => __( 'Space Used' ),
		);
	}
/**
	 * Get the meta keys for a WPCLOUD_Site.
	 *
	 * @return array
	 */
	public static function get_meta_fields(): array {
		return wpcloud_client_site_meta_keys();
	}

	/**
	 * Get the options for modifiable site meta for a WPCLOUD_Site.
	 *
	 * see https://wp.cloud/apidocs-webhost/#api-Sites-site-meta
	 * if a key is false, it is only used for getting the value
	 * @return array
	 */
	public static function get_meta_options(): array {
		return [
			// db_charset and db_collate should be paired ?
			"db_charset" => [
				'type' => 'select',
				'options' => [ "latin1 "=> "latin1", "utf8" => "utf8", "utf8mb4" => "utf8mb4" ],
				'default' => 'utf8mb4',
				'hint' => '',
				],

			"db_collate" => [
				'type' => 'select',
				'options' => [ "latin1_swedish_ci" => "latin1_swedish_ci", "utf8_general_ci" => "utf8_general_ci", "utf8mb4_unicode_ci" => "utf8mb4_unicode_ci" ],
				'default' => 'utf8mb4_unicode_ci',
				'hint' => '',
				],

			"suspended" => [
				'type' => 'select',
				'options' => ["404" => __("404 - Not Found "), "410" => __( "410 - Gone" ), "451" => __( "451 - Unavailable For Legal Reasons" ), "480" => __( "480 - Temporarily Unavailable" ) ],
				'default' => '480',
				'hint' => __('Suspends a site. The value is the HTTP 4xx status code the site will respond with. The supported statuses are "404", "410", "451", and "480".'),
				],

			"suspend_after" => [
				'type' => 'text',
				'options' => null,
				'default' => false,
				'hint' => __( 'Suspends a site after a specified time. The value is a unix Timestamp.' ),
				],

			"php_version" => [
				'type' => 'select',
				'options' => wpcloud_client_php_versions_available(),
				'default' => '',
				'hint' => 'Sets the sites PHP version.',
				],

			"wp_version" => [
				'type' => 'select',
				'options' =>  [ "latest" => __("latest"), "previous" => __("previous"), "beta" => __("beta") ],
				'default' => 'latest',
				'hint' => 'Sets the sites WordPress version.',
				],

			"do_not_delete" => [
				'type' => 'checkbox',
				'default' => false,
				'hint' => __( 'Prevent a site from begin deleted. This can be useful in some cases. For example, you might wish to preserve a site while it is being reviewed for Terms of Service violations.'),
				],

			"space_quota" => [
				'type' => 'text',
				'default' => 0,
				'hint' => __('Sets the space quota for a site. Values should be in gigabytes.'),
				],

			"photon_subsizes" => [
				'type' => 'checkbox',
				'default' => false,
				'hint' => __('Controls whether WP skips generating intermediate image files when an image is uploaded. The platform is able to satisfy requests for intermediate image files whether or not they exist, so sites can save disk space by not creating them. When the a site web server receives a request for a non-existent intermediate image file, it proxies the request to Photon which responds with the intermediate image size.'),
				],

			"privacy_model" => [
				'type' => 'select',
				'options' => [ "wp_uploads" => "WP Uploads" ],
				'default' => 'wp_uploads',
				'hint' => __( 'Facilitates protection of site assets. May be set to "wp_uploads" to block logged-out requests for WP uploads. If set, an AT_PRIVACY_MODEL constant will be defined in the PHP environment. Use the "site-wordpress-version" endpoint to set "wp_version".' )
				],

			"static_file_404" => [
				'type' => 'select',
				'options' => [ "lightweight" => __( 'Lightweight' ), "wordpress" => __( 'WordPress' )],
				'default' => 'wordpress',
				'hint' => __( 'Set how a site responds to requests for non-existent static files. May be set to "lightweight" for simple, fast web server 404s. May be set to "wordpress" to delegate such requests to WordPress. The current default is "wordpress".' ),
				],

			"default_php_conns" => [
				'type' => 'select',
				'options' => range(2,10),
				'default' => 0,
				'hint' => __( 'May be used to either limit allowed concurrent PHP connections or to increase the default number of concurrent connections a site can use if the web server has spare PHP connections capacity. Clients may set any value for a site between 2 and 10; the platform has more leeway if needed.' ),
				],

			"burst_php_conns" => [
				'type' => 'checkbox',
				'default' => false,
				'hint' => __( 'Enable burst for sites with fewer than 10 default_php_conns. 0 or absent when default_php_conns < 10 means burst is disabled, 1 means burst is enabled.'),
			],

			"php_fs_permissions" => [
				'type' => 'select',
				'options' => [ "RW" => __( "Read/Write"), "RO" => __( "Read Only"), "LOGGEDIN" => __("Read only unless logged into WordPress") ],
				'default' => 'RW',
				'hint' => __( 'Sets the PHP file system permissions. May be set to `Read/Write`, `Read Only`, or `Logged in` for read only unless logged into WordPress.' ),
				],

			"canonicalize_aliases" => [
				'type' => 'checkbox',
				'default' => true,
				'hint' => __( 'May be used to change whether a sites domain aliases redirect (default, "true") to the sites primary domain name or are served directly (when set to "false")' ),
			],
		];
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
