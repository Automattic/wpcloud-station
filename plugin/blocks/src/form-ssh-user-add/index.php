<?php
/**
 * Add the ssh add user fields to the form.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_site_ssh_user_add_fields( array $fields) {
	return array_merge( $fields, [ 'pass', 'pkey' ] );
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

	$response['message'] = 'SSH user failed to add.';
	$response['status'] = 400;
	$response['success'] = false;

	return $response;
}
add_filter('wpcloud_form_process_site_ssh_user_add', 'wpcloud_block_form__site_ssh_user_add_handler', 10, 2);