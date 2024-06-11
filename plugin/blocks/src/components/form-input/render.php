<?php

$limit_to_admin = $attributes['adminOnly'] ?? false;

if ( $limit_to_admin && ! current_user_can( 'manage_options' ) ) {
	return '';
}

$name = $attributes['name'] ?? 'any';
$type = $attributes['type'] ?? 'text';

$allowed = apply_filters( 'wpcloud_block_form_allow_field_' . $name,  true, $attributes, $block );

if ( ! $allowed ) {
	return '';
}

$content = apply_filters( 'wpcloud_block_form_render_field_' . $name, $content, $attributes, $block );
$content = apply_filters( 'wpcloud_block_form_render_field', $content, $attributes, $block );

$dynamic_select_options = array( 'php_version', 'data_center', 'wp_version' );
if ( 'select' === $type && in_array( $name, $dynamic_select_options ) ) {
	switch( $name ) {
		case 'php_version':
			$options = wpcloud_block_available_php_options();
			break;
		case 'data_center':
			$options = wpcloud_block_available_datacenters_options();
			break;
		case 'wp_version':
			$options = wpcloud_block_available_wp_versions();
			break;
		default:
			$options = array();
	}

	$current_value = wpcloud_get_site_detail(get_the_ID(), $name);
	if ( is_wp_error( $current_value ) ) {
		error_log( 'WP Cloud: ' . $current_value->get_error_message() );
		$current_value = '';
	}

	$options_html = '';
	foreach ( $options as $value => $label ) {
		$options_html .= sprintf(
			'<option value="%s" %s>%s</option>',
			esc_attr( $value ),
			selected( $current_value, $value, false ),
			esc_html( $label )
		);
	}

	$regex = '/(<select[^>]*>)(?:\s*<option[^>]*>.*?<\/option>)*\s*(<\/select>)/';
	$content = preg_replace($regex, '$1' . $options_html . '$2', $content);
}

echo $content;