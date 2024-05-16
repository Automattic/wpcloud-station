<?php
/**
 * @package wpcloud-station
 */

declare( strict_types = 1 );

require_once plugin_dir_path( __FILE__ ) . 'includes/wpcloud-headstart.php';

function wpcloud_admin_get_available_themes() {
	return array(
		'themes/twentytwentyfour'  => __( 'Twenty Twenty Four', 'wpcloud' ),
		'themes/twentytwentythree' => __( 'Twenty Twenty Three', 'wpcloud' ),
		'themes/twentytwentytwo'   => __( 'Twenty Twenty Two', 'wpcloud' ),
		'themes/twentytwentyone'   => __( 'Twenty Twenty One', 'wpcloud' ),
	);
}

function wpcloud_admin_get_available_plugins() {
	return array(
		'plugins/classic-editor'             => __( 'Classic Editor', 'wpcloud' ),
		'plugins/crowdsignal-forms'          => __( 'CrowSignal Forms', 'wpcloud' ),
		'plugins/mailpoet'                   => __( 'MailPoet', 'wpcloud' ),
		'plugins/polldaddy'                  => __( 'Crowdsignal', 'wpcloud' ),
		'plugins/woocommerce'                => __( 'WooCommerce', 'wpcloud' ),
		'plugins/-bookings'                  => __( 'WooCommerce Bookings', 'wpcloud' ),
		'plugins/woocommerce-payments'       => __( 'WooCommerce Payments', 'wpcloud' ),
		'plugins/woocommerce-subscriptions'  => __( 'WooCommerce Subscriptions', 'wpcloud' ),
		'plugins/wordpress-seo'              => __( 'WordPress SEO', 'wpcloud' ),
	);
}

function wpcloud_settings_sanitize( $input ) {
	$input['software'] = array_filter( $input['software'] ?? []);

	if ( empty( $input['software'] ) ) {
		unset( $input['software'] );
	}

	$input = array_filter( $input );

	return $input;
}

function wpcloud_settings_init(): void {
	register_setting( 'wpcloud', 'wpcloud_settings', 'wpcloud_settings_sanitize' );

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
			'label_for'           => 'wpcloud_domain',
			'class'               => 'wpcloud_row',
			'wpcloud_custom_data' => 'custom',
			'default'             => WPCLOUD_DEMO_DOMAIN,
			'description'         => __( 'The default domain to use for new sites. Each site will use this root domain with the site name as the subdomain. If using the default WP Cloud demo domain, a unique subdomain will be generated for each site.' ),
		]
	);

	$themes = wpcloud_admin_get_available_themes();
	add_settings_field(
		'wpcloud_field_default_theme',
		__( 'Default Theme', 'wpcloud' ),
		'wpcloud_field_select_cb',
		'wpcloud',
		'wpcloud_section_settings',
		[
			'label_for'           => 'wpcloud_default_theme',
			'class'               => 'wpcloud_row',
			'wpcloud_custom_data' => 'custom',
			'description'         => __( 'The default theme to install on new sites.'),
			'items'               => $themes,
			'default'             => array_keys( $themes )[0],
		]
	);

	add_settings_field(
		'wpcloud_field_plugins',
		__( 'Default Plugins', 'wpcloud' ),
		'wpcloud_field_software_cb',
		'wpcloud',
		'wpcloud_section_settings',
		[
			'label_for'           => 'software',
			'class'               => 'wpcloud_row',
			'wpcloud_custom_data' => 'custom',
			'description'         => __( 'Plugins available to install or activate with new installs. '),
			'items'               => wpcloud_admin_get_available_plugins(),
		]
	);

	// Only allow headstart if no settings have been saved yet
	// headstart is might make unwanted changes if there are existing settings.
	// It can be forced to run via the cli command `wp wpcloud client headstart`
	$allow_headstart = empty( get_option( 'wpcloud_settings' ) );
	add_settings_field(
		'wpcloud_field_headstart',
		__( 'Headstart Set Up', 'wpcloud' ),
		'wpcloud_field_input_cb',
		'wpcloud',
		'wpcloud_section_settings',
		[
			'label_for'         => 'wpcloud_headstart',
			'class'             => 'wpcloud_row',
			'type'              => 'checkbox',
			'wpcloud_custom_data' => 'custom',
			'description'         => __( 'Run the headstart script to setup the demo site. This can only be ran when saving WP Cloud setting for the first time.' ),
			'checked' => $allow_headstart,
			'disabled' => ! $allow_headstart,
		]
	);
}
add_action( 'admin_init', 'wpcloud_settings_init' );

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
add_action( 'admin_menu', 'wpcloud_options_page' );

function wpcloud_get_action() {
	$action = '';
	if ( isset( $_REQUEST[ 'action' ] ) ) {
		$action = sanitize_text_field( wp_unslash( $_REQUEST[ 'action' ] ) );
	}
	return $action;
}

function wpcloud_field_input_cb( array $args ): void {
	$label = $args['label_for'] ?? '';
	$options = get_option( 'wpcloud_settings' );
	$default = $args[ 'default' ] ?? '';
	$value = $options[ $label ] ?? $default;
	$type = $args['type'] ?? 'text';
	$checked = $args[ 'checked' ] ?? false;
	// output the field
	if ( 'checkbox' === $type ) {
		$value = '1';
	}
	$disabled = $args['disabled'] ?? false;
	?>
	<input
		type="<?php echo $type ?>"
		id="<?php echo esc_attr( $args['label_for'] ); ?>"
		name="wpcloud_settings[<?php echo esc_attr( $args['label_for'] ); ?>]"
		value="<?php echo $value ?>"
		<?php if ( 'checkbox' === $type && $checked ) { echo ' checked '; } ?>
		<?php if ( $disabled ) { echo ' disabled '; } ?>
	>
	<?php if ( isset( $args['description'] ) ) { ?>
		<td><p class="setting-description"><?php echo esc_html( $args['description'] ); ?></p></td>
	<?php
	}
}

function wpcloud_field_select_cb( array $args ): void {
	$options   = get_option( 'wpcloud_settings' );
	$label_for = esc_attr( $args['label_for'] );
	$name      = "wpcloud_settings[$label_for]";
	$default   = $args[ 'default' ] ?? '';
	$value     = esc_attr( $options[ $label_for ] ?? $default );
	$items     = $args['items'];

	// output the field
	?>
	<select name="<?php echo $name; ?>" id="<?php echo $label_for; ?>">
		<option value=''></option>
	<?php
	foreach( $items as $item_value => $item_label ) {
		$selected = $item_value === $value ? 'selected' : '';
		echo "<option value='$item_value' $selected>$item_label</option>";
	}
	?>
	</select>
	<?php if ( isset( $args['description'] ) ) { ?>
		<td><p class="setting-description"><?php echo esc_html( $args['description'] ); ?></p></td>
	<?php
	}
}

function wpcloud_field_software_cb( array $args ): void {
	$options   = get_option( 'wpcloud_settings' );
	$label_for = esc_attr( $args['label_for'] );
	$items     = $args['items'];

	// output the field
	echo "<table>";
	foreach( $items as $item_value => $item_label ) {
		$name = "wpcloud_settings[$label_for][$item_value]";
		$value     = isset( $options[ "software" ][ $item_value ] ) ? esc_attr( $options[ "software" ][ $item_value ] ) : '';
		?>
		<tr>
			<td style="padding: 5px;">
				<label><?php echo $item_label; ?></label>
			</td>
			<td style="padding: 5px;">
				<select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
					<option value=""></option>
					<option value="install" <?php echo ($value === 'install') ? 'selected' : ''; ?>>Install</option>
					<option value="activate" <?php echo ($value === 'activate') ? 'selected' : ''; ?>>Activate</option>
				</select>
			</td>
		</tr>


	<?php
	}
	echo "</table>";
	if ( isset( $args['description'] ) ) { ?>
		<td><p class="setting-description"><?php echo esc_html( $args['description'] ); ?></p></td>
	<?php
	}
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
	if ( is_wp_error( $wpcloud_site ) ) {
		return $wpcloud_site;
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
		require_once  plugin_dir_path(__FILE__) . 'options.php';
}
