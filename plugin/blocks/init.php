<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
require_once plugin_dir_path( __FILE__ ) . 'render.php';
require_once plugin_dir_path( __DIR__ ) . 'lib/html5.php';

function wpcloud_include_blocks() {
	$block_directories = array_merge(
		glob( __DIR__ . '/build/*' ),
		glob( __DIR__ . '/build/components/*' )
	);

	foreach( $block_directories as $block_directory ) {
		if ( ! file_exists( $block_directory . '/block.json' ) ) {
			continue;
		}

		try {
			register_block_type( $block_directory );
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

function wpcloud_block_available_wp_versions(): array {
	return array( "latest" => __( "latest" ), "previous" => __( "previous" ), "beta" => _( "beta" ) );
}

function wpcloud_block_admin_enqueue_scripts() {
	wp_register_script( 'wpcloud-blocks-site-form', '',);
	wp_enqueue_script( 'wpcloud-blocks-site-form' );
	wp_add_inline_script(
		'wpcloud-blocks-site-form',
		'window.wpcloud = window.wpcloud ?? {};' .
		'wpcloud.siteDetails=' . json_encode( WPCloud_Site::get_detail_options() ) . ';' .
		'wpcloud.phpVersions=' . json_encode( wpcloud_block_available_php_options() ) . ';' .
		'wpcloud.wpVersions=' . json_encode( wpcloud_block_available_wp_versions() ) . ';' .
		'wpcloud.dataCenters=' . json_encode( wpcloud_block_available_datacenters_options() ) . ';' .
		'wpcloud.linkableSiteDetails=' . json_encode( WPCloud_Site::get_linkable_detail_options() ) . ';'.
		'wpcloud.siteMetaOptions=' .json_encode( WPCloud_Site::get_meta_options() ) . ';' .
		'wpcloud.siteMetaFields=' .json_encode( array_keys( WPCloud_Site::get_meta_fields() ) ) . ';'
	);
}
add_action( 'admin_enqueue_scripts', 'wpcloud_block_admin_enqueue_scripts' );
