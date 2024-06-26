<?php

function wpcloud_block_form_site_access_type_fields( array $fields ): array {
	return array_merge( $fields, [ 'site_access_with_ssh' ] );
}
add_filter( 'wpcloud_block_form_submitted_fields_site_access_type', 'wpcloud_block_form_site_access_type_fields' );

function wpcloud_block_form_site_access_type_handler( $response, $data ) {
	$wpcloud_site_id = get_post_meta( $data['site_id'], 'wpcloud_site_id', true );

	if ( ! $wpcloud_site_id ) {
		$response['message'] = 'Site not found.';
		$response['status'] = 400;
		return $response;
	}

	$site_access_type =  $data['site_access_with_ssh'] === "1" ? 'ssh' : 'sftp';

	$result = wpcloud_client_site_set_access_type( $wpcloud_site_id, $site_access_type );

	if ( is_wp_error( $result ) ) {
		$response['success'] = false;
		$response['message'] = $result->get_error_message();
		$response['status'] = 400;
		return $response;
	}

	$response['message'] = "Site access type updated successfully to $site_access_type.";

	return $response;
}
add_filter('wpcloud_form_process_site_access_type', 'wpcloud_block_form_site_access_type_handler', 10, 2);


function wpcloud_block_form_site_ssh_disconnect_all_users_handler( $response, $data ) {
	$wpcloud_site_id = get_post_meta( $data['site_id'], 'wpcloud_site_id', true );

	if ( ! $wpcloud_site_id ) {
		$response['message'] = 'Site not found.';
		$response['status'] = 400;
		return $response;
	}

	$result = wpcloud_client_ssh_disconnect_all_users( $wpcloud_site_id );

	if ( is_wp_error( $result ) ) {
		$response['success'] = false;
		$response['message'] = $result->get_error_message();
		$response['status'] = 400;
		return $response;
	}

	$response['message'] = 'SSH users disconnected successfully.';

	return $response;
}
add_filter('wpcloud_form_process_site_ssh_disconnect_all_users', 'wpcloud_block_form_site_ssh_disconnect_all_users_handler', 10, 2);