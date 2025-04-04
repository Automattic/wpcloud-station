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
	 * The global request mock instance.
	 *
	 * @var Mock_WPCloud_API_Request|null
	 */
	public static $request_mock = null;

	/**
	 * Get the global request mock instance.
	 *
	 * @return Mock_WPCloud_API_Request The global request mock instance.
	 */
	public static function get_request_mock() {
		if ( ! self::$request_mock ) {
			self::$request_mock = Mockery::mock( 'Mock_WPCloud_API_Request', array( 'test_client', 'test_api_key' ) )->makePartial();
			self::$request_mock->shouldAllowMockingMethod( '__get' );
		}
		return self::$request_mock;
	}

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
	 * Gets the global API mock object.
	 *
	 * @return Mock_WPCloud_API_Request|null The global API mock object.
	 */
	public static function get_api_mock() {
		global $mock_api_request;
		return $mock_api_request;
	}

	/**
	 * Creates a mock for WPCloud_API_Request with a default return value.
	 *
	 * @param string       $path The path to the API endpoint.
	 * @param array|object $response The response to return.
	 * @param int|null     $times The number of times the method should be called.
	 */
	public static function mock_api_request( string $path = '', array|object $response = array(), ?int $times = null ) {
		if ( ! self::$request_mock ) {
			self::$request_mock = Mockery::mock( 'Mock_WPCloud_API_Request', array( 'test_client', 'test_api_key' ) )->makePartial();
			self::$request_mock->shouldAllowMockingMethod( '__get' );
		}

		if ( is_array( $response ) ) {
			$response = (object) $response;
		}

		self::$request_mock->mock_api_request( $path, $response );
	}

	/**
	 * Clear all mocked responses.
	 */
	public static function clear_responses() {
		self::$mocked_responses = array();
	}

	/**
	 * Resets all Mockey mocks.
	 */
	public static function shutdown() {
		self::$mocked_responses = array();
		Mockery::close();
	}
}
