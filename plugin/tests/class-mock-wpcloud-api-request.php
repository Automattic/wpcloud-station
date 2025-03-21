<?php
/**
 * Mock WP Cloud API Request.
 *
 * @package wpcloud-client
 */

declare( strict_types = 1 );


require_once dirname( __DIR__ ) . '/includes/interface-wpcloud-api-request.php';

/**
 * Mock WP Cloud API Request.
 */
class Mock_WPCloud_API_Request implements WPCloud_API_Request_Interface {
	/**
	 * Call the API.
	 *
	 * @param string $path   The path to the API endpoint.
	 * @param string $method The HTTP method to use.
	 * @param array  $body   The data to send to the API.
	 * @return mixed|WP_Error The response from the API or a WP_Error object.
	 */
	public function call( string $path, string $method = 'GET', array $body = array() ): array|stdClass|WP_Error {
		// This will be replaced by Mockery.
		return new WP_Error( 'not_mocked', 'This method should be mocked' );
	}

	/**
	 * Constructor.
	 *
	 * @param string $client_name The client name.
	 * @param string $api_key The API key.
	 */
	public function __construct( string $client_name, string $api_key ) {
		// Do nothing.
	}
}
