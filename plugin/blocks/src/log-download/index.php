<?php
/**
 * Log download.
 *
 * @package wpcloud-block
 */

/**
 * Add the log download fields to the form.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_log_download_fields( array $fields ): array {
	return array_merge(
		$fields,
		array(
			'type',
			'start',
			'end',
			'page_size',
			'scroll_id',
			'sort_order',
			'filter_error',
			'filter_server_cached',
			'filter_server_renderer',
			'filter_server_request_type',
			'filter_server_status',
			'filter_server_user_ip',
		)
	);
}
add_filter( 'wpcloud_block_form_submitted_fields_log_download', 'wpcloud_block_form_log_download_fields' );


/**
 * Process the form data for log download.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 *
 * @return void
 */
function wpcloud_block_form_log_download_handler( $response, $data ): void {
	$log_data = '{"some": "data"}';

	header( 'Content-Description: File Transfer' );
	header( 'Content-Type: application/octet-stream' );
	header( 'Content-Disposition: attachment; filename=log.json' );
	header( 'Content-Transfer-Encoding: binary' );
	header( 'Connection: Keep-Alive' );
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Pragma: public' );
	header( 'Content-Length: ' . strlen( $log_data ) );

	echo $log_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	header( 'Connection: close' );
	die();
}
add_filter( 'wpcloud_form_process_log_download', 'wpcloud_block_form_log_download_handler', 10, 2 );
