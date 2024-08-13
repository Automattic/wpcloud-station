<?php
/**
 * Site Update block.
 *
 * @package wpcloud-block
 * @subpackage site-update
 */

/**
 * Add the site update fields to the form.

 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_site_update_fields( array $fields ): array {
	$meta_fields = array_filter(
		WPCloud_Site::get_mutable_fields(),
		function ( $field ) {
			return false !== $field;
		}
	);

	return array_merge( $fields, array_keys( $meta_fields ) );
}
add_filter( 'wpcloud_block_form_submitted_fields_site_update', 'wpcloud_block_form_site_update_fields' );

/**
 * Process the form data for site update
 *
 * @param array $response The response data.
 * @param array $data The form data.
 *
 * @return array The response data.
 */
function wpcloud_block_form_site_update_handler( $response, $data ) {
	$mutable_fields = array_keys( WPCloud_Site::get_mutable_fields() );

	$site_mutable_fields = array_filter(
		$data,
		function ( $value, $key ) use ( $mutable_fields ) {
			return in_array( $key, $mutable_fields, true );
		},
		ARRAY_FILTER_USE_BOTH
	);

	$result = null;

	foreach ( $site_mutable_fields as $key => $value ) {
		switch ( $key ) {
			case 'canonical_aliases':
				// canonicalize_aliases doesn't like "truthy" values.
				$value = $value ? 'true' : 'false';
				break;

			case 'suspend_after':
				// Don't set the suspend_after value if it's the same as the current value or is not set.
				$current_suspend_value = wpcloud_get_site_detail( $data['site_id'], 'suspend_after' );
				if ( is_null( $current_suspend_value ) ) {
					$current_suspend_value = '';
				}
				if ( ! is_null( $current_suspend_value ) && $value === $current_suspend_value ) {
					continue 2;
				}
				break;

			case 'space_quota':
				$value = intval( $value ) . 'G';
				break;

			case 'edge_cache':
				$result = wpcloud_client_edge_cache_update( $data['wpcloud_site_id'], $value );
				break;
		}

		if ( is_null( $result ) ) {
			$result = $value ? wpcloud_client_update_site_meta( $data['wpcloud_site_id'], $key, $value ) : wpcloud_client_delete_site_meta( $data['wpcloud_site_id'], $key );
		}

		if ( is_wp_error( $result ) ) {
			$response['success'] = false;
			$response['message'] = $result->get_error_message();
			$response['status']  = 400;
			return $response;
		}
	}

	$response['message'] = 'Site updated successfully.';
	return $response;
}
add_filter( 'wpcloud_form_process_site_update', 'wpcloud_block_form_site_update_handler', 10, 2 );
