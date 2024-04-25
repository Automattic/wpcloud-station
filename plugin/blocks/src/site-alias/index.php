<?php

/**
 * Add the site alias field to the form.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_site_alias_fields( array $fields) {
	return array_merge( $fields, [ 'site_alias' ]);
}
add_filter('wpcloud_block_form_submitted_fields_site_alias_add', 'wpcloud_block_form_site_alias_fields' );
add_filter('wpcloud_block_form_submitted_fields_site_alias_remove', 'wpcloud_block_form_site_alias_fields' );

/**
 * Process the form data for adding a site alias.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response data.
 */
function wpcloud_block_form_site_alias_add_handler($response, $data) {
	$wpcloud_site_id = get_post_meta( $data['site_id'], 'wpcloud_site_id', true );

	if ( ! $wpcloud_site_id ) {
		$response['message'] = 'Site not found.';
		$response['status'] = 400;
		return $response;
	}

	$added = wpcloud_client_site_domain_alias_add( $wpcloud_site_id, $data[ 'site_alias' ] );

	if ( is_wp_error( $added ) ) {
		$response['success'] = false;
		$response['message'] = $added->get_error_message();
		$response['status'] = 400;
		return $response;
	}

	$response['message'] = 'Site alias added successfully.';
	$response['site_alias'] = $data['site_alias'];

	return $response;
}
add_filter('wpcloud_form_process_site_alias_add', 'wpcloud_block_form_site_alias_add_handler', 10, 2);

/**
 * Process the form data for removing a domain alias.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response data.
 */
function wpcloud_block_form_site_alias_remove_handler($response, $data) {

	$wpcloud_site_id = get_post_meta( $data['site_id'], 'wpcloud_site_id', true );

	if ( ! $wpcloud_site_id ) {
		$response['message'] = 'Site not found.';
		$response['status'] = 400;
		return $response;
	}

	$removed = wpcloud_client_site_domain_alias_remove( $wpcloud_site_id, $data['site_alias'] );

	if ( is_wp_error( $removed ) ) {
		$response['success'] = false;
		$response['message'] = $removed->get_error_message();
		$response['status'] = 400;
		return $response;
	}

	$response['message'] = 'Site alias removed successfully.';
	$response['site_alias'] = $data['site_alias'];
	return $response;
}
add_filter('wpcloud_form_process_site_alias_remove', 'wpcloud_block_form_site_alias_remove_handler', 10, 2);