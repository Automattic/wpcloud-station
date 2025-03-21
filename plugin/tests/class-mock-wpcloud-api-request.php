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
	 * Success state.
	 *
	 * @var bool|null
	 */
	private ?bool $success = null;

	/**
	 * Result data.
	 *
	 * @var stdClass
	 */
	private stdClass $result;

	/**
	 * Error
	 *
	 * @var WP_Error|null
	 */
	private ?WP_Error $error = null;

	/**
	 * Call the API.
	 *
	 * @param string $path   The path to the API endpoint.
	 * @param string $method The HTTP method to use.
	 * @param array  $body   The data to send to the API.
	 * @return WPCloud_API_Request_Interface|WP_Error The response from the API or a WP_Error object.
	 */
	public function call( string $path, string $method = 'GET', array $body = array() ): WPCloud_API_Request_Interface|WP_Error {
		// This will be replaced by Mockery.
		return new WP_Error( 'not_mocked', 'This method should be mocked' );
	}

	/**
	 * Check if the request was successful.
	 *
	 * @return bool
	 */
	public function is_ok(): bool {
		return $this->success ?? false;
	}

	/**
	 * Get a property from the result.
	 *
	 * @param string $name The property name.
	 * @return mixed The property value.
	 */
	public function __get( string $name ): mixed {
		if ( isset( $this->result->$name ) ) {
			return $this->result->$name;
		}
		return null;
	}

	/**
	 * Get the error
	 *
	 * @return WP_Error
	 */
	public function get_error(): WP_Error {
		if ( ! $this->error ) {
			return new WP_Error( 'no_error', 'No error' );
		}
		return $this->error;
	}

	/**
	 * Get the error message.
	 *
	 * @return string
	 */
	public function get_error_message(): string {
		if ( ! $this->error ) {
			return 'No error';
		}
		return $this->error->get_error_message();
	}

	/**
	 * Constructor.
	 *
	 * @param string $client_name The client name.
	 * @param string $api_key The API key.
	 */
	public function __construct( string $client_name, string $api_key ) {
		// Do nothing.
		$this->result = new stdClass();
	}
}
