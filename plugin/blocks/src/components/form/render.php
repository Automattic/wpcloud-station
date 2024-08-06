<?php
/**
 * Render forms.
 *
 * @package wpcloud-block
 * @subpackage form
 */

$processed_content = new WP_HTML_Tag_Processor( $content );
$processed_content->next_tag( 'form' );

$fields  = apply_filters( 'wpcloud_block_form_fields', '', $attributes );
$fields .= wpcloud_block_form_hidden_field( 'action', 'wpcloud_block_form_submit' );

if ( isset( $attributes['wpcloudAction'] ) && $attributes['wpcloudAction'] ) {
	$fields .= wpcloud_block_form_hidden_field( 'wpcloud_action', $attributes['wpcloudAction'] );
	$processed_content->set_attribute( 'data-original-action', $attributes['wpcloudAction'] );
}

if ( isset( $attributes['redirect'] ) ) {
	$fields .= wpcloud_block_form_hidden_field( 'redirect', $attributes['redirect'] );
}

// Get the action for this form.
$form_action = '';
if ( isset( $attributes['ajax'] ) && $attributes['ajax'] ) {
	$form_action = admin_url( 'admin-ajax.php' );
	$processed_content->set_attribute( 'data-ajax', 'true' );
	$fields .= wpcloud_block_form_hidden_field( '_ajax_nonce', wp_create_nonce( 'wpcloud_form' ) );
} elseif ( isset( $attributes['action'] ) ) {
	$form_action = str_replace(
		array( '{SITE_URL}', '{ADMIN_URL}' ),
		array( site_url(), admin_url() ),
		$attributes['action']
	);
	$fields     .= wpcloud_block_form_hidden_field( '_wpnonce', wp_create_nonce( 'wpcloud_form' ) );
}

if ( isset( $attributes['redirect'] ) ) {
	$processed_content->set_attribute( 'data-redirect', esc_url( $attributes['redirect'] ) );
}

$processed_content->set_attribute( 'action', esc_attr( $form_action ) );

$method = empty( $attributes['method'] ) ? 'post' : $attributes['method'];
$processed_content->set_attribute( 'method', esc_attr( $method ) );

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo str_replace(
	'</form>',
	$fields . '</form>',
	$processed_content->get_updated_html()
);
