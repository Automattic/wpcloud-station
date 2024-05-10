<?php
/**
 * Plugin Name:       Example Static
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       example-static
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once __DIR__ . '/blocks/render.php';

function wpcloud_include_blocks() {
	// for some reason wp-env doesn't support GLOB_BRACE when starting up.
	if (! defined('GLOB_BRACE')) {
		define('GLOB_BRACE', 0);
	}
	foreach( glob( __DIR__ . '/blocks/src/{*,components/*}' , GLOB_BRACE) as $block_directory ) {

		if ( ! file_exists( $block_directory . '/block.json' ) ) {
			continue;
		}

		try {
			register_block_type(
				preg_replace( '/src/', 'build', $block_directory),
			);
		} catch ( Exception $e ) {
			error_log( 'Error registering block: ' . $e->getMessage() );
		}


		if ( is_file( $block_directory . '/index.php' ) ) {
			// If we need any server code, include a blocks/src/{block}/index.php file.
			require_once $block_directory . '/index.php';
		}

	}
}
add_action( 'init', 'wpcloud_include_blocks' );

add_filter( 'block_categories_all' , function( $categories ) {
  // Adding a new category.
	$categories[] = array(
		'slug'  => 'wpcloud',
		'title' => 'WP Cloud'
	);

	return $categories;
} );

function wpcloud_block_available_php_options(): array {
	$php_versions = wpcloud_client_php_versions_available( true );
	if ( is_wp_error( $php_versions) ) {
		error_log( 'WP Cloud: ' . $php_versions->get_error_message() );
		return [];
	}
	return $php_versions;
}

function wpcloud_block_available_datacenters_options(): array {
	$available_data_centers  = wpcloud_client_data_centers_available( true );
	if ( is_wp_error( $available_data_centers ) ) {
		error_log( 'WP Cloud: ' . $available_data_centers->get_error_message() );
		return array( '' => __( 'No Preference' ) );
	}

	return $available_data_centers;
}
