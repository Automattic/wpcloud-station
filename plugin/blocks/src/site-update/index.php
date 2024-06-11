<?php

function wpcloud_block_form_site_update_fields( array $fields ): array {
	$meta_fields = array_filter( WPCloud_Site::get_meta_fields(), function( $field ) {
		return $field !== false;
	} );

	return array_merge( $fields, array_keys( $meta_fields ) );
}
add_filter( 'wpcloud_block_form_submitted_fields_site_update', 'wpcloud_block_form_site_update_fields' );

function wpcloud_block_form_site_update_handler($response, $data) {
	$meta_fields = array_keys( WPCloud_Site::get_meta_fields() );

	$site_meta = array_filter( $data, function( $value, $key ) use ( $meta_fields ){
		return in_array( $key, $meta_fields );
	}, ARRAY_FILTER_USE_BOTH );

	foreach ( $site_meta as $key => $value) {
		// canonicalize_aliases doesn't like "truthy" values
		if ( 'canonical_aliases'  === $key ) {
			$value = $value ? "true" : "false";
		}

		// @TODO might have to do a similar check for other meta fields
		if ( 'suspend_after' === $key ) {
			$current_suspend_value = wpcloud_get_site_detail( $data['site_id'], 'suspend_after');
			if ( is_null( $current_suspend_value ) ) {
				$current_suspend_value = '';
			}
			if ( ! is_null( $current_suspend_value ) && $value === $current_suspend_value ) {
				continue;
			}
		}

		$result = wpcloud_client_update_site_meta( $data['wpcloud_site_id'], $key, $value );
		if ( is_wp_error( $result ) ) {
			$response['success'] = false;
			$response['message'] = $result->get_error_message();
			$response['status'] = 400;
			return $response;
		}
	}

	$response['message'] = 'Site updated successfully.';
	return $response;
}
add_filter('wpcloud_form_process_site_update', 'wpcloud_block_form_site_update_handler', 10, 2);