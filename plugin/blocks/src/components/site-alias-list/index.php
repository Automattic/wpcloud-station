<?php
/**
 * Site Alias List block.
 *
 * @package wpcloud-block
 * @subpackage site-alias-list
 */

/**
 * Add the required fields for the site alias forms.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_site_alias_list_fields( array $fields ) {
	return array_merge( $fields, array( 'site_alias' ) );
}
add_filter( 'wpcloud_block_form_submitted_fields_site_alias_remove', 'wpcloud_block_form_site_alias_fields' );
add_filter( 'wpcloud_block_form_submitted_fields_site_alias_make_primary', 'wpcloud_block_form_site_alias_fields' );

/**
 * Process the form data for removing a domain alias.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response data.
 */
function wpcloud_block_form_site_alias_remove_handler( $response, $data ) {
	$removed = wpcloud_client_site_domain_alias_remove( $data['wpcloud_site_id'], $data['site_alias'] );

	if ( is_wp_error( $removed ) ) {
		$response['success'] = false;
		$response['message'] = $removed->get_error_message();
		$response['status']  = 400;
		return $response;
	}

	$response['message']    = 'Site alias removed successfully.';
	$response['site_alias'] = $data['site_alias'];
	return $response;
}
add_filter( 'wpcloud_form_process_site_alias_remove', 'wpcloud_block_form_site_alias_remove_handler', 10, 2 );

/**
 * Process the form data for making a domain alias primary.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response data.
 */
function wpcloud_block_form_site_alias_make_primary( $response, $data ) {
	// For now we will always keep the existing primary domain as an alias.
	// Just need to figure out the UX for changing this behavior.
	$primary = wpcloud_client_site_domain_primary_set( $data['wpcloud_site_id'], $data['site_alias'], true );

	if ( is_wp_error( $primary ) ) {
		$response['success'] = false;
		$response['message'] = $primary->get_error_message();
		$response['status']  = 400;
		return $response;
	}

	$response['message']    = 'Site alias set as primary successfully.';
	$response['site_alias'] = $data['site_alias'];

	return $response;
}
add_filter( 'wpcloud_form_process_site_alias_make_primary', 'wpcloud_block_form_site_alias_make_primary', 10, 2 );


/**
 * Add the required fields for the site alias forms.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_retry_ssl_fields( array $fields ) {
	return array_merge( $fields, array( 'domain_name' ) );
}
add_filter( 'wpcloud_block_form_submitted_fields_retry_ssl', 'wpcloud_block_form_retry_ssl_fields' );

/**
 * Process the form data for retrying an SSL certificate request.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response data.
 */
function wpcloud_block_form_retry_ssl( $response, $data ) {
	// Check if we need to retry the SSL certificate request.
	$ssl_valid = WPCLOUD_Site::is_domain_ssl_valid( $data['domain_name'] );

	if ( is_wp_error( $ssl_valid ) ) {
		$response['success'] = false;
		$response['message'] = $ssl_valid->get_error_message();
		$response['status']  = 500;
		return $response;
	}
	if ( $ssl_valid ) {
		$response['message'] = 'SSL certificate is already valid.';
		return $response;
	}

	$retry = wpcloud_client_site_ssl_retry( $data['wpcloud_site_id'], $data['domain_name'] );

	if ( is_wp_error( $retry ) ) {
		$response['success'] = false;
		$response['message'] = $retry->get_error_message();
		$response['status']  = 500;
		return $response;
	}

	if ( ! $retry ) {
		$response['success'] = false;
		$response['message'] = 'Failed to retry SSL certificate request.';
		$response['status']  = 500;
		return $response;
	}

	$response['message'] = 'SSL certificate request retried successfully.';

	return $response;
}
add_filter( 'wpcloud_form_process_retry_ssl', 'wpcloud_block_form_retry_ssl', 10, 2 );
