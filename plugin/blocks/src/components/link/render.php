<?php
if ( $attributes['adminOnly'] && ! current_user_can( 'manage_options' ) ) {
	return;
}
// If there is a custom url just return the content.
if ( $attributes['url'] ) {
	echo $content;
	return;
}

$pattern = '/(<a\s+[^>]*href\s*=\s*["\'])([^"\']*)(["\'])/';

if ( 'wp_admin_url' === $attributes['name'] ) {
	$url = 'https://' . get_the_title() . '/wp-admin';
} else {
	$url = wpcloud_get_site_detail( get_the_ID(), $attributes['name'] );
}
$replacement = '$1' . $url . '$3';
echo preg_replace($pattern, $replacement, $content);