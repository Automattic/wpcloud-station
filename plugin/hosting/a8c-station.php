<?php
/**
 * /**
 * Plugin Name: WPCOM Station Config
 * Description: Applies WPCOM Station configurations.
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
// Require the WPCOM Station class.
require_once WP_PLUGIN_DIR . '/wpcloud-station-plugin/includes/class-wpcloud-station.php';

// Die if not proxied.
if ( isset( $_SERVER['A8C_PROXIED_REQUEST'] ) && ! defined( 'WP_CLI' ) ) {
	if ( '1' !== sanitize_text_field( wp_unslash( $_SERVER['A8C_PROXIED_REQUEST'] ) ) ) {
		if ( function_exists( 'wp_die' ) ) {
			wp_die( 'Your IP is not special enough. Please proxy.', 'Please Proxy' );
		} else {
			die( 'Your IP is not special enough. Please proxy.' );
		}
	}
}

// Verify that the WPCOM user has access to the site.
add_action(
	'init',
	function () {
		if ( defined( 'WP_CLI' ) ) {
			return;
		}
		$station     = new WPCloud_Station();
		$wpcom_users = $station->wpcom_users;
		if ( empty( $wpcom_users ) ) {
			wp_logout();
		}
		$current_user = wp_get_current_user();
		if ( ! in_array( $current_user->user_email, $wpcom_users, true ) ) {
			wp_logout();
			wp_safe_redirect( home_url() );
			exit;
		}
	}
);

// Disable the ability to manage users.
add_action(
	'admin_menu',
	function () {
		remove_menu_page( 'users.php' );
		remove_submenu_page( 'users.php', 'user-new.php' );
	},
	5,
	0
);

add_filter(
	'rest_pre_dispatch',
	function ( $response, $handler, $request ) {
		if ( $request->get_route() === '/wp/v2/users' ) {
			return new WP_Error( 'rest_forbidden', __( 'User api is disabled' ), array( 'status' => 403 ) );
		}
		return $response;
	},
	10,
	3
);

// Allow access to users.php ( although it's been removed from the menu ) but disable editing and deleting users.
add_action(
	'admin_init',
	function () {
		if ( is_admin() && isset( $_SERVER['REQUEST_URI'] ) ) {
			$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
			if ( strpos( $request_uri, 'users.php' ) !== false && isset( $_GET['action'] ) && 'delete' === $_GET['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				wp_safe_redirect( admin_url( 'index.php' ) );
			}
			$blocked_pages = array( 'user-edit.php', 'profile.php', 'user-new.php' );
			foreach ( $blocked_pages as $page ) {
				if ( strpos( $request_uri, $page ) !== false ) {
					wp_safe_redirect( admin_url( 'index.php' ) );
					exit;
				}
			}
		}
	}
);

add_filter( 'wp_pre_insert_user_data', '__return_empty_array', 10, 0 );
add_filter( 'pre_user_login', '__return_empty_string', 10, 0 );
