<?php
/**
 * The template for displaying single posts and pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WP Cloud
 * @subpackage WP_Cloud_Dashboard
 */


function wpcloud_set_sites_current ($classes) {
	if (in_array('wpcloud-nav-sites', $classes) ){
		$classes[] = ' current-menu-item ';
	}
  return $classes;
}
add_filter('nav_menu_css_class' , 'wpcloud_set_sites_current' , 10 , 1);

$new_site = true;

if ( isset( $_POST['action'] ) && 'create' === $_POST['action'] ) {
	wp_verify_nonce( 'wpcloud-site-form' );
	$post_title  = sanitize_text_field( $_POST['post_title'] );
	$php_version = sanitize_text_field( $_POST['php_version'] );
	$data_center = sanitize_text_field( $_POST['data_center'] );

	$post_id = wp_insert_post( array(
		'post_title' => wpcloud_site_get_default_domain( $post_title ),
		'post_type' => 'wpcloud_site',
		'post_status' => 'publish', // @TODO: Change to draft after the demo
		'post_author' => get_current_user_id(),
		'meta_input' => array(
			'php_version' => $php_version,
			'data_center' => $data_center,
			'name'				=> $post_title,
		),
	) );

	if ( ! is_wp_error( $post_id ) ) {
		wpcloud_dashboard_add_notice( __( 'Site created successfully' ), 'success');
		wp_safe_redirect( '/sites' );
	}

}

get_header( 'sidebar' );
?>
<main class="wpcloud-new-site">
	<header>
		<nav class="breadcrumbs">
			<a href="/sites"><img src="<?php echo get_theme_file_uri( 'assets/chevron-left.svg' )?>"/><?php _e('Sites', 'wpcloud-dashboard'); ?><span></a>
		</nav>

		<h1><?php _e('Add Site', 'wpcloud-dashboard'); ?></h1>

	</header>
	<article >
		<form action="/new-site" method="post" class="wpcloud-form" >
			<?php
			wp_nonce_field( 'wpcloud-site-form' );
			?>
			<input type="hidden" name="action" value="<?php echo $new_site ? 'create' : 'edit' ?>">
			<div class="wpcloud-form-group wpcloud-form-group--stacked">
				<label for="post_title"><?php _e( 'Site Name', 'wpcloud-dashboard' ) ?></label>
				<input type="text" name="post_title" placeholder="Type name here">
			</div>
			<div class="wpcloud-form-group wpcloud-form-group--stacked">
				<label for="php_version"><?php _e( 'PHP Version', 'wpcloud-dashboard' ) ?></label>
				<select id="wpcloud-php-version" name="php_version">
					<?php foreach ( WPCLOUD_PHP_VERSIONS as $version => $label ) : ?>
						<option value="<?php echo esc_attr( $version ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="wpcloud-form-group wpcloud-form-group--stacked">
				<label for="data_center"><?php _e( 'Data Center', 'wpcloud-dashboard' ) ?></label>
				<select id="wpcloud-site-geo" name="data_center">
					<?php foreach ( WPCLOUD_DATA_CENTERS as $data_center => $label ) : ?>
						<option value="<?php echo esc_attr( $data_center ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<button class="button disabled" disabled><?php _e( 'Create Site', 'wpcloud-dashboard' ) ?></button>
		</form>
	</article>
</main>

<script>
	const wpcloudReady = fn => document.readyState !== 'loading' ? fn() : document.addEventListener('DOMContentLoaded', fn);
	wpcloudReady(() => {
		const form = document.querySelector('.wpcloud-form');
		const button = form.querySelector('button');
		const siteName = form.querySelector('input[name="post_title"]');

		const disableButton = () => {
			button.classList.add('disabled');
			button.setAttribute( 'disabled', 'disabled' );
		};

		const enableButton = () => {
			button.classList.remove('disabled');
			button.removeAttribute( 'disabled' );
		};

		const validateForm = () => {
			siteName.value.length > 0 ? enableButton() : disableButton();
		};

		form.addEventListener('input', validateForm);
		form.addEventListener("submit", disableButton );
	} );
</script>
<?php
get_footer();
