<?php
/**
 * Render the site alias block.
 *
 * @param string $content The block content.
 */
if ( ! is_wpcloud_site_post() ) {
	error_log("WP Cloud Site Alias Block: Not a site post.");
	return;
}

function wpcloud_block_add_hidden_site_alias_field( $dom, $element,  string $alias = '' ) {
 	$domain_input = $dom->createElement('input');
	$domain_input->setAttribute('type', 'hidden');
	$domain_input->setAttribute('name', 'site_alias');
	$domain_input->setAttribute('value', $alias);
	$element->appendChild($domain_input);
}

// Fetch the site aliases
$siteAliases = wpcloud_get_domain_alias_list();

error_log($content);
$dom = new DOMDocument();
$dom->loadHTML( $content );
$xpath = new DOMXPath($dom);

// Find the remove forms ( should be only one, but just in case )
$forms = $xpath->query('//form[contains(@class, "wpcloud-block-form-site-alias--remove")]');

// hold on to the first one
$removeForm = $forms[0];
$formContainer = $removeForm->parentNode;

foreach( $siteAliases as $alias) {
	$clonedForm = $removeForm->cloneNode(true);
	$clonedForm->setAttribute('data-site-alias', $alias);

	$valueDivs = $clonedForm->getElementsByTagName('div');
	foreach ($valueDivs as $valueDiv) {
		error_log(print_r($valueDiv, true));
		error_log($valueDiv->getAttribute('class'));
		if ($valueDiv->getAttribute('class') === 'wpcloud-block-site-detail__value') {
			$valueDiv->nodeValue = $alias;
		}
	}

	wpcloud_block_add_hidden_site_alias_field($dom, $clonedForm, $alias);

	// Append the cloned form node to its parent node
	$formContainer->appendChild($clonedForm);
}

// Hide the default form so we have at least one form to clone
// add set up a hidden field for the alias
$removeForm->setAttribute('style', 'display: none;');
wpcloud_block_add_hidden_site_alias_field( $dom, $removeForm );

$modifiedHtml = $dom->saveHTML();
error_log("\n==================================\n");
//error_log($modifiedHtml);
echo $modifiedHtml;