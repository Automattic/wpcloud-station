<?php
/*
 * The site detail block is only rendered when the current post is a `wpcloud_site` post.
 * If we're not on a `wpcloud_site` post, we should return early.
 * But we can still render the block in demo mode.
 */
if ( ! is_wpcloud_site_post() ) {
	return;
}

// @TODO get real site thumbnail
$site_thumbnail = wpcloud_station_get_assets_url( '/images/Gravatar_filled_' . get_the_ID() % 5 . '.png' );

$wrapper = 'div';
$classNames = $attributes['className'] ?? '';

$wrapper_attributes = $wrapper . ' ' .  get_block_wrapper_attributes( array( 'class' => trim( $classNames ) ) );
$domain = wpcloud_get_site_detail( get_the_ID(), 'domain_name' );
if (is_wp_error($domain)) {
	error_log('Error getting domain name: ' . $domain->get_error_message());
	$domain = '';
}

?>

<<?php echo $wrapper_attributes ?> >
	<img src="<?php echo $site_thumbnail ?>" />
	<h2 class="site-title">
		<a href="<?php echo get_the_permalink() ?>"><?php echo get_post_field( 'post_name', get_post() ); ?></a>
	</h2>
	<h3 class="site-url">
		<a href="https://<?php echo $domain ?>" target="_blank">
			<span><?php echo $domain ?></span>
			<span className="dashicons dashicons-external" ></span>
		</a>
	</h3>
</<?php echo $wrapper ?> >
