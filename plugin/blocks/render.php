<?php

/** block render helper functions */
declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add a hidden input field to a form DOM element.
 */
function wpcloud_block_add_hidden_site_alias_field( DOMDocument $dom, DOMNode $form, string $name, string $value = '' ):void {
 		$domain_input = $dom->createElement('input');
		$domain_input->setAttribute('type', 'hidden');
		$domain_input->setAttribute('name', $name);
		$domain_input->setAttribute('value', $value);
		$form->appendChild($domain_input);
}
