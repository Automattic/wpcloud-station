<?php
if ( $attributes['adminOnly'] && ! current_user_can( 'manage_options' ) ) {
	return;
}
$layout = $block->context['wpcloud/layout'] ?? '';

if ('table' === $layout) {
	$wrapper = 'td';
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'wpcloud-block-link wpcloud-block-table-cell' ) );
} else {
	$wrapper = 'div';
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'wpcloud-block-link' ) );
}

// If there is a custom url just return the content.
if ( $attributes['url'] ) {
	printf('<%1$s %2%s>%3$s</%1$s>', $wrapper, $wrapper_attributes, $content);
	return;
}

$pattern = '/(<a\s+[^>]*href\s*=\s*["\'])([^"\']*)(["\'])/';

if ( 'wp_admin_url' === $attributes['name'] ) {
	$url = 'https://' . get_the_title() . '/wp-admin';
} else {
	$url = wpcloud_get_site_detail( get_the_ID(), $attributes['name'] );
}
$replacement = '$1' . $url . '$3';
$new_link = preg_replace($pattern, $replacement, $content);

printf('<%1$s %2$s>%3$s</%1$s>', $wrapper, $wrapper_attributes, $new_link);