<?php


function wpcloud_site_details_fixture() {
	$site = new stdClass();
	$site->atomic_site_id = 1;
	$site->domain_name = 'example.com';
	$site->server_pool_id = 1;
	$site->atomic_client_id = 1;
	$site->chroot_path = '/var/www/example.com';
	$site->chroot_ssh_path = '/var/www/example.com';
	$site->cache_prefix = 'example.com';
	$site->db_charset = 'utf8mb4';
	$site->db_collate = '';
	$site->db_password = 'password';
	$site->php_version = '7.4';
	$site->site_api_key = 'api_key_123';
	$site->wp_admin_email = 'admin@example.com';
	$site->wp_admin_user = 'admin';
	$site->wp_version = '5.7';
	$site->static_file_404 = '404.html';
	$site->smtp_pass = 'password';
	$site->geo_affinity = 'bur';
	$site->db_file_size = 100;
	$site->ip_addresses = array( '199.16.172.201', '199.16.172.202' );

	return $site;
}

function wpcloud_is_demo_mode(): bool {
	return defined( 'WPCLOUD_DEMO_MODE' ) && WPCLOUD_DEMO_MODE;
}
