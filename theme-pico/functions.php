<?php
/**
 * WP Cloud theme.
 *
 * @package wpcloud-station
 */

// We don't want to show the admin bar in the front end.
add_filter( 'show_admin_bar', '__return_false' );

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'wpcloud-station', get_theme_file_uri( 'assets/styles/global.css' ), array(), '1.0.0' );
		wp_enqueue_style( 'pico-css', 'https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css', array(), '1.0.0' );
	}
);

if ( function_exists( 'register_block_pattern_category' ) ) {
	register_block_pattern_category(
		'wpcloud_forms',
		array(
			'label'       => __( 'WP Cloud Forms' ),
			'description' => __( 'Core WP Cloud forms' ),
		)
	);
}


add_action(
	'init',
	function () {
		if ( ! function_exists( 'wpcloud_station_register_site_view' ) ) {
			return;
		}
		wpcloud_station_register_site_view( 'admin' );
		wpcloud_station_register_site_view( 'domains' );
		wpcloud_station_register_site_view( 'settings' );
		wpcloud_station_register_site_view( 'users' );
		wpcloud_station_register_site_view( 'metrics' );
		flush_rewrite_rules();

		// Register patterns.
		if ( function_exists( 'wpcloud_register_patterns' ) ) {
			wpcloud_register_patterns();
		}
	}
);
