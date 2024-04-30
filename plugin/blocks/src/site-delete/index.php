<?php


function wpcloud_block_form_site_delete_handler( $response, $data ) {
	$response['success'] = false;
	$response['status'] = 501;

	return $response;
}
add_filter('wpcloud_form_process_site_delete', 'wpcloud_block_form_site_delete_handler', 10, 2);
