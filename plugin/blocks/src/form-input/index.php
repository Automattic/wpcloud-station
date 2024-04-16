<?php


function wpcdb_register_form_input_block() {
	register_block_type( dirname( __DIR__, 2 ) . '/build/form-input', );
}
add_action( 'init', 'wpcdb_register_form_input_block' );