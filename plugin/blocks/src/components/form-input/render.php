<?php
/**
 * Render a form field.
 *
 * @package wpcloud-block
 * @subpackage form-input
 */

 // phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log

$limit_to_admin = $attributes['adminOnly'] ?? false;

if ( $limit_to_admin && ! current_user_can( 'manage_options' ) ) {
	return '';
}

$name       = $attributes['name'] ?? 'any';
$input_type = $attributes['type'] ?? 'text';

$allowed = apply_filters( 'wpcloud_block_form_allow_field_' . $name, true, $attributes, $block );

if ( ! $allowed ) {
	return '';
}

$attributes = apply_filters( 'wpcloud_block_form_field_attributes_' . $name, $attributes, $block );
$content    = apply_filters( 'wpcloud_block_form_render_field_' . $name, $content, $attributes, $block );
$content    = apply_filters( 'wpcloud_block_form_render_field', $content, $attributes, $block );

// Check block data for values and options.
$current_value = $attributes['value'] ?? '';
$options       = $attributes['options'] ?? array();
$aliases       = array();

// Check mutable options for values and options.
$site_mutable_options = WPCloud_Site::get_mutable_options();
if ( array_key_exists( $name, $site_mutable_options ) ) {
	$current_value = WPCLOUD_Site::get_detail( get_the_ID(), $name );

	if ( is_wp_error( $current_value ) ) {
		error_log( 'WP Cloud: ' . $current_value->get_error_message() );
		$current_value = '';
	}
	if ( ! $current_value ) {
		$current_value = $site_mutable_options[ $name ]['default'] ?? '';
	}

	if ( 'select' === $input_type ) {
		$options = $site_mutable_options[ $name ]['options'] ?? $options;
		$aliases = $site_mutable_options[ $name ]['option_aliases'] ?? $aliases;
		if ( is_wp_error( $options ) ) {
			error_log( 'WP Cloud: ' . $options->get_error_message() );
			$options = array();
		}
	}
}

if ( 'select' === $input_type ) {
	$options_html = '';

	foreach ( $options as $value => $option ) {
		// Check if the option is the label or an array with a value and label.
		if ( is_array( $option ) ) {
			$value = $option['value'];
			$label = $option['label'];
		} else {
			$label = $option;
		}

		$selected = selected( $current_value, $value, false );
		// check for option aliases.
		if ( ! $selected && array_key_exists( $value, $aliases ) ) {
			$selected = selected( $current_value, $aliases[ $value ], false );
		}
		$options_html .= sprintf(
			'<option value="%s" %s>%s</option>',
			esc_attr( $value ),
			esc_attr( $selected ),
			esc_html( $label )
		);
	}


	$regex   = '/(<select[^>]*>)(?:\s*<option[^>]*>.*?<\/option>)*\s*(<\/select>)/';
	$content = preg_replace( $regex, '$1' . $options_html . '$2', $content );
} else {
	$regex = '/(<input .*)\/>/';
	if ( 'checkbox' === $input_type ) {
		if ( $current_value ) {
			$content = preg_replace( $regex, '$1 checked />', $content, 1 );
		}
	} else {
		$content = preg_replace( $regex, '$1 value="' . $current_value . '" />', $content, 1 );
	}
}

echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
