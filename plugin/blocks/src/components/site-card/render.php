<?php
/*
 * The site detail block is only rendered when the current post is a `wpcloud_site` post.
 * If we're not on a `wpcloud_site` post, we should return early.
 * But we can still render the block in demo mode.
 */
if ( ! is_wpcloud_site_post() ) {
	return;
}

// placeholder pulled from github.com/automattic/color-studio/dist/colors.meta.json as of v2.6.0
// background colors are 40's, foreground colors are 60's
$placeholder_background_colors = ['#787c82', '#1689db', '#b35eb1', '#e34c84', '#e65054', '#d67709', '#c08c00', '#00a32a', '#009e73', '#1490c7', '#187aa2', '#4678eb', '#9a69c7', '#069e08'];
$placeholder_foreground_colors = ['#50575e', '#055d9c', '#7c3982', '#ab235a', '#b32d2e', '#8a4d00', '#7d5600', '#007017', '#007053', '#036085', '#004e6e', '#1d4fc4', '#674399', '#007117'];

// @TODO get real site thumbnail
$site_thumbnail = false;


$wrapper = 'div';
$classNames = $attributes['className'] ?? '';
$post_in_loop_id = get_the_ID();
$post_id = get_queried_object_id();
if ( $post_in_loop_id === $post_id ) {
	$classNames .= ' is-current';
}

$site_logo = sprintf('<img src="%s" />' , $site_thumbnail);
if ( ! $site_thumbnail ) {
	$color_index = $post_in_loop_id % ( count( $placeholder_background_colors ) - 1 ) ;
	$placeholder_background_color = $placeholder_background_colors[ $color_index ];
	$placeholder_foreground_color = $placeholder_foreground_colors[ $color_index ];
	$placeholder_character = strtoupper( substr( get_post_field( 'post_name', get_post() ), 0, 1 ) );
	$site_logo = sprintf('<div class="wpcloud-site-card--placeholder" style="background-color: %s;"><p style="color:%s;">%s</p></div>', $placeholder_background_color, $placeholder_foreground_color, $placeholder_character );
}
$wrapper_attributes = $wrapper . ' ' .  get_block_wrapper_attributes( array( 'class' => trim( $classNames ) ) );

$domain = wpcloud_get_site_detail( $post_in_loop_id, 'domain_name' );
if (is_wp_error($domain)) {
	error_log('Error getting domain name: ' . $domain->get_error_message());
	$domain = '';
}

?>

<<?php echo $wrapper_attributes ?> >
	<?php echo $site_logo; ?>
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
