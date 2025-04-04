<?php
/**
 * Class WPCloud_API_RequestTest
 *
 * @package wpcloud-station
 */

/**
 * Test case for the WPCloud_API_Request class.
 */
class WPCloud_API_RequestTest extends WP_UnitTestCase {

	/**
	 * The request instance.
	 *
	 * @var WPCloud_API_Request
	 */
	private $request;

	/**
	 * Set up the test.
	 */
	public function setUp(): void {
		parent::setUp();

		// Create a test request instance.
		$this->request = new WPCloud_API_Request( 'test-client', 'test-api-key' );
	}

	/**
	 * Test the constructor.
	 */
	public function test_constructor() {
		// Test with default parameters.
		$request = new WPCloud_API_Request( 'test-client', 'test-api-key' );
		$this->assertInstanceOf( WPCloud_API_Request::class, $request );
	}

	/**
	 * Test the __get method.
	 */
	public function test_get() {
		// Set up a mock response.
		$mock_response = array(
			'response' => array(
				'code' => 200,
			),
			'body'     => '{"success":true,"data":{"id":123,"name":"Test"}}',
		);

		// Mock the wp_remote_request function.
		add_filter(
			'pre_http_request',
			function ( $preempt, $args, $url ) use ( $mock_response ) {
				return $mock_response;
			},
			10,
			3
		);

		// Call the API.
		$this->request->call( 'test-endpoint' );

		// Test getting properties from the result.
		$this->assertTrue( $this->request->is_ok() );
		$this->assertIsObject( $this->request->data );
		$this->assertEquals( 123, $this->request->id );
		$this->assertEquals( 'Test', $this->request->data->name );
		$this->assertNull( $this->request->non_existent_property );

		// Remove the filter.
		remove_all_filters( 'pre_http_request' );
	}

	/**
	 * Test the call method with a successful response.
	 */
	public function test_call_success() {
		// Set up a mock response.
		$mock_response = array(
			'response' => array(
				'code' => 200,
			),
			'body'     => '{"success":true,"message":"OK"}',
		);

		// Mock the wp_remote_request function.
		add_filter(
			'pre_http_request',
			function ( $preempt, $args, $url ) use ( $mock_response ) {
				// Verify the request URL.
				$this->assertStringContainsString( 'https://atomic-api.wordpress.com/api/v1.0/test-endpoint', $url );

				// Verify the request method.
				$this->assertEquals( 'GET', $args['method'] );

				// Verify the request headers.
				$this->assertEquals( 'test-api-key', $args['headers']['auth'] );
				$this->assertEquals( 'test-client', $args['headers']['user-agent'] );
				$this->assertEquals( 'atomic-api.wordpress.com', $args['headers']['host'] );

				return $mock_response;
			},
			10,
			3
		);

		// Call the API.
		$result = $this->request->call( 'test-endpoint' );

		// Verify the result.
		$this->assertSame( $this->request, $result );
		$this->assertTrue( $this->request->is_ok() );
		$this->assertEquals( 'OK', $this->request->result->message );

		// Remove the filter.
		remove_all_filters( 'pre_http_request' );
	}

	/**
	 * Test the call method with a POST request.
	 */
	public function test_call_post() {
		// Set up a mock response.
		$mock_response = array(
			'response' => array(
				'code' => 200,
			),
			'body'     => '{"success":true,"message":"Created"}',
		);

		// Mock the wp_remote_request function.
		add_filter(
			'pre_http_request',
			function ( $preempt, $args, $url ) use ( $mock_response ) {
				// Verify the request URL.
				$this->assertStringContainsString( 'https://atomic-api.wordpress.com/api/v1.0/test-endpoint', $url );

				// Verify the request method.
				$this->assertEquals( 'POST', $args['method'] );

				// Verify the request body.
				$body = $args['body'];
				// $body = json_decode( $args['body'], true )
				$this->assertEquals( 'value', $body['key'] );

				return $mock_response;
			},
			10,
			3
		);

		// Call the API.
		$result = $this->request->call( 'test-endpoint', 'POST', array( 'key' => 'value' ) );

		// Verify the result.
		$this->assertSame( $this->request, $result );
		$this->assertTrue( $this->request->is_ok() );
		$this->assertEquals( 'Created', $this->request->message );

		// Remove the filter.
		remove_all_filters( 'pre_http_request' );
	}

	/**
	 * Test the call method with a WP_Error response.
	 */
	public function test_call_wp_error() {
		// Set up a mock WP_Error.
		$mock_error = new WP_Error( 'http_request_failed', 'Connection failed' );

		// Mock the wp_remote_request function.
		add_filter(
			'pre_http_request',
			function ( $preempt, $args, $url ) use ( $mock_error ) {
				return $mock_error;
			},
			10,
			3
		);

		// Call the API.
		$result = $this->request->call( 'test-endpoint' );

		// Verify the result.
		$this->assertInstanceOf( WP_Error::class, $result->error );
		$this->assertEquals( 'http_request_failed', $result->error->get_error_code() );
		$this->assertEquals( 'Connection failed', $result->get_error_message() );
		$this->assertFalse( $this->request->is_ok() );

		// Remove the filter.
		remove_all_filters( 'pre_http_request' );
	}

	/**
	 * Test the call method with an error response code.
	 */
	public function test_call_error_response_code() {
		// Set up a mock response with an error code.
		$mock_response = array(
			'response' => array(
				'code' => 404,
			),
			'body'     => '{"error":"not_found","message":"Resource not found"}',
		);

		// Mock the wp_remote_request function.
		add_filter(
			'pre_http_request',
			function ( $preempt, $args, $url ) use ( $mock_response ) {
				return $mock_response;
			},
			10,
			3
		);

		// Call the API.
		$result = $this->request->call( 'test-endpoint' );

		// Verify the result.
		$this->assertInstanceOf( WP_Error::class, $result->error );
		$this->assertEquals( 404, $result->error->get_error_code() );
		$this->assertEquals( 'Resource not found', $result->get_error_message() );
		$this->assertFalse( $this->request->is_ok() );

		// Remove the filter.
		remove_all_filters( 'pre_http_request' );
	}

	/**
	 * Test the call method with an error response code but no error details.
	 */
	public function test_call_error_response_code_no_details() {
		// Set up a mock response with an error code but no error details.
		$mock_response = array(
			'response' => array(
				'code' => 500,
			),
			'body'     => '{}',
		);

		// Mock the wp_remote_request function.
		add_filter(
			'pre_http_request',
			function ( $preempt, $args, $url ) use ( $mock_response ) {
				return $mock_response;
			},
			10,
			3
		);

		// Call the API.
		$result = $this->request->call( 'test-endpoint' );

		// Verify the result.
		$this->assertInstanceOf( WP_Error::class, $result->error );
		$this->assertEquals( 500, $result->error->get_error_code() );
		$this->assertEquals( 'Unknown error', $result->get_error_message() );
		$this->assertFalse( $this->request->is_ok() );

		// Remove the filter.
		remove_all_filters( 'pre_http_request' );
	}

	/**
	 * Test the call method with a JSON decode error.
	 */
	public function test_call_json_decode_error() {
		// Set up a mock response with invalid JSON.
		$mock_response = array(
			'response' => array(
				'code' => 200,
			),
			'body'     => '{invalid json}',
		);

		// Mock the wp_remote_request function.
		add_filter(
			'pre_http_request',
			function ( $preempt, $args, $url ) use ( $mock_response ) {
				return $mock_response;
			},
			10,
			3
		);

		// Call the API.
		$result = $this->request->call( 'test-endpoint' );

		// Verify the result.
		$this->assertInstanceOf( WP_Error::class, $result->error );
		$this->assertEquals( 'json_error', $result->error->get_error_code() );
		$this->assertStringContainsString( 'JSON error:', $result->get_error_message() );
		$this->assertFalse( $this->request->is_ok() );

		// Remove the filter.
		remove_all_filters( 'pre_http_request' );
	}

	/**
	 * Test the is_ok method.
	 */
	public function test_is_ok() {
		// Set up a mock response.
		$mock_response = array(
			'response' => array(
				'code' => 200,
			),
			'body'     => '{"success":true}',
		);

		// Mock the wp_remote_request function.
		add_filter(
			'pre_http_request',
			function ( $preempt, $args, $url ) use ( $mock_response ) {
				return $mock_response;
			},
			10,
			3
		);

		// Call the API.
		$this->request->call( 'test-endpoint' );

		// Verify is_ok returns true for a successful request.
		$this->assertTrue( $this->request->is_ok() );

		// Remove the filter.
		remove_all_filters( 'pre_http_request' );

		// Set up a mock error response.
		$mock_error_response = array(
			'response' => array(
				'code' => 404,
			),
			'body'     => '{"error":"not_found"}',
		);

		// Mock the wp_remote_request function.
		add_filter(
			'pre_http_request',
			function ( $preempt, $args, $url ) use ( $mock_error_response ) {
				return $mock_error_response;
			},
			10,
			3
		);

		// Call the API.
		$this->request->call( 'test-endpoint' );

		// Verify is_ok returns false for an error response.
		$this->assertFalse( $this->request->is_ok() );

		// Remove the filter.
		remove_all_filters( 'pre_http_request' );
	}
}
