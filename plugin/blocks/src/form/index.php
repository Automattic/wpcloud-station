<?php


function wpcdb_form_render( $attributes, $content ) {
	/** @disregard P1009 Undefined type */
	$processed_content = new WP_HTML_Tag_Processor( $content );
	$processed_content->next_tag( 'form' );

	// Get the action for this form.
	$action = '';
	if ( isset( $attributes['action'] ) ) {
		$action = str_replace(
			array( '{SITE_URL}', '{ADMIN_URL}' ),
			array( site_url(), admin_url() ),
			$attributes['action']
		);
	}
	$processed_content->set_attribute( 'action', esc_attr( $action ) );

	$method = empty( $attributes['method'] ) ? 'post' : $attributes['method'];
	$processed_content->set_attribute( 'method', esc_attr( $method ) );

	$fields = apply_filters( 'wpcdb_form_fields', '', $attributes );

	return str_replace(
		'</form>',
		$fields . '</form>',
		$processed_content->get_updated_html()
	);
 }

 function wpcdb_register_form_block() {
	register_block_type(
		dirname( __DIR__, 2 ) . '/build/form',
		array(
			'render_callback' => 'wpcdb_form_render',
		)
	);
 }
 add_action( 'init', 'wpcdb_register_form_block' );