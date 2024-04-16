<?php


function wpcloud_register_form_input_block() {
	register_block_type( dirname( __DIR__, 2 ) . '/build/form-input', );
}
add_action( 'init', 'wpcloud_register_form_input_block' );