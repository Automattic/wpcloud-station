<?php
/**
 * WP Cloud Station Admin
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

require_once 'includes/wpcloud-headstart.php';
$wpcloud_request_api_status = wpcloud_client_test_status();
$wpcloud_api_healthy        = is_wp_error( $wpcloud_request_api_status ) ? false : true;

/**
 * Get the available themes.
 *
 * @return array The available themes.
 */
function wpcloud_admin_get_available_themes() {
	return array(
		'themes/twentytwentyfour'  => __( 'Twenty Twenty Four', 'wpcloud' ),
		'themes/twentytwentythree' => __( 'Twenty Twenty Three', 'wpcloud' ),
		'themes/twentytwentytwo'   => __( 'Twenty Twenty Two', 'wpcloud' ),
		'themes/twentytwentyone'   => __( 'Twenty Twenty One', 'wpcloud' ),
	);
}

/**
 * Get the available plugins.
 *
 * @return array The available plugins.
 */
function wpcloud_admin_get_available_plugins() {
	return array(
		'plugins/classic-editor'            => __( 'Classic Editor', 'wpcloud' ),
		'plugins/crowdsignal-forms'         => __( 'CrowSignal Forms', 'wpcloud' ),
		'plugins/mailpoet'                  => __( 'MailPoet', 'wpcloud' ),
		'plugins/polldaddy'                 => __( 'Crowdsignal', 'wpcloud' ),
		'plugins/woocommerce'               => __( 'WooCommerce', 'wpcloud' ),
		'plugins/-bookings'                 => __( 'WooCommerce Bookings', 'wpcloud' ),
		'plugins/woocommerce-payments'      => __( 'WooCommerce Payments', 'wpcloud' ),
		'plugins/woocommerce-subscriptions' => __( 'WooCommerce Subscriptions', 'wpcloud' ),
		'plugins/wordpress-seo'             => __( 'WordPress SEO', 'wpcloud' ),
	);
}

/**
 * Sanitize the settings
 *
 * @param array $input The input to sanitize.
 *
 * @return array The sanitized input.
 */
function wpcloud_settings_sanitize( $input ) {
	$input['software'] = array_filter( $input['software'] ?? array() );

	if ( empty( $input['software'] ) ) {
		unset( $input['software'] );
	}

	$input = array_filter( $input );

	return $input;
}

/**
 * Initialize the settings
 *
 * @return void
 */
function wpcloud_settings_init(): void {
	global $wpcloud_api_healthy;

	register_setting( 'wpcloud', 'wpcloud_settings', 'wpcloud_settings_sanitize' );

	add_settings_section(
		'wpcloud_section_settings',
		__( 'Settings', 'wpcloud' ),
		null,
		'wpcloud'
	);
	add_settings_field(
		'wpcloud_field_client',
		__( 'Client Name', 'wpcloud' ),
		'wpcloud_field_input_cb',
		'wpcloud',
		'wpcloud_section_settings',
		array(
			'label_for'           => 'wpcloud_client',
			'class'               => 'wpcloud_row',
			'wpcloud_custom_data' => 'custom',
		)
	);

	// Only show the API key field if it's not set in the environment.
	if ( ! wpcloud_get_api_key() ) {
		add_settings_field(
			'wpcloud_field_api_key',
			__( 'API Key', 'wpcloud' ),
			'wpcloud_field_input_cb',
			'wpcloud',
			'wpcloud_section_settings',
			array(
				'label_for'           => 'wpcloud_api_key',
				'class'               => 'wpcloud_row',
				'wpcloud_custom_data' => 'custom',
			)
		);
	}

	add_settings_field(
		'wpcloud_field_domain',
		__( 'Domain', 'wpcloud' ),
		'wpcloud_field_input_cb',
		'wpcloud',
		'wpcloud_section_settings',
		array(
			'label_for'           => 'wpcloud_domain',
			'class'               => 'wpcloud_row',
			'wpcloud_custom_data' => 'custom',
			'default'             => '',
			'description'         => __( 'The default domain to use for new sites. Each site will use this root domain with the site name as the subdomain. If left empty a unique subdomain will be generated for each site.' ),
			'disabled'            => ! $wpcloud_api_healthy,
		)
	);

	add_settings_field(
		'wpcloud_field_webhook',
		__( 'Webhook URL', 'wpcloud' ),
		'wpcloud_client_meta_field_input_cb',
		'wpcloud',
		'wpcloud_section_settings',
		array(
			'label_for'       => 'wpcloud_webhook_url',
			'class'           => 'wpcloud_row',
			'description'     => __( 'The URL to send site creation events to. This can be used to trigger actions on site creation.' ),
			'client_meta_key' => 'webhook_url',
			'disabled'        => ! $wpcloud_api_healthy,
		)
	);

	/*
	@TODO Still need to implement the secret key handshake on wp cloud.
	add_settings_field(
		'wpcloud_field_webhook_secret_key',
		__( 'Webhook Secret Key', 'wpcloud' ),
		'wpcloud_client_meta_field_input_cb',
		'wpcloud',
		'wpcloud_section_settings',
		[
			'label_for'       => 'wpcloud_webhook_secret_key',
			'class'           => 'wpcloud_row',
			'client_meta_key' => 'webhook_secret_key',
			'description'       => __( 'The secret key to use when sending site creation events to the webhook URL. This can be used to verify the request came from WP Cloud.' ),
		]
	);
	*/

	$themes = wpcloud_admin_get_available_themes();
	add_settings_field(
		'wpcloud_field_default_theme',
		__( 'Default Theme', 'wpcloud' ),
		'wpcloud_field_select_cb',
		'wpcloud',
		'wpcloud_section_settings',
		array(
			'label_for'           => 'wpcloud_default_theme',
			'class'               => 'wpcloud_row',
			'wpcloud_custom_data' => 'custom',
			'description'         => __( 'The default theme to install on new sites.' ),
			'items'               => $themes,
			'default'             => array_keys( $themes )[0],
			'disabled'            => ! $wpcloud_api_healthy,
		)
	);

	add_settings_field(
		'wpcloud_field_plugins',
		__( 'Default Plugins', 'wpcloud' ),
		'wpcloud_field_software_cb',
		'wpcloud',
		'wpcloud_section_settings',
		array(
			'label_for'           => 'software',
			'class'               => 'wpcloud_row',
			'wpcloud_custom_data' => 'custom',
			'description'         => __( 'Plugins available to install or activate with new installs. ' ),
			'items'               => wpcloud_admin_get_available_plugins(),
			'disabled'            => ! $wpcloud_api_healthy,
		)
	);

	add_settings_field(
		'wpcloud_field_client_cache',
		__( 'Enable client request caching', 'wpcloud' ),
		'wpcloud_field_input_cb',
		'wpcloud',
		'wpcloud_section_settings',
		array(
			'label_for'           => 'client_cache',
			'class'               => 'wpcloud_row',
			'wpcloud_custom_data' => 'custom',
			'description'         => __( 'Enable caching of common client requests to reduce the number of requests to the WP Cloud API and speed up page loads. The cache is stored in memory per request.' ),
			'type'                => 'checkbox',
			'checked'             => get_option( 'wpcloud_settings', array() )['client_cache'] ?? true,
			'disabled'            => ! $wpcloud_api_healthy,
		)
	);

	add_settings_field(
		'wpcloud_field_headstart',
		__( 'Headstart Set Up', 'wpcloud' ),
		'wpcloud_field_input_cb',
		'wpcloud',
		'wpcloud_section_settings',
		array(
			'label_for'           => 'wpcloud_headstart',
			'class'               => 'wpcloud_row',
			'type'                => 'checkbox',
			'wpcloud_custom_data' => 'custom',
			'description'         => __( 'Run the headstart script to setup the demo site. The script will not delete or overwrite any existing pages or settings so it\'s safe to run multiple times.' ),
			'checked'             => false,
			'disabled'            => ! $wpcloud_api_healthy,
		)
	);
}
add_action( 'admin_init', 'wpcloud_settings_init' );

/**
 * Add the options page
 *
 * @return void
 */
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
		'All Sites',
		'All Sites',
		'manage_options',
		'wpcloud',
		'wpcloud_admin_controller',
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

/**
 * Get the action from the request
 *
 * @return string The action.
 */
function wpcloud_get_action() {
	$action = '';
	if ( isset( $_REQUEST['action'] ) ) {
		$action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) );
	}
	return $action;
}

/**
 * Get the site id from the request
 *
 * @param array $args The request args.
 * @return void.
 */
function wpcloud_field_input_cb( array $args ): void {
	$label   = $args['label_for'] ?? '';
	$options = get_option( 'wpcloud_settings' );
	$default = $args['default'] ?? '';
	$value   = $args['value'] ?? $options[ $label ] ?? $default;
	$type    = $args['type'] ?? 'text';
	$checked = $args['checked'] ?? false;

	// output the field.
	if ( 'checkbox' === $type ) {
		$value = '1';
	}
	$disabled = $args['disabled'] ?? false ? ' disabled ' : '';
	?>
	<input
			type="<?php echo esc_attr( $type ); ?>"
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			name="wpcloud_settings[<?php echo esc_attr( $args['label_for'] ); ?>]"
			value="<?php echo esc_attr( $value ); ?>"
			<?php
			if ( 'checkbox' === $type && $checked ) {
				echo ' checked '; }
			?>
			<?php echo esc_attr( $disabled ); ?>
		>
	<?php if ( isset( $args['description'] ) ) { ?>
		<p class="description <?php echo esc_attr( $disabled ); ?>"><?php echo esc_html( $args['description'] ); ?></p>
		<?php
	}
	if ( isset( $args['error'] ) ) {
		?>
		<p class="error"><?php echo esc_html( $args['error'] ); ?></p>
		<?php
	}
}

/**
 * Get the client meta field input
 *
 * @param array $args The args.
 * @return void.
 */
function wpcloud_client_meta_field_input_cb( array $args ): void {
	$disabled = $args['{disabled'] ?? false;
	if ( ! $disabled ) {
		$meta_key = $args['client_meta_key'] ?? '';
		$request  = wpcloud_client_get_client_meta( $meta_key );
		if ( ! is_wp_error( $request ) ) {
			$args['value'] = $request->$meta_key;
		}
	}

	wpcloud_field_input_cb( $args );
}

/**
 * Hook to update the client meta when the option is updated.
 *
 * @param array $value The new value.
 * @param array $old_value The old value.
 * @return array The new value.
 */
function wpcloud_update_remote_client_meta( $value, $old_value ) {
	foreach ( array( 'webhook_url', 'webhook_secret_key' ) as $meta_key ) {
		$option_key = 'wpcloud_' . $meta_key;
		$old_meta   = $old_value[ $option_key ] ?? '';
		$new_meta   = $value[ $option_key ] ?? '';
		if ( $old_meta !== $new_meta ) {
			wpcloud_client_set_client_meta( $meta_key, $new_meta );
		}
		// Let's use the remote value as the only source.
		unset( $value[ $option_key ] );
	}

	if ( isset( $value['wpcloud_headstart'] ) ) {
		$value['wpcloud_headstart'] = time();
	}
	return $value;
}
add_action( 'pre_update_option_wpcloud_settings', 'wpcloud_update_remote_client_meta', 10, 2 );

/**
 * Get the client meta field input
 *
 * @param array $args The args.
 * @return void.
 */
function wpcloud_field_select_cb( array $args ): void {
	$options   = get_option( 'wpcloud_settings' );
	$label_for = esc_attr( $args['label_for'] );
	$name      = "wpcloud_settings[$label_for]";
	$default   = $args['default'] ?? '';
	$value     = esc_attr( $options[ $label_for ] ?? $default );
	$items     = $args['items'];
	$disabled  = $args['disabled'] ?? false ? ' disabled ' : '';

	// output the field.
	?>
	<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $label_for ); ?>" <?php echo esc_attr( $disabled ); ?> >
		<option value=''></option>
	<?php
	foreach ( $items as $item_value => $item_label ) {
		$selected = $item_value === $value ? 'selected' : '';
		echo '<option value="' . esc_attr( $item_value ) . '"' . esc_attr( $selected ) . '>' . esc_html( $item_label ) . '</option>';
	}
	?>
	</select>
	<?php if ( isset( $args['description'] ) ) { ?>
		<p class="description <?php echo esc_attr( $disabled ); ?>"><?php echo esc_html( $args['description'] ); ?></p>
		<?php
	}
}

/**
 * Get the client meta field input
 *
 * @param array $args The args.
 * @return void.
 */
function wpcloud_field_software_cb( array $args ): void {
	$options   = get_option( 'wpcloud_settings' );
	$label_for = esc_attr( $args['label_for'] );
	$items     = $args['items'];
	$disabled  = $args['disabled'] ?? false ? ' disabled ' : '';

	// output the field.
	echo '<table>';
	foreach ( $items as $item_value => $item_label ) {
		$name  = "wpcloud_settings[$label_for][$item_value]";
		$value = isset( $options['software'][ $item_value ] ) ? esc_attr( $options['software'][ $item_value ] ) : '';
		?>
		<tr>
			<td style="padding: 5px;">
				<label class="<?php echo esc_attr( $disabled ); ?>"><?php echo esc_html( $item_label ); ?></label>
			</td>
			<td style="padding: 5px;">
				<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>"
				<?php echo esc_attr( $disabled ); ?>
				>
					<option value=""></option>
					<option value="install" <?php echo ( 'install' === $value ) ? 'selected' : ''; ?>>Install</option>
					<option value="activate" <?php echo ( 'activate' === $value ) ? 'selected' : ''; ?>>Activate</option>
				</select>
			</td>
		</tr>
		<?php
	}
	echo '</table>';
	if ( isset( $args['description'] ) ) {
		?>
		<p class="description <?php echo esc_attr( $disabled ); ?>"><?php echo esc_html( $args['description'] ); ?></p>
		<?php
	}
}

/**
 * Get the client meta field input
 *
 * @param int $wpcloud_site_id The site id.
 * @return void.
 */
function site_created__success( int $wpcloud_site_id ): void {
	$wpcloud_site = get_post( $wpcloud_site_id );
	?>
<div class="notice notice-success is-dismissible">
	<p>
		<?php
		/* translators: %s: name of the site */
		printf( esc_html__( 'Provisioning  %s', 'wpcloud' ), esc_html( $wpcloud_site->post_title ) )
		?>
	</p>
</div>
	<?php
}

/**
 * Get the client meta field input
 *
 * @return void.
 */
function wpcloud_admin_controller(): void {
	// check user capabilities
	// @TODO make this a wpcloud capability...
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	wpcloud_admin_list_sites();
}

/**
 * Get the client meta field input
 *
 * @return void.
 */
function wpcloud_admin_list_sites(): void {
	do_action( 'admin_notices' );

	require_once plugin_dir_path( __FILE__ ) . '/includes/class-wpcloud-site-list.php';
	$wpcloud_site_list = new WPCLOUD_Site_List();
	$wpcloud_site_list->prepare_items();
	$confirm_delete_message = __( 'Are you sure you want to delete this site?', 'wpcloud' );
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<?php $wpcloud_site_list->display(); ?>
	</div>
	<script type="text/javascript" >
	(() => {
		document.querySelectorAll('.delete').forEach((el) => {
			el.addEventListener('click', (e) => {
				if (!confirm('<?php echo esc_js( $confirm_delete_message ); ?>')) {
					e.preventDefault();
				}
			});
		});
	})();
	</script>
	<?php
}

/**
 * Get the client meta field input
 *
 * @return void.
 */
function wpcloud_admin_options_controller(): void {
		// Check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
			return;
	}

	if ( isset( $_GET['settings-updated'] ) ) {
		// Add settings saved message with the class of "updated".
		add_settings_error( 'wpcloud_messages', 'wpcloud_message', __( 'Settings Saved', 'wpcloud' ), 'updated' );
	}

		settings_errors( 'wpcloud_messages' );
		require_once plugin_dir_path( __FILE__ ) . 'options.php';
}

// Allow SVG.
add_filter(
	'wp_check_filetype_and_ext',
	function ( $data, $file, $filename, $mimes ) {
		$filetype = wp_check_filetype( $filename, $mimes );

		return array(
			'ext'             => $filetype['ext'],
			'type'            => $filetype['type'],
			'proper_filename' => $data['proper_filename'],
		);
	},
	10,
	4
);

/**
 * Add SVG support
 *
 * @param array $mimes The mime types.
 * @return array The mime types.
 */
function cc_mime_types( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );


/**
 * Enqueue the admin styles
 *
 * @return void
 */
add_action(
	'admin_enqueue_scripts',
	function () {
		$config = require_once plugin_dir_path( __FILE__ ) . 'assets/js/build/index.asset.php';
		wp_enqueue_script( 'wpcloud-admin', plugin_dir_url( __FILE__ ) . 'assets/js/build/index.js', $config['dependencies'], $config['version'], true );
	}
);
