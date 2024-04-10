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
 * Site Role Created State PHP Performance Datacenter IP Addresses ★(starred) Actions
 */
?>

<tr class="wpcloud-list-row wpcloud-list-data" id="site-<?php the_ID(); ?>" <?php post_class(); ?>>
	<td class="wpcloud-list-item wpcloud-site"><?php wpcloud_dashboard_list_site_card() ?></td>
	<td class="wpcloud-list-item wpcloud-role"><?php the_author(); ?></td>
	<td class="wpcloud-list-item wpcloud-created"><?php wpcloud_dashboard_created_on(); ?></td>
	<td class="wpcloud-list-item wpcloud-state"><?php wpcloud_dashboard_site_status(); ?></td>
	<td class="wpcloud-list-item wpcloud-php">7.4</td>
	<td class="wpcloud-list-item wpcloud-perf"><?php wpcloud_dashboard_list_performance(); ?></td>
	<td class="wpcloud-list-item wpcloud-datacenter">us-west-2</td>
	<td class="wpcloud-list-item wpcloud-ip-addresses"><?php wpcloud_dashboard_list_ip_addresses(); ?></td>
	<td class="wpcloud-list-item wpcloud-fav">★</td>
	<td class="wpcloud-list-item wpcloud-actions" ><?php wpcloud_dashboard_wp_admin_button(); ?></td>
</tr><!-- #site-<?php the_ID(); ?> -->
