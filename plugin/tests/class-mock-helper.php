<?php
/**
 * Helper class for creating mocks.
 *
 * @package wpcloud-station
 */

/**
 * Helper class for creating mocks.
 */
class Mock_Helper {
	/**
	 * Store mocked paths and responses
	 *
	 * @var array
	 */
	private static $mocked_responses = array();

	/**
	 * Creates a mock for WPCloud_API_Request with a default return value.
	 *
	 * @param array $mocks An array of expected first parameters and their return values.
	 */
	public static function mock_api_requests( array $mocks ) {
		foreach ( $mocks as $path => $response ) {
			self::mock_api_request( $path, $response );
		}
	}

	/**
	 * Creates a mock for WPCloud_API_Request with a default return value.
	 *
	 * @param string       $path The path to the API endpoint.
	 * @param array|object $response The response to return.
	 * @param int|null     $times The number of times the method should be called.
	 */
	public static function mock_api_request( string $path = '', array|object $response = array(), ?int $times = null ) {
		// Store the path and response in our static array.
		self::$mocked_responses[ $path ] = $response;

		// Create a mock of our Mock_WPCloud_API_Request class.
		$mock = Mockery::mock( 'Mock_WPCloud_API_Request', array( 'test_client', 'test_api_key' ) )->makePartial();

		$expectation = $mock->shouldReceive( 'call' )
		->withAnyArgs()
		->andReturnUsing(
			function ( ...$args ) {
				$arg0 = reset( $args );

				wpcloud_l( 'Mocked API request: ' . $arg0 );

				// Check if we have a mock for this path.
				foreach ( Mock_Helper::$mocked_responses as $mocked_path => $mocked_response ) {
					if ( $arg0 === $mocked_path ) {
						wpcloud_l( 'Mocked API response for ' . $mocked_path );
						return $mocked_response;
					}
				}

				return (object) array(
					'success' => true,
					'mocked'  => true,
					'message' => 'No mock match',
				);
			}
		);

		if ( ! is_null( $times ) ) {
			$expectation = $expectation->times( $times );
		}

		// Store the mock globally so it can be used by WPCloud_API_Client.
		global $mock_api_request;
		$mock_api_request = $mock;
	}

	/**
	 * Clear all mocked responses.
	 */
	public static function clear_responses() {
		self::$mocked_responses = array();
	}

	/**
	 * Resets all Mockery mocks.
	 */
	public static function shutdown() {
		self::$mocked_responses = array();
		Mockery::close();
	}
}
