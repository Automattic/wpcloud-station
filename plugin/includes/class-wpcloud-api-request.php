<?php
/**
 * WP Cloud API Request.
 *
 * @package wpcloud-client
 */

declare( strict_types = 1 );

/**
 * WP Cloud API Request.
 */
class WPCloud_API_Request implements WPCloud_API_Request_Interface {

	/**
	 * The client name.
	 *
	 * @var string
	 */
	private string $client_name;

	/**
	 * The API key.
	 *
	 * @var string
	 */
	private string $api_key;


	/**
	 * The result.
	 *
	 * @var stdClass
	 */
	private stdClass $result;


	/**
	 * Success state.
	 *
	 * @var bool|null
	 */
	private ?bool $did_succeed = null;

	/**
	 * Constructor.
	 *
	 * @param string $client_name The client name.
	 * @param string $api_key The API key.
	 */
	public function __construct( string $client_name, string $api_key ) {
		$this->client_name = $client_name;
		$this->api_key     = $api_key;
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
	 * Call the API.
	 *
	 * @param string $path The path to the API endpoint.
	 * @param string $method The HTTP method to use.
	 * @param array  $body The data to send to the API.
	 * @return mixed|WP_Error The response from the API or a WP_Error object.
	 */
	public function call( string $path, string $method = 'GET', array $body = array() ): array|stdClass|WP_Error {
		$host = 'atomic-api.wordpress.com';
		$path = ltrim( $path, '/' );
		$url  = "https://$host/api/v1.0/$path";

		// Set up the request arguments.
		$args = array(
			'method'  => $method,
			'headers' => array(
				'auth'       => $this->api_key,
				'user-agent' => $this->client_name,
				'host'       => $host,
			),
			'timeout' => 10,
		);

		// Add the body if it's not empty.
		if ( ! empty( $body ) ) {
			$args['body'] = wp_json_encode( $body );
		}
		// Make the request.
		$response = wp_remote_request( $url, $args );

		// Check for errors.
		if ( is_wp_error( $response ) ) {
			return $this->fail( $response );
		}

		// Get the response code.
		$response_code = wp_remote_retrieve_response_code( $response );

		// Check for error response codes.
		if ( $response_code >= 400 ) {
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body );
			if ( $data && isset( $data->error ) ) {
				return $this->fail( $data->error, $data->message ?? 'Unknown error' );
			}
			return $this->fail( 'api_error', 'API error: ' . $response_code );
		}

		// Get the response body.
		$body = wp_remote_retrieve_body( $response );

		// Decode the response.
		$this->result = json_decode( $body );

		// Check for JSON errors.
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return $this->fail( 'json_error', 'JSON error: ' . json_last_error_msg() );
		}

		$this->did_succeed = true;

		return self;
	}

	/**
	 * Check if the request was successful.
	 *
	 * @return bool
	 */
	public function is_ok(): bool {
		return $this->did_succeed ?? false;
	}

	/**
	 * Return failure.
	 *
	 * @param mixed ...$args The error arguments.
	 * @return WP_Error The error object.
	 */
	private function fail( mixed ...$args ): WP_Error {
		$this->did_succeed = false;
		if ( is_wp_error( $args[0] ) ) {
			return $args[0];
		}
		return new WP_Error( $args[0], $args[1] ?? 'Unknown error', $args[2] ?? null );
	}
}
