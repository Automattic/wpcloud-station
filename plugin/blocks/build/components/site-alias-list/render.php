<?php

// test setting up software
$response = wpcloud_client_site_manage_software(wpcloud_get_current_site_id(), array('themes/pub/twentytwentytwo' => 'activate'));

/**
 * Render the site alias block.
 *
 * @param string $content The block content.
 */
if ( ! is_wpcloud_site_post() ) {
	error_log("WP Cloud Site Alias Block: Not a site post.");
	return;
}

// Fetch the site aliases
$site_aliases = wpcloud_get_domain_alias_list();

if (is_wp_error($site_aliases)) {
	error_log("WP Cloud Site Alias Block: Error fetching site aliases.");
	return '';
}

$dom = new DOMDocument();
$dom->loadHTML( $content );
$xpath = new DOMXPath($dom);

// Find the remove forms ( should be only one, but just in case )
$forms = $xpath->query('//form[contains(@class, "wpcloud-block-form-site-alias--remove")]');

// hold on to the first one
$remove_form = $forms[0];
$form_container = $remove_form->parentNode;

foreach( $site_aliases as $alias) {
	$cloned_form = $remove_form->cloneNode(true);
	$cloned_form->setAttribute('data-site-alias', $alias);

	$value_divs = $cloned_form->getElementsByTagName('div');
	foreach ($value_divs as $value_div) {
		if ($value_div->getAttribute('class') === 'wpcloud-block-site-detail__value') {
			$value_div->nodeValue = $alias;
		}
	}

	wpcloud_block_add_hidden_field($dom, $cloned_form, 'site_alias', $alias);

	// Append the cloned form node to its parent node
	$form_container->appendChild($cloned_form);
}

// Hide the default form so we have at least one form to clone
// add set up a hidden field for the alias
$remove_form->setAttribute('style', 'display: none;');
wpcloud_block_add_hidden_field( $dom, $remove_form, 'site_alias' );

$modified_html = $dom->saveHTML();
echo $modified_html;