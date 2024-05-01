<?php

/** block render helper functions */
declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add a hidden input field to a form DOM element.
 */
function wpcloud_block_add_hidden_field( DOMDocument $dom, DOMNode $form, string $name, string $value = '' ):void {
 		$domain_input = $dom->createElement('input');
		$domain_input->setAttribute('type', 'hidden');
		$domain_input->setAttribute('name', $name);
		$domain_input->setAttribute('value', $value);
		$form->appendChild($domain_input);
}

// Add the site id to any form that is on a site post.
function wpcloud_block_add_site_id_hidden_field( $content ) {
	if ( get_post_type() !== 'wpcloud_site' ) {
		return $content;
	}
	$site_id = get_the_ID();
	return wpcloud_block_form_hidden_field( 'site_id', $site_id ) . $content;
}
add_filter( 'wpcloud_block_form_fields', 'wpcloud_block_add_site_id_hidden_field' );

// Allow the site id in the form if present.
function wpcloud_block_allow_site_id_field( $fields, $post_keys ) {
	if ( in_array( 'site_id', $post_keys ) ) {
		return array_merge( $fields, [ 'site_id' ] );
	}
	return $fields;
}
add_filter( 'wpcloud_block_form_submitted_fields', 'wpcloud_block_allow_site_id_field', 10, 2 );
