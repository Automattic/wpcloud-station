<?php
/**
 * Site SSH Settings block.
 *
 * @package wpcloud-block
 * @subpackage site-ssh-settings
 */

/**
 * Add the site access type fields to the form.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_site_access_type_fields( array $fields ): array {
	return array_merge( $fields, array( 'site_access_with_ssh' ) );
}
add_filter( 'wpcloud_block_form_submitted_fields_site_access_type', 'wpcloud_block_form_site_access_type_fields' );

/**
 * Process the form data for site access type.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response.
 */
function wpcloud_block_form_site_access_type_handler( $response, $data ) {
	$wpcloud_site_id = get_post_meta( $data['site_id'], 'wpcloud_site_id', true );

	if ( ! $wpcloud_site_id ) {
		$response['message'] = 'Site not found.';
		$response['status']  = 400;
		return $response;
	}

	$site_access_type = '1' === $data['site_access_with_ssh'] ? 'ssh' : 'sftp';

	$result = wpcloud_client_site_set_access_type( $wpcloud_site_id, $site_access_type );

	if ( is_wp_error( $result ) ) {
		$response['success'] = false;
		$response['message'] = $result->get_error_message();
		$response['status']  = 400;
		return $response;
	}

	$response['message'] = "Site access type updated successfully to $site_access_type.";

	return $response;
}
add_filter( 'wpcloud_form_process_site_access_type', 'wpcloud_block_form_site_access_type_handler', 10, 2 );

/**
 * Process ssh disconnect all users request.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response.
 */
function wpcloud_block_form_site_ssh_disconnect_all_users_handler( $response, $data ) {
	$wpcloud_site_id = get_post_meta( $data['site_id'], 'wpcloud_site_id', true );

	if ( ! $wpcloud_site_id ) {
		$response['message'] = 'Site not found.';
		$response['status']  = 400;
		return $response;
	}

	$result = wpcloud_client_ssh_disconnect_all_users( $wpcloud_site_id );

	if ( is_wp_error( $result ) ) {
		$response['success'] = false;
		$response['message'] = $result->get_error_message();
		$response['status']  = 400;
		return $response;
	}

	$response['message'] = 'SSH users disconnected successfully.';

	return $response;
}
add_filter( 'wpcloud_form_process_site_ssh_disconnect_all_users', 'wpcloud_block_form_site_ssh_disconnect_all_users_handler', 10, 2 );
