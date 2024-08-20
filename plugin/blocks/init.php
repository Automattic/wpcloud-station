<?php
/**
 * WP Cloud Blocks
 *
 * @package wpcloud-block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
require_once plugin_dir_path( __FILE__ ) . 'render.php';
require_once plugin_dir_path( __DIR__ ) . 'lib/html5.php';

/**
 * Include the blocks.
 *
 * @return void
 */
function wpcloud_include_blocks() {
	$block_directories = array_merge(
		glob( __DIR__ . '/build/*' ),
		glob( __DIR__ . '/build/components/*' )
	);

	foreach ( $block_directories as $block_directory ) {
		if ( ! file_exists( $block_directory . '/block.json' ) ) {
			continue;
		}

		try {
			register_block_type( $block_directory );
		} catch ( Exception $e ) {
			error_log( 'Error registering block: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}

		if ( is_file( $block_directory . '/index.php' ) ) {
			// If we need any server code, include a blocks/src/{block}/index.php file.
			require_once $block_directory . '/index.php';
		}
	}
}
add_action( 'init', 'wpcloud_include_blocks' );

add_filter(
	'block_categories_all',
	function ( $categories ) {
		// Adding a new category.
		$categories[] = array(
			'slug'  => 'wpcloud',
			'title' => 'WP Cloud',
		);

		return $categories;
	}
);

/**
 * Get available PHP versions.
 *
 * @return array available PHP versions.
 */
function wpcloud_block_available_php_options(): array {
	$php_versions = wpcloud_client_php_versions_available( true );
	if ( is_wp_error( $php_versions ) ) {
		error_log( 'WP Cloud: ' . $php_versions->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		return array( '' => __( 'No Preference' ) );
	}
	return (array) $php_versions;
}

/**
 * Get available data centers.
 *
 * @return array available data centers.
 */
function wpcloud_block_available_datacenters_options(): array {
	$available_data_centers = wpcloud_client_data_centers_available( true );
	if ( is_wp_error( $available_data_centers ) ) {
		error_log( 'WP Cloud: ' . $available_data_centers->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		return array( '' => __( 'No Preference' ) );
	}

	return (array) $available_data_centers;
}

/**
 * Get available WP versions.
 *
 * @return array available WP versions.
 */
function wpcloud_block_available_wp_versions(): array {
	return array(
		'latest'   => __( 'latest' ),
		'previous' => __( 'previous' ),
		'beta'     => __( 'beta' ),
	);
}

/**
 * Enqueue the admin scripts.
 *
 * @return void
 */
function wpcloud_block_admin_enqueue_scripts(): void {
	if ( ! wp_doing_ajax() ) {
		wp_register_script( 'wpcloud-blocks-site-form', '', array(), '1.0.0', true );
		wp_enqueue_script( 'wpcloud-blocks-site-form' );
		wp_add_inline_script(
			'wpcloud-blocks-site-form',
			'window.wpcloud = window.wpcloud ?? {};' .
			'wpcloud.siteDetails=' . wp_json_encode( WPCloud_Site::get_detail_options() ) . ';' .
			'wpcloud.phpVersions=' . wp_json_encode( wpcloud_block_available_php_options() ) . ';' .
			'wpcloud.wpVersions=' . wp_json_encode( wpcloud_block_available_wp_versions() ) . ';' .
			'wpcloud.dataCenters=' . wp_json_encode( wpcloud_block_available_datacenters_options() ) . ';' .
			'wpcloud.linkableSiteDetails=' . wp_json_encode( WPCloud_Site::get_linkable_detail_options() ) . ';' .
			'wpcloud.siteMutableOptions=' . wp_json_encode( WPCloud_Site::get_mutable_options() ) . ';' .
			'wpcloud.siteMutableFields=' . wp_json_encode( WPCloud_Site::get_mutable_fields() ) . ';'
		);
	}
}
add_action( 'admin_init', 'wpcloud_block_admin_enqueue_scripts' );
