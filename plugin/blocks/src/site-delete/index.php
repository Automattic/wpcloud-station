<?php


function wpcloud_block_form_site_delete_handler( $response, $data) {
	//$wpcloud_site_id = get_post_meta( $data['site_id'], 'wpcloud_site_id', true );
	$post = get_post( $data['site_id'] );
	if ( ! $post) {
		$response['message'] = 'Site not found.';
		$response['status'] = 400;
		return $response;
	}

	$result = wp_delete_post( $data['site_id'], true );

	if ( is_wp_error( $result ) ) {
		$response['success'] = false;
		$response['message'] = 'Error deleting site.';
		$response['status'] = 400;
		return $response;
	}

	$response['message'] = 'Site deleted successfully.';
	$response['redirect'] = '/sites';

	return $response;
}
add_filter('wpcloud_form_process_site_delete', 'wpcloud_block_form_site_delete_handler', 10, 2);
