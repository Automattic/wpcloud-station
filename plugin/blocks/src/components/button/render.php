<?php
if ( $attributes['adminOnly'] && ! current_user_can( 'manage_options' ) ) {
	return;
}


$layout = $block->context['wpcloud/layout'] ?? '';

$classes = array( 'wpcloud-block-link' );
$button_attributes = array();

$type  = $attributes['type'] ?? 'link';
$style = $attributes['style'] ?? '';
$url = '';

if( 'button' === $style ) {
	$classes[] = 'wp-block-button__link wp-element-button';
}

switch ($type) {
	case 'link':

	case 'action':
		if (  isset( $attributes['action'] ) ) {
			$classes[] = 'wpcloud-block-button__action';
			$button_attributes['data-wpcloud-action'] = $attributes['action'];
		}
		break;

	case 'detail':
		$classes[] = 'wpcloud-block-button__detail';
		$detail = wpcloud_get_site_detail( get_the_ID(), $attributes['name'] );
		$button_attributes[ 'data-wpcloud-detail' ] = $attributes['name'];
		$url = 'https://' . $detail;

		if ( wpcloud_should_refresh_detail( $attributes['name'] ) ) {
			$nonce = wp_create_nonce( 'wpcloud_refresh_link' );
			$button_attributes[ 'data-nonce' ] = $nonce;
			$button_attributes[ 'data-refresh-rate' ] = $attributes['refreshRate'] ?? 10000;
			$button_attributes[ 'data-site-id' ] = get_the_ID();
		}
		break;

	default:
		$url = $attributes['url'] ?? '/';
}


if ('table' === $layout) {
	$wrapper = 'td';
	$classes[] = 'wpcloud-block-table-cell';
} else {
	$wrapper = 'div';
}

$button_attributes['class'] = implode( ' ', $classes );

if ( $url ) {
	$dom = new DOMDocument;
	@$dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
	$xpath = new DOMXPath($dom);
	$span = $xpath->query('//span[@class="wpcloud-block-button--label"]')->item(0);

	if ($span) {
		$span_wrapper = $span->parentNode;
	  $anchor = $dom->createElement('a');
	  $anchor->setAttribute('href', $url);

		// if there are multiple children, insert the anchor before the first child.
		if ( $span_wrapper->childNodes->length > 1 ) {
			$span_wrapper->insertBefore($anchor, $span_wrapper->childNodes[0]);
		} else {
	  	$anchor->appendChild($span);
			$span_wrapper->appendChild($anchor);
		}
	}
	$content = $dom->saveHTML();
}
$wrapper_attributes = get_block_wrapper_attributes( $button_attributes );
//$pattern = '/(<a\s+[^>]*href\s*=\s*["\'])([^"\']*)(["\'])/';

//$replacement = '$1' . $url . '$3';
//$new_link = preg_replace($pattern, $replacement, $content);

$new_content = sprintf('<%1$s %2$s>%3$s</%1$s>', $wrapper, $wrapper_attributes, $content);

//error_log('new content: ' . $new_content);
echo $new_content;
