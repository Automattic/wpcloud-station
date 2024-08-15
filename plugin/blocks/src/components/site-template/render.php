<?php
/**
 * Render the Site Template block.
 *
 * @package wpcloud-block
 * @subpackage site-template
 */

// lets see if we can remove the first block, which would be the header.
// if it is, capture and remove it and add back in later.
$inner_blocks   = $block->parsed_block['innerBlocks'];
$header         = $inner_blocks[0];
$header_content = '';
if ( 'wpcloud/site-template-header' === $header['blockName'] ) {
	$header                             = array_shift( $inner_blocks );
	$header_content                     = ( new WP_Block( $header ) )->render( array( 'dynamic' => false ) );
	$header_item                        = '<tr>' . $header_content . '</tr>';
	$block->parsed_block['innerBlocks'] = $inner_blocks;
	array_splice( $block->parsed_block['innerContent'], 1, 2 );
}

$page_key            = isset( $block->context['queryId'] ) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
$enhanced_pagination = isset( $block->context['enhancedPagination'] ) && $block->context['enhancedPagination'];
$the_page            = empty( $_GET[ $page_key ] ) ? 1 : (int) $_GET[ $page_key ]; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

// Use global query if needed.
$use_global_query = ( isset( $block->context['query']['inherit'] ) && $block->context['query']['inherit'] );
if ( $use_global_query ) {
	global $wp_query;

	/*
	 * If already in the main query loop, duplicate the query instance to not tamper with the main instance.
	 * Since this is a nested query, it should start at the beginning, therefore rewind posts.
	 * Otherwise, the main query loop has not started yet and this block is responsible for doing so.
	 */
	if ( in_the_loop() ) {
		$query = clone $wp_query;
		$query->rewind_posts();
	} else {
		$query = $wp_query;
	}
} else {
	$query_args = build_query_vars_from_query_block( $block, $the_page );

	// Add in some special handling for the site template block.
	$query_args['post_status'] = 'any';

	// Limit the query to the current user if they are not an admin.
	if ( ! current_user_can( 'manage_options' ) ) {
		$query_args['author__in'] = array( get_current_user_id() );
	}

	$query_args = apply_filters( 'wpcloud_site_list_query_args', $query_args, $block );
	$query      = new WP_Query( $query_args );
}

if ( ! $query->have_posts() ) {
	return '';
}


$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'wpcloud-site-template' ) );

$content = '';
while ( $query->have_posts() ) {
	$query->the_post();

	// Get an instance of the current Post Template block.
	$block_instance = $block->parsed_block;

	// Set the block name to one that does not correspond to an existing registered block.
	// This ensures that for the inner instances of the Post Template block, we do not render any block supports.
	$block_instance['blockName'] = 'core/null';

	$the_post_id          = get_the_ID();
	$the_post_type        = get_post_type();
	$filter_block_context = static function ( $context ) use ( $the_post_id, $the_post_type ) {
		$context['postType']       = $the_post_type;
		$context['wpcloud/layout'] = 'table';
		$context['postId']         = $the_post_id;
		return $context;
	};

	// Use an early priority to so that other 'render_block_context' filters have access to the values.
	add_filter( 'render_block_context', $filter_block_context, 1 );
	// Render the inner blocks of the Post Template block with `dynamic` set to `false` to prevent calling
	// `render_callback` and ensure that no wrapper markup is included.
	$block_content = ( new WP_Block( $block_instance ) )->render( array( 'dynamic' => false ) );
	remove_filter( 'render_block_context', $filter_block_context, 1 );


	// Wrap the render inner blocks in a `li` element with the appropriate post classes.
	$post_classes = implode( ' ', get_post_class( 'wp-block-post' ) );

	$inner_block_directives = $enhanced_pagination ? ' data-wp-key="post-template-item-' . $the_post_id . '"' : '';

	$content .= '<tr' . $inner_block_directives . ' class="' . esc_attr( $post_classes ) . '">' . $block_content . '</tr>';
}


/*
 * Use this function to restore the context of the template tags
 * from a secondary query loop back to the main query loop.
 * Since we use two custom loops, it's safest to always restore.
*/
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
printf(
	'<table %1$s><thead>%2$s</thead><tbody>%3$s</tbody></table>',
	$wrapper_attributes,
	$header_content,
	$content
);
