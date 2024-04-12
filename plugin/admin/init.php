<?php
/**
 * @package wpcloud-dashboard
 */

 declare( strict_types = 1 );

add_action( 'admin_enqueue_scripts', 'wpcloud_admin_enqueue_scripts' );
function wpcloud_admin_enqueue_scripts(): void {

	error_log(print_r(get_permalink(),true));
	wp_enqueue_script( 'wpcloud-admin', plugin_dir_url( __FILE__ ) . 'assets/js/wpcloud-admin.js' );

	wp_localize_script( 'wpcloud-admin', 'wpcloud', [
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'wpcloud' ),
	] );
}

add_action( 'admin_init', 'wpcloud_settings_init' );
function wpcloud_settings_init(): void {
	register_setting( 'wpcloud', 'wpcloud_settings' );

	add_settings_section(
		'wpcloud_section_settings',
		__( 'Settings', 'wpcloud' ),
		null,
		'wpcloud'
	);

	add_settings_field(
		'wpcloud_field_api_key',
		__( 'API Key', 'wpcloud' ),
		'wpcloud_field_input_cb',
		'wpcloud',
		'wpcloud_section_settings',
		[
			'label_for'         => 'wpcloud_api_key',
			'class'             => 'wpcloud_row',
			'wpcloud_custom_data' => 'custom',
		]
	);

	add_settings_field(
		'wpcloud_field_client',
		__( 'Client Name', 'wpcloud' ),
		'wpcloud_field_input_cb',
		'wpcloud',
		'wpcloud_section_settings',
		[
			'label_for'         => 'wpcloud_client',
			'class'             => 'wpcloud_row',
			'wpcloud_custom_data' => 'custom',
		]
	);

	add_settings_field(
		'wpcloud_field_domain',
		__( 'Domain', 'wpcloud' ),
		'wpcloud_field_input_cb',
		'wpcloud',
		'wpcloud_section_settings',
		[
			'label_for'         => 'wpcloud_domain',
			'class'             => 'wpcloud_row',
			'wpcloud_custom_data' => 'custom',
		]
	);

	add_settings_field(
		'wpcloud_field_backfill',
		__( 'Backfill (DEV ONLY)', 'wpcloud' ),
		'wpcloud_field_input_cb',
		'wpcloud',
		'wpcloud_section_settings',
		[
			'label_for'         => 'wpcloud_backfill',
			'class'             => 'wpcloud_row',
			'wpcloud_custom_data' => 'custom',
		]
		);
}

add_action( 'admin_menu', 'wpcloud_options_page' );
function wpcloud_options_page(): void {
    add_menu_page(
        'WP Cloud',
        'WP Cloud',
        'manage_options',
        'wpcloud',
        'wpcloud_admin_controller',
        '',
        20
    );

		add_submenu_page(
			'wpcloud',
			'New Site',
			'Add Site',
			'manage_options',
			'wpcloud_admin_new_site',
			'wpcloud_admin_new_site_controller',
		);

		add_submenu_page(
			'wpcloud',
			'Settings',
			'Settings',
			'manage_options',
			'wpcloud_admin_settings',
			'wpcloud_admin_options_controller',
		);
}


function wpcloud_get_action() {
	$action = '';
	if ( isset( $_REQUEST[ 'action' ] ) ) {
		$action = sanitize_text_field( wp_unslash( $_REQUEST[ 'action' ] ) );
	}
	return $action;
}

function wpcloud_section_options_cb( array $args ): void {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( '.', 'wpcloud' ); ?></p>
	<?php
}

function wpcloud_field_domain_cb( array $args ): void {
	$options = get_option( 'wpcloud_settings' );
	// output the field
	?>
	<input
		type="text"
		id="<?php echo esc_attr( $args['label_for'] ); ?>"
		name="wpcloud_settings[<?php echo esc_attr( $args['label_for'] ); ?>]"
		value="<?php echo isset( $options[ $args['label_for'] ] ) ? esc_attr( $options[ $args['label_for'] ] ) : ''; ?>"
	>
	<?php
}

function wpcloud_field_input_cb( array $args ): void {
	$options = get_option( 'wpcloud_settings' );
	// output the field
	?>
	<input
		type="text"
		id="<?php echo esc_attr( $args['label_for'] ); ?>"
		name="wpcloud_settings[<?php echo esc_attr( $args['label_for'] ); ?>]"
		value="<?php echo isset( $options[ $args['label_for'] ] ) ? esc_attr( $options[ $args['label_for'] ] ) : ''; ?>"
	>
	<?php
}

function site_created__success( int $wpcloud_site_id ): void {
	$wpcloud_site = get_post( $wpcloud_site_id );
?>
<div class="notice notice-success is-dismissible">
	<p>
		<?php
		/* translators: %s: name of the site */
		echo sprintf( __( 'Provisioning  %s', 'wpcloud' ), $wpcloud_site->post_title )
		?>
	</p>
</div>
<?php
}

function wpcloud_admin_controller(): void {
	// check user capabilities
	// @TODO make this a wpcloud capability...
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	switch ( wpcloud_get_action() ) {
		case 'create':
			$wpcloud_site_id = wpcloud_admin_create_site();
			if ( is_wp_error( $wpcloud_site_id ) ) {
				add_action( 'admin_notices', function() use ( $wpcloud_site_id ): void {
					?>
					<div class="notice notice-error is-dismissible">
						<p><?php echo esc_html( $wpcloud_site_id->get_error_message() ); ?></p>
					</div>
					<?php
				});
			} else {
				add_action( 'admin_notices', function() use ( $wpcloud_site_id ): void {
					site_created__success( $wpcloud_site_id );
				});
			}
			break;

		case 'edit':
			$site = WPCloud_Site::find( intval( $_GET[ 'post' ] ) );
			wpcloud_admin_site_form( $site );
			return;

		case 'view':
			$view_site = wpcloud_admin_view_site();
			if ( ! is_wp_error( $view_site ) ) {
				return;
			}
	}

	wpcloud_admin_list_sites();
}

function wpcloud_admin_new_site_controller(): void {
	wpcloud_admin_site_form( null );
}

function wpcloud_admin_list_sites(): void {
	do_action( 'admin_notices' );

	require_once  plugin_dir_path(__FILE__) . '/includes/class-wpcloud-site-list.php';
	$wpcloud_site_list = new WPCLOUD_Site_List();
	$wpcloud_site_list->prepare_items();
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<?php $wpcloud_site_list->display(); ?>
	</div>
	<?php
}


function wpcloud_admin_view_site(): mixed  {
	$wpcloud_site = WPCloud_Site::find( intval( $_GET[ 'post' ] ) );
	if ( ! $wpcloud_site ) {
		return new WP_Error( 'not_found', __( 'Site not found.' ) );
	}

	$set_details = $wpcloud_site->set_client_details();

	if ( is_wp_error( $set_details ) ) {
		return new WP_Error( 'not_found', __( 'Unable to fetch WP Cloud site details' ) );
	}

	require_once  plugin_dir_path(__FILE__) . 'view-site.php';

	return null;
}

function wpcloud_admin_site_form( ?WPCLOUD_Site $site ): void {
	$wpcloud_site = $site ?? new WPCloud_Site();

	require_once  plugin_dir_path(__FILE__) . 'edit-site.php';
}

function wpcloud_admin_create_site(): mixed {
	check_admin_referer( 'wpcloud-admin-site-form' );
	if ( ! isset( $_POST['wpcloud_site'] ) ) {
		return new WP_Error( 'no-data', __( 'No data found', 'wpcloud' ) );
	}

	$_POST['wpcloud_site']['post_title'] = wpcloud_site_get_default_domain( $_POST['wpcloud_site']['post_title'] );

	$post = wp_insert_post( $_POST['wpcloud_site'] );
	if ( is_wp_error( $post ) ) {
		error_log( $post->get_error_message() );
	}

	return $post;
}

function wpcloud_admin_options_controller(): void {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
				return;
		}

		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'wpcloud_messages', 'wpcloud_message', __( 'Settings Saved', 'wpcloud' ), 'updated' );
		}

		settings_errors( 'wpcloud_messages' );
		?>
	<div class="wrap">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<form action="options.php" method="post">
						<?php
						settings_fields( 'wpcloud' );
						do_settings_sections( 'wpcloud' );
						submit_button( __( 'Save Settings', 'wpcloud' ) );
						?>
				</form>
		</div>
		<?php

}
