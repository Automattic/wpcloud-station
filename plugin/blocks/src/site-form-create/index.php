<?php

function wpcloud_block_form_create_site_fields( array $fields ) {
	$create_site_fields = array( 'site_name', 'domain_name', 'php_version', 'data_center', 'site_owner_id', 'site_pass', 'site_email', 'admin_pass' );
	return array_merge(	$fields, $create_site_fields );
}
add_filter( 'wpcloud_block_form_submitted_fields_create_site', 'wpcloud_block_form_create_site_fields', 11, 1 );

function wpcloud_block_form_create_site_handler( $response, $data ) {
	if ( ! isset( $data['site_owner_id' ] ) ) {
		$data[ 'site_owner_id' ] = get_current_user_id();
	}

	if ( ! isset( $data[ 'site_name' ] ) ) {
		$data[ 'site_name' ] = $data[ 'domain_name' ];
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

function wpcloud_block_site_form_enqueue_scripts() {
	wp_register_script( 'wpcloud-blocks-site-form', '',);
	wp_enqueue_script( 'wpcloud-blocks-site-form' );
	wp_add_inline_script(
		'wpcloud-blocks-site-form',
		'window.wpcloud = window.wpcloud ?? {};' .
		 'wpcloud.siteDetailKeys=' . json_encode( WPCloud_Site::DETAIL_KEYS ) . ';' .
		 'wpcloud.phpVersions=' . json_encode( wpcloud_block_available_php_options() ) . ';' .
		 'wpcloud.dataCenters=' . json_encode( wpcloud_block_available_datacenters_options() ) . ';'
	);
}
add_action( 'admin_enqueue_scripts', 'wpcloud_block_site_form_enqueue_scripts' );

/**
 * Render the PHP version field with the available PHP versions.
 * We need to dynamically populate the PHP versions since they could change after the create site form is added to a page.
 * @param string $content The content of the field.
 * @return string The content of the field with the PHP versions.
 */
function wpcloud_block_form_render_field_php_version( $content ):string {
	$options = wpcloud_block_available_php_options();

	// if for some reason we don't have any php versions, return the content as is.
	if ( empty( $options ) ) {
		return $content;
	}

	$options = array_reduce( $options, function( $options, $version ) {
		$options .= sprintf(
			'<option value="%s">%s</option>',
			$version,
			$version
		);
		return $options;
	}, '' );

	$regex = '/(<select[^>]*>)(?:\s*<option[^>]*>.*?<\/option>)*\s*(<\/select>)/';
	return preg_replace($regex, '$1' . $options . '$2', $content);
}
add_action('wpcloud_block_form_render_field_php_version', 'wpcloud_block_form_render_field_php_version');

/**
 * Render the data center field with the available data centers.
 * @param string $content The content of the field.
 * @return string The content of the field with the data centers.
 */
function wpcloud_block_form_render_field_data_center( $content ):string{
	$options = wpcloud_block_available_datacenters_options();

	// if for some reason we don't have any data centers, return the content as is.
	if ( empty( $options ) ) {
		return $content;
	}

	$options = array_reduce( $options, function( $options, $city ) {
		$options .= sprintf(
			'<option value="%s">%s</option>',
			$city,
			$city
		);
		return $options;
	}, '' );

	$regex = '/(<select[^>]*>)(?:\s*<option[^>]*>.*?<\/option>)*\s*(<\/select>)/';
	return preg_replace($regex, '$1' . $options . '$2', $content);
}
add_action( 'wpcloud_block_form_render_field_data_center', 'wpcloud_block_form_render_field_data_center' );
