<?php
/**
 * The template for displaying the sidebar.
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WP Cloud
 * @subpackage WP_Cloud_Dashboard
 */

?>
<aside id="secondary" class="sidebar" role="complementary">
	<section class="navigation">
		<?php echo get_custom_logo(); ?>
		<div class="wpcloud-menu">
			<?php wp_nav_menu('WP Cloud Dashboard'); ?>
		</div>
	</section>
	<section class="settings">
		<div class="settings-avatar">
			<?php echo get_avatar( get_current_user_id(), 32 ); ?>
		</div>
		<div class="settings-links">
			<a href="#" class="settings" >Settings</a>
			<a href="#" class="help" >Help</a>
		</div>
	</section>
</aside><!-- #secondary -->
