<?php
/**
 * @package wpcloud-dashboard
 */
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	do_action( 'admin_notices' );

	require_once  plugin_dir_path(__FILE__) . '/includes/class-wpcloud-site-list.php';
	$wpcloud_site_list = new WPCLOUD_Site_List();
	$wpcloud_site_list->prepare_items();
	error_log( 'Site list prepared' );
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<?php $wpcloud_site_list->display(); ?>
	</div>