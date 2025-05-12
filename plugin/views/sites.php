<?php
/**
 * Template Name: Site Metrics
 *
 * @package wpcloud-station
 */

// Redirect to login page if user is not logged in
if ( ! is_user_logged_in() ) {
	wp_redirect( wp_login_url( $_SERVER['REQUEST_URI'] ) );
	exit;
}

$site_name = get_query_var( 'site_name' );
$view      = get_query_var( 'view', '' );
$site_cpt  = get_page_by_path( $site_name, OBJECT, 'wpcloud_site' );

if ( ! $site_cpt ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( 404 );
	exit();
}
global $post;
$post = $site_cpt; // phpcs:ignore

$template       = '';
$theme          = get_stylesheet();
$block_template = get_block_template( "$theme//wpcloud_site-$view" );

// Check if there are modifications to the block template.
if ( $block_template && ! empty( $block_template->content ) ) {
	$template = $block_template->content;
} else {
	// Else check if there is a custom template in the theme.
	$template_path = get_stylesheet_directory() . "/templates/wpcloud_site-$view.html";
	if ( file_exists( $template_path ) ) {
		$template = file_get_contents( $template_path ); // phpcs:ignore
	}
}

// @NOTE we need to build the blocks in the head so the styles are enqueued.
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset=" < ? php bloginfo( 'charset' ); ?>">
	<?php
	$content = do_blocks( $template );
	wp_head();
	?>
</head>

<body <?php body_class(); ?>>
<div class="wp-site-blocks">
	<?php echo $content; // phpcs:ignore ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
