<?php
/**
 * Render the site ssh user block.
 *
 * @param string $content The block content.
 */



if ( ! is_wpcloud_site_post() ) {
	error_log("WP Cloud Site SSH User List Block: Not a site post.");
	return;
}
$wpcloud_site_id = wpcloud_get_current_site_id();

if ( ! $wpcloud_site_id ) {
	error_log("WP Cloud Site SSH User List Block: Site not found.");
	return '';
}

// Fetch the site ssh users
$ssh_users = wpcloud_client_ssh_user_list( $wpcloud_site_id ) ?: [];

if (is_wp_error($ssh_users)) {
	error_log("WP Cloud Site SSH User List Block: Error fetching site ssh users.");
	return '';
}

$html5 = new Masterminds\HTML5(['disable_html_ns' => true]);

$dom = $html5->loadHTML( $content );
$xpath = new DOMXPath($dom);

// Find the row ( should be only one, but just in case )
$rows = $xpath->query('//div[contains(@class, "wp-block-group wpcloud-block-site-ssh-user--row")]');

// hold on to the first one
if ( ! $rows ) {
	error_log("WP Cloud Site SSH User List Block: No row found.");
	return '';
}

$row = $rows[0];
$list = $row->parentNode;

foreach( $ssh_users as $user) {
	// add data attribute for the forms to use
	$new_row = $row->cloneNode(true);
	$new_row->setAttribute('data-site-ssh-user', $user);

	// Update the site detail
	$value_query = './/div[contains(concat(" ", normalize-space(@class), " "), " wpcloud-block-site-detail__value ")]';
	$value_node = $xpath->query($value_query, $new_row)[0];
	if ( $value_node ) {
		$value_node->textContent = $user;
	}

	// Append the cloned form node to its parent node
	$list->appendChild($new_row);
}

// Hide the default form so we have at least one form to clone

$row->setAttribute('style', 'display:none;');

$modified_html = $dom->saveHTML();
echo $modified_html;