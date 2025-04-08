<?php
/**
 * Class WPCloud_SiteTest
 *
 * @package wpcloud-station
 */

/**
 * Test case for the WPCloud_Site class.
 */
class WPCloud_SiteTest extends WP_UnitTestCase {

	/**
	 * Test instance of the site.
	 *
	 * @var WPCloud_Site
	 */
	private $site;

	/**
	 * Test post for the site.
	 *
	 * @var WP_Post
	 */
	private $post;

	/**
	 * Mock for the WPCloud_API_Request class.
	 *
	 * @var Mock_WPCloud_API_Request
	 */
	private $mock_api_request;

	/**
	 * Mock for the WPCloud_API_Client class.
	 *
	 * @var WPCloud_API_Client
	 */
	private $api_client;

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
		update_post_meta( $post_id, 'wpcloud_site_id', 123 );
	}

	/**
	 * Tear down test environment.
	 */
	public function tear_down() {
		wp_delete_post( $this->post->ID, true );
		parent::tear_down();
	}

	/**
	 * Test constructor.
	 */
	public function test_constructor() {
		// Test with post parameter.
		$site = new WPCloud_Site( $this->post );
		$this->assertInstanceOf( WPCloud_Site::class, $site );

		// Test with null parameter (should use get_post()).
		$GLOBALS['post'] = $this->post;
		$site            = new WPCloud_Site();
		$this->assertInstanceOf( WPCloud_Site::class, $site );
		unset( $GLOBALS['post'] );

		// Test with non-wpcloud_site post type.
		$regular_post_id = $this->factory->post->create(
			array(
				'post_title'  => 'Regular Post',
				'post_type'   => 'post',
				'post_status' => 'publish',
			)
		);
		$regular_post    = get_post( $regular_post_id );
		$site            = new WPCloud_Site( $regular_post );

		// The post property should not be set for non-wpcloud_site post types.
		$reflection    = new ReflectionClass( $site );
		$post_property = $reflection->getProperty( 'post' );
		$post_property->setAccessible( true );
		$this->assertNull( $post_property->getValue( $site ) );

		wp_delete_post( $regular_post_id, true );
	}

	/**
	 * Test create method.
	 */
	public function test_create() {
		// Create a test user for the author.
		$user_id = $this->factory->user->create(
			array(
				'user_login' => 'testauthor',
				'user_email' => 'testauthor@example.com',
				'role'       => 'administrator',
			)
		);

		Mock_Helper::mock_api_requests(
			array(
				'check-can-host-domain/test_client/awesome-sauce.com' => (object) array(
					'mock_is_ok' => true,
					'data'       => (object) array( 'allowed' => true ),
				),
				'create-site/test_client' => (object) array(
					'status' => 'success',
					'data'   => (object) array(
						'atomic_site_id' => 456,
					),
				),
			)
		);

		// Test creating a site.
		$options = array(
			'site_name'     => 'New Test Site',
			'php_version'   => '8.1',
			'data_center'   => 'dca',
			'admin_pass'    => 'password123',
			'site_owner_id' => $user_id,
			'domain_name'   => 'awesome-sauce.com',
		);
		$result  = WPCloud_Site::create( $options );
		$this->assertInstanceOf( WP_Post::class, $result );
		$this->assertEquals( 456, get_post_meta( $result->ID, 'wpcloud_site_id', true ) );
	}

	/**
	 * Test get method.
	 */
	public function test_get_method() {
		// Set up the global post.
		$GLOBALS['post'] = $this->post;

		// Test getting the current site.
		$site = WPCloud_Site::get();
		$this->assertInstanceOf( WPCloud_Site::class, $site );

		// Test with no global post.
		unset( $GLOBALS['post'] );
		$site = WPCloud_Site::get();
		$this->assertNull( $site );
	}

	/**
	 * Test get_detail_options method.
	 */
	public function test_get_detail_options() {
		$options = WPCloud_Site::get_detail_options();
		$this->assertIsArray( $options );
		$this->assertArrayHasKey( 'site_name', $options );
		$this->assertArrayHasKey( 'domain_name', $options );
		$this->assertArrayHasKey( 'php_version', $options );
	}

	/**
	 * Test get_linkable_detail_options method.
	 */
	public function test_get_linkable_detail_options() {
		$options = WPCloud_Site::get_linkable_detail_options();
		$this->assertIsArray( $options );
		$this->assertArrayHasKey( 'domain_name', $options );
		$this->assertArrayHasKey( 'wp_admin_url', $options );
		$this->assertArrayHasKey( 'phpmyadmin_url', $options );
	}

	/**
	 * Test get_mutable_options method.
	 */
	public function test_get_mutable_options() {
		// Mock the API response for PHP versions.
		Mock_Helper::mock_api_request(
			'get-php-versions/test_client',
			(object) array(
				'data' => array(
					'8.1',
					'8.2',
				),
			)
		);

		$options = WPCloud_Site::get_mutable_options();
		$this->assertIsArray( $options );
		$this->assertArrayHasKey( 'php_version', $options );

		// Check that the PHP versions were correctly extracted from the API response.
		$this->assertIsArray( $options['php_version']['options'] );

		$this->assertSame(
			array(
				'8.1' => '8.1',
				'8.2' => '8.2',
			),
			$options['php_version']['options']
		);
	}


	/**
	 * Test readable_size method.
	 */
	public function test_readable_size() {
		$this->assertEquals( '0 B', WPCloud_Site::readable_size( 0 ) );
		$this->assertEquals( '1 B', WPCloud_Site::readable_size( 1 ) );
		$this->assertEquals( '1 KB', WPCloud_Site::readable_size( 1024 ) );
		$this->assertEquals( '1 MB', WPCloud_Site::readable_size( 1024 * 1024 ) );
		$this->assertEquals( '1 GB', WPCloud_Site::readable_size( 1024 * 1024 * 1024 ) );
		$this->assertEquals( '1.5 GB', WPCloud_Site::readable_size( 1.5 * 1024 * 1024 * 1024 ) );
	}

	/**
	 * Test get_detail method.
	 */
	public function test_get_detail() {
		$mock_details = (object) array(
			'data' => (object) array(
				'domain_name' => 'test-site.example.com',
				'php_version' => '8.1',
				'extra'       => (object) array(
					'server_pool' => (object) array(
						'geo_affinity' => 'us-east',
					),
				),
			),
		);

		Mock_Helper::mock_api_request( 'get-site/123/extra', $mock_details );

		$domain = WPCloud_Site::get_detail( $this->post, 'domain_name' );

		$this->assertEquals( 'test-site.example.com', $domain );

		$php_version = WPCloud_Site::get_detail( $this->post, 'php_version' );
		$this->assertEquals( '8.1', $php_version );

		$data_center = WPCloud_Site::get_detail( $this->post, 'data_center' );
		$this->assertEquals( 'us-east', $data_center );

		// Test site meta requests.
		$about_a_gig = 1073741824;
		Mock_Helper::mock_api_requests(
			array(
				'site-meta/123/space_used/get'      => (object) array(
					'data' => $about_a_gig,
				),
				'site-meta/123/db_file_size/get'    => (object) array(
					'data' => $about_a_gig,
				),
				// Check for empty values.
				'site-meta/123/space_quota/get'     => (object) array(
					'data' => '',
				),
				// Test other site meta.
				'site-meta/123/burst_php_conns/get' => (object) array(
					'data' => '6',
				),
			)
		);
		foreach ( array( 'space_used', 'db_file_size' ) as $meta_key ) {
			$space_used = WPCloud_Site::get_detail( $this->post, $meta_key );
			$this->assertEquals( '1 GB', $space_used );
		}
		$no_value = WPCloud_Site::get_detail( $this->post, 'space_quota' );
		$this->assertEquals( '0 B', $no_value );

		// Test that raw metadata is returned correctly.
		$burst_php_conns = WPCloud_Site::get_detail( $this->post, 'burst_php_conns' );
		$this->assertEquals( '6', $burst_php_conns );

		// Check edge cache.
		Mock_Helper::mock_api_request(
			'edge-cache/123',
			(object) array(
				'data' => (object) array(
					'status'     => 'enabled',
					'ddos_until' => 1743453005,
				),
			)
		);
		$edge_cache_status = WPCloud_Site::get_detail( $this->post, 'edge_cache_status' );
		$this->assertEquals( 'enabled', $edge_cache_status );
		$ddos_until = WPCloud_Site::get_detail( $this->post, 'defensive_mode' );
		$this->assertEquals( 1743453005, $ddos_until );
	}

	/**
	 * Test replace_attr method.
	 */
	public function test_replace_attr() {
		$site = new WPCloud_Site( $this->post );

		$site_mock = \Mockery::mock( $site )->makePartial();
		$site_mock->shouldReceive( '__get' )
			->with( 'domain_name' )
			->andReturn( 'test-site.example.com' );
		$site_mock->shouldReceive( '__get' )
			->with( 'php_version' )
			->andReturn( '8.1' );

		$result = $site_mock->replace_attr( 'The domain is {site.domain_name}' );
		$this->assertEquals( 'The domain is test-site.example.com', $result );

		$result = $site_mock->replace_attr( 'Domain: {site.domain_name}, PHP: {site.php_version}' );
		$this->assertEquals( 'Domain: test-site.example.com, PHP: 8.1', $result );

		$result = $site_mock->replace_attr( 'No replacements here' );
		$this->assertEquals( 'No replacements here', $result );

		$result = $site_mock->replace_attr( null );
		$this->assertEquals( '', $result );
	}

	/**
	 * Test is_domain_ssl_valid method.
	 */
	public function test_is_domain_ssl_valid() {
		$valid_response = (object) array(
			'data' => (object) array(
				'broken_record' => false,
				'broken_check'  => false,
			),
		);
		Mock_Helper::mock_api_request( 'ssl-info/valid-domain.com', $valid_response );

		$invalid_response = (object) array(
			'data' => (object) array(
				'broken_record' => true,
				'broken_check'  => false,
			),
		);
		Mock_Helper::mock_api_request( 'ssl-info/invalid-domain.com', $invalid_response );

		$result = WPCloud_Site::is_domain_ssl_valid( 'valid-domain.com' );
		$this->assertTrue( $result );

		$result = WPCloud_Site::is_domain_ssl_valid( 'invalid-domain.com' );
		$this->assertFalse( $result );
	}

	/**
	 * Test update_detail method.
	 */
	public function test_update_detail() {
		$success_response = (object) array( 'success' => true );
		Mock_Helper::mock_api_requests(
			array(
				'get-php-versions/test_client'       => (object) array(
					'data' => array(
						'8.1',
						'8.2',
					),
				),
				'site-meta/123/php_version/update'   => $success_response,
				'site-meta/123/db_charset/update'    => $success_response,
				'site-meta/123/invalid_field/update' => (object) array(
					'error'   => 'Invalid field',
					'success' => false,
				),
			)
		);

		$result = WPCloud_Site::update_detail(
			array(
				'site_id'         => $this->post->ID,
				'wpcloud_site_id' => 123,
				'php_version'     => '8.2',
				'db_charset'      => 'utf8mb4',
			)
		);
		$this->assertTrue( $result );

		$result = WPCloud_Site::update_detail(
			array(
				'site_id'         => $this->post->ID,
				'wpcloud_site_id' => 123,
				'invalid_field'   => 'value',
			)
		);
		$this->assertInstanceOf( WP_Error::class, $result );
	}

	/**
	 * Test import method.
	 */
	public function test_import() {
		$user_id = $this->factory->user->create(
			array(
				'user_login' => 'testimport',
				'user_email' => 'testimport@example.com',
				'role'       => 'administrator',
			)
		);
		$user    = get_user_by( 'id', $user_id );

		$site_response = (object) array(
			'data' => (object) array(
				'domain_name'    => 'import-test.example.com',
				'atomic_site_id' => 789,
			),
		);
		Mock_Helper::mock_api_request( 'get-site/789/extra', $site_response );

		$result = WPCloud_Site::import( 789, $user );
		$this->assertTrue( $result );

		$args  = array(
			'post_type'  => 'wpcloud_site',
			'meta_query' => array(
				array(
					'key'   => 'wpcloud_site_id',
					'value' => 789,
				),
			),
		);
		$query = new WP_Query( $args );
		$this->assertEquals( 1, $query->post_count );
		$this->assertEquals( 'import-test.example.com', $query->posts[0]->post_title );
		$this->assertEquals( $user_id, $query->posts[0]->post_author );

		wp_delete_post( $query->posts[0]->ID, true );
	}
}
