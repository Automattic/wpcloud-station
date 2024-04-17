<?php

function wpcloud_block_create_site() {
	check_ajax_referer( 'wpcloud-form' );
	error_log('wpcloud block create site ');
}

add_action( 'wp_ajax_wpcloud_create_site', 'wpcloud_block_create_site' );
add_action( 'wp_ajax_nopriv_wpcloud_create_site', 'wpcloud_block_create_site' );
