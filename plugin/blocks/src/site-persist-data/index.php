<?php
/**
 * Site Persist Data block.
 *
 * @package wpcloud-block
 * @subpackage site-persist-data
 */

/**
 * Add the site persist data fields to the form.
 *
 * @param array $fields The form fields.
 *
 * @return array The form fields.
 */
function wpcloud_block_form_site_persist_data_fields( array $fields ): array {
	return array_merge( $fields, array( 'key', 'value' ) );
}
add_filter( 'wpcloud_block_form_submitted_fields_site_persist_data_set', 'wpcloud_block_form_site_persist_data_fields' );
add_filter( 'wpcloud_block_form_submitted_fields_site_persist_data_delete', 'wpcloud_block_form_site_persist_data_fields' );


/**
 * Handle request to persist data for a site.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 *
 * @return array The response.
 */
function wpcloud_block_form_site_persist_data_set_handler( array $response, array $data ): array {
	$result = wpcloud_client_site_persist_data_set( $data['wpcloud_site_id'], $data['key'], $data['value'] );

	if ( is_wp_error( $result ) ) {
		$response['success'] = false;
		$response['message'] = $result->get_error_message();
		$response['status']  = 400;
		return $response;
	}

	$response['message'] = __( 'Set persist data request succeeded.', 'wpcloud' );

	return $response;
}
add_filter( 'wpcloud_form_process_site_persist_data_set', 'wpcloud_block_form_site_persist_data_set_handler', 10, 2 );

/**
 * Handle request to delete persisted data for a site.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 *
 * @return array The response.
 */
function wpcloud_block_form_site_persist_data_delete_handler( array $response, array $data ): array {
	$result = wpcloud_client_site_persist_data_delete( $data['wpcloud_site_id'], $data['key'] );

	if ( is_wp_error( $result ) ) {
		$response['success'] = false;
		$response['message'] = $result->get_error_message();
		$response['status']  = 400;
		return $response;
	}

	$response['message'] = __( 'Remove persist data request succeeded.', 'wpcloud' );

	return $response;
}
add_filter( 'wpcloud_form_process_site_persist_data_delete', 'wpcloud_block_form_site_persist_data_delete_handler', 10, 2 );
