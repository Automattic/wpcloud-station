<?php
/**
 * WP Cloud API client library.
 *
 * @package wpcloud-dashboard
 */

declare( strict_types = 1 );

/**
 * Get WP Cloud Client Name from settings.
 *
 * @return string|null Client Name on success. WP_Error on error.
 */
function wpcloud_get_client_name(): mixed {
	$wpcloud_settings = get_option( 'wpcloud_settings' );

	if ( ! $wpcloud_settings ) {
		return null;
	}

    return $wpcloud_settings['wpcloud_client'];
}

/**
 * Get WP Cloud API Key from settings.
 *
 * @return string|null Client API Key on success. WP_Error on error.
 */
function wpcloud_get_client_api_key(): mixed {
	$wpcloud_settings = get_option( 'wpcloud_settings' );

	if ( ! $wpcloud_settings ) {
		return null;
	}

    return $wpcloud_settings['wpcloud_api_key'];
}

/**
 * Validate whether a domain is in use on WP Cloud.
 *
 * @param string $domain The name of the domain to validate.
 *
 * @return bool|WP_Error True if domain can be used. False if domain is currently in use. WP_Error on error.
 */
function wpcloud_client_domain_validate( string $domain ): mixed {
    $client_name = wpcloud_get_client_name();

	$response = wpcloud_client_get( null, "check-can-host-domain/{$client_name}/{$domain}" );

	// If an error is returned, check if it is for existing domain mapping.
	// If so, provide a friendly response with information about next steps.
	// Otherwise, return the error.
	if ( is_wp_error( $response ) ) {
		if ( ! strpos( $response->get_error_message(), 'Domain mapping record already exists' ) ) {
			$domain_verification_record = wpcloud_client_domain_verification_record( $domain );

			return new WP_Error(
				'conflict',
				'Domain mapping already exists. To map domain to new site, add the domain verification record to DNS TXT records to provide proof of ownership.',
				array(
					'domain-verification-record' => $domain_verification_record,
					'status'                     => 409
				)
			);
		}

		return $response;
	}

	return (bool) $response->allowed;
}

/**
 * Retrieve the domain verification record for a domain.
 *
 * @param string $domain The domain for which to get the domain verification record.
 *
 * @return string|WP_Error Domain verification record. WP_Error on error.
 */
function wpcloud_client_domain_verification_record( string $domain ): mixed {
    $client_name = wpcloud_get_client_name();

	return wpcloud_client_get( null, "get-domain-verification-code/{$client_name}/{$domain}" );
}

/**
 * Create a new site.
 *
 * When a new site is created, it returns a Job ID for the site provisioning job which can be checked for completion.
 *
 * @param string  $domain         The domain name for the site.
 * @param string  $admin_user     The WordPress admin username for the new site.
 * @param string  $admin_email    The WordPress admin email for the new site.
 * @param array   $data           Optional. An array of optional site creation parameters. Default: array()
 *                                          admin_pass    The password for the admin user.
 *                                          clone_from    WP Cloud Site ID of the site from which to clone.
 *                                          db_charset    The database character set.
 *                                                        Options:
 *                                                            latin1
 *                                                            utf8
 *                                                            utf8mb4
 *                                                        Default: latin1
 *                                          db_collate    The database collation.
 *                                                        Options:
 *                                                            latin1_swedish_ci
 *                                                            utf8_general_ci
 *                                                            utf8mb4_general_ci
 *                                                        Default: Corresponds to the appropriate collation for the character set.
 *                                          geo_affinity  if specified, the site will be assigned a primary pool server based on the preferred datacenter.
 *                                                        Options:
 *                                                            ams Amsterdam
 *                                                            bur Burbank
 *                                                            dca Washington DC
 *                                                            dfw Dallas/Ft. Worth
 *                                          php_version   The desired PHP version.
 *                                                        Options:
 *                                                            7.4
 *                                                            8.1
 *                                                            8.2
 *                                                            8.3
 *                                                        Default: 8.1
 *                                                        Note: PHP version options are subject to change as new versions are released and old version retired.
 *                                          space_quota   The desired space quota. Default: 200G
 * @param array   $software       Optional. An array of plugins and themes with actions to install and/or activate on site creation. Default: array()
 * @param array   $meta           Optional. An array of keys and values for meta to be set for a site. Default: array()
 *                                Supported keys:
 *                                    development_mode
 *                                    privacy_model
 *                                    photon_subsizes
 *                                    static_file_404
 *
 * @return object|WP_Error Create site job details. WP_Error on error.
 */
function wpcloud_client_site_create( string $domain, string $admin_user, string $admin_email, array $data = array(), array $software = array(), array $meta = array() ): mixed {
    $client_name = wpcloud_get_client_name();

	// First validate if the domain can be hosted
	$validate_domain = wpcloud_client_domain_validate( $domain );
	if ( is_wp_error( $validate_domain ) ) {
		return $validate_domain;
	} elseif ( ! $validate_domain ) {
		$result = new WP_Error( 'forbidden', 'Domain cannot be hosted' );
	}

	$data = array_merge(
		$data,
		array(
			'domain_name' => $domain,
			'admin_user'  => $admin_user,
			'admin_email' => $admin_email,
			'software'    => $software,
			'meta'        => $meta,
		),
	);

	return wpcloud_client_post( null, "create-site/{$client_name}", $data );
}

function wpcloud_client_site_delete( int $wpcloud_site_id ): mixed {
  $client_name = wpcloud_get_client_name();

	return wpcloud_client_post( $wpcloud_site_id, "delete-site/{$client_name}/{$wpcloud_site_id}" );
}

/**
 * Delete a site.
 *
 * @param int  $wpcloud_site_id The WP Cloud Site ID.
 *
 * @return object|WP_Error Delete site job detals. WP_Error on error.
 */
function wpcloud_client_site_delete( int $wpcloud_site_id ): mixed {
    $client_name = wpcloud_get_client_name();

	return wpcloud_client_post( $wpcloud_site_id, "delete-site/{$client_name}/{$wpcloud_site_id}" );
}

/**
 * Get details of a site.
 *
 * @param int  $wpcloud_site_id The WP Cloud Site ID.
 * @param bool $extra           Include extra details.
 *
 * @return object|WP_Error Site details. WP_Error on error.
 */
function wpcloud_client_site_details( int $wpcloud_site_id, bool $extra = false ): mixed {
	$path = "get-site/{$wpcloud_site_id}";

	if ( $extra ) {
		$path .= '/extra';
	}

	return wpcloud_client_get( $wpcloud_site_id, $path );
}

/**
 * Manages plugins/themes on WP Cloud site.
 *
 * Accepts an associative array of plugins/themes and their status.
 * Keys:
 *  - plugins/<plugin-slug>[/<version>] // Optional version. Discouraged. Defaults to latest.
 *  - themes/<theme-slug>
 * Possible statuses:
 *  - 'activate'   // Install if missing and activate.
 *  - 'install'    // Just install, don't activate.
 *  - 'deactivate' // Deactivate if active, but do not remove.
 *  - 'remove'     // Deactivate if active, and remove.
 *
 * Example:
 * $software = [
 *     'plugins/hello-dolly'                => 'remove',
 *     'themes/pub/akuta'                   => 'deactivate',
 *     'plugins/wordpress-seo/2.4.2'        => 'install',
 *     'themes/premium/caldwell'            => 'activate',
 * ];
 *
 * @param int   $wpcloud_site_id The WP Cloud Site ID.
 * @param array $software        Array of plugins and themes.
 *
 * @return object|WP_Error Job ID on success. WP_Error on error.
 */
function wpcloud_client_site_manage_software( int $wpcloud_site_id, array $software ): mixed {
    $client_name = wpcloud_get_client_name();

	return woa_client_post( $wpcloud_site_id, "site-manage-software/{$client_name}/{$wpcloud_site_id}", $software );
}

/**
 * Get a list of sites for the client.
 *
 * @param string[] ...$meta_keys One or more meta keys to include in response.
 *                               Supported: wp_version, php_version, space_quota, db_file_size, static_file_404, suspended
 *
 * @return array|WP_Error Site status details or error.
 */
function wpcloud_client_site_list( string ...$meta_keys ): mixed {
    $client_name = wpcloud_get_client_name();
    $path        = "get-sites/{$client_name}/";

    foreach ( $meta_keys as $meta_key ) {
		$path .= "{$meta_key}/";
	}

	return wpcloud_client_get( null, $path );
}

/**
 * Make a GET request the WP Cloud API.
 *
 * @param int|null $wpcloud_site_id The WP Cloud Site ID.
 * @param string   $path            The request path without host. e.g. 'get-site/example.com'.
 *
 * @return mixed|WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_get( ?int $wpcloud_site_id, string $path ): mixed {
	return wpcloud_client_request( $wpcloud_site_id, 'GET', $path );
}

/**
 * Make a POST request the WP Cloud API.
 *
 * @param int|null $wpcloud_site_id The WP Cloud Site ID.
 * @param string   $path            The request path without host. e.g. 'get-site/example.com'.
 * @param array    $body            The body of the request as an array.
 *
 * @return mixed|WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_post( ?int $wpcloud_site_id, string $path, array $body = array() ): mixed {
	return wpcloud_client_request( $wpcloud_site_id, 'POST', $path, $body );
}

/**
 * Make a request to WP Cloud API.
 *
 * @param int|null $wpcloud_site_id The WP Cloud Site ID.
 * @param string   $method          HTTP Request method. 'GET' or 'POST'.
 * @param string   $path            The request path without host. e.g. 'get-site/example.com'.
 * @param array    $body            The body of the request as an array.
 *
 * @return mixed|WP_Error Response body on success. WP_Error on failure.
 */
function wpcloud_client_request( ?int $wpcloud_site_id, string $method, string $path, array $body = array() ): mixed {
    $api_key     = wpcloud_get_client_api_key();
    $client_name = wpcloud_get_client_name();

	if ( empty( $api_key ) ) {
		return new WP_Error( 'unauthorized', 'Please provide a WP Cloud API Key in Settings', array( 'status' => 401 ) );
	}
	if ( empty( $client_name ) ) {
		return new WP_Error( 'unauthorized', 'Please provide a WP Cloud Client Name in Settings', array( 'status' => 401 ) );
	}

	$scheme   = 'https';
	$hostname = 'atomic-api.wordpress.com';
	$url      = "{$scheme}://{$hostname}/api/v1.0/{$path}";

	$args = array(
		'redirection' => 0,
		'timeout'     => 5,
		'body'        => $body,
		'headers'     => array(
			'auth'       => $api_key,
			'user-agent' => $client_name,
			'host'       => $hostname,
		),
	);

	switch ( $method ) {
		case 'GET':
			$response = wp_remote_get( $url, $args );
			break;
		case 'POST':
			$response = wp_remote_post( $url, $args );
			break;
		default:
			return new WP_Error( 'method_not_supported', 'Request method must be GET or POST', array( 'status' => 400 ) );
	}

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$response_code    = wp_remote_retrieve_response_code( $response );
	$response_body    = wp_remote_retrieve_body( $response );
	$result           = json_decode( $response_body );
	$response_message = $result->message;

	switch ( $response_code ) {
		case 200:
		case 201:
		case 202:
		case 204:
			break;
		case 400:
			$message = 'Request was not properly formed';
			if ( ! empty( $response_message ) ) {
				$message = $response_message;
			}
			$result = new WP_Error( 'bad_request', $response_message, array( 'status' => 400 ) );
			break;
		case 401:
			$result = new WP_Error( 'unauthorized', 'Request is unauthorized', array( 'status' => 401 ) );
			break;
		case 403:
			$result = new WP_Error( 'forbidden', 'Request is not allowed', array( 'status' => 403 ) );
			break;
		case 404:
			$result = new WP_Error( 'not_found', 'Resource not found', array( 'status' => 404 ) );
			break;
		case 409:
			$message = 'Request conflicts with expectations';
			if ( ! empty( $response_message ) ) {
				$message = $response_message;
			}
			$result = new WP_Error( 'conflict', $message, array( 'status' => 409 ) );
			break;
		case 500:
			$message = 'Internal server error while executing the request';
			if ( ! empty( $response_message ) ) {
				$message = $response_message;
			}
			$result = new WP_Error( 'internal_server_error', $message, array( 'status' => 500 ) );
			break;
		case 501:
			$result = new WP_Error( 'not_implemented', 'Request is not implemented', array( 'status' => 501 ) );
			break;
		case 504:
			$result = new WP_Error( 'timeout', 'Request did not complete in the allowed time period', array( 'status' => 504 ) );
			break;
		default:
			$message = 'Error while executing the request';
			if ( ! empty( $response_message ) ) {
				$message = $response_message;
			}
			$result = new WP_Error( $response_code, $message, array( 'status' => 500 ) );
	}

	if ( is_wp_error( $result ) ) {
		/**
		 * Action triggered when an error occurs during a WP Cloud API request.
		 *
		 * @param int|null       The WP Cloud Site ID.
 		 * @param string         HTTP Request method. 'GET' or 'POST'.
 		 * @param string         The request path without host. e.g. 'get-site/example.com'.
		 * @param array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'. WP_Error on failure.
		 */
		do_action( 'wpcloud_client_response_error', $wpcloud_site_id, $method, $path, $response );

		return $result;
	}

	/**
	 * Action triggered on successful WP Cloud API request.
	 *
	 * @param int|null       The WP Cloud Site ID.
 	 * @param string         HTTP Request method. 'GET' or 'POST'.
 	 * @param string         The request path without host. e.g. 'get-site/example.com'.
	 * @param array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'. WP_Error on failure.
	 */
	do_action( 'wpcloud_client_response_success', $wpcloud_site_id, $method, $path, $response );

	// Return data if provided
	if ( is_object( $result ) && isset( $result->data ) ) {
		return $result->data;
	}

	return $result;
}
