<?php
/**
 * Render Site Detail block.
 *
 * @package wpcloud-block
 * @subpackage accordion
 */

/* Return early if the user is not an admin and the block is admin only */
if ( $attributes['adminOnly'] && ! current_user_can( 'manage_options' ) ) {
	return;
}

list( $summary_block, $details_block ) = $block->parsed_block['innerBlocks'];

$summary = ( new WP_Block( $summary_block ) )->render( array( 'dynamic' => false ) );
$details = ( new WP_Block( $details_block ) )->render( array( 'dynamic' => false ) );

$attrs = $block->attributes;

$class_name  = $attrs['className'] ?? '';
$class_name .= $attrs['outline'] ? ' outline' : '';
$class_name .= $attrs['contrast'] ? ' contrast' : '';
$class_name .= $attrs['secondary'] ? ' secondary' : '';


$as_button = $attrs['button'] ? 'button' : '';

?>
<details class="wp-cloud-accordion">
	<summary role="<?php echo $as_button; ?>" class="<?php echo $class_name; ?>" ><?php echo $summary;  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></summary>
	<?php echo $details;  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</details>
