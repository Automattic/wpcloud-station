<?php
/**
 * Plugin Name:     WP Cloud Dashboard
 * Plugin URI:      https://github.com/Automattic/wpcloud-dashboard
 * Description:     Dashboard for managing your WP Cloud services.
 * Author:          Automattic Inc.
 * Author URI:      https://wp.cloud/
 * Text Domain:     wp-cloud-dashboard
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         WP_Cloud_Dashboard
 */

// Your code starts here.
add_action( 'admin_init', 'wpcloud_settings_init' );
function wpcloud_settings_init() {
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

function wpcloud_section_options_cb( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( '.', 'wpcloud' ); ?></p>
	<?php
}

function wpcloud_field_domain_cb( $args ) {

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

function wpcloud_field_input_cb( $args ) {

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


add_action( 'admin_menu', 'wpcloud_options_page' );
function wpcloud_options_page() {
    $hookname = add_menu_page(
        'WP Cloud',
        'WP Cloud',
        'manage_options',
        'wpcloud',
        'wpcloud_options_page_html',
        '',
        20
    );
		add_action( 'load-' . $hookname, 'wpcloud_options_page_submit' );
}

function wpcloud_options_page_html() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
				return;
		}

		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'wpcloud_messages', 'wpcloud_message', __( 'Settings Saved', 'wpcloud' ), 'updated' );
		}

		settings_errors( 'wpcloud_messages' );
		require_once  plugin_dir_path(__FILE__) . 'admin/options.php';
}

function wpcloud_options_page_submit() {
	return;
}