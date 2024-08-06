<?php
/**
 * This is a PHP file for handling form submissions.
 *
 * @package wpcloud-block
 * @subpackage form
 */

/**
 * Add a hidden field to the form.
 *
 * @param string $name The name of the field.
 * @param string $value The value of the field.
 * @return string The hidden field.
 */
function wpcloud_block_form_hidden_field( $name, $value ) {
	return '<input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" />';
}

/**
 * Handle form submissions.
 *
 * @return void
 */
function wpcloud_form_submit_handler() {
	check_ajax_referer( 'wpcloud_form' );
	$action = trim( sanitize_text_field( wp_unslash( $_POST['wpcloud_action'] ?? '' ) ) );

	$post_data = $_POST;

	if ( isset( $post_data['site_id'] ) ) {
		$post_data['wpcloud_site_id'] = get_post_meta( $post_data['site_id'], 'wpcloud_site_id', true );
	}

	// Get the form fields.
	$fields = apply_filters(
		'wpcloud_block_form_submitted_fields',
		array(
			'wpcloud_action',
			'redirect',
			'wpcloud_site_id',
		),
		array_keys( $post_data )
	);

	$fields = apply_filters( 'wpcloud_block_form_submitted_fields_' . $action, $fields, array_keys( $post_data ) );

	// Get the form data.
	$data = array();
	foreach ( $fields as $field ) {
		if ( isset( $post_data[ $field ] ) ) {
			$data[ $field ] = sanitize_text_field( wp_unslash( $post_data[ $field ] ) );
		}
	}

	// Process the form data.
	$success_result = array(
		'success'  => true,
		'message'  => 'Form submitted successfully.',
		'redirect' => $data['redirect'] ?? '',
		'status'   => 200,
		'action'   => $action,
	);

	$result = apply_filters( 'wpcloud_form_process_' . $action, $success_result, $data );

	if ( false === $result['success'] ) {
		wp_send_json_error( $result, $result['status'] ?? 400 );
	}

	wp_send_json_success( $result, $result['status'] ?? 200 );
}

add_action( 'wp_ajax_wpcloud_form_submit', 'wpcloud_form_submit_handler' );
add_action( 'wp_ajax_nopriv_wpcloud_form_submit', 'wpcloud_form_submit_handler' );
