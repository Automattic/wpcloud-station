<?php


function wpcloud_block_form_site_delete_handler( $response, $data) {
	$wpcloud_site_id = get_post_meta( $data['site_id'], 'wpcloud_site_id', true );

	if ( ! $wpcloud_site_id ) {
		$response['message'] = 'Site not found.';
		$response['status'] = 400;
		return $response;
	}

	$result = wpcloud_client_site_delete( $wpcloud_site_id );

	if ( is_wp_error( $result ) ) {
		$response['success'] = false;
		$response['message'] = $result->get_error_message();
		$response['status'] = 400;
		return $response;
	}

	$response['message'] = 'Site deleted successfully.';
	$response['redirect'] = '/sites';

	return $response;
}
add_filter('wpcloud_form_process_site_delete', 'wpcloud_block_form_site_delete_handler', 10, 2);
