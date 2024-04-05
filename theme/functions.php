<?php
/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package wpcloud-dashboard
 * @since 1.0.0
 */

/**
 * Enqueue the CSS files.
 *
 * @since 1.0.0
 *
 * @return void
 */
function wpcloud_dashboard_styles() {
	wp_enqueue_style(
		'wpcloud-dashboard-style',
		get_stylesheet_uri(),
		[],
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'wpcloud_dashboard_styles' );


add_action( 'init', function() {
	add_filter('default_wp_template_part_areas', function($areas) {
		$areas[] = array(
			'area'        => 'sidebar',
			'label'       => _x( 'Sidebar', 'template part area' ),
			'description' => __(
				'The sidebar'
			),
			'icon'        => 'sidebar',
			'area_tag'    => 'sidebar',
		);
		return $areas;
	} );
} );
