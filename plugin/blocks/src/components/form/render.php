<?php
/** @disregard P1009 Undefined type */
$processed_content = new WP_HTML_Tag_Processor( $content );
$processed_content->next_tag( 'form' );

$fields = apply_filters( 'wpcloud_block_form_fields', '', $attributes );
$fields .= wpcloud_block_form_hidden_field( 'action', 'wpcloud_block_form_submit' );

$submitOnChange = $attributes['submitOnChange'] ?? false;
if ( $submitOnChange ) {
	$processed_content->set_attribute( 'data-submit-on-change', 'true' );
}

if ( isset( $attributes[ 'wpcloudAction' ] ) && $attributes[ 'wpcloudAction' ] ) {
	$fields .= wpcloud_block_form_hidden_field( 'wpcloud_action', $attributes[ 'wpcloudAction' ] );
	$processed_content->set_attribute( 'data-original-action', $attributes[ 'wpcloudAction' ] );
}

if ( isset( $attributes[ 'redirect' ] ) ) {
	$fields .= wpcloud_block_form_hidden_field( 'redirect', $attributes[ 'redirect' ] );
}

// Get the action for this form.
$action = '';
if ( isset( $attributes['ajax'] ) && $attributes['ajax'] ) {
	$action = admin_url( 'admin-ajax.php' );
	$processed_content->set_attribute( 'data-ajax', 'true' );
	$fields .=  wpcloud_block_form_hidden_field('_ajax_nonce', wp_create_nonce( 'wpcloud_form' ));
}
elseif ( isset( $attributes['action'] ) ) {
	$action = str_replace(
		array( '{SITE_URL}', '{ADMIN_URL}' ),
		array( site_url(), admin_url() ),
		$attributes['action']
	);
	$fields .= wpcloud_block_form_hidden_field('_wpnonce', wp_create_nonce( 'wpcloud_form' ));
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
