<?php
/**
 * Add the ssh add user fields to the form.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_site_ssh_user_remove_fields( array $fields) {
	return array_merge( $fields, [ 'ssh_user' ] );
}
add_filter('wpcloud_block_form_submitted_fields_site_ssh_user_remove', 'wpcloud_block_form_site_ssh_user_remove_fields' );

/**
 * Process the form data for remove ssh user
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response data.
 */
function wpcloud_block_form__site_ssh_user_remove_handler( $response, $data ) {
	$wpcloud_site_id = get_post_meta( $data['site_id'], 'wpcloud_site_id', true );

	if ( ! $wpcloud_site_id ) {
		$response['message'] = 'Site not found.';
		$response['status'] = 400;
		return $response;
	}

	$user = sanitize_user( $data[ 'ssh_user' ] );
	$result = wpcloud_client_ssh_user_remove( $wpcloud_site_id, $user );

	if ( is_wp_error( $result ) ) {
		$response['success'] = false;
		$response['message'] = $result->get_error_message();
		$response['status'] = 400;
		return $response;
	}

	$response['message'] = 'SSH user removed successfully.';
	$response['user'] = $user;

	return $response;
}
add_filter('wpcloud_form_process_site_ssh_user_remove', 'wpcloud_block_form__site_ssh_user_remove_handler', 10, 2);
