<?php
/**
 * Plugin Name:     WP Cloud Station
 * Plugin URI:      https://github.com/Automattic/wpcloud-station
 * Description:     Dashboard for managing your WP Cloud services.
 * Author:          Automattic
 * Author URI:      https://wp.cloud/
 * Text Domain:     wpcloud
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package        wpcloud-station
 */

// @TODO: change this to wpcloudstation.dev once we have that domain set up.
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
 * Page categories
 */

// Pages that are only accessible to logged in users.
define( 'WPCLOUD_CATEGORY_PRIVATE', 'wpcloud_private' );
// Pages required for the core functionality of the plugin.
define( 'WPCLOUD_CATEGORY_CORE', 'wpcloud_core_pages' );


// Initialize the plugin.
require_once plugin_dir_path( __FILE__ ) . 'init.php';
