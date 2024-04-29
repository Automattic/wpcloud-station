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

echo $content;
