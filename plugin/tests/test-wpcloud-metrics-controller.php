<?php
/**
 * Class WPCLOUD_Metrics_ControllerTest
 *
 * @package wpcloud-station
 */

/**
 * Test case for the WPCLOUD_Metrics_Controller class.
 */
class WPCLOUD_Metrics_ControllerTest extends WP_UnitTestCase {

	/**
	 * The controller instance.
	 *
	 * @var WPCLOUD_Metrics_Controller
	 */
	private $controller;

	/**
	 * Test site ID.
	 *
	 * @var int
	 */
	private $site_id;

	/**
	 * Test post for the site.
	 *
	 * @var WP_Post
	 */
	private $post;

	/**
	 * Set up test environment.
	 */
	public function set_up() {
		parent::set_up();

		// Create a test user for the site.
		$user_id = $this->factory->user->create(
			array(
				'user_login' => 'testuser',
				'user_email' => 'testuser@example.com',
				'role'       => 'administrator',
			)
		);

		// Create a test post for the site.
		$post_id    = $this->factory->post->create(
			array(
				'post_title'  => 'Test Site',
				'post_type'   => 'wpcloud_site',
				'post_status' => 'publish',
				'post_author' => $user_id,
			)
		);
		$this->post = get_post( $post_id );

		// Add site ID meta.
		$this->site_id = 123;
		update_post_meta( $post_id, 'wpcloud_site_id', $this->site_id );

		// Create controller instance.
		$this->controller = new WPCLOUD_Metrics_Controller();

		// Define the capability constant if not already defined.
		if ( ! defined( 'WPCLOUD_CAN_MANAGE_SITES' ) ) {
			define( 'WPCLOUD_CAN_MANAGE_SITES', 'manage_options' );
		}
	}

	/**
	 * Set up before each test.
	 */
	public function setUp(): void {
		parent::setUp();
		\Mockery::close();
	}

	/**
	 * Tear down test environment.
	 */
	public function tear_down() {
		wp_delete_post( $this->post->ID, true );
		parent::tear_down();
	}

	/**
	 * Clean up after each test.
	 */
	public function tearDown(): void {
		\Mockery::close();
		parent::tearDown();
	}

	/**
	 * Test parseTime method with valid time strings.
	 */
	public function test_parseTime_valid_strings() {
		// Test with null.
		$this->assertNull( $this->controller->parseTime( null, 'start' ) );

		// Test with 'now'.
		$now = time();
		$this->assertEqualsWithDelta( $now, $this->controller->parseTime( 'now', 'start' ), 2 );

		// Test with 'now-1h'.
		$one_hour_ago = strtotime( '-1 hour' );
		$this->assertEqualsWithDelta( $one_hour_ago, $this->controller->parseTime( 'now-1h', 'start' ), 2 );

		// Test with '1h' (should be interpreted as now-1h).
		$this->assertEqualsWithDelta( $one_hour_ago, $this->controller->parseTime( '1h', 'start' ), 2 );

		// Test with other units.
		$this->assertEqualsWithDelta( strtotime( '-30 seconds' ), $this->controller->parseTime( '30s', 'start' ), 2 );
		$this->assertEqualsWithDelta( strtotime( '-15 minutes' ), $this->controller->parseTime( '15m', 'start' ), 2 );
		$this->assertEqualsWithDelta( strtotime( '-2 days' ), $this->controller->parseTime( '2d', 'start' ), 2 );
		$this->assertEqualsWithDelta( strtotime( '-3 months' ), $this->controller->parseTime( '3M', 'start' ), 2 );

		// Test with absolute time string.
		$this->assertEqualsWithDelta( strtotime( '2023-01-01' ), $this->controller->parseTime( '2023-01-01', 'start' ), 2 );
	}

	/**
	 * Test parseTime method with invalid time strings.
	 */
	public function test_parseTime_invalid_strings() {
		// Test with invalid time string.
		$result = $this->controller->parseTime( 'invalid-time', 'start' );
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertEquals( 'rest_invalid_param', $result->get_error_code() );
		$this->assertEquals( 'Invalid start time', $result->get_error_message() );

		// Test with future time.
		$result = $this->controller->parseTime( '2050-01-01', 'end' );
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertEquals( 'rest_invalid_param', $result->get_error_code() );
		$this->assertEquals( 'Invalid end time', $result->get_error_message() );
	}

	/**
	 * Test user_access_check method with authorized user.
	 */
	public function test_user_access_check_authorized() {
		// Create an admin user.
		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		// Call the method.
		$result = $this->controller->user_access_check();

		// Check the result.
		$this->assertTrue( $result );
	}

	/**
	 * Test user_access_check method with unauthorized user.
	 */
	public function test_user_access_check_unauthorized() {
		// Create a subscriber user.
		$user_id = $this->factory->user->create(
			array(
				'role' => 'subscriber',
			)
		);
		wp_set_current_user( $user_id );

		// Call the method.
		$result = $this->controller->user_access_check();

		// Check the result.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertEquals( 'rest_forbidden', $result->get_error_code() );
		$this->assertEquals( 'Unauthorized request', $result->get_error_message() );
	}

	/**
	 * Test user_access_check method with no user.
	 */
	public function test_user_access_check_no_user() {
		// Set current user to 0 (no user).
		wp_set_current_user( 0 );

		// Call the method.
		$result = $this->controller->user_access_check();

		// Check the result.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertEquals( 'rest_forbidden', $result->get_error_code() );
		$this->assertEquals( 'Unauthorized request', $result->get_error_message() );
	}

	/**
	 * Test convert_filters_format method with single value.
	 */
	public function test_convert_filters_format_single_value() {
		// Create a filter with a single value.
		$filters = array(
			array( 'http_verb', '=', 'GET' ),
		);

		// Call the method.
		$result = $this->controller->convert_filters_format( $filters );

		// Check the result.
		$this->assertIsArray( $result );
		$this->assertCount( 1, $result );
		$this->assertEquals( 'http_verb', $result[0]['field'] );
		$this->assertEquals( '=', $result[0]['operator'] );
		$this->assertEquals( 'GET', $result[0]['value'] );
	}

	/**
	 * Test convert_filters_format method with multiple values.
	 */
	public function test_convert_filters_format_multiple_values() {
		// Create a filter with multiple values.
		$filters = array(
			array( 'http_verb', 'IN', 'GET,POST,PUT' ),
		);

		// Call the method.
		$result = $this->controller->convert_filters_format( $filters );

		// Check the result.
		$this->assertIsArray( $result );
		$this->assertCount( 1, $result );
		$this->assertEquals( 'http_verb', $result[0]['field'] );
		$this->assertEquals( 'IN', $result[0]['operator'] );
		$this->assertIsArray( $result[0]['value'] );
		$this->assertCount( 3, $result[0]['value'] );
		$this->assertEquals( 'GET', $result[0]['value'][0] );
		$this->assertEquals( 'POST', $result[0]['value'][1] );
		$this->assertEquals( 'PUT', $result[0]['value'][2] );
	}

	/**
	 * Test convert_filters_format method with multiple filters.
	 */
	public function test_convert_filters_format_multiple_filters() {
		// Create multiple filters.
		$filters = array(
			array( 'http_verb', '=', 'GET' ),
			array( 'http_status', 'IN', '200,404,500' ),
		);

		// Call the method.
		$result = $this->controller->convert_filters_format( $filters );

		// Check the result.
		$this->assertIsArray( $result );
		$this->assertCount( 2, $result );

		// Check first filter.
		$this->assertEquals( 'http_verb', $result[0]['field'] );
		$this->assertEquals( '=', $result[0]['operator'] );
		$this->assertEquals( 'GET', $result[0]['value'] );

		// Check second filter.
		$this->assertEquals( 'http_status', $result[1]['field'] );
		$this->assertEquals( 'IN', $result[1]['operator'] );
		$this->assertIsArray( $result[1]['value'] );
		$this->assertCount( 3, $result[1]['value'] );
		$this->assertEquals( '200', $result[1]['value'][0] );
		$this->assertEquals( '404', $result[1]['value'][1] );
		$this->assertEquals( '500', $result[1]['value'][2] );
	}

	/**
	 * Test convert_filters_format method with invalid filter.
	 */
	public function test_convert_filters_format_invalid_filter() {
		// Create an invalid filter (missing value).
		$filters = array(
			array( 'http_verb', '=' ), // Missing value.
		);

		// Call the method.
		$result = $this->controller->convert_filters_format( $filters );

		// Check the result - should be an empty array since the filter is invalid.
		$this->assertIsArray( $result );
		$this->assertEmpty( $result );
	}
}
