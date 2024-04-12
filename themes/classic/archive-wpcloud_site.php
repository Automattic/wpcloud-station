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
$args = array(
	'post_type' => 'wpcloud_site',
	'post_status' => 'any',
);
if ( ! current_user_can( 'manage_options' ) ) {
	$args['author__in'] = array( get_current_user_id() );
}
$sites = new WP_Query( $args );
?>

<main class="wpcloud-site-list site-content" id="site-content">
	<header class="page-header">
		<div>
			<h1 class="page-title"><?php esc_html_e( 'Sites', 'wpcloud-dashboard' ); ?></h1>
			<a class="wpcloud-add-site button button--solid" href="/new-site">Add Site</a>
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
<script>
	const wpcloudReady = fn => document.readyState !== 'loading' ? fn() : document.addEventListener('DOMContentLoaded', fn);
	wpcloudReady(() => {
		document.querySelectorAll('.wpcloud-fav').forEach((fav) => {
			fav.addEventListener('click', () => {
				fav.querySelector('.wpcloud-list-favorite').classList.toggle('is-favorite');
			});
		});
	});
</script>
<?php
get_footer();
