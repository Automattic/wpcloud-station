<?php
/**
 * @package wpcloud-dashboard
 */

 declare( strict_types = 1 );

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
}

add_action( 'admin_menu', 'wpcloud_options_page' );
function wpcloud_options_page(): void {
    add_menu_page(
        'WP Cloud',
        'WP Cloud',
        'manage_options',
        'wpcloud',
        'wpcloud_site_list_cb',
        '',
        20
    );

		add_submenu_page(
			'wpcloud',
			'New Site',
			'Add Site',
			'manage_options',
			'wpcloud_admin_new_site',
			'wpcloud_admin_new_site_cb',
		);

		add_submenu_page(
			'wpcloud',
			'Settings',
			'Settings',
			'manage_options',
			'wpcloud_admin_settings',
			'wpcloud_options_page_html',
		);
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

function site_created__success( WPCloud_Site $wpcloud_site ): void {
?>
<div class="notice notice-success is-dismissible">
	<p>
		<?php
		/* translators: %s: name of the site */
		echo sprintf( __( 'Provisioning  %s', 'wpcloud' ), $wpcloud_site->name )
		?>
	</p>
</div>
<?php
}

function wpcloud_site_list_cb(): void {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['wpcloud_site'] ) ) {
		check_admin_referer( 'wpcloud-edit-site' );
		$wpcloud_site = $_POST['wpcloud_site'];
		if ( $wpcloud_site['id'] === '' ) {
			// create a new site
			$wpcloud_site = WPCloud_Site::create(
				name: $wpcloud_site['site_name'],
				php_version: $wpcloud_site['php_version'],
				data_center: $wpcloud_site['data_center'],
				owner_id: $wpcloud_site['owner_id']
			);
			if ( is_wp_error( $wpcloud_site ) ) {
				// handle the error
			} else {
				add_action( 'admin_notices', function() use ( $wpcloud_site ):void {
					site_created__success( $wpcloud_site );
				});
			}
		} else {
			// @TODO update the site
		}
	}
	require_once	plugin_dir_path(__FILE__) . 'list-sites.php';
}

function wpcloud_admin_new_site_cb(): void {
	// check user capabilities - need to add a capability for this 'wpcloud_add_site' or something
	if ( ! current_user_can( 'manage_options' ) ) {
			return;
	}

	$wpcloud_site = new WPCloud_Site();

	require_once  plugin_dir_path(__FILE__) . 'edit-site.php';
}

function wpcloud_options_page_html(): void {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
				return;
		}

		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'wpcloud_messages', 'wpcloud_message', __( 'Settings Saved', 'wpcloud' ), 'updated' );
		}

		settings_errors( 'wpcloud_messages' );
		require_once  plugin_dir_path(__FILE__) . 'options.php';
}
