<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WP_Cloud_Dashboard
 */

//  add_filter('nav_menu_css_class' , 'wpcloud-nav-sites' , 10 , 2);
// function special_nav_class($classes, $item){
//     if( in_array('current_page_parent', $classes) ){
//     }
// return $classes;
// }
function wpcloud_sites_set_current_menu_item( $classes, $item ) {
    if ( is_single() && 'Sites' === $item->title ) {
        $classes[] = 'current-menu-item';
    }

    return $classes;
}
add_filter( 'nav_menu_css_class', 'wpcloud_sites_set_current_menu_item', 10, 2 );

get_header();
?>

<main class="wpcloud-site-details site-content" id="site-content">

	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/single', get_post_type() );

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;

	endwhile; // End of the loop.
	?>

</main><!-- #main -->

<?php
get_footer();
