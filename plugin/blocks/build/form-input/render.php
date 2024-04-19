<?php

$limit_to_admin = $attributes['adminOnly'] ?? false;

if ( $limit_to_admin && ! current_user_can( 'manage_options' ) ) {
	return '';
}

$name = $attributes['fieldName'] ?? 'any';

$allowed = apply_filters( 'wpcloud_block_form_allow_field_' . $name,  true, $attributes, $block );

if ( ! $allowed ) {
	return '';
}

$content = apply_filters( 'wpcloud_block_form_render_field_' . $name, $content, $attributes, $block );


echo $content;