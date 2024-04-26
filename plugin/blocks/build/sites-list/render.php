<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

 $args = array(
	'post_type' => 'wpcloud_site',
	'post_status' => 'any',
);
if ( ! current_user_can( 'manage_options' ) ) {
	$args['author__in'] = array( get_current_user_id() );
}
$results = new WP_Query( $args );
$sites = $results->posts;
?>

<table class="wpcloud-block-sites-list">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Site', 'wpcloud-dashboard' ); ?></th>
			<th><?php esc_html_e( 'Owner', 'wpcloud-dashboard' ); ?></th>
			<th><?php esc_html_e( 'Created', 'wpcloud-dashboard' ); ?></th>
			<th><?php esc_html_e( 'State', 'wpcloud-dashboard' ); ?></th>
			<th><?php esc_html_e( 'PHP', 'wpcloud-dashboard' ); ?></th>
			<th><?php esc_html_e( 'Performance', 'wpcloud-dashboard' ); ?></th>
			<th><?php esc_html_e( 'Datacenter', 'wpcloud-dashboard' ); ?></th>
			<th><?php esc_html_e( '★', 'wpcloud-dashboard' ); ?></th>
			<th><?php esc_html_e( 'Actions', 'wpcloud-dashboard' ); ?></th>
		</tr>
	</thead>
		<tbody>

<?php
foreach( $sites as $site ) {
	$wpcloud_site = WPCLOUD_Site::from_post( $site );
	?>
	<tr>
		<td class="wpcloud-list-item wpcloud-site"><?php echo wpcloud_dashboard_list_site_card( $wpcloud_site, $site ) ?></td>
		<td class="wpcloud-list-item wpcloud-role"><?php echo wpcloud_dashboard_list_owner( $wpcloud_site ) ?></td>
		<td class="wpcloud-list-item wpcloud-created"><?php echo wpcloud_dashboard_list_created_on( $site ) ?></td>
		<td class="wpcloud-list-item wpcloud-state"><?php echo $site->post_status === 'publish' ? 'Live' : 'Provisioning' ?></td>
		<td class="wpcloud-list-item wpcloud-php"><?php echo $wpcloud_site->php_version ?></td>
		<td class="wpcloud-list-item wpcloud-perf"><?php echo wpcloud_dashboard_list_performance() ?></td>
		<td class="wpcloud-list-item wpcloud-data_center"><?php echo $wpcloud_site->data_center ?></td>
		<td class="wpcloud-list-item wpcloud-fav"></td>
		<td class="wpcloud-list-item wpcloud-actions"><?php echo wpcloud_dashboard_wp_admin_button( $wpcloud_site ) ?></td>
	</tr>
	<?php
}

?>
	</tbody>
</table>
