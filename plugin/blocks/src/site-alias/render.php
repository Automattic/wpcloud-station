<?php

error_log($content);
if ( ! is_wpcloud_site_post() ) {
		return;
}
// Fetch the site aliases
$siteAliases = wpcloud_get_domain_alias_list();

$dom = new DOMDocument();
$dom->loadHTML( $content );
$xpath = new DOMXPath($dom);

// Find the remove forms ( should be only one, but just in case )
$forms = $xpath->query('//form[contains(@class, "wpcloud-block-site-alias-form-remove")]');

// hold on to the first one
$removeForm = $forms[0];

// Remove the placeholder forms
foreach ($forms as $form) {
	//$form->parentNode->removeChild($form);
}

foreach( $siteAliases as $alias) {
	$clonedForm = $removeForm->cloneNode(true) ;

	$valueDivs = $clonedForm->getElementsByTagName('div');
  foreach ($valueDivs as $valueDiv) {
    if ($valueDiv->getAttribute('class') === 'wpcloud-block-site-detail__value') {
    	$valueDiv->nodeValue = $alias;
    }
  }
  // Append the cloned form node to its parent node
  $form->parentNode->appendChild($clonedForm);
}
$removeForm->parentNode->removeChild($removeForm);
// Get the modified HTML content
$modifiedHtml = $dom->saveHTML();
// Output the modified HTML
echo $modifiedHtml;
