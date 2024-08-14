<?php
/**
 * Site Defensive Mode block.
 *
 * @package wpcloud-block
 */

/**
 * Add the site defensive mode fields to the form.
 *
 * @param array $fields The form fields.
 */
function wpcloud_block_form_defensive_mode_update_fields( array $fields ): array {
	return array_merge( $fields, array( 'timestamp' ) );
}
add_filter( 'wpcloud_block_form_submitted_fields_defensive_mode_update', 'wpcloud_block_form_defensive_mode_update_fields' );

/**
 * Process the form data for updating defensive mode.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 *
 * @return array The response data.
 */
function wpcloud_block_form_defensive_mode_update_handler( $response, $data ) {
	$value     = $data['timestamp'] || 0;
	$timestamp = strtotime( "+$value minutes" );
	$result    = wpcloud_client_defensive_mode_update( $data['wpcloud_site_id'], $timestamp );
	if ( is_wp_error( $result ) ) {
		$response['success'] = false;
		$response['message'] = $result->get_error_message();
		$response['status']  = 400;
		return $response;
	}
	$response['message'] = 'Defensive mode disabled successfully.';

	$date_format           = get_option( 'date_format' );
	$time_format           = get_option( 'time_format' );
	$response['ddosUntil'] = __( 'Enabled until: ' ) . gmdate( "$date_format $time_format", $timestamp );
	return $response;
}
add_filter( 'wpcloud_form_process_defensive_mode_update', 'wpcloud_block_form_defensive_mode_update_handler', 10, 2 );

/**
 * Disable defensive mode request handler
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response.
 */
function wpcloud_block_form_defensive_mode_disable_handler( $response, $data ) {
	$wpcloud_site_id = $data['wpcloud_site_id'];
	$result          = wpcloud_client_defensive_mode_update( $wpcloud_site_id );
	if ( is_wp_error( $result ) ) {
		$response['success'] = false;
		$response['message'] = $result->get_error_message();
		$response['status']  = 400;
		return $response;
	}
	$response['message'] = 'Defensive mode disabled successfully.';
	return $response;
}
add_filter( 'wpcloud_form_process_defensive_mode_disable', 'wpcloud_block_form_defensive_mode_disable_handler', 10, 2 );
