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
	<style>
		.wpcloud-site-details {
			display: grid;
			grid-template-areas: "site-thumbnail site-title site-title"
							     "site-thumbnail site-url site-url"
								 ". nav content";
			grid-template-columns: 100px 240px auto;
			grid-template-rows: 50px 80px auto;

			ul {
				display: flex;
				height: 100%;
				padding: 0;
				flex-direction: column;
				align-items: flex-start;
				flex-shrink: 0;
				margin: 0;

				.current-menu-item {
					border-radius: 4px;
					background: #F72B00;
				}

				li {
					display: flex;
					align-items: center;
					align-self: stretch;
					overflow: hidden;
					color: var(--color-bw-white, #FFF);
					text-overflow: ellipsis;
					white-space: nowrap;
					font-size: 14px;
					font-style: normal;
					font-weight: 400;
					line-height: 20px;

					a {
						height: 40px;
						width: 100%;
						display: flex;
						align-items: center;
						column-gap: 12px;
						text-decoration: none;
						padding: 8px var(--grid-unit-15, 12px);
					}
				}
			}

			.wpcloud-site-details-content {
				grid-area: content;
				display: flex;

				.wpcloud-site-details-section {
					display: none;
				}

				.active {
					display: block;
				}
			}

			.site-thumbnail {
				grid-area: site-thumbnail;
				height: 80px;
			}

			.site-title {
				grid-area: site-title;
				margin: 0;

				a {
					display: inline-block;
				}

				a.button {
					display: flex;
					align-items: center;
					justify-content: space-between;
					border-radius: 100px;
					padding: 4px var(--grid-unit-15, 12px) 4px 8px;
					width: 150px;
					float: right;
				}
			}

			.site-url {
				grid-area: site-url;
				margin: 0;

				a {
					color: #ABA3A3;
				}
			}

			.wpcloud-site-details-nav {
				grid-area: nav;
			}

			.wpcloud-site-details-section {
				width: 600px;
				color: #fff;
				border: 1px solid rgba(85, 85, 85, 0.50);
				margin-left: 50px;
				padding: 0 30px 30px 30px;

				h3 {
					color: #ABA3A3;
					margin: 20px 0 5px;
				}

				p {
					margin: 0;
				}

				select {
					border-radius: 2px;
					border: 1px solid rgba(171, 163, 163, 0.50);
					background: none;
					outline: none;
					color: var(--color-bw-white, #FFF);
					padding: 4px;
					font-size: 14px;
					width: 100%;
				}
			}
		}
	</style>

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
