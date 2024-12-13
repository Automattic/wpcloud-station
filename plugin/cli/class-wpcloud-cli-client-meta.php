<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli.php';

/**
 * WP Cloud CLI Client Meta
 */
class WPCloud_CLI_Client_Meta extends WPCloud_CLI {

	/**
	 * Get the client meta.
	 *
	 * @param array $args The arguments.
	 */
	public function get( $args ) {
		$key = $args[0] ?? '';
		if ( ! $key ) {
			WP_CLI::error( 'Please provide a key' );
		}
		$this->api()->get_client_meta( $key )->log();
	}

	/**
	 * Set the client meta.
	 *
	 * @param array $args The arguments.
	 */
	public function set( $args ) {
		$key = $args[0] ?? '';
		if ( ! $key ) {
			WP_CLI::error( 'Please provide a key' );
		}

		$value = $args[1] ?? '';
		if ( ! $value ) {
			WP_CLI::error( 'Please provide a value' );
		}
		$this->api()->set_client_meta( $key, $value )->log( success: 'OK' );
	}

	/**
	 * Remove the client meta.
	 *
	 * @param array $args The arguments.
	 */
	public function remove( $args ) {
		$key = $args[0] ?? '';
		if ( ! $key ) {
			WP_CLI::error( 'Please provide a key' );
		}

		$this->api()->remove_client_meta( $key )->log( success: 'OK' );
	}
}
