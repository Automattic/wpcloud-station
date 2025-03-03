<?php
/**
 * Plugin Name: WP Cloud Config
 * Description: Applies WP Cloud Station configurations for clients.
 * Version: 1.0
 * Author: Automattic
 * License: GPLv3 or later
 *
 * @package wpcloud - station
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

// Force Jetpack SSO module on .
add_filter(
	'option_jetpack_active_modules',
	function ( $modules ) {
		return array_values( array_merge( $modules, array( 'sso' ) ) );
	},
	10,
	1
);

// Try auto-connecting Jetpack.
add_action(
	'init',
	function () {
		if ( ! class_exists( 'Jetpack' ) ) {
			return;
		}

		if ( method_exists( 'Jetpack', 'try_connection' ) && ! Jetpack::is_active() ) {
			Jetpack::try_connection();
		}
	}
);

// Require Jetpack SSO & 2FA.
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
