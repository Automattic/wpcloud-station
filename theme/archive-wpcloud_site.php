<?php
/**
 * The template for displaying single posts and pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WP Cloud
 * @subpackage WP_Cloud_Dashboard
 */
get_header();
$sites = new WP_Query( array(
	'post_type' => 'wpcloud_site',
	'author' => get_current_user_id(),
	'post_status' => 'any',
));
?>

<main id="site-content">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Sites', 'wpcloud-dashboard' ); ?></h1>
	</header><!-- .page-header -->

	<?php
	if ( $sites->have_posts() ) {

		while ( $sites->have_posts() ) {
			$sites->the_post();

			get_template_part( 'template-parts/content', get_post_type() );
		}
	}

	?>

</main><!-- #site-content -->

<?php
get_footer();
