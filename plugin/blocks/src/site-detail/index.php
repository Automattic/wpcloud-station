<?php

function wpcloud_site_detail_options() {
	$keys = WPCLOUD_Site:: WPCLOUD_DETAIL_KEYS;
	$options = array();
	foreach ( $keys as $key ) {
		$label = ucwords( str_replace( '_', ' ', $key ) );
		$options[] = array(
			'label' => $key,
			'value' => $key,
		);
	}
}

function wpcloud_block_site_detail_localize_script() {
	error_log('localizing script');
	wp_localize_script(
		'wpcloud-site-detail',
		'wpcloudSiteDetail',
		array( 'options' => wpcloud_site_detail_options() )
	);
}

add_action( 'admin_init', 'wpcloud_block_site_detail_localize_script' );