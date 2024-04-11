<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WP_Cloud_Dashboard
 */

/**
 * Columns:
 * 	Site Role Created State PHP Performance Datacenter IP Addresses ★(starred) Actions
 */
$cloud_site = WPCLOUD_Site::from_post( get_post(),true );
$site_details = $cloud_site->details;
?>

<tr class="wpcloud-list-row wpcloud-list-data" id="site-<?php the_ID(); ?>" <?php post_class(); ?>>
	<td class="wpcloud-list-item wpcloud-site"><?php wpcloud_dashboard_list_site_card() ?></td>
	<td class="wpcloud-list-item wpcloud-role"><?php the_author(); ?></td>
	<td class="wpcloud-list-item wpcloud-created"><?php wpcloud_dashboard_list_created_on(); ?></td>
	<td class="wpcloud-list-item wpcloud-state"><?php wpcloud_dashboard_list_status(); ?></td>
	<td class="wpcloud-list-item wpcloud-php"><?php wpcloud_dashboard_list_php_version($site_details); ?></td>
	<td class="wpcloud-list-item wpcloud-perf"><?php wpcloud_dashboard_list_performance(); ?></td>
	<td class="wpcloud-list-item wpcloud-datacenter"><?php wpcloud_dashboard_list_datacenter($site_details); ?></td>
	<td class="wpcloud-list-item wpcloud-ip-addresses"><?php wpcloud_dashboard_list_ip_addresses($site_details); ?></td>
	<td class="wpcloud-list-item wpcloud-fav"><?php wpcloud_dashboard_list_is_favorite(); ?></td>
	<td class="wpcloud-list-item wpcloud-actions" ><?php wpcloud_dashboard_wp_admin_button(); ?></td>
</tr><!-- #site-<?php the_ID(); ?> -->
