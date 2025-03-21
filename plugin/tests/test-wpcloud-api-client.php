<?php
/**
 * Class WPCloud_API_ClientTest
 *
 * @package wpcloud-station
 */

use Mockery\Mock;

/**
 * Test case for the WPCloud_API_Client class.
 */
class WPCloud_API_ClientTest extends WP_UnitTestCase {

	/**
	 * The client instance.
	 *
	 * @var WPCloud_API_Client
	 */
	private $client;

	/**
	 * Set up the test.
	 */
	public function setUp(): void {
		parent::setUp();

		// Create a test client instance.
		$this->client = new WPCloud_API_Client();
	}

	/**
	 * Test the constructor.
	 */
	public function test_constructor() {
		// Test with default parameters.
		$client = new WPCloud_API_Client();
		$this->assertInstanceOf( WPCloud_API_Client::class, $client );

		// Test with use_cache = false.
		$client = new WPCloud_API_Client( false );
		$this->assertInstanceOf( WPCloud_API_Client::class, $client );

		// Test with settings.
		add_filter(
			'pre_option_wpcloud_settings',
			function () {
				return array(
					'wpcloud_client' => 'test-client',
					'wpcloud_key'    => 'test-key',
					'wpcloud_cache'  => true,
				);
			}
		);
		$client = new WPCloud_API_Client();
		$this->assertInstanceOf( WPCloud_API_Client::class, $client );
	}

	/**
	 * Test the set_domain_name method.
	 */
	public function test_set_domain_name() {
		$result = $this->client->set_domain_name( 'example.com' );
		$this->assertInstanceOf( WPCloud_API_Client::class, $result );
	}

	/**
	 * Test the get method.
	 */
	public function test_get() {
		// Test with cache.
		$client = new WPCloud_API_Client( use_cache: true );

		$result = $client->get( 'test-endpoint' );
		$this->assertNotNull( $result );
	}

	/**
	 * Test the post method.
	 */
	public function test_post() {
		// Set up the client.
		$client = new WPCloud_API_Client( 123 );

		$result = $client->post( 'test-endpoint', array( 'key' => 'value' ) );
		$this->assertNotNull( $result );
	}

	/**
	 * Test the parse_path method.
	 */
	public function test_parse_path() {
		// Set up the client.
		add_filter(
			'wpcloud_client_name',
			function () {
				return 'test-client';
			}
		);
		$client = new WPCloud_API_Client();

		// Use reflection to access the private method.
		$reflection = new ReflectionClass( $client );
		$method     = $reflection->getMethod( 'parse_path' );
		$method->setAccessible( true );

		// Test with no arguments.
		$result = $method->invokeArgs( $client, array( 'test-endpoint', array() ) );
		$this->assertEquals( 'test-endpoint', $result );

		// Test with arguments.
		$result = $method->invokeArgs( $client, array( 'test-endpoint', array( 'arg1', 'arg2' ) ) );
		$this->assertEquals( 'test-endpoint/arg1/arg2', $result );

		// Test with site ID.
		$client->set_site_id( 123 );
		$result = $method->invokeArgs( $client, array( 'test-endpoint/:site_id', array() ) );
		$this->assertEquals( 'test-endpoint/123', $result );

		// Test with domain name.
		$client->set_domain_name( 'example.com' );
		$result = $method->invokeArgs( $client, array( 'test-endpoint/:domain', array() ) );
		$this->assertEquals( 'test-endpoint/example.com', $result );

		$result = $method->invokeArgs( $client, array( 'test-endpoint/:client', array() ) );
		$this->assertEquals( 'test-endpoint/test-client', $result );
	}


	/**
	 * Test the call method with is_ok.
	 */
	public function test_call_with_is_ok() {
		// Use the Mock_Helper to set up a mock.
		Mock_Helper::mock_api_request( 'test-endpoint', array( 'success' => true ) );

		// Test the call method with is_ok.
		$result = WPCloud_API_Client::call( 'test-endpoint' );
		$this->assertTrue( $result->is_ok(), 'call() with is_ok() should return true for a successful request' );

		// Clean up.
		Mock_Helper::clear_responses();
	}
}
