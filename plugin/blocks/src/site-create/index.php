<?php
/**
 * Site Create block.
 *
 * @package wpcloud-block
 * @subpackage site-create
 */

/**
 * Add the site create fields to the form.
 *
 * @param array $fields The form fields.
 * @return array The form fields.
 */
function wpcloud_block_form_site_create_fields( array $fields ) {
	$site_create_fields = array(
		'site_name',
		'domain_name',
		'php_version',
		'data_center',
		'site_owner_id',
		'site_pass',
		'site_email',
		'admin_pass',
		// available meta fields.
		'development_mode',
		'privacy_model',
		'photon_subsizes',
		'static_file_404',
		'default_php_conns',
		'burst_php_conns',
	);
	return array_merge( $fields, $site_create_fields );
}
add_filter( 'wpcloud_block_form_submitted_fields_site_create', 'wpcloud_block_form_site_create_fields', 11, 1 );


/**
 * Add the site owner options to the form.
 *
 * @param array $attributes The attributes of the field.
 *
 * @return array The attributes of the field.
 */
function wpcloud_block_site_owner_options( $attributes ): array {
	$users   = get_users();
	$options = array();
	foreach ( $users as $user ) {
		$options[ $user->ID ] = $user->display_name;
	}

	$current_user          = wp_get_current_user();
	$attributes['value']   = $current_user->ID;
	$attributes['options'] = $options;
	return $attributes;
}
add_filter( 'wpcloud_block_form_field_attributes_site_owner_id', 'wpcloud_block_site_owner_options' );


/**
 * Add Data Center options to the form.
 *
 * @param array $attributes The attributes of the field.
 * @return array The attributes of the field.
 */
function wpcloud_block_attributes_data_center( $attributes ): array {
	$dcs     = wpcloud_block_available_datacenters_options();
	$options = array();
	foreach ( $dcs as $key => $dc ) {
		$options[ $key ] = $dc;
	}

	$attributes['options'] = $options;
	return $attributes;
}
add_filter( 'wpcloud_block_form_field_attributes_data_center', 'wpcloud_block_attributes_data_center' );

/**
 * Add PHP version options to the form.
 *
 * @param array $attributes The attributes of the field.
 * @return array The attributes of the field.
 */
function wpcloud_block_attributes_php_version( $attributes ): array {
	$php_versions = wpcloud_block_available_php_options();
	$options      = array();
	foreach ( $php_versions as $key => $php_version ) {
		$options[ $key ] = $php_version;
	}

	$attributes['options'] = $options;
	return $attributes;
}
add_filter( 'wpcloud_block_form_field_attributes_php_version', 'wpcloud_block_attributes_php_version' );

/**
 * Process the form data for site create
 *
 * @param array $response The response data.
 * @param array $data The form data.
 * @return array The response data.
 */
function wpcloud_block_form_site_create_handler( $response, $data ) {
	if ( ! isset( $data['site_owner_id'] ) ) {
		$data['site_owner_id'] = get_current_user_id();
	}

	if ( ! isset( $data['site_name'] ) ) {
		$data['site_name'] = $data['domain_name'];
	}

	$data['meta'] = array();

	$meta_keys = array(
		'development_mode',
		'privacy_model',
		'photon_subsizes',
		'static_file_404',
		'default_php_conns',
		'burst_php_conns',
	);

	foreach ( $meta_keys as $key ) {
		if ( isset( $data[ $key ] ) ) {
			$data['meta'][ $key ] = $data[ $key ];
		}
		unset( $data[ $key ] );
	}

	$site = WPCloud_Site::create( $data );
	if ( is_wp_error( $site ) ) {
		$response['success'] = false;
		$response['message'] = $site->get_error_message();
		$response['status']  = 400;
		return $response;
	}
	if ( $site->error_message ) {
		$response['success'] = false;
		$response['message'] = $site->error_message;
		$response['status']  = 400;
		return $response;
	}

	$response['message']  = 'Site request successful.';
	$response['post_id']  = $site->ID;
	$response['redirect'] = '/sites';

	return $response;
}
add_filter( 'wpcloud_form_process_site_create', 'wpcloud_block_form_site_create_handler', 10, 2 );
