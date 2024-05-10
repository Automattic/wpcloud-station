<?php

add_action( 'init', 'wpcloud_station_enqueue_block_styles' );

function wpcloud_station_enqueue_block_styles() {
	foreach( glob( __DIR__ . '/assets/blocks/*.css' ) as $block_style_sheet ) {
		$block = preg_replace('/-/','/', basename( $block_style_sheet, '.css' ), 1);
		wp_enqueue_block_style( $block, array(
			'handle' => basename( $block_style_sheet, '.css'),
			'src'    => get_theme_file_uri( 'assets/blocks/' . basename( $block_style_sheet ) ),
			'path'   => get_theme_file_path( 'assets/blocks/' . basename( $block_style_sheet ) )
			) );
	}
}

// We don't want to show the admin bar in the front end.
add_filter( 'show_admin_bar', '__return_false' );


add_action( 'pre_get_posts', function($query) {
	//error_log(print_r($query->query, true));
});