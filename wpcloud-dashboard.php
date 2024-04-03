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

 const WP_CLOUD_PHP_VERSIONS = [
	'7.4' => '7.4',
	'8.1' => '8.1',
	'8.2' => '8.2',
	'8.3' => '8.3',
];

const WP_CLOUD_DATA_CENTERS = [
	'NA' => 'No Preference',
	'AMS' => 'Amsterdam, NL',
	'BUR' => 'Los Angeles, CA',
	'DCA' => 'Washington, D.C., USA',
	'DFW' => 'Dallas, TX, USA',
];

const WPCLOUD_ACTION_CREATE_SITE = 'wpcloud_create_site';
const WPCLOUD_ACTION_UPDATE_SITE = 'wpcloud_update_site';


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

function site_created__success() {
?>
<div class="notice notice-success is-dismissible">
<p><?php _e( 'Provisioning a new Site', 'sample-text-domain' ); ?></p>
</div>
<?php
}

function wpcloud_site_list_cb() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['wpcloud_site'] ) ) {
		check_admin_referer( 'wpcloud-edit-site' );
		$wpcloud_site = $_POST['wpcloud_site'];
		$wpcloud_site['user_id'] = get_current_user_id();
		if ( $wpcloud_site['id'] !== '' ) {
			do_action( WPCLOUD_ACTION_UPDATE_SITE, $wpcloud_site);
		}
		else {
			$site_id = wp_insert_post( array(
				'post_type' => 'wpcloud_site',
				'post_title' => $wpcloud_site['site_name'],
				'post_status' => 'draft',
				'comment_status'=> 'closed',
			) );
			add_action( 'admin_notices', 'site_created__success' );
			do_action( WPCLOUD_ACTION_CREATE_SITE, $wpcloud_site);
		}
	}
	require_once	plugin_dir_path(__FILE__) . 'admin/list-sites.php';
}

function wpcloud_admin_new_site_cb() {
	// check user capabilities - need to add a capability for this 'wpcloud_add_site' or something
	if ( ! current_user_can( 'manage_options' ) ) {
			return;
	}

	$wpcloud_site = array(
		'id' => '',
		'site_name' => '',
		'php_version' => '',
		'datacenter' => '',
	);

	require_once  plugin_dir_path(__FILE__) . 'admin/edit-site.php';
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

function wpcloud_site_post_type() {
	register_post_type( 'wpcloud_site',
		array(
			'labels'	=> array(
				'name'			=> __( 'Sites', 'wpcloud' ),
				'singular_name'	=> __( 'Site', 'wpcloud' ),
				'add_new' => 'Add New Site',
			),
			'public'	=> true,
			'has_archive' => true,
			'show_in_rest' => true,
			'show_in_ ui' => false,
			'show_in_menu' => false,
			'capabilities' => array(
				'wpcloud_add_site' => true,
			),

		)
	);
}
add_action( 'init', 'wpcloud_site_post_type' );