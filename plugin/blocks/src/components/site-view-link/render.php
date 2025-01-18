<?php
/**
 * Render Site View Link
 *
 * @package wpcloud-block
 * @subpackage site-view-link
 */

if ( ! is_wpcloud_site_post() ) {
	return;
}

/* Return early if the user is not an admin and the block is admin only */
if ( $attributes['adminOnly'] && ! current_user_can( 'manage_options' ) ) {
	return;
}

$view = $attributes['view'] ?? '';
global $post;

printf(
	'<a href="/sites/%s/%s" class="wpcloud-block-site-view-link">%s</a>',
	$post->post_name, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$view,            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$content          // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
