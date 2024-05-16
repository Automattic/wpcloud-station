<?php
/**
 * Plugin Name:     WP Cloud Station
 * Plugin URI:      https://github.com/Automattic/wpcloud-station
 * Description:     Dashboard for managing your WP Cloud services.
 * Author:          Automattic Inc.
 * Author URI:      https://wp.cloud/
 * Text Domain:     wpcloud
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package        wpcloud-station
 */

/**
 * Demo mode
 */
define( 'WPCLOUD_DEMO_MODE', false );
//@TODO: change this to wpcloudstation.dev once we have that domain set up.
define( 'WPCLOUD_DEMO_DOMAIN', 'jhnstn.dev' );

/**
 *  Actions
 */
define( 'WPCLOUD_ACTION_UPDATE_SITE', 'wpcloud_update_site' );

define( 'WPCLOUD_CLIENT_RESPONSE_ERROR', 'wpcloud_client_response_error' );
define( 'WPCLOUD_CLIENT_RESPONSE_SUCCESS', 'wpcloud_client_response_success' );


/**
 * Filters
 */
define( 'WPCLOUD_SHOULD_CREATE_SITE', 'wpcloud_should_create_site' );
define( 'WPCLOUD_INITIAL_SITE_STATUS', 'wpcloud_initial_site_status' );


/**
 * Capabilities
 */
define( 'WPCLOUD_CAN_MANAGE_SITES', 'wpcloud_manage_sites' );

/**
 * Access Control
 */
define( 'WPCLOUD_PRIVATE_CATEGORY', 'wpcloud_private' );

// Initialize the plugin.
require_once plugin_dir_path( __FILE__ ) . 'init.php';
