<?php
/**
 * The template for displaying the login page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WP Cloud
 * @subpackage WP_Cloud_Dashboard
 */

  if ( is_user_logged_in() ) {
 	wp_redirect( '/sites' );
 	exit;
 }

 $errors = null;


 if (isset($_POST['_wpnonce']) && wp_verify_nonce( $_POST['_wpnonce'], 'wpcloud-login' ) ) {
	$user = wp_signon();
	if ( is_wp_error( $user ) ) {
		$errors = $user->get_error_message();
	} else {
		$ref = wp_parse_args( wp_parse_url( $_POST[ '_wp_http_referer' ], PHP_URL_QUERY ) )[ 'ref' ] ?? 'sites';
		wp_safe_redirect( '/' . $ref );
		exit;
	}
 }
 get_header( 'logged-out' );
?>
<main class="wpcloud-login">

	<form method="post" class="wpcloud-form" novalidate >
		<?php wp_nonce_field( 'wpcloud-login' ); ?>
		<?php wpcloud_show_logo(); ?>
		<h1><?php
			/* translators: %s: Site title */
			printf( __( 'Login to %s'), wpcloud_site_name() ); ?>
		</h1>
		<p>Any information we want to add here about their login credentials.</p>
			<div class="wpcloud-form-group wpcloud-form-group--stacked">
				<label for="log"><?php _e( 'Email address', 'wpcloud-dashboard' ) ?></label>
				<input type="text" name="log" placeholder="youremail@domain.com" required />
			</div>
			<div class="wpcloud-form-group wpcloud-form-group--stacked">
				<label for="pwd"><?php _e( 'Password', 'wpcloud-dashboard' ) ?></label>
				<input type="password" name="pwd" placeholder="**************" required />
			</div>
			<button class="button" disabled><?php _e( 'Login', 'wpcloud-dashboard' ) ?></button>

		<?php if ( $errors ) : ?>
		<div class="wpcloud-notice wpcloud-notice--error">
			<?php echo $errors; ?>
		</div>
	<?php endif; ?>
	</form>
</main>
<script>

( () => {
		window.history.pushState({}, document.title, "/login" );
		const form = document.querySelector( '.wpcloud-form' );
		const button = form.querySelector( 'button' );
		const errors = form.querySelector( '.wpcloud-notice--error' );

		form.addEventListener('input', () => {
			errors?.remove();
			if ( form.checkValidity() ) {
				button?.removeAttribute( 'disabled' );
			} else {
				button?.setAttribute( 'disabled','' );
			}
		} );
		form.addEventListener("submit", disableButton );
	} )();
</script>
<?php
get_footer();