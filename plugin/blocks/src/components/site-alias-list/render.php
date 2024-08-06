<?php
/**
 * Render the site alias block.
 *
 * @package wpcloud-block-site-alias-list
 */

if ( ! is_wpcloud_site_post() ) {
	return;
}

// Fetch the site aliases.
$site_aliases = wpcloud_get_domain_alias_list();

if ( is_wp_error( $site_aliases ) ) {
	error_log( 'WP Cloud Site Alias Block: Error fetching site aliases.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	return '';
}

$html5 = new Masterminds\HTML5( array( 'disable_html_ns' => true ) );
$dom   = $html5->loadHTML( $content );
$xpath = new DOMXPath( $dom );

$rows = $xpath->query( '//div[contains(@class, "wpcloud-block-dynamic-row")]' );

if ( ! $rows ) {
	error_log( 'WP Cloud Site Alias Block: No row found.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	return '';
}

$row  = $rows[0];
$list = $row->parentNode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

foreach ( $site_aliases as $alias ) {
	$new_row = $row->cloneNode( true );
	$new_row->setAttribute( 'data-site-alias', $alias );

	$value_query = './/div[contains(concat(" ", normalize-space(@class), " "), " wpcloud-block-site-detail__value ")]';
	$value_node  = $xpath->query( $value_query, $new_row )[0];
	if ( $value_node ) {
		$el = $dom->createElement( 'a', $alias );
		$a  = $value_node->appendChild( $el );
		$a->setAttribute( 'href', 'https://' . $alias );
	}
	$list->appendChild( $new_row );
}

// Hide the default form so we have at least one form to clone.
$row->setAttribute( 'style', 'display:none;' );

$modified_html = $dom->saveHTML();
echo $modified_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
