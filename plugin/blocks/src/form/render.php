<?php



/** @disregard P1009 Undefined type */
$processed_content = new WP_HTML_Tag_Processor( $content );
$processed_content->next_tag( 'form' );

$fields = apply_filters( 'wpcloud_block_form_fields', '', $attributes );
$fields .= wpcloud_block_form_hidden_field( 'action', 'wpcloud_block_form_submit' );

if ( isset( $attributes[ 'wpcloudAction' ] ) && $attributes[ 'wpcloudAction' ] ) {
	$fields .= wpcloud_block_form_hidden_field( 'wpcloud_action', $attributes[ 'wpcloudAction' ] );
}

$owner_id = $attributes[ 'site_owner_id' ] ?? get_current_user_id();
$fields .= wpcloud_block_form_hidden_field( 'site_owner_id', $owner_id );

// Get the action for this form.
$action = '';
if ( isset( $attributes['ajax'] ) && $attributes['ajax'] ) {
	$action = admin_url( 'admin-ajax.php' );
	$processed_content->set_attribute( 'data-ajax', 'true' );
	$fields .= wp_nonce_field( 'wpcloud_form', '_ajax_nonce', true, false );
}
elseif ( isset( $attributes['action'] ) ) {
	$action = str_replace(
		array( '{SITE_URL}', '{ADMIN_URL}' ),
		array( site_url(), admin_url() ),
		$attributes['action']
	);
	$fields .= wp_nonce_field( 'wpcloud_form', 'nonce', true, false );
}

if ( isset( $attributes['redirect'] ) ) {
	$processed_content->set_attribute( 'data-redirect', esc_url( $attributes['redirect'] ) );
}

$processed_content->set_attribute( 'action', esc_attr( $action ) );

$method = empty( $attributes['method'] ) ? 'post' : $attributes['method'];
$processed_content->set_attribute( 'method', esc_attr( $method ) );


echo str_replace(
	'</form>',
	$fields . '</form>',
	$processed_content->get_updated_html()
);
