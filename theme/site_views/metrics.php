<?php
/**
 * Template Name: Site Metrics
 *
 * @package wpcloud-station
 */

$site_name = get_query_var( 'site_name' );
$site_cpt  = get_page_by_path( $site_name, OBJECT, 'wpcloud_site' );

if ( ! $site_cpt ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( 404 );
	exit();
}
global $post;
$post = $site_cpt; // phpcs:ignore

// @NOTE we need to build the blocks in the head so the styles are enqueued.
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php
	$header  = do_blocks( '<!-- wp:template-part {"slug":"header","tagName":"header","area":"header","theme":"wpcloud-station"} /-->' );
	$footer  = do_blocks( '<!-- wp:template-part {"slug":"footer","tagName":"footer","area":"footer","theme":"wpcloud-station"} /-->' );
	$content = do_blocks(
		'
<!-- wp:spacer {"height":"12px","className":"wpcloud-site-view--content--spacer"} -->
<div style="height:12px" aria-hidden="true" class="wp-block-spacer wpcloud-site-view--content--spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"className":"wpcloud-site-view--content"} -->
<div class="wp-block-columns wpcloud-site-view--content">

	<!-- wp:column {"width":""} -->
		<div class="wp-block-column"></div>
	<!-- /wp:column -->

	<!-- wp:column {"width":"360px","className":"wpcloud-site-view\u002d\u002dsidebar"} -->
	<div class="wp-block-column wpcloud-site-view--sidebar" style="flex-basis:360px">
		<!-- wp:template-part {"slug":"sites-sidebar","theme":"wpcloud-station"} /-->
	</div>
	<!-- /wp:column -->

	<!-- wp:column {"width":"1100px","className":"wpcloud-site-view\u002d\u002ddetails"} -->
	<div class="wp-block-column wpcloud-site-view--details" style="flex-basis:1100px">
		<!-- wp:group {"metadata":{"name":"Site header"},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
		<div class="wp-block-group">
			<!-- wp:wpcloud/site-card {"metadata":{"name":"Current Site Card"},"className":"wpcloud-site-card\u002d\u002dprimary"} -->
				<div class="wp-block-wpcloud-site-card wpcloud-site-card--primary wp-block-wpcloud-site-card"><img src="https://via.placeholder.com/50"/>
				<h2 class="site-title">
					<a href="#">Site Name</a>
				</h2>
				<h3 class="site-url"><a href="#" target="_blank" rel="noopener">
						<span>Site Domain</span>
					<span class="dashicon dashicons dashicons-external"></span>
					</a>
					</h3>
				</div>
			<!-- /wp:wpcloud/site-card -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group">
		<!-- wp:template-part {"slug":"site-metric","theme":"wpcloud-station"} /-->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:column -->

	<!-- wp:column -->
	<div class="wp-block-column"></div>
	<!-- /wp:column -->

</div>
<!-- /wp:columns -->
	'
	);
	?>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php echo $header  // phpcs:ignore ?>
<div class="wp-site-blocks">
<?php echo $content; // phpcs:ignore ?>

</div>
<?php echo $footer; // phpcs:ignore ?>
