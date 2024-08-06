<?php
/**
 * File description goes here.
 *
 * @package YourPackageName
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$client_ips = null;
$server_ip  = filter_var( wp_unslash( $_SERVER['SERVER_ADDR'] ?? '' ), FILTER_VALIDATE_IP );

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
			<?php echo esc_html( $server_ip ); ?>
			</td>
		</tr>
</tbody></table>
