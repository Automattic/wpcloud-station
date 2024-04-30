<?php
/**
 * Render the site alias block.
 *
 * @param string $content The block content.
 */
if ( ! is_wpcloud_site_post() ) {
	error_log("WP Cloud Site SSH User List Block: Not a site post.");
	return;
}

if ( ! function_exists( 'wpcloud_block_add_hidden_site_alias_field' ) ) {
	function wpcloud_block_add_hidden_site_alias_field( $dom, $element,  string $user = '' ) {
 		$domain_input = $dom->createElement('input');
		$domain_input->setAttribute('type', 'hidden');
		$domain_input->setAttribute('name', 'site_ssh_user');
		$domain_input->setAttribute('value', $user);
		$element->appendChild($domain_input);
	}
}

$wpcloud_site_id = wpcloud_get_current_site_id();

if ( ! $wpcloud_site_id ) {
	error_log("WP Cloud Site SSH User List Block: Site not found.");
	return '';
}

// Fetch the site ssh users
$ssh_users = wpcloud_client_ssh_user_list( $wpcloud_site_id ) ?: [];

$dom = new DOMDocument();
$dom->loadHTML( $content );
$xpath = new DOMXPath($dom);

// Find the remove forms ( should be only one, but just in case )
$forms = $xpath->query('//form[contains(@class, "wpcloud-block-site-ssh-user--remove")]');

// hold on to the first one
$remove_form = $forms[0];
$form_container = $remove_form->parentNode;

foreach( $ssh_users as $user) {
	$cloned_form = $remove_form->cloneNode(true);
	$cloned_form->setAttribute('data-site-ssh-user', $user);

	$value_divs = $cloned_form->getElementsByTagName('div');
	foreach ($value_divs as $value_div) {
		if ($value_div->getAttribute('class') === 'wpcloud-block-site-detail__value') {
			$value_div->nodeValue = $user;
		}
	}

	wpcloud_block_add_hidden_site_alias_field($dom, $cloned_form, $alias);

	// Append the cloned form node to its parent node
	$form_container->appendChild($cloned_form);
}

// Hide the default form so we have at least one form to clone
// add set up a hidden field for the alias
$remove_form->setAttribute('style', 'display: none;');
wpcloud_block_add_hidden_site_alias_field( $dom, $remove_form );

$modified_html = $dom->saveHTML();
echo $modified_html;