<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/../includes/wpcloud-client.php';

/**
 * WP Cloud CLI Api
 */
class WPCLOUD_CLI_Api {

	/**
	 * Call the API.
	 *
	 * ## OPTIONS
	 *
	 * <endpoint>...
	 * : The API endpoint to call.
	 *
	 * ## EXAMPLES
	 *
	 *     wp cloud api client-meta/:client/a-key/get
	 *
	 * @param array $args The command arguments.
	 * @param array $assoc_args The command options.
	 * @return void
	 */
	public function __invoke( $args, $assoc_args ) {
		$endpoint = implode( '/', $args );
		$site_id  = $assoc_args['site'] ?? 0;
		unset( $assoc_args['site'] );
		$client = new WPCloud_API_Client( $site_id );

		$post      = $assoc_args['post'] ?? null;
		$post_data = array();
		unset( $assoc_args['post'] );
		if ( $post ) {
			$post_data = json_decode( $post, true );
			if ( ! $post_data ) {
				$post_data = array();
			}
		}
		if ( ! empty( $assoc_args ) || $post ) {
			$post_data = array_merge( $post_data, $assoc_args );

			$response = $client->post( $endpoint, $post_data );
		} else {
			$response = $client->get( $endpoint );
		}

		if ( is_wp_error( $response ) ) {
			WP_CLI::error( $response->get_error_message() );
		} else {
			WP_CLI::line( wp_json_encode( $response->data, JSON_PRETTY_PRINT ) );
		}
	}
}
