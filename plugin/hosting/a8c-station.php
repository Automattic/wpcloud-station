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

if ( file_exists( WP_PLUGIN_DIR . '/wpcloud-station-plugin/includes/class-wpcloud-station.php' ) ) {
	require_once WP_PLUGIN_DIR . '/wpcloud-station-plugin/includes/class-wpcloud-station.php';
} else {
	return;
}

// Die if not proxied, but allow webhook and Jetpack requests.
$is_allowed_request = false;
if ( isset( $_SERVER['REQUEST_URI'] ) ) {
	$request_uri = '';
	// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$request_uri = wp_unslash( $_SERVER['REQUEST_URI'] );
	// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$request_uri = sanitize_text_field( $request_uri );

	// Check for webhook requests.
	$is_webhook_request = strpos( $request_uri, '/wp-json/wpcloud-station/v1/webhook' ) !== false;

	// Check for specific Jetpack requests that need to bypass the proxy check.
	// XML-RPC is used by Jetpack for the WordPress.com connection.
	$is_xmlrpc_request = strpos( $request_uri, '/xmlrpc.php' ) !== false;

	// Only allow specific Jetpack endpoints that are necessary for the connection.
	// This is more secure than allowing all /wp-json/jetpack/ requests.
	$is_jetpack_connection_request = false;
	$jetpack_allowed_endpoints     = array(
		'/wp-json/jetpack/v4/connection',
		'/wp-json/jetpack/v4/verify_registration',
		'/wp-json/jetpack/v4/remote_connect',
		'/wp-json/jetpack/v4/remote_provision',
	);

	foreach ( $jetpack_allowed_endpoints as $endpoint ) {
		if ( strpos( $request_uri, $endpoint ) !== false ) {
			$is_jetpack_connection_request = true;
			break;
		}
	}

	// Allow webhook and specific Jetpack requests.
	$is_allowed_request = $is_webhook_request || $is_xmlrpc_request || $is_jetpack_connection_request;
}

// Check for status query parameter.
$status_ok = false;
if ( isset( $_GET['status'] ) && 'ok' === sanitize_text_field( wp_unslash( $_GET['status'] ) ) ) {
	$status_ok = true;
}

if ( isset( $_SERVER['A8C_PROXIED_REQUEST'] ) && ! defined( 'WP_CLI' ) && ! $is_allowed_request ) {
	if ( '1' !== sanitize_text_field( wp_unslash( $_SERVER['A8C_PROXIED_REQUEST'] ) ) ) {
		if ( function_exists( 'wp_die' ) ) {
			if ( $status_ok ) {
				wp_die( 'Your IP is not special enough. Please proxy.', 'Please Proxy', array( 'response' => 200 ) );
			} else {
				wp_die( 'Your IP is not special enough. Please proxy.', 'Please Proxy' );
			}
		} else {
			die( 'Your IP is not special enough. Please proxy.' );
		}
	}
}

// Verify that the WPCOM user has access to the site.
add_action(
	'init',
	function () {
		if ( defined( 'WP_CLI' ) || ! is_user_logged_in() ) {
			return;
		}
		$station     = new WPCloud_Station();
		$wpcom_users = $station->wpcom_users;

		if ( ! is_array( $wpcom_users ) || empty( $wpcom_users ) ) {
			wp_logout();
			wp_safe_redirect( home_url() );
			exit;
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
