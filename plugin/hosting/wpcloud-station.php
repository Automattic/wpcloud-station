<?php
/**
 * Plugin Name: WP Cloud Config
 * Description: Applies WP Cloud Station configurations .
 * Version: 1.0
 * Author: Automattic
 * License: GPLv3 or later
 *
 * @package wpcloud - station
 */

// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

// Retrieve Key from ADP.
add_filter(
	'wpcloud_api_key',
	function ( $key ) {
		$apd = new Atomic_Persistent_Data();
		if ( ! empty( $apd->WP_CLOUD_API_KEY ) ) {
			return $apd->WP_CLOUD_API_KEY;
		}
		return $key;
	},
	10,
	1
);

// Force Jetpack SSO module on.
add_filter(
	'option_jetpack_active_modules',
	function ( $modules ) {
		return array_values( array_merge( $modules, array( 'sso' ) ) );
	},
	10,
	1
);

// Requre Jetpack SSO & 2FA.
add_filter( 'jetpack_remove_login_form', '__return_true' );
add_filter( 'jetpack_sso_require_two_step', '__return_true' );
add_filter( 'jetpack_sso_match_by_email', '__return_true' );

// Prevent Jetpack & WP Cloud Station plugins from being deactivated.
add_filter(
	'plugin_action_links',
	function ( $actions, $plugin_file ) {
		// Define the plugins you want to protect from being deactivated.
		$protected_plugins = array(
			'jetpack/jetpack.php',
			'wpcloud-station-plugin/wpcloud-station.php', // Add your plugin paths here.
		);
		if ( in_array( $plugin_file, $protected_plugins, true ) ) {
			unset( $actions['deactivate'] );
		}
		return $actions;
	},
	10,
	2
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
