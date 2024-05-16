<?php

/**
 * WP Cloud Station Head start.
 *
 * @package wpcloud-station
 */

 declare( strict_types = 1 );

if ( ! defined( 'WP_CLOUD_STATION_REPO' ) ) {
	define( 'WP_CLOUD_STATION_REPO', 'https://github.com/automattic/wpcloud-station' );
}

require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/class-theme-upgrader.php';

class WPCloud_Quiet_Skin extends WP_Upgrader_Skin {
	public function feedback($string, ...$args)
	{
		// Silence is golden.
		return '';
	}
	public function header()
	{
		// Silence is golden.
		return '';
	}

	public function footer()
	{
		// Silence is golden.
		return '';
	}
}

class WPCloud_Debug_Skin extends WPCloud_Quiet_Skin {
	public function feedback($string, ...$args)
	{
		error_log($string);
	}
}

/**
 * Headstart the WP Cloud Station.
 *
 * @param string $client The client name.
 * @param string $key The API key.
 * @param bool $force Force the headstart.
 * @param WP_Upgrader_Skin $headstartSkin The skin to use for the headstart.
 * @return bool True if the headstart was successful.
 */
function wpcloud_headstart(string $client='', string $key='', bool $force = false, WP_Upgrader_Skin $headstartSkin = new WPCloud_Quiet_Skin()): bool {
	$wpcloud_settings = get_option( 'wpcloud_settings' ) ?: array();

	// Check if the client is already setup. If so bail out unless forced to continue.
	if ( ! $force && ( isset( $wpcloud_settings[ 'wpcloud_client'] ) || isset( $wpcloud_settings[ 'wpcloud_api_key'] ) ) ) {
		$headstartSkin->feedback( 'Headstart already installed.' );
		return false;
	}

	// Update the client and key if they are provided.
	if ( $client && $key ) {
		$wpcloud_settings[ 'wpcloud_client' ] = $client;
		$wpcloud_settings[ 'wpcloud_api_key' ] = $key;

		update_option( 'wpcloud_settings', $wpcloud_settings );
		$headstartSkin->feedback( 'Client and key updated.' );
	}

	// Install the theme and activate it.
	$headstartSkin->feedback('Installing wpcloud-station-theme');
	$package =  WP_CLOUD_STATION_REPO . '/releases/latest/download/wpcloud-station-theme.zip ';
	$up_grader = new Theme_Upgrader( $headstartSkin );
	$installed = $up_grader->install( $package );
	if ( is_wp_error( $installed ) ) {
		$headstartSkin->feedback( $installed->get_error_message() );
	}

	if ( $installed ) {
		switch_theme( 'wpcloud-station-theme' );
	} else {
		$headstartSkin->feedback( 'Failed to install theme.' );
	}

	// Configure permalinks

	global $wp_rewrite;

	$permalink_structure = '/%postname%/';
	$wp_rewrite->set_permalink_structure($permalink_structure);
	$wp_rewrite->flush_rules(true);
	//@TODO warn if running in the CLI and apache mod_rewrite is not enabled.

	// Create the core pages
	$wpcloud_private_cat = get_category_by_slug( WPCLOUD_PRIVATE_CATEGORY );
	if ( ! $wpcloud_private_cat ) {
		$wpcloud_private_cat = wp_insert_term( 'WP Cloud Private Page', 'category', array(
			'description' => 'Private category for WP Cloud specific pages.',
			'slug' => WPCLOUD_PRIVATE_CATEGORY,
		) );
	}

	// 1. Login page
	$login_content = <<<LOGIN
<!-- wp:wpcloud/login -->
<div class="wpcloud-login-form wp-block-wpcloud-login"><!-- wp:wpcloud/form {"ajax":true,"wpcloudAction":"login","redirect":"/sites"} -->
<form class="wp-block-wpcloud-form wpcloud-block-form" enctype="text/plain"><!-- wp:wpcloud/form-input {"name":"log"} -->
<div class="wp-block-wpcloud-form-input"><label class="wpcloud-block-form-input__label"><span class="wpcloud-block-form-input__label-content">Username or Email Address</span><input class="wpcloud-block-form-input__input" type="text" name="log" required aria-required="true"/></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"password","name":"pwd"} -->
<div class="wp-block-wpcloud-form-input"><label class="wpcloud-block-form-input__label"><span class="wpcloud-block-form-input__label-content">Password</span><input class="wpcloud-block-form-input__input" type="password" name="pwd" required aria-required="true"/></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"checkbox","name":"rememberme"} -->
<div class="wp-block-wpcloud-form-input"><label class="wpcloud-block-form-input__label"><span class="wpcloud-block-form-input__label-content">Remember Me</span><input class="wpcloud-block-form-input__input" type="checkbox" name="rememberme" aria-required="false"/></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/button {"text":"Login In"} -->
<div class="wp-block-wpcloud-button"><!-- wp:button {"tagName":"button","type":"submit"} -->
<div class="wp-block-button"><button type="submit" class="wp-block-button__link wp-element-button">Login In</button></div>
<!-- /wp:button --></div>
<!-- /wp:wpcloud/button --></form>
<!-- /wp:wpcloud/form --></div>
<!-- /wp:wpcloud/login -->
LOGIN;
	$login_page = array(
		'post_title' => 'Login',
		'post_content' => $login_content,
		'post_status' => 'publish',
		'post_type' => 'page',
	);
	$login_page_id = wp_insert_post( $login_page );
	if (is_wp_error( $login_page_id )) {
		$headstartSkin->feedback( $login_page_id->get_error_message() );
	} else {
		$headstartSkin->feedback( 'Login page created.' );
	}

	// 2. Add Site page

	$add_site_content = <<<ADD_SITE
<!-- wp:wpcloud/site-create -->
<div class="wpcloud-new-site-form wp-block-wpcloud-site-create"><!-- wp:wpcloud/form {"ajax":true,"wpcloudAction":"site_create"} -->
<form class="wp-block-wpcloud-form wpcloud-block-form" enctype="text/plain"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Add Site</h3>
<!-- /wp:heading -->

<!-- wp:wpcloud/form-input {"name":"site_name"} -->
<div class="wp-block-wpcloud-form-input"><label class="wpcloud-block-form-input__label"><span class="wpcloud-block-form-input__label-content">Name</span><input class="wpcloud-block-form-input__input" type="text" name="site_name" required aria-required="true" placeholder="Enter site name"/></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"select","name":"php_version","options":[]} -->
<div class="wp-block-wpcloud-form-input"><label class="wpcloud-block-form-input__label"><span class="wpcloud-block-form-input__label-content">PHP Version</span><select class="wpcloud-station-form-input__select wpcloud-block-form-input__input" name="php_version" required aria-required="true"></select></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"select","name":"data_center","options":[{"value":"No Preference","label":"No Preference"}]} -->
<div class="wp-block-wpcloud-form-input"><label class="wpcloud-block-form-input__label"><span class="wpcloud-block-form-input__label-content">Data Center</span><select class="wpcloud-station-form-input__select wpcloud-block-form-input__input" name="data_center" required aria-required="true"><option value="No Preference">No Preference</option></select></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"password","name":"admin_pass"} -->
<div class="wp-block-wpcloud-form-input"><label class="wpcloud-block-form-input__label"><span class="wpcloud-block-form-input__label-content">Admin Password</span><input class="wpcloud-block-form-input__input" type="password" name="admin_pass" aria-required="false"/></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"select","name":"site_owner_id","adminOnly":true,"options":[{"value":"1","label":"Site Owner"}]} -->
<div class="wp-block-wpcloud-form-input"><label class="wpcloud-block-form-input__label"><span class="wpcloud-block-form-input__label-content">Owner</span><select class="wpcloud-station-form-input__select wpcloud-block-form-input__input" name="site_owner_id" aria-required="false"><option value="1">Site Owner</option></select></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/button {"text":"Create Site"} -->
<div class="wp-block-wpcloud-button"><!-- wp:button {"tagName":"button","type":"submit"} -->
<div class="wp-block-button"><button type="submit" class="wp-block-button__link wp-element-button">Create Site</button></div>
<!-- /wp:button --></div>
<!-- /wp:wpcloud/button --></form>
<!-- /wp:wpcloud/form --></div>
<!-- /wp:wpcloud/site-create -->
ADD_SITE;

	$add_site_page = array(
		'post_title' => 'Add Site',
		'post_content' => $add_site_content,
		'post_status' => 'publish',
		'post_type' => 'page',
		'post_category' => array( $wpcloud_private_cat->term_id ),
	);
	$add_site_page_id = wp_insert_post( $add_site_page );
	if (is_wp_error( $add_site_page_id )) {
		$headstartSkin->feedback( $add_site_page_id->get_error_message() );
	} else {
		$headstartSkin->feedback( 'Add Site page created.' );
	}

	$intro_content = <<<INTRO
<!-- wp:heading -->
<h2 class="wp-block-heading">Welcome to WP Cloud Station.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Some other intro text....</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Go to<a href="http://localhost:8888/?page_id=5" data-type="page" data-id="5"> Add Site</a> to get started.</p>
<!-- /wp:paragraph -->
INTRO;
	// 3. Add Intro page
	$intro_page = array(
		'post_title' => 'Intro',
		'post_content' => $intro_content,
		'post_status' => 'publish',
		'post_type' => 'page',
	);

	$intro_page_id = wp_insert_post( $intro_page );
	if (is_wp_error( $intro_page_id )) {
		$headstartSkin->feedback( $intro_page_id->get_error_message() );
	} else {
		update_option( 'page_on_front', $intro_page_id );
		update_option( 'show_on_front', 'page' );
		$headstartSkin->feedback( 'Intro page created and set to homepage.' );
	}

	$headstartSkin->feedback( 'Headstart complete.' );
	return true;
}

// Add the demo domain if we are adding the api creds for the first time.
add_filter( 'pre_update_option_wpcloud_settings', function( $value, $old_value ) {
	// If we adding api creds for the first time, add the demo domain.
	$has_api_creds = isset( $old_value['wpcloud_client'] ) && isset( $old_value['wpcloud_api_key'] );
	if ( ! $has_api_creds && isset( $value['wpcloud_client'] ) && isset( $value['wpcloud_api_key'] ) ) {
		$value['wpcloud_domain'] = $value['wpcloud_domain'] ?? WPCLOUD_DEMO_DOMAIN;
	}
	return $value;
}, 10, 2);


// Run the headstart if we are adding the settings for the first time and the headstart is requested.
add_action('add_option', function( $option, $value ) {
		if ( "wpcloud_settings" !== $option ) {
			return;
		}
		$should_do_headstart = $value['wpcloud_headstart'] ?? false;
		if ( $should_do_headstart ) {
			wpcloud_headstart( '','', true, new WPCloud_Debug_Skin() );
		}
}, 10, 3);