<?php
/**
 * WP Cloud Station Site Details
 *
 * @package wpcloud
 */

if ( $attributes['adminOnly'] && ! current_user_can( 'manage_options' ) ) {
	return;
}

echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
