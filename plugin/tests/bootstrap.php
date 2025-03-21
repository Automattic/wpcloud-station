<?php // phpcs:ignore
/**
 * PHPUnit bootstrap file.
 *
 * @package wpcloud-client
 */

require_once dirname( __DIR__ ) . '/tests/class-mock-wpcloud-api-request.php';

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Forward custom PHPUnit Polyfills autoloader file for WP < 5.9.
if ( ! defined( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' ) ) {
	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', __DIR__ . '/../vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php' );
}

if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
	echo "Could not find {$_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}


require_once "{$_tests_dir}/includes/functions.php";

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
require_once dirname( __DIR__ ) . '/tests/class-mock-helper.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( __DIR__ ) . '/wpcloud-station.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require "{$_tests_dir}/includes/bootstrap.php";

// Check if verbose logging is enabled.
if ( file_exists( __DIR__ . '/verbose_logging_enabled' ) ) {
	define( 'WPCLOUD_VERBOSE_LOGGING', true );
	echo "Verbose logging enabled\n";
}

// Define WPCLOUD_TESTING constant to enable mocking in wpcloud_client functions.
if ( ! defined( 'WPCLOUD_TESTING' ) ) {
	define( 'WPCLOUD_TESTING', true );
}

// Add a filter to provide a mock API key for testing.
add_filter(
	'wpcloud_client_key',
	function ( $api_key ) {
		return 'test_api_key';
	}
);

// Add a filter to provide a mock API key for testing.
add_filter(
	'wpcloud_api_key',
	function ( $api_key ) {
		return 'test_api_key';
	}
);

// Add a filter to provide a mock client name for testing.
add_filter(
	'wpcloud_client_name',
	function ( $client_name ) {
		return 'test_client';
	}
);

// Mock the WPCloud_API_Client::init method.
add_filter(
	'wpcloud_api_client_init',
	function ( $client, $site_id, $use_cache ) {
		global $mock_api_client;
		return $mock_api_client;
	},
	10,
	3
);

// Add a filter to provide a mock WPCloud_API_Request.
add_filter(
	'wpcloud_api_request',
	function ( $request ) {
		global $mock_api_request;
		return $mock_api_request ?? $request;
	}
);

// Make sure no api calls are made during tests.
Mock_Helper::mock_api_request();

// Set up teardown to clean up Mockery.
register_shutdown_function(
	function () {
		Mock_Helper::shutdown();
	}
);
