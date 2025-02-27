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
			'wpcloud-station/wpcloud-station.php', // Add your plugin paths here.
		);
		if ( in_array( $plugin_file, $protected_plugins, true ) ) {
			unset( $actions['deactivate'] );
		}
		return $actions;
	},
	10,
	2
);
