<?php
/**
 * WP Cloud Metrics
 *
 * @package wpcloud
 */

/**
 * Enqueue uplot assets.
 */
function enqueue_uplot_assets() {
	wp_enqueue_style(
		'uplot-css',
		'https://unpkg.com/uplot/dist/uPlot.min.css',
		array(),
		null
	);
}
add_action( 'enqueue_block_assets', 'enqueue_uplot_assets' );
