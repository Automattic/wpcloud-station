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
$site_thumbnail = wpcloud_dashboard_get_assets_url( '/images/Gravatar_filled_' . get_the_ID() % 5 . '.png' );
$ex_link        = wpcloud_dashboard_get_assets_url( '/images/external-link.svg' );
?>

<div class="wp-block-wpcloud-site-card">
	<img src="<?php echo $site_thumbnail ?>" />
	<h2 class="site-title">
		<a href="<?php echo get_the_permalink() ?>"><?php echo get_post_field( 'post_name', get_post() ); ?></a>
	</h2>
	<h3 class="site-url">
		<a href="https://<?php echo get_the_title() ?>" target="_blank">
			<span><?php echo get_the_title() ?></span>
			<img src="<?php echo $ex_link ?>"/>
		</a>
	</h3>
</div>
