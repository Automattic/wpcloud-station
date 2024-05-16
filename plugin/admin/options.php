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
	<?php if ( $client_ips ) : ?>
	<h3>IP Addresses</h3>
	<ul>
		<?php foreach ( $client_ips as $ip ) : ?>
			<li><b><?php echo esc_html( $ip ); ?></b></li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>