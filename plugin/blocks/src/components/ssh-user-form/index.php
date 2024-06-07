<?php
/**
 * Add the ssh add user fields to the form.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_site_ssh_user_add_fields( array $fields) {
	return array_merge( $fields, [ 'pass', 'pkey', 'user' ] );
}
add_filter('wpcloud_block_form_submitted_fields_site_ssh_user_add', 'wpcloud_block_form_site_ssh_user_add_fields' );

/**
 * Process the form data for add ssh user
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response data.
 */
function wpcloud_block_form__site_ssh_user_add_handler( $response, $data ) {
	$wpcloud_site_id = get_post_meta( $data['site_id'], 'wpcloud_site_id', true );

	if ( ! $wpcloud_site_id ) {
		$response['message'] = 'Site not found.';
		$response['status'] = 400;
		return $response;
	}

	// Use current user if user is not set
	if ( ! isset( $data['user'] ) ) {
		$user = wp_get_current_user();
		$data[ 'user' ] = $user->user_login;
	}

	$user = sanitize_user( $data[ 'user' ] );
	$result = wpcloud_client_ssh_user_add( $wpcloud_site_id, uniqid( $user ), $data['pkey'], $data['pass'] );

	if ( is_wp_error( $result ) ) {
		$response['success'] = false;
		$response['message'] = $result->get_error_message();
		$response['status'] = 400;
		return $response;
	}

	$response['message'] = 'SSH user added successfully.';
	$response['user'] = $result;

	return $response;
}
add_filter('wpcloud_form_process_site_ssh_user_add', 'wpcloud_block_form__site_ssh_user_add_handler', 10, 2);