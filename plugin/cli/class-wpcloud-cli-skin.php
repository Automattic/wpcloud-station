<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/class-theme-upgrader.php';

/**
 * WP Cloud CLI Skin
 */
class WPCloud_CLI_Skin extends WP_Upgrader_Skin {

	/**
	 * Feedback.
	 *
	 * @param string $str The string to log.
	 * @param mixed  ...$args The arguments.
	 * @return string
	 */
	public function feedback( $str, ...$args ) {
		WP_CLI::log( $str );
		return '';
	}

	/**
	 * Header.
	 *
	 * @return string
	 */
	public function header() {
		// Silence is golden.
		return '';
	}

	/**
	 * Footer.
	 *
	 * @return string
	 */
	public function footer() {
		// Silence is golden.
		return '';
	}
}
