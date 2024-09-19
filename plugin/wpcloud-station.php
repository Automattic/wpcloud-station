<?php
/**
 * Plugin Name:     WP Cloud Station
 * Plugin URI:      https://github.com/Automattic/wpcloud-station
 * Description:     Dashboard for managing your WP Cloud services.
 * Author:          Automattic
 * Author URI:      https://wp.cloud/
 * Text Domain:     wpcloud
 * Domain Path:     /languages
 * Version:         1.0.0-beta.1
 *
 * @package        wpcloud-station
 */

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

define( 'WP_STATION_CLIENT_ID', 61 );


// Initialize the plugin.
require_once plugin_dir_path( __FILE__ ) . 'init.php';
