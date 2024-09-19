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
$ip_error   = false;

global $wpcloud_request_api_status;
global $wpcloud_api_healthy;

// Check for hosted Station sites.
$wpcloud_is_atomic         = defined( 'IS_ATOMIC' ) && IS_ATOMIC;
$wpcloud_is_hosted_station = defined( 'ATOMIC_CLIENT_ID' ) && defined( 'WP_STATION_CLIENT_ID' ) && WP_STATION_CLIENT_ID !== ATOMIC_CLIENT_ID;

$wpcloud_api_error = '';

if ( ! $wpcloud_api_healthy ) {
	$wpcloud_api_error = $wpcloud_request_api_status->get_error_message();
	$ip_error          = str_contains( $wpcloud_api_error, 'not allowed' );
} else {
	$client_ips_request = wpcloud_client_domain_ip_addresses( null );
	if ( is_wp_error( $client_ips_request ) ) {
		$client_ips = $client_ips_request;
	} else {
		$client_ips = ( (array) $client_ips_request )['ips'] ?? array();
	}
}

?>
<style>
	.wp-cloud-settings {
		.description {
			width: 400px;
		}
		.disabled {
			opacity: 0.5;
		}
		.warning {
			font-weight: bold;
		}
		ul {
			list-style: disc;
			padding-left: 40px;
		}
	}
</style>
<div class="wrap wp-cloud-settings">
	<h1>WP Cloud</h1>
	<?php if ( ! $wpcloud_api_healthy ) : ?>
	</div>
		<div class="notice notice-error" >
			<h3>WP Cloud API is not healthy</h3>
			<p>
				<?php esc_attr_e( 'Your site is unable to connect to the WP Cloud API' ); ?>
				<br />
				<strong><?php esc_attr_e( 'Please check the following:' ); ?></strong>
			</p>

			<ul>
			<?php if ( $wpcloud_is_atomic && ! $wpcloud_is_hosted_station ) : ?>
				<li>
					<p>
						<?php esc_attr_e( 'It appears you are trying to run Station on a WP Cloud site. For security reasons, WP Cloud sites are unable to connect to the WP Cloud API directly. ' ); ?>
					</p>
					<p>
						<strong><?php esc_attr_e( 'Please install Station on a different platform.' ); ?></strong>
					</p>
				</li>
			<?php else : ?>
				<?php if ( $ip_error ) : ?>
				<li>
					<p>
						<?php esc_attr_e( 'The WP Cloud API limits access by IP address.' ); ?>
						<br/>
						<?php esc_attr_e( 'The server IP address appears to be:' ); ?>
					</p>
					<ul><li><strong><?php echo esc_attr( $server_ip ); ?></strong></li></ul>
					<p>
						<?php esc_attr_e( 'Please verify and update the server IP address with your WP Cloud representative.' ); ?>
				<?php else : ?>
					<li>
						<p>
							<?php echo esc_attr( $wpcloud_api_error ); ?>
						</p>
					</li>
				<?php endif; ?>
			<?php endif; ?>
			</ul>
			<p>
				<?php esc_html_e( 'Return back to this page after correcting the API issues to continue configuring Station.' ); ?>
			</p>
		</div>
	<?php else : ?>
		<h2>Details</h2>
		<table class="form-table" role="presentation">
			<tbody>
				<?php if ( $client_ips ) : ?>
				<tr class="wpcloud_row">
					<th scope="row">
						<label for="wpcloud_address_range">WP Cloud IP Address Range</label>
					</th>
					<td>
					<?php if ( is_wp_error( $client_ips ) ) : ?>
						<?php echo esc_html( $client_ips->get_error_message() ); ?>
					<?php else : ?>
						<?php foreach ( $client_ips as $ip ) : ?>
							<?php echo esc_html( $ip ); ?>
						<?php endforeach; ?>
					<?php endif; ?>
					</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	<?php endif; ?>


	<form action="options.php" method="post">
			<?php
			settings_fields( 'wpcloud' );
			do_settings_sections( 'wpcloud' );
			submit_button( 'Save Settings' );
			?>
	</form>
