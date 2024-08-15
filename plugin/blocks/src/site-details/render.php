<?php
/**
 * WP Cloud Station Site Details
 *
 * @package wpcloud
 */

if ( ! is_wpcloud_site_post() ) {
	return;
}

/* Return early if the user is not an admin and the block is admin only */
if ( $attributes['adminOnly'] && ! current_user_can( 'manage_options' ) ) {
	return;
}

echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
