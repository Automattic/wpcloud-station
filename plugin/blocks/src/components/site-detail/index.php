<?php


function wpcloud_refresh_link_handler() {
	check_ajax_referer( 'wpcloud_refresh_link' );
	$new_link = WPCLOUD_Site::refresh_linkable_detail( $_POST['site_id'], $_POST[ 'site_detail' ] );
	$new_link = apply_filters( 'wpcloud_refresh_link', $new_link, $_POST );
	wp_send_json_success( array( 'message' => 'Link refreshed.', 'url' => $new_link ) );
}

add_action( 'wp_ajax_wpcloud_refresh_link', 'wpcloud_refresh_link_handler' );