<?php

function wpcloud_block_form_hidden_field( $name, $value ) {
	return '<input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" />';
}

function wpcloud_form_submit_handler() {
	check_ajax_referer( 'wpcloud_form' );
	$action = $_POST[ 'wpcloud_action' ] ?? '';

	// Get the form fields.
	$fields = apply_filters( 'wpcloud_block_form_submitted_fields', array(
		'wpcloud_action',
		'redirect',
	), array_keys( $_POST ) );
	$fields = apply_filters( 'wpcloud_block_form_submitted_fields_' . $action , $fields, array_keys( $_POST ) );


	// Get the form data.
	$data = array();
	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			$data[ $field ] = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
		}
	}

	// Process the form data.
	$success_result = array(
		'success' => true,
		'message' => 'Form submitted successfully.',
		'redirect' => $data[ 'redirect' ] ?? '',
		'status' => 200,
		'action' => $action,
	);

	$result = apply_filters( 'wpcloud_form_process_' . $action, $success_result, $data );

	if ( $result[ 'success' ] === false ) {
		wp_send_json_error($result, $result[ 'status' ] ?? 400);
	}

	wp_send_json_success($result, $result[ 'status' ] ?? 200);
}

add_action( 'wp_ajax_wpcloud_form_submit', 'wpcloud_form_submit_handler' );
add_action( 'wp_ajax_nopriv_wpcloud_form_submit', 'wpcloud_form_submit_handler' );