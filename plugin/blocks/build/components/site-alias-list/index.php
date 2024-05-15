<?php

/**
 * Add the required fields for the site alias forms.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_site_alias_list_fields( array $fields) {
	return array_merge( $fields, [ 'site_alias' ]);
}
add_filter('wpcloud_block_form_submitted_fields_site_alias_remove', 'wpcloud_block_form_site_alias_fields' );
add_filter('wpcloud_block_form_submitted_fields_site_alias_make_primary', 'wpcloud_block_form_site_alias_fields' );

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

function wpcloud_block_form_site_alias_make_primary($response, $data) {
	$wpcloud_site_id = get_post_meta( $data['site_id'], 'wpcloud_site_id', true );

	if ( ! $wpcloud_site_id ) {
		$response['message'] = 'Site not found.';
		$response['status'] = 400;
		return $response;
	}

	// For now we will always keep the existing primary domain as an alias.
	// Just need to figure out the UX for changing this behavior.
	$primary = wpcloud_client_site_domain_primary_set( $wpcloud_site_id, $data['site_alias'], true );

	if ( is_wp_error( $primary ) ) {
		$response['success'] = false;
		$response['message'] = $primary->get_error_message();
		$response['status'] = 400;
		return $response;
	}

	$response['message'] = 'Site alias set as primary successfully.';
	$response['site_alias'] = $data['site_alias'];

	return $response;
}
add_filter('wpcloud_form_process_site_alias_make_primary', 'wpcloud_block_form_site_alias_make_primary', 10, 2);