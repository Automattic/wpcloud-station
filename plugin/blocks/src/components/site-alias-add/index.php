<?php
/**
 * Add Site Alias block.
 *
 * @package wpcloud-block
 * @subpackage site-alias-add
 */

// Rest of the code...

/**
 * Add the required fields for the site alias forms.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_site_alias_fields( array $fields ) {
	return array_merge( $fields, array( 'site_alias' ) );
}
add_filter( 'wpcloud_block_form_submitted_fields_site_alias_add', 'wpcloud_block_form_site_alias_fields' );

/**
 * Process the form data for adding a site alias.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response data.
 */
function wpcloud_block_form_site_alias_add_handler( $response, $data ) {
	$added = wpcloud_client_site_domain_alias_add( $data['wpcloud_site_id'], $data['site_alias'] );

	if ( is_wp_error( $added ) ) {
		$message = $added->get_error_message();
		if ( str_contains( $message, 'TXT' ) ) {
			$response['needsVerification'] = true;
			$response['site_alias']        = $data['site_alias'];
			do_action( 'wpcloud_site_alias_needs_verification', $data['site_alias'], $data['wpcloud_site_id'] );
		}
		$response['success'] = false;
		$response['message'] = $added->get_error_message();
		$response['status']  = 400;
		return $response;
	}

	$response['message']    = 'Site alias added successfully.';
	$response['site_alias'] = $data['site_alias'];

	return $response;
}
add_filter( 'wpcloud_form_process_site_alias_add', 'wpcloud_block_form_site_alias_add_handler', 10, 2 );

/**
 * Add the required fields for the domain verification forms.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_request_txt_verification_fields( array $fields ) {
	return array_merge( $fields, array( 'domain_name' ) );
}
add_filter( 'wpcloud_block_form_submitted_fields_request_txt_verification', 'wpcloud_block_form_request_txt_verification_fields' );

/**
 * Process the form data for verifying a site alias.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response data.
 */
function wpcloud_block_form_request_txt_verification_handler( $response, $data ) {

	$verification_code = wpcloud_client_domain_verification_record( $data['domain_name'] ?? '' );

	if ( is_wp_error( $verification_code ) ) {
		$response['success'] = false;
		$response['message'] = $verification_code->get_error_message();
		$response['status']  = 409;
		return $response;
	}

	$response['message'] = 'TXT verification request sent successfully.';
	$response['code']    = $verification_code;

	return $response;
}
add_filter( 'wpcloud_form_process_request_txt_verification', 'wpcloud_block_form_request_txt_verification_handler', 10, 2 );
