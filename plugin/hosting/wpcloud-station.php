<?php
/**
 * Plugin Name: WP Cloud Config
 * Description: Applies WP Cloud Station configurations.
 * Version: 1.0
 * Author: Automattic
 * License: GPLv3 or later
 *
 * @package wpcloud - station
 */

 // phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( exists( WP_PLUGIN_DIR . '/wpcloud-station-plugin/includes/class-wpcloud-station.php' ) ) {
	require_once WP_PLUGIN_DIR . '/wpcloud-station-plugin/includes/class-wpcloud-station.php';
} else {
	return;
}

add_filter(
	'wpcloud_api_key',
	function ( $key ) {
		$station = new WPCloud_Station();
		$env_key = $station->wp_cloud_api_key;
		return $env_key ? $env_key : $key;
	},
	10,
	1
);

// Retrieve Client Name from ADP.
add_filter(
	'wpcloud_client_name',
	function ( $name ) {
		$station  = new WPCloud_Station();
		$env_name = $station->wp_cloud_client_name;
		return $env_name ? $env_name : $name;
	},
	10,
	1
);

// Custom Headers.
add_filter(
	'wp_headers',
	function ( $headers ) {
		$headers['Content-Security-Policy'] = "default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval';";
		$headers['X-Frame-Options']         = 'SAMEORIGIN';
		$headers['X-Xss-Protection']        = '1; mode=block';
		$headers['X-Content-Type-Options']  = 'nosniff';
		$headers['Referrer-Policy']         = 'strict-origin-when-cross-origin';
		$headers['Permissions-Policy']      = 'geolocation=(),midi=(),sync-xhr=(),microphone=(),camera=(),magnetometer=(),gyroscope=(),fullscreen=(self),payment=(self)';
		return $headers;
	},
	10,
	1
);

// Add robots.txt.
add_filter(
	'robots_txt',
	function () {
		return "User-agent: *\nDisallow: /";
	},
	10,
	0
);
