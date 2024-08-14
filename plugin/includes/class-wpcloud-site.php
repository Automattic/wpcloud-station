<?php
/**
 * WP Cloud Site.
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

/**
 * Class WPCLOUD_Site
 */
class WPCLOUD_Site {

	const LINKABLE_DETAIL_KEYS = array(
		'domain_name',
		'wp_admin_url',
		'phpmyadmin_url',
	);

	/**
	 * The ID of the site.
	 *
	 * @var int
	 */
	public int $id;
	/**
	 * The name of the site.
	 *
	 * @var string
	 */
	public string $name;

	/**
	 * The PHP version of the site.
	 *
	 * @var string
	 */
	public string $php_version;

	/**
	 * The data center of the site.
	 *
	 * @var string
	 */
	public string $data_center;

	/**
	 * The status of the site.
	 *
	 * @var string
	 */
	public string $status;

	/**
	 * The owner ID of the site.
	 *
	 * @var int
	 */
	public int $owner_id;

	/**
	 * The domain of the site.
	 *
	 * @var string
	 */
	public string $domain;

	/**
	 * The WP Cloud site ID.
	 *
	 * @var int
	 */
	public int $wpcloud_site_id;

	/**
	 * The details of the site.
	 *
	 * @var array
	 */
	public array $details;

	/**
	 * The error message of the site.
	 *
	 * @var string
	 */
	public string $error_message;

	/**
	 * WPCLOUD_Site constructor.
	 */
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
	 * Example options:
	 * $options = [
	 *  'site_name' => 'example',
	 *  'php_version' => '8.4',
	 *  'data_center' => 'cda',
	 *  'site_owner_id' => 1,
	 *  'admin_pass' => 'password123',
	 * ];
	 *
	 * @param array $options The options for the site.
	 * @return WP_Post|WP_Error
	 */
	public static function create( array $options ): mixed {
		$author = get_user_by( 'id', $options['site_owner_id'] ?? 0 );

		if ( ! $author ) {
			return new WP_Error( 'invalid_user', __( 'Invalid user.', 'wpcloud' ) );
		}

		$can_create = apply_filters( 'wpcloud_can_create_site', true, $author, $options );
		if ( ! $can_create ) {
			return new WP_Error( 'unauthorized', __( 'You are not authorized to create a site.', 'wpcloud' ) );
		}

		$php_version = $options['php_version'];
		$data_center = $options['data_center'];
		$site_name   = $options['site_name'];
		$domain      = $options['domain_name'] ?? '';

		if ( empty( $domain ) ) {
			$settings       = get_option( 'wpcloud_settings' );
			$default_domain = $settings['wpcloud_domain'] ?? '';
			if ( $default_domain ) {
				$domain = strtolower( str_replace( ' ', '-', $site_name ) . '.' . $default_domain );
			}
		}

		add_filter(
			'wpcloud_site_create_data',
			function ( $data ) use ( $options, $domain ) {

				$admin_pass = $options['admin_pass'] ?? '';
				if ( $admin_pass ) {
					$data['admin_pass'] = $admin_pass;
				}

				if ( empty( $domain ) ) {
					$data['demo_domain'] = true;
				}

				return $data;
			}
		);

		$post_id = wp_insert_post(
			array(
				'post_title'  => $site_name,
				'post_name'   => $site_name,
				'post_type'   => 'wpcloud_site',
				'post_status' => 'draft',
				'post_author' => $author->ID,
				'meta_input'  => array(
					'php_version'    => $php_version,
					'data_center'    => $data_center,
					'initial_domain' => $domain,
				),
			)
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		return get_post( $post_id );
	}

	/**
	 * Get a WPCLOUD_Site by ID.
	 *
	 * @return array The detail options
	 */
	public static function get_detail_options(): array {
		$options = array(
			'site_name'        => __( 'Site Name' ), // only used locally.
			'site_owner_id'    => __( 'Site Owner ID' ), // only used locally.
			'domain_name'      => __( 'Domain Name' ),
			'site_alias'       => __( 'Site Alias' ),
			'wp_admin_email'   => __( 'Admin Email' ),
			'wp_admin_user'    => __( 'Admin User' ),
			'smtp_pass'        => __( 'SMTP Password' ),
			'geo_affinity'     => __( 'Geo Affinity' ),
			'data_center'      => __( 'Data Center' ),  // We advertise this as data center but it maps to geo_affinity.
			'ip_addresses'     => __( 'IP Addresses' ),
			'wp_version'       => __( 'WP Version' ),
			'php_version'      => __( 'PHP Version' ),
			'static_file_404'  => __( 'Static File 404' ),
			'db_charset'       => __( 'DB Charset' ),
			'db_collate'       => __( 'DB Collate' ),
			'cache_prefix'     => __( 'Cache Prefix' ),
			'chroot_path'      => __( 'Chroot Path' ),
			'chroot_ssh_path'  => __( 'Chroot SSH Path' ),
			'site_api_key'     => __( 'Site API Key' ),
			'atomic_site_id'   => __( 'Atomic Site ID' ),
			'atomic_client_id' => __( 'Atomic Client ID' ),
			'server_pool_id'   => __( 'Server Pool ID' ),
			'phpmyadmin_url'   => __( 'phpMyAdmin URL' ),
			'ssl_info'         => __( 'SSL Info' ),
			'wp_admin_url'     => __( 'WP Admin URL' ),
			'site_ssh_user'    => __( 'Site SSH User' ),
			'edge_cache'       => __( 'Edge Cache' ),
			'defensive_mode'   => __( 'Defensive Mode' ),
			'_data'            => __( 'Site Data' ),

			// These are read only meta fields.
			'max_space_quota'  => __( 'Max Space Quota' ),
			'space_used'       => __( 'Space Used' ),
		);
		return apply_filters( 'wpcloud_site_detail_options', $options );
	}
	/**
	 * Get mutable option keys
	 *
	 * @return array
	 */
	public static function get_mutable_fields(): array {
		$fields = array();
		foreach ( self::get_mutable_options() as $key => $option ) {
			if ( isset( $option['label'] ) ) {
				$fields[ $key ] = $option;
			}
		}
		return $fields;
	}

	/**
	 * Get the options for modifiable site meta for a WPCLOUD_Site.
	 *
	 * See https://wp.cloud/apidocs-webhost/#api-Sites-site-meta
	 * if a key is false, it is only used for getting the value
	 *
	 * @return array
	 */
	public static function get_mutable_options(): array {
		$options = array(
			// db_charset and db_collate should be paired ?
			'db_charset'           => array(
				'label'   => __( 'DB Charset' ),
				'type'    => 'select',
				'options' => array(
					'latin1 ' => 'latin1',
					'utf8'    => 'utf8',
					'utf8mb4' => 'utf8mb4',
				),
				'default' => 'utf8mb4',
				'hint'    => '',
			),

			'db_collate'           => array(
				'label'   => __( 'DB Collate' ),
				'type'    => 'select',
				'options' => array(
					'latin1_swedish_ci'  => 'latin1_swedish_ci',
					'utf8_general_ci'    => 'utf8_general_ci',
					'utf8mb4_unicode_ci' => 'utf8mb4_unicode_ci',
				),
				'default' => 'utf8mb4_unicode_ci',
				'hint'    => '',
			),

			'suspended'            => array(
				'label'   => __( 'Suspended' ),
				'type'    => 'select',
				'options' => array(
					''    => '',
					'404' => __( '404 - Not Found ' ),
					'410' => __( '410 - Gone' ),
					'451' => __( '451 - Unavailable For Legal Reasons' ),
					'480' => __( '480 - Temporarily Unavailable' ),
				),
				'default' => '',
				'hint'    => __( 'Suspends a site. The value is the HTTP 4xx status code the site will respond with. The supported statuses are "404", "410", "451", and "480". Leave blank to unsuspend the site.' ),
			),

			'suspend_after'        => array(
				'label'   => __( 'Suspend After' ),
				'type'    => 'text',
				'options' => null,
				'default' => false,
				'hint'    => __( 'Suspends a site after a specified time. The value is a unix Timestamp.' ),
			),

			'php_version'          => array(
				'label'   => __( 'PHP Version' ),
				'type'    => 'select',
				'options' => wpcloud_client_php_versions_available(),
				'default' => '',
				'hint'    => 'Sets the sites PHP version.',
			),

			'wp_version'           => array(
				'label'   => __( 'WP Version' ),
				'type'    => 'select',
				'options' => array(
					'latest'   => __( 'latest' ),
					'previous' => __( 'previous' ),
					'beta'     => __( 'beta' ),
				),
				'default' => 'latest',
				'hint'    => 'Sets the sites WordPress version.',
			),

			'do_not_delete'        => array(
				'label'   => __( 'Do Not Delete' ),
				'type'    => 'checkbox',
				'default' => false,
				'hint'    => __( 'Prevent a site from begin deleted. This can be useful in some cases. For example, you might wish to preserve a site while it is being reviewed for Terms of Service violations.' ),
			),

			'space_quota'          => array(
				'label'   => __( 'Space Quota' ),
				'type'    => 'text',
				'default' => 0,
				'hint'    => __( 'Sets the space quota for a site. Values should be in gigabytes.' ),
			),

			'photon_subsizes'      => array(
				'label'   => __( 'Photon Subsizes' ),
				'type'    => 'checkbox',
				'default' => false,
				'hint'    => __( 'Controls whether WP skips generating intermediate image files when an image is uploaded. The platform is able to satisfy requests for intermediate image files whether or not they exist, so sites can save disk space by not creating them. When the a site web server receives a request for a non-existent intermediate image file, it proxies the request to Photon which responds with the intermediate image size.' ),
			),

			'privacy_model'        => array(
				'label'   => __( 'Privacy Model' ),
				'type'    => 'select',
				'options' => array(
					''           => '',
					'wp_uploads' => __( 'WP Uploads' ),
				),
				'default' => '',
				'hint'    => __( 'Facilitates protection of site assets. May be set to "wp_uploads" to block logged-out requests for WP uploads. If set, an AT_PRIVACY_MODEL constant will be defined in the PHP environment. Use the "site-wordpress-version" endpoint to set "wp_version".' ),
			),

			'static_file_404'      => array(
				'label'   => __( 'Static File 404' ),
				'type'    => 'select',
				'options' => array(
					'lightweight' => __( 'Lightweight' ),
					'wordpress'   => __( 'WordPress' ),
				),
				'default' => 'wordpress',
				'hint'    => __( 'Set how a site responds to requests for non-existent static files. May be set to "lightweight" for simple, fast web server 404s. May be set to "wordpress" to delegate such requests to WordPress. The current default is "wordpress".' ),
			),

			'default_php_conns'    => array(
				'label'   => __( 'Default PHP Conns' ),
				'type'    => 'select',
				'options' => range( 2, 10 ),
				'default' => 0,
				'hint'    => __( 'May be used to either limit allowed concurrent PHP connections or to increase the default number of concurrent connections a site can use if the web server has spare PHP connections capacity. Clients may set any value for a site between 2 and 10; the platform has more leeway if needed.' ),
			),

			'burst_php_conns'      => array(
				'label'   => __( 'Burst PHP Conns' ),
				'type'    => 'checkbox',
				'default' => false,
				'hint'    => __( 'Enable burst for sites with fewer than 10 default_php_conns. 0 or absent when default_php_conns < 10 means burst is disabled, 1 means burst is enabled.' ),
			),

			'php_fs_permissions'   => array(
				'label'   => __( 'PHP FS Permissions' ),
				'type'    => 'select',
				'options' => array(
					'RW'       => __( 'Read/Write' ),
					'RO'       => __( 'Read Only' ),
					'LOGGEDIN' => __( 'Read only unless logged into WordPress' ),
				),
				'default' => 'RW',
				'hint'    => __( 'Sets the PHP file system permissions. May be set to `Read/Write`, `Read Only`, or `Logged in` for read only unless logged into WordPress.' ),
			),

			'canonicalize_aliases' => array(
				'label'   => __( 'Canonicalize Aliases' ),
				'type'    => 'checkbox',
				'default' => true,
				'hint'    => __( 'May be used to change whether a sites domain aliases redirect (default, "true") to the sites primary domain name or are served directly (when set to "false")' ),
			),

			'site_access_with_ssh' => array(
				'label'   => __( 'Site Access With SSH' ),
				'type'    => 'checkbox',
				'default' => false,
				'hint'    => __( 'Site access is via SFTP by default. Enabling allows access via SSH' ),
			),
			'edge_cache'           => array(
				'label'          => __( 'Edge Cache' ),
				'type'           => 'select',
				'options'        => array(
					'on'    => __( 'On' ),
					'off'   => __( 'Off' ),
					'purge' => __( 'Purge' ),
				),
				'option_aliases' => array(
					'on'  => __( 'Enabled', 'wpcloud' ),
					'off' => __( 'Disabled', 'wpcloud' ),
				),
				'default'        => '',
				'hint'           => __( 'Change the edge cache status. Either `on`, `off` or `purge`' ),
			),
			'defensive_mode'       => array(
				'label'   => __( 'Defensive Mode' ),
				'type'    => 'text',
				'default' => '',
				'hint'    => __( 'Enable or disable defensive mode for a site. The value is in minutes. minimum 30 minutes and a maximum of 7 days' ),
			),
			'_data'                => array(
				'label'   => __( 'Site Data' ),
				'type'    => 'select',
				'default' => '',
				'options' => array(
					''         => '',
					'staging'  => __( 'Staging' ),
					'billing'  => __( 'Billing' ),
					'internal' => __( 'Internal' ),
				),
				'hint'    => __( 'Site data' ),
			),
		);
		return apply_filters( 'wpcloud_site_mutable_options', $options );
	}

	/**
	 * Get the options for linkable site meta for a WPCLOUD_Site.
	 *
	 * @return array
	 */
	public static function get_linkable_detail_options(): array {
		return array_intersect_key( self::get_detail_options(), array_flip( self::LINKABLE_DETAIL_KEYS ) );
	}

	/**
	 * Refresh a linkable detail for a site.
	 *
	 * @param int    $post_id The post ID of the site.
	 * @param string $detail  The detail to refresh.
	 * @return string The refreshed detail.
	 */
	public static function refresh_linkable_detail( $post_id, $detail ): string {
		// If the detail is unknown let the filter `wpcloud_refresh_link` handle it.
		if ( ! in_array( $detail, self::LINKABLE_DETAIL_KEYS, true ) ) {
			return '';
		}

		switch ( $detail ) {
			case 'phpmyadmin_url':
				$site_id        = get_post_meta( $post_id, 'wpcloud_site_id', true );
				$phpmyadmin_url = wpcloud_client_site_phpmyadmin_url( (int) $site_id );
				if ( is_wp_error( $phpmyadmin_url ) ) {
					error_log( 'Error fetching phpMyAdmin URL: ' . $phpmyadmin_url->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					return '';
				}
				return $phpmyadmin_url;
		}
		return '';
	}
}
