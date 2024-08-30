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
	error_log( print_r( $fields, true ) );
	return array_merge(
		$fields,
		array(
			'type',
			'log_start',
			'log_end',
			'timezone',
			'page_size',
			'scroll_id',
			'sort_order',
			'filter-error__severity',
			'filter-site__cached',
			'filter-site__renderer',
			'filter-site__request_type',
			'filter-site__status',
			'filter-site__user_ip',
		)
	);
}
add_filter( 'wpcloud_block_form_submitted_fields_log_download', 'wpcloud_block_form_log_download_fields' );

/**
 * Process the log filters
 *
 * @param array $available_filters The available filters.
 * @param array $data The form data.
 *
 * @return array The filters.
 */
function wpcloud_block_process_log_filters( array $available_filters, array $data ): array {

	$filters = array();
	foreach ( $available_filters as $available_filter ) {
		if ( ! empty( $data[ $available_filter ] ) ) {
			$key    = explode( '__', $available_filter )[1];
			$filter = explode( ',', $data[ $available_filter ] );
			$filter = array_map( 'trim', $filter );

			$filters[ $key ] = $filter;
		}
	}

	return $filters;
}
/**
 * Process the form data for log download.
 *
 * @param array $response The response data.
 * @param array $data The form data.
 *
 * @return mixed The response data.
 */
function wpcloud_block_form_log_download_handler( $response, $data ): mixed {

	$type  = $data['type'] ?? false;
	$start = $data['log_start'] ?? false;
	$end   = $data['log_end'] ?? false;

	if ( ! $type || ! $start || ! $end ) {
		wp_send_json_error( array( 'message' => 'Missing required fields.' ), 400 );
	}

	$page_size = $data['page_size'];
	if ( empty( $page_size ) ) {
		$page_size = 500;
	}

	$time_zone = $data['timezone'] ?? 'UTC';

	$start_date = date_create( $start, new DateTimeZone( $time_zone ) );
	$start_ts   = date_timestamp_get( $start_date );

	$end_date = date_create( $end, new DateTimeZone( $time_zone ) );
	$end_ts   = date_timestamp_get( $end_date );

	$result = null;

	$options = array(
		'page_size'  => $page_size,
		'scroll_id'  => $data['scroll_id'] ?? null,
		'sort_order' => $data['sort_order'] ?? 'asc',
	);

	switch ( $type ) {
		case 'error':
			$options['filter'] = wpcloud_block_process_log_filters( array( 'filter-error__severity' ), $data );
			$result            = wpcloud_client_site_error_logs( $data['site_id'], $start_ts, $end_ts, $options );
			break;
		case 'site':
			$site_filters      = array(
				'filter-error__severity',
				'filter-site__cached',
				'filter-site__renderer',
				'filter-site__request_type',
				'filter-site__status',
				'filter-site__user_ip',
			);
			$options['filter'] = wpcloud_block_process_log_filters( $site_filters, $data );
			$result            = wpcloud_client_site_logs( $data['wpcloud_site_id'], $start_ts, $end_ts, $options );
			break;
		default:
			$response['success'] = false;
			$response['message'] = __( 'Unknown log type', 'wpcloud-block' );
			$response['status']  = 400;
			return $response;
	}

	if ( is_wp_error( $result ) ) {
		$response['success'] = false;
		$response['message'] = $result->get_error_message();
		$response['status']  = 500;
	}

	// We could use the domain name but the site name saves us a request to the API.
	$site_name = get_post_field( 'post_name', $data['site_id'] );
	$filename  = sprintf( '%s_%s_log_%s--%s.json', $site_name, $type, $start, $end );

	$log_data = wp_json_encode( $result, JSON_PRETTY_PRINT );

	header( 'Content-Description: File Transfer' );
	header( 'Content-Type: application/octet-stream' );
	header( 'Content-Disposition: attachment; filename=' . $filename );
	header( 'Content-Transfer-Encoding: binary' );
	header( 'Connection: Keep-Alive' );
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Pragma: public' );
	header( 'Content-Length: ' . strlen( $log_data ) );
	header( 'Connection: close' );
	echo $log_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	die();
}
add_filter( 'wpcloud_form_process_log_download', 'wpcloud_block_form_log_download_handler', 10, 2 );
