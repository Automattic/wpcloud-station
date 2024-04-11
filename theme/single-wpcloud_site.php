<?php
/**
 * The template for displaying single posts and pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WP Cloud
 * @subpackage WP_Cloud_Dashboard
 */
get_header();

$sites = new WP_Query( array(
	'post_type' => 'wpcloud_site',
	'author' => get_current_user_id(),
	'post_status' => 'any',
));
?>

<main class="wpcloud-site-list" id="site-content">
	<header class="page-header">
		<div>
			<h1 class="page-title"><?php esc_html_e( 'Sites', 'wpcloud-dashboard' ); ?></h1>
			<a class="wpcloud-add-site button button--solid" href="#">Add Site</a>
		</div>
		<div>
			<div class="wpcloud-site-search">
				<button>Find</button>
				<input type="search" placeholder="Search sites">
			</div>
			<div class="wpcloud-site-filter hidden">
				<select>
					<option value="all">All</option>
					<option value="active">Active</option>
					<option value="inactive">Inactive</option>
				</select>
			</div>
		</div>
	</header><!-- .page-header -->

	<table class="wpcloud-site-table">
		<thead>
		<tr class="wpcloud-list-row" >
			<th class="wpcloud-list-item wpcloud-site"><?php esc_html_e( 'Site', 'wpcloud-dashboard' ); ?></th>
			<th class="wpcloud-list-item wpcloud-role"><?php esc_html_e( 'Owner', 'wpcloud-dashboard' ); ?></th>
			<th class="wpcloud-list-item wpcloud-created"><?php esc_html_e( 'Created', 'wpcloud-dashboard' ); ?></th>
			<th class="wpcloud-list-item wpcloud-state"><?php esc_html_e( 'State', 'wpcloud-dashboard' ); ?></th>
			<th class="wpcloud-list-item wpcloud-php"><?php esc_html_e( 'PHP', 'wpcloud-dashboard' ); ?></th>
			<th class="wpcloud-list-item wpcloud-perf"><?php esc_html_e( 'Performance', 'wpcloud-dashboard' ); ?></th>
			<th class="wpcloud-list-item wpcloud-datacenter"><?php esc_html_e( 'Datacenter', 'wpcloud-dashboard' ); ?></th>
			<th class="wpcloud-list-item wpcloud-ip-addresses"><?php esc_html_e( 'IP Addresses', 'wpcloud-dashboard' ); ?></th>
			<th class="wpcloud-list-item wpcloud-fav" ><?php esc_html_e( '★', 'wpcloud-dashboard' ); ?></th>
			<th class="wpcloud-list-item wpcloud-actions" ><?php esc_html_e( 'Actions', 'wpcloud-dashboard' ); ?></th>
		</tr>
		</thead>
		<tbody>

	<?php
	if ( $sites->have_posts() ) {

		while ( $sites->have_posts() ) {
			$sites->the_post();

			get_template_part( 'template-parts/content', get_post_type() );
		}
	}

	?>
		</tbody>
	</table>
</main><!-- #site-content -->

<?php
get_footer();
