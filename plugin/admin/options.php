<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$client_ips = null;
$ip_request = wpcloud_client_site_ip_addresses();
if ( ! is_wp_error( $ip_request ) ) {
	$client_ips = $ip_request->ips;
}

?>
<style>
	.wp-cloud-settings {
		.description {
			width: 400px;
		}
	}
</style>
<div class="wrap wp-cloud-settings">
	<h1>WP Cloud</h1>
	<form action="options.php" method="post">
			<?php
			settings_fields( 'wpcloud' );
			do_settings_sections( 'wpcloud' );
			submit_button( 'Save Settings' );
			?>
	</form>
<h2>Details</h2>
<table class="form-table" role="presentation">
	<tbody>
		<?php if ( $client_ips ) : ?>
		<tr class="wpcloud_row">
			<th scope="row">
				<label for="wpcloud_api_key">WP Cloud IP Address Range</label>
			</th>
			<td>
			<?php foreach ( $client_ips as $ip ) : ?>
				<?php echo esc_html( $ip ); ?>
			<?php endforeach; ?>
			</td>
		</tr>
		<?php endif; ?>
			<tr class="wpcloud_row">
			<th scope="row">
				<label for="wpcloud_api_key">Server IP Address</label>
			</th>
			<td>
			<?php echo $_SERVER['SERVER_ADDR']; ?>
			</td>
		</tr>
</tbody></table>