<?php

function wpcloud_block_form_create_site_fields( array $fields ) {
	$create_site_fields = array( 'site_name', 'php_version', 'data_center', 'site_owner_id', 'site_pass', 'site_email' );
	return array_merge(	$fields, $create_site_fields );
}
add_filter( 'wpcloud_block_form_submitted_fields', 'wpcloud_block_form_create_site_fields', 11, 1 );

function wpcloud_block_form_create_site_handler( $response, $data ) {
	error_log(print_r($data,true));

	if ( ! isset( $data['site_owner_id' ] ) ) {
		$data[ 'site_owner_id' ] = get_current_user_id();
	}

	$site = WPCloud_Site::create( $data );
	if ( is_wp_error( $site ) ) {
		$response['success'] = false;
		$response['message'] = $site->get_error_message();
		$response['status'] = 400;
		return $response;
	}
	if ( $site->error_message ) {
		$response['success'] = false;
		$response['message'] = $site->error_message;
		$response['status'] = 400;
		return $response;
	}

	$response['message'] = 'Site request successful.';
	$response['post_id'] = $site->id;
	$response['redirect'] = get_post_permalink($site->id);

	return $response;
}

add_filter('wpcloud_form_process_create_site', 'wpcloud_block_form_create_site_handler', 10, 2);


function wpcloud_block_form_render_field_site_owner_id( $content ) {

	$users = get_users();
	$options = '';
	foreach ( $users as $user ) {
		$options .= sprintf(
			'<option value="%d">%s</option>',
			$user->ID,
			$user->display_name
		);
	}

	$regex = '/(<select[^>]*>)(?:\s*<option[^>]*>.*?<\/option>)*\s*(<\/select>)/';
	return preg_replace($regex, '$1' . $options . '$2', $content);
}
add_filter( 'wpcloud_block_form_render_field_site_owner_id', 'wpcloud_block_form_render_field_site_owner_id' );
