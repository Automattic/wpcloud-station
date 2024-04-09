<?php
/**
 * The template for displaying the sidebar.
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WP Cloud
 * @subpackage WP_Cloud_Dashboard
 */
$nav_items =  wp_get_nav_menu_items( 'WP Cloud Dashboard' );


	error_log( print_r(wp_get_nav_menus(), true) );

?>
<aside id="secondary" class="sidebar" role="complementary">
	<section class="navigation">
		<?php echo get_custom_logo(); ?>
		<ul class="menu menu-wp-cloud-dashboard">
			<?php
			foreach ( $nav_items as $nav_item ) {
				?>
				<li>
					<a href="<?php echo $nav_item->url; ?>">
						<?php echo $nav_item->title; ?>
					</a>
				</li>
				<?php
			}
			?>
		</ul>

	</section>
	<section class="settings">
		<a href="#">Settings</a>
		<a href="#">Help</a>
	</section>
</aside><!-- #secondary -->
