<?php

function wpcloud_block_form_site_update_fields( array $fields ): array {
	$meta_fields = array_filter( WPCloud_Site::get_meta_fields(), function( $field ) {
		return $field !== false;
	} );

	return array_merge( array_keys($fields), $meta_fields );
}
add_filter( 'wpcloud_block_form_submitted_fields_site_update', 'wpcloud_block_form_site_update_fields' );

function wpcloud_block_form_site_update_handler($response, $data) {
	error_log( 'wpcloud_block_form_site_update_handler' );
	error_log( print_r( $data, true ) );
	return $response;
}
add_filter('wpcloud_form_process_update_create', 'wpcloud_block_form_site_update_handler', 10, 2);
