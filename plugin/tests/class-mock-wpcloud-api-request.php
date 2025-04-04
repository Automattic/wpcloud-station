<?php
/**
 * Mock WP Cloud API Request.
 *
 * @package wpcloud-client
 */

declare( strict_types = 1 );
require_once dirname( __DIR__ ) . '/includes/class-wpcloud-api-request.php';
require_once dirname( __DIR__ ) . '/includes/interface-wpcloud-api-request.php';

/**
 * Mock WP Cloud API Request.
 */
class Mock_WPCloud_API_Request extends WPCloud_API_Request implements WPCloud_API_Request_Interface {


	/**
	 * Mocked responses.
	 *
	 * @var stdClass|null
	 */
	public array $mocked_responses;

	/**
	 * Constructor.
	 *
	 * @param string $client_name The client name.
	 * @param string $api_key The API key.
	 */
	public function __construct( string $client_name, string $api_key ) {
		parent::__construct( $client_name, $api_key );
		$this->mocked_responses = array();
	}

	/**
	 * Call the API.
	 *
	 * @param string $path   The path to the API endpoint.
	 * @param string $method The HTTP method to use.
	 * @param array  $body   The data to send to the API.
	 * @return WPCloud_API_Request_Interface|WP_Error The response from the API or a WP_Error object.
	 */
	public function call( string $path, string $method = 'GET', array $body = array() ): WPCloud_API_Request_Interface {

		wpcloud_l( 'Mocking API call: ' . $path );
		// This will be replaced by Mockery but let's just make sure we don't make any network requests.
		foreach ( $this->mocked_responses as $mock_path => $response ) {
			if ( $path === $mock_path ) {
				wpcloud_l( 'Mocking API response: ' . $path );
				$this->result = (object) $response;
				if ( isset( $this->result->data ) ) {
					$this->data = $this->result->data;
				} else {
					$this->data = new stdClass();
				}
				$this->did_succeed = true;
				return $this;
			}
		}
		return $this;
	}

	/**
	 * Set the mock result.
	 *
	 * @param object $result The result to set.
	 * @return void
	 */
	public function set_mock_result( object $result ): void {
		$this->result = $result;
	}

	/**
	 * Mock an api request
	 *
	 * @param string       $path The path to the API endpoint.
	 * @param array|object $response The response to return.
	 */
	public function mock_api_request( string $path = '', array|object $response = array() ) {
		$this->mocked_responses[ $path ] = $response;
	}

	/**
	 * Set mock api requests
	 *
	 * @param array $mocks An array of expected first parameters and their return values.
	 */
	public function mock_api_requests( array $mocks ) {
		foreach ( $mocks as $path => $response ) {
			$this->set_mock_result( (object) $response );
		}
	}

	/**
	 * Mock all requests as ok.
	 */
	public function is_ok(): bool {
		return true;
	}
}
