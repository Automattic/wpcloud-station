<?php
/**
 * Render Site Detail block.
 *
 * @package wpcloud-block
 * @subpackage site-detail
 */

if ( ! is_wpcloud_site_post() ) {
	return;
}

/* Return early if the user is not an admin and the block is admin only */
if ( $attributes['adminOnly'] && ! current_user_can( 'manage_options' ) ) {
	return;
}
// Grab the detail name and value.
$name   = $attributes['name'] ?? '';
$value  = wpcloud_get_site_detail( get_the_ID(), $name ) ?? '';
$detail = '';

if ( is_wp_error( $value ) ) {
	error_log( 'WP Cloud Site Detail Block: ' . $value->get_error_message() ); // phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
	return '';
}
$node_attributes = array();
switch ( true ) {
	case is_array( $value ):
		$detail = "<ul class='wpcloud_block_site_detail__value__list'>";
		foreach ( $value as $key => $li ) {
			$detail .= "<li>$li</li>";
		}
		$detail .= '</ul>';
		break;

	case 'domain_name' === $name || 'site_url' === $name:
		$detail = sprintf( '<a href="https://%s">%s</a>', $value, $value );
		break;

	case str_starts_with( $value, 'http' ):
		$label     = $attributes['label'] ?? '';
		$link_text = $value;

		if ( $attributes['hideLabel'] ) {
			$link_text = $label;
		}

		$data   = '';
		$detail = sprintf( '<a href="%s" %s >%s</a>', $value, $data, $link_text );
		break;

	case 'wp_version' === $name:
		$value = ucfirst( $value );
		break;

	case 'ssh_user' === $name:
		// @TODO: Generalize the copy to clipboard pattern, just not how to handle alternatives like with ssh vs sftp

		// @TODO Switch this to sftp once we have that added to the site settings
		$node_attributes['data-clipboard-pattern'] = 'ssh -v {ssh_user}@ssh.atomicsites.net';
		break;
	default:
		$detail = $value;
}

// Match the placeholder which is in the last set of curly braces  { The placeholder }.
$regex  = '/\{[^{}]*\}(?=[^{}]*$)/';
$detail = preg_replace( $regex, $detail, $content );

$node_attributes['data-site-detail'] = $name;

if ( '' !== $value ) {
	$data_name = 'data-' . preg_replace( '/_/', '-', $name );
	if ( is_array( $value ) ) {
		$value = implode( ',', $value );
	}
	$node_attributes[ $data_name ] = $value;
}
$wrapper = 'div';

$node_attributes['class'] = 'wpcloud-block-site-detail__wrapper';

$wrapper_attributes = get_block_wrapper_attributes( $node_attributes );
printf( '<%1$s %2$s>%3$s</%1$s>', $wrapper, $wrapper_attributes, $detail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
