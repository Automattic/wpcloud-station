<?php
/**
 * Block render helper functions.
 *
 * @package wpcloud-block
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add a hidden input field to a form DOM element.
 *
 * @param DOMDocument $dom The DOM document.
 * @param DOMNode     $form The form element.
 * @param string      $name The name of the field.
 * @param string      $value The value of the field.
 * @return void
 */
function wpcloud_block_add_hidden_field( DOMDocument $dom, DOMNode $form, string $name, string $value = '' ): void {
		$domain_input = $dom->createElement( 'input' );
		$domain_input->setAttribute( 'type', 'hidden' );
		$domain_input->setAttribute( 'name', $name );
		$domain_input->setAttribute( 'value', $value );
		$form->appendChild( $domain_input );
}

/**
 * Add the site id to any form that is on a site post.
 *
 * @param string $content The content of the form.
 * @return string The content of the form with the site id.
 */
function wpcloud_block_add_site_id_hidden_field( string $content ): string {
	if ( get_post_type() !== 'wpcloud_site' ) {
		return $content;
	}
	$site_id = get_the_ID();
	return wpcloud_block_form_hidden_field( 'site_id', $site_id ) . $content;
}
add_filter( 'wpcloud_block_form_fields', 'wpcloud_block_add_site_id_hidden_field' );

/**
 * Allow the site id in the form if present.
 *
 * @param array $fields The form fields.
 * @param array $post_keys The post keys.
 * @return array The form fields.
 */
function wpcloud_block_allow_site_id_field( array $fields, array $post_keys ): array {
	if ( in_array( 'site_id', $post_keys, true ) ) {
		return array_merge( $fields, array( 'site_id' ) );
	}
	return $fields;
}
add_filter( 'wpcloud_block_form_submitted_fields', 'wpcloud_block_allow_site_id_field', 10, 2 );
