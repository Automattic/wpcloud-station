<?php

// test setting up software
$response = wpcloud_client_site_manage_software(wpcloud_get_current_site_id(), array('themes/pub/twentytwentytwo' => 'activate'));

/**
 * Render the site alias block.
 *
 * @param string $content The block content.
 */
if ( ! is_wpcloud_site_post() ) {
	return;
}

// Fetch the site aliases
$site_aliases = wpcloud_get_domain_alias_list();

if (is_wp_error($site_aliases)) {
	error_log("WP Cloud Site Alias Block: Error fetching site aliases.");
	return '';
}

$html5 = new Masterminds\HTML5( [ 'disable_html_ns' => true ] );
$dom = $html5->loadHTML( $content );
$xpath = new DOMXPath($dom);

$rows = $xpath->query('//div[contains(@class, "wpcloud-block-dynamic-row")]');

if ( ! $rows ) {
	error_log("WP Cloud Site Alias Block: No row found.");
	return '';
}

$row = $rows[0];
$list = $row->parentNode;

foreach( $site_aliases as $alias) {
	$new_row = $row->cloneNode(true);
	$new_row->setAttribute( 'data-site-alias', $alias );

	$value_query = './/div[contains(concat(" ", normalize-space(@class), " "), " wpcloud-block-site-detail__value ")]';
	$value_node = $xpath->query( $value_query, $new_row )[ 0 ];
	if ( $value_node ) {
		$el = $dom->createElement('a', $alias);
		$a = $value_node->appendChild($el);
		$a->setAttribute( 'href', 'https://' . $alias );
	}
	$list->appendChild( $new_row );
}

// Hide the default form so we have at least one form to clone
$row->setAttribute( 'style', 'display:none;' );

$modified_html = $dom->saveHTML();
echo $modified_html;