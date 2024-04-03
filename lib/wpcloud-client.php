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
		return new WP_Error( 'unauthorized', 'Please provide a WP Cloud API Key in Settings' );
	}
	if ( empty( $client_name ) ) {
		return new WP_Error( 'unauthorized', 'Please provide a WP Cloud Client Name in Settings' );
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
			'user-agent' => 'wpcloud-dashboard',
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
			return new WP_Error( 'method_not_supported', 'Request method must be GET or POST' );
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
			$result = new WP_Error( 'bad_request', $response_message );
			break;
		case 401:
			$result = new WP_Error( 'unauthorized', 'Request is unauthorized' );
			break;
		case 403:
			$result = new WP_Error( 'forbidden', 'Request is not allowed' );
			break;
		case 404:
			$result = new WP_Error( 'not_found', 'Resource not found' );
			break;
		case 409:
			$message = 'Request conflicts with expectations';
			if ( ! empty( $response_message ) ) {
				$message = $response_message;
			}
			$result = new WP_Error( 'conflict', $message );
			break;
		case 500:
			$message = 'Internal server error while executing the request';
			if ( ! empty( $response_message ) ) {
				$message = $response_message;
			}
			$result = new WP_Error( 'internal_server_error', $message );
			break;
		case 501:
			$result = new WP_Error( 'not_implemented', 'Request is not implemented' );
			break;
		case 504:
			$result = new WP_Error( 'timeout', 'Request did not complete in the allowed time period' );
			break;
		default:
			$message = 'Error while executing the request';
			if ( ! empty( $response_message ) ) {
				$message = $response_message;
			}
			$result = new WP_Error( $response_code, $message );
	}

	if ( is_wp_error( $result ) ) {
		/**
		 * Action triggered when an error occurs during a WP Cloud API request.
		 *
		 * @param int            The WP Cloud Site ID.
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
	 * @param int            The WP Cloud Site ID.
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
