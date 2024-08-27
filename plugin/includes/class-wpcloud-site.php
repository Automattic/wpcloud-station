<?php
/**
 * WP Cloud Site.
 *
 * @package wpcloud-station
 */

// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log

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
	 * @param array   $options The options for the site.
	 * @param WP_Post $post The post object for the site.
	 * @return WP_Post|WP_Error
	 */
	public static function create( array $options, WP_Post $post = null ): WP_Post|WP_Error {
		if ( ! $post ) {
			$post = self::create_post( $options );
		}
		if ( is_wp_error( $post ) ) {
			return $post;
		}

		// Unpack the options.
		$php_version = $options['php_version'] ?? get_post_meta( $post->ID, 'php_version', true );
		$data_center = $options['data_center'] ?? get_post_meta( $post->ID, 'data_center', true );
		$admin_pass  = $options['admin_pass'] ?? '';
		$site_name   = $options['site_name'] ?? $post->post_title;
		$domain      = $options['domain_name'] ?? get_post_meta( $post->ID, 'initial_domain', true );

		// Set up site data.
		$data = array(
			'php_version'  => $php_version,
			'geo_affinity' => $data_center,
			'admin_pass'   => $admin_pass,
		);

		// Set up domain.
		if ( empty( $domain ) ) {
			$settings       = get_option( 'wpcloud_settings' );
			$default_domain = $settings['wpcloud_domain'] ?? '';
			if ( $default_domain ) {
				$domain = strtolower( str_replace( ' ', '-', $site_name ) . '.' . $default_domain );
			}
		}
		// Use a demo domain if the domain is still empty.
		if ( empty( $domain ) ) {
			$data['demo_domain'] = true;
		} else {
			$data['domain_name'] = $domain;
		}

		$data = apply_filters( 'wpcloud_site_create_data', $data, $post );

		// Set up default software.
		$wpcloud_settings = get_option( 'wpcloud_settings' );
		$software         = $wpcloud_settings['software'] ?? array();
		$default_theme    = $wpcloud_settings['wpcloud_default_theme'] ?? '';
		if ( ! empty( $default_theme ) ) {
			$software[ $default_theme ] = 'activate';
		}
		$software = apply_filters( 'wpcloud_site_create_software', $software, $post );

		$author = get_user_by( 'id', $post->post_author );
		$result = wpcloud_client_site_create( $author->user_login, $author->user_email, $data, $software );

		if ( is_wp_error( $result ) ) {
			error_log( $result->get_error_message() );
			update_post_meta( $post->ID, 'wpcloud_site_error', $result->get_error_message() );
			return $result;
		}

		update_post_meta( $post->ID, 'wpcloud_site_id', $result->atomic_site_id );

		do_action( 'wpcloud_site_created', $post->ID, $post, $result->atomic_site_id );

		return $post;
	}


	/**
	 * Create a new wpcloud_site custom post type.
	 *
	 * @param array $options The options for the site.
	 * @return WP_Post|WP_Error
	 */
	protected static function create_post( array $options ): WP_Post|WP_Error {
		$author = get_user_by( 'id', $options['site_owner_id'] ?? 0 );

		if ( ! $author ) {
			return new WP_Error( 'invalid_user', __( 'Invalid user.', 'wpcloud' ) );
		}

		$can_create = apply_filters( 'wpcloud_can_create_site', true, $author, $options );

		if ( ! $can_create ) {
			return new WP_Error( 'unauthorized', __( 'You are not authorized to create a site.', 'wpcloud' ) );
		}

		$site_name = $options['site_name'] ?? '';
		// Create the CPT post.
		$post_id = wp_insert_post(
			array(
				'post_title'  => $site_name,
				'post_name'   => $site_name,
				'post_type'   => 'wpcloud_site',
				'post_status' => 'draft',
				'post_author' => $author->ID,
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
			'chroot_ssh_path'  => __( 'Chroot SSH Path' ),
			'site_api_key'     => __( 'Site API Key' ),
			'atomic_site_id'   => __( 'Atomic Site ID' ),
			'atomic_client_id' => __( 'Atomic Client ID' ),
			'server_pool_id'   => __( 'Server Pool ID' ),
			'phpmyadmin_url'   => __( 'phpMyAdmin URL' ),
			'wp_admin_url'     => __( 'WP Admin URL' ),
			'site_ssh_user'    => __( 'Site SSH User' ),
			'edge_cache'       => __( 'Edge Cache' ),
			'defensive_mode'   => __( 'Defensive Mode' ),
			'_data'            => __( 'Site Data' ),

			// These are read only meta fields.
			'max_space_quota'  => __( 'Max Space Quota' ),
			'ssl_status'       => __( 'SSL Status' ),
			'space_used'       => __( 'Space Used' ),
			'db_file_size'     => __( 'DB File Size' ),
			'owner_sites_link' => __( 'Owner Sites Link' ),
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
	 * Get read only option keys
	 */
	public static function get_read_only_fields(): array {
		return array_diff( array_keys( self::get_detail_options() ), array_keys( self::get_mutable_fields() ) );
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
				'options' => array_combine( range( 2, 10 ), range( 2, 10 ) ),
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
	 * Check if the API is connected.
	 *
	 * @return bool True if the API is connected.
	 */
	public static function is_api_connected(): bool {
		$api_health = wpcloud_client_test_status();
		return ! is_wp_error( $api_health );
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
	/**
	 * Get a site detail.
	 *
	 * @param int|WP_Post $post The site post or ID.
	 * @param string      $key The detail key.
	 *
	 * @return mixed The detail value. WP_Error on error.
	 */
	public static function get_detail( int|WP_Post $post, string $key, ): mixed {
		$wpcloud_site_id = wpcloud_get_site_id( $post );
		if ( empty( $wpcloud_site_id ) ) {
			return null;
		}

		$result = '';
		switch ( $key ) {
			case 'owner_sites_link':
				$owner = get_user_by( 'id', get_post_field( 'post_author', $post ) );
				if ( ! $owner ) {
					return '';
				}
				return sprintf( '<a href="/sites?owner=%s">%s</a>', $owner->user_nicename, $owner->display_name );
			case 'phpmyadmin_url':
				$result = wpcloud_client_site_phpmyadmin_url( $wpcloud_site_id );
				return $result;

			case 'ssl_status':
				$details = wpcloud_client_site_details( $wpcloud_site_id );
				if ( is_wp_error( $details ) ) {
					error_log( $details->get_error_message() );
					return '';
				}
				$result = wpcloud_client_site_ssl_info( $details->domain_name );
				if ( is_wp_error( $result ) ) {
						error_log( $result->get_error_message() );
					return '';
				}
				$invalid = $result->broken_record || $result->broken_check;

				if ( $invalid ) {
					return $invalid;
				}
				return 'OK';

			case 'ip_addresses':
				$details = wpcloud_client_site_details( $wpcloud_site_id );
				if ( is_wp_error( $details ) ) {
					error_log( $details->get_error_message() );
					return '';
				}
				$domain = $details->domain_name;
				$result = wpcloud_client_site_ip_addresses( $domain );

				if ( is_wp_error( $result ) ) {
					error_log( $result->get_error_message() );
					return '';
				}
				return $result->suggested ?? $result->ips ?? '';

			case 'site_name':
				return $post->post_title;

			case 'wp_admin_url':
				$result = wpcloud_client_site_details( $wpcloud_site_id, true );
				if ( is_wp_error( $result ) ) {
					error_log( $result->get_error_message() );
					return '';
				}

				return 'https://' . $result->domain_name . '/wp-admin';

			case 'space_used':
				$result = wpcloud_client_get_site_meta( $wpcloud_site_id, 'space_used' );
				if ( is_wp_error( $result ) ) {
					error_log( $result->get_error_message() );
					return '';
				}

				$space_used = ! isset( $result->space_used ) ? 0 : $result->space_used;

				return self::readable_size( (float) $space_used );

			case 'db_file_size':
				$result = wpcloud_client_get_site_meta( $wpcloud_site_id, 'db_file_size' );
				if ( is_wp_error( $result ) ) {
					error_log( $result->get_error_message() );
					return '';
				}
				$db_file_size = ! isset( $result->db_file_size ) ? 0 : $result->db_file_size;
				return self::readable_size( (float) $db_file_size );

			case 'space_quota':
				$result = wpcloud_client_get_site_meta( $wpcloud_site_id, 'space_quota' );
				if ( is_wp_error( $result ) ) {
					error_log( $result->get_error_message() );
					return '';
				}

				return self::readable_size( (float) $result->space_quota );

			case 'site_access_with_ssh':
				$result = wpcloud_client_get_site_meta( $wpcloud_site_id, 'ssh_port' );
				if ( is_wp_error( $result ) ) {
					error_log( $result->get_error_message() );
					return '';
				}
				$ssh_port = $result->ssh_port ?? -1;
				// @TODO: Confirm that this is always the case, it appears that the port will be 2223 for ssh and 2221 for sftp
				return 2223 === $ssh_port;

			case 'edge_cache':
				$result = wpcloud_client_edge_cache_status( $wpcloud_site_id );
				if ( is_wp_error( $result ) ) {
					error_log( $result->get_error_message() );
					return '';
				}
				switch ( $result->status ) {
					case 0:
						return __( 'Disabled', 'wpcloud' );
					case 1:
						return __( 'Enabled', 'wpcloud' );
					case 2:
						return __( 'DDoS', 'wpcloud' );
					default:
						return __( 'Unknown', 'wpcloud' );
				}
				return '';

			case 'defensive_mode':
				$result = wpcloud_client_edge_cache_status( $wpcloud_site_id );

				if ( is_wp_error( $result ) ) {
					error_log( $result->get_error_message() );
					return '';
				}
				return $result->ddos_until ?? '';

			case 'data_center':
				$key = 'geo_affinity';
				// Fallthrough intentional to set the result.
			default:
				$result = wpcloud_client_site_details( $wpcloud_site_id, true );
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}
		if ( 'geo_affinity' === $key ) {
			return $result->extra->server_pool->geo_affinity;
		}

		if ( ! isset( $result->$key ) ) {
			return null;
		}

		return $result->$key;
	}

	/**
	 * Update a site detail.
	 *
	 * @param array $data The data to update.
	 * @return true|WP_Error
	 */
	public static function update_detail( array $data ): true|WP_Error {
		error_log( print_r( $data, true ) );
		$site_id = (int) ( $data['site_id'] ?? 0 );
		if ( ! $site_id ) {
			return new WP_Error( 'invalid_site_id', __( 'Invalid site ID.', 'wpcloud' ) );
		}
		$wpcloud_site_id = (int) ( $data['wpcloud_site_id'] ?? get_post_meta( $site_id, 'wpcloud_site_id', true ) );
		if ( empty( $wpcloud_site_id ) ) {
			return new WP_Error( 'invalid_wpcloud_site_id', __( 'Invalid WP Cloud Site ID.', 'wpcloud' ) );
		}

		$mutable_fields = array_keys( self::get_mutable_fields() );

		$site_mutable_fields = array_filter(
			$data,
			function ( $value, $key ) use ( $mutable_fields ) {
				return in_array( $key, $mutable_fields, true );
			},
			ARRAY_FILTER_USE_BOTH
		);

		$result = null;

		foreach ( $site_mutable_fields as $key => $value ) {
			switch ( $key ) {
				case 'canonical_aliases':
					// canonicalize_aliases doesn't like "truthy" values.
					$value = $value ? 'true' : 'false';
					break;

				case 'suspend_after':
					// Don't set the suspend_after value if it's the same as the current value or is not set.
					$current_suspend_value = self::get_detail( $site_id, 'suspend_after' );
					if ( is_null( $current_suspend_value ) ) {
						$current_suspend_value = '';
					}
					if ( ! is_null( $current_suspend_value ) && $value === $current_suspend_value ) {
						continue 2;
					}
					break;

				case 'space_quota':
					$value = intval( $value ) . 'G';
					break;

				case 'edge_cache':
					$result = wpcloud_client_edge_cache_update( $wpcloud_site_id, $value );
					break;
			}

			if ( is_null( $result ) ) {
				$result = $value ? wpcloud_client_update_site_meta( $wpcloud_site_id, $key, $value ) : wpcloud_client_delete_site_meta( $wpcloud_site_id, $key );
			}

			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}
		return true;
	}

	/**
	 * Check if a site detail should be refreshed.
	 *
	 * @param string $key The detail key.
	 * @return bool True if the detail should be refreshed.
	 */
	public static function should_refresh_detail( string $key ): bool {
		$refresh_keys = array(
			'phpmyadmin_url',
		);

		return in_array( $key, $refresh_keys, true );
	}

	/**
	 * Check if a domain has a valid SSL certificate.
	 *
	 * @param string $domain The domain to check.
	 * @return bool|WP_Error True if the domain has a valid SSL certificate.
	 */
	public static function is_domain_ssl_valid( string $domain ): bool|WP_Error {
		$ssl_status = wpcloud_client_site_ssl_info( $domain );
		if ( is_wp_error( $ssl_status ) ) {
			error_log( $ssl_status->get_error_message() );
			return $ssl_status;
		}
		return ! $ssl_status->broken_record && ! $ssl_status->broken_check;
	}

	/**
	 * Get readable size.
	 *
	 * @param float $bytes The size in bytes.
	 */
	protected static function readable_size( float $bytes = 0 ): string {
		if ( $bytes < 1024 ) {
			return $bytes . 'B';
		}
		$i     = max( 1, floor( log( $bytes, 1024 ) ) );
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		$gigs  = round( $bytes / pow( 1024, $i ), 2 );
		return $gigs . 'G';
	}
}
