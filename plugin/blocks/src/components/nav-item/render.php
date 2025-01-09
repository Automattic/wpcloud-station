<?php
/**
 * Render the nav link block.
 *
 * @package wpcloud-block
 * @subpackage nav-link
 */

$site = WPCLOUD_Site::get();
if ( $site ) {
	$content = $site->replace_attr( $content );
}

echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
