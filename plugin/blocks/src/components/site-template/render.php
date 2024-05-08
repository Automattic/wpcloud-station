<?php

// lets see if we can remove the first block, which would be the header
// if it is, capture and remove it and add back in later
$inner_blocks = $block->parsed_block['innerBlocks'];
$header = $inner_blocks[0];
if ('wpcloud/site-template-header' === $header['blockName'] )  {
	$header = array_shift($inner_blocks);
	$header_content = ( new WP_Block( $header ) )->render( array( 'dynamic' => false ) );
	$header_item = '<tr>' . $header_content . '</tr>';
	$block->parsed_block['innerBlocks'] = $inner_blocks;
	array_splice( $block->parsed_block['innerContent'], 1, 2);
}

$page_key            = isset( $block->context['queryId'] ) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
$enhanced_pagination = isset( $block->context['enhancedPagination'] ) && $block->context['enhancedPagination'];
$page                = empty( $_GET[ $page_key ] ) ? 1 : (int) $_GET[ $page_key ];

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
	$query_args = build_query_vars_from_query_block( $block, $page );
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

	$post_id              = get_the_ID();
	$post_type            = get_post_type();
	$filter_block_context = static function ( $context ) use ( $post_id, $post_type ) {
		$context['postType'] = $post_type;
		$context['wpcloud/layout'] = 'table';
		$context['postId']   = $post_id;
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

	$inner_block_directives = $enhanced_pagination ? ' data-wp-key="post-template-item-' . $post_id . '"' : '';

	$content .= '<tr' . $inner_block_directives . ' class="' . esc_attr( $post_classes ) . '">' . $block_content . '</tr>';
}


/*
 * Use this function to restore the context of the template tags
 * from a secondary query loop back to the main query loop.
 * Since we use two custom loops, it's safest to always restore.
*/
printf(
	'<table %1$s><thead>%2$s</thead></tbody>%3$s</tbody></table>',
	$wrapper_attributes,
	$header_content,
	$content
);
