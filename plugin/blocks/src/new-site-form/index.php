<?php


function wpcloud_register_new_site_form_block() {
	register_block_type( dirname( __DIR__, 2 ) . '/build/new-site-form', );
}
add_action( 'init', 'wpcloud_register_new_site_form_block' );