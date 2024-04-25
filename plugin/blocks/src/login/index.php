<?php
/**
 * Add the site alias field to the form.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_login_fields( array $fields) {
	return array_merge( $fields, [ 'log', 'pwd', 'rememberme' ]);
}
add_filter('wpcloud_block_form_submitted_fields_login', 'wpcloud_block_form_login_fields' );

/**
 * Process the form data for adding a site alias.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response data.
 */
function wpcloud_block_form_login_handler( $response ) {
	$user = wp_signon();

	if ( is_wp_error( $user ) ) {
		$response['success'] = false;
		$response['message'] = $user->get_error_message();
		$response['status'] = 400;
		return $response;
	}
	wp_set_current_user( $user->ID );

	$response['message'] = 'Login successful.';
	$response['user'] = $user;

	return $response;
}
add_filter('wpcloud_form_process_login', 'wpcloud_block_form_login_handler');