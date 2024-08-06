<?php
/**
 * Render the site alias block.
 *
 * @package wpcloud-block
 * @subpackage site-alias-add
 */

/**
 * Render the site alias block.
 *
 * @param string $content The block content.
 */
if ( ! is_wpcloud_site_post() ) {
	return;
}

echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
