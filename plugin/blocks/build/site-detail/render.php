<?php
/*
 * The site detail block is only rendered when the current post is a `wpcloud_site` post.
 * If we're not on a `wpcloud_site` post, we should return early.
 * But we can still render the block in demo mode.
 */
if ( ! is_wpcloud_site_post() ) {
	return;
}

/* Return early if the user is not an admin and the block is admin only */
if ( $attributes[ 'adminOnly' ] && ! current_user_can( 'manage_options' ) ) {
	return;
}

$detail = wpcloud_get_site_detail( get_the_ID(), $attributes[ 'name' ] ) ?? '';
if ( is_wp_error( $detail ) ) {
	error_log( 'WP Cloud Site Detail Block: ' . $detail->get_error_message() );
	return;
}

if ( is_array( $detail ) ) {
	$detail = implode( ', ', $detail );
}

// match the placeholder which is in the last set of curly braces  { The placeholder }
$regex = '/\{[^{}]*\}(?=[^{}]*$)/';

echo preg_replace($regex, $detail, $content);