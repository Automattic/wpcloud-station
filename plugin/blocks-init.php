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

function wpcloud_include_blocks() {

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


// Add the site id to any form that is on a site post.
function wpcloud_block_add_site_id_hidden_field( $content ) {
	if ( get_post_type() !== 'wpcloud_site' ) {
		return $content;
	}
	$site_id = get_the_ID();
	return wpcloud_block_form_hidden_field( 'site_id', $site_id ) . $content;
}
add_filter( 'wpcloud_block_form_fields', 'wpcloud_block_add_site_id_hidden_field' );


// Allow the site id in the form if present.
function wpcloud_block_allow_site_id_field( $fields, $post_keys ) {
	if ( in_array( 'site_id', $post_keys ) ) {
		return array_merge( $fields, [ 'site_id' ] );
	}
	return $fields;
}
add_filter( 'wpcloud_block_form_submitted_fields', 'wpcloud_block_allow_site_id_field', 10, 2 );


function wpcloud_block_available_php_options(): array {
	$php_versions = wpcloud_client_php_versions_available();
	if ( is_wp_error( $php_versions) ) {
		error_log( 'WP Cloud: ' . $php_versions->get_error_message() );
		return [];
	}
	return array_reduce( $php_versions, function( $versions, $version ) {
			$versions[ $version ] = $version;
			return $versions;
		}, [] );
}

function wpcloud_block_available_datacenters_options(): array {
	$data_centers = array( ' ' => __( 'No Preference' ) );
	$available_data_centers  = wpcloud_client_datacenters_available();
	if ( is_wp_error( $available_data_centers ) ) {
		error_log( 'WP Cloud: ' . $available_data_centers->get_error_message() );
	} else {
		$data_centers += array_intersect_key( wpcloud_client_data_center_cities(), array_flip( $available_data_centers ) );
	}
	return $data_centers;
}
