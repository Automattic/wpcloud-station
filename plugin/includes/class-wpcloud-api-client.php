<?php
/**
 * WP Cloud API client library.
 *
 * @package wpcloud-client
 *
 * $client->get('foobar' )->successeful;
 */

declare( strict_types = 1 );

require_once 'class-wpcloud-api-request.php';

/**
 * WP Cloud API client library.
 */
class WPCloud_API_Client {

	/**
	 * The cache time to live.
	 *
	 * @var int
	 */
	const CACHE_TTL = 60;

	/**
	 * The site id.
	 *
	 * @var int
	 */
	private int $site_id;

	/**
	 * The client name.
	 *
	 * @var string
	 */

	private string $client_name;

	/**
	 * The client key.
	 *
	 * @var string
	 */
	private string $api_key;

	/**
	 * The client secret.
	 *
	 * @var bool
	 */
	private bool $use_cache;

	/**
	 * The domain name.
	 *
	 * @var string
	 */
	private string $domain_name = '';

	/**
	 * The API request object.
	 *
	 * @var WPCloud_API_Request_Interface
	 */
	private mixed $api;

	/**
	 * Throw exception if API call fails.
	 *
	 * @var bool
	 */
	private bool $throw_exception = false;


	/**
	 * Constructor.
	 *
	 * @param string|int $site_id The site ID.
	 * @param bool       $use_cache Whether to use cache.
	 * @param bool       $throw_exception Whether to throw exception on error.
	 *
	 * @throws Exception If the API request object is invalid.
	 */
	public function __construct( string|int $site_id = 0, bool $use_cache = true, bool $throw_exception = false ) {
		$this->site_id         = $site_id;
		$settings              = get_option( 'wpcloud_settings', array() );
		$this->client_name     = apply_filters( 'wpcloud_client_name', $settings['wpcloud_client'] ?? '' );
		$this->api_key         = apply_filters( 'wpcloud_api_key', $settings['wpcloud_api_key'] ?? '' );
		$this->throw_exception = $throw_exception;

		$api_request = apply_filters( 'wpcloud_api_request_instance', new WPCloud_API_Request( $this->client_name, $this->api_key ) );
		if ( ! $api_request instanceof WPCloud_API_Request_Interface ) {
			throw new Exception( 'Invalid API request object' );
		}

		$this->api = $api_request;
		if ( ! $use_cache ) {
			$this->use_cache = false;
		} else {
			$this->use_cache = $settings['wpcloud_cache'] ?? false;
		}
	}


	/**
	 * Call the WP Cloud API.
	 *
	 * @param string     $endpoint  The endpoint.
	 * @param string|int ...$arguments The arguments.
	 * @return WPCloud_API_Request_Interface|WP_Error
	 */
	public static function call( string $endpoint, string|int ...$arguments ): WPCloud_API_Request_Interface|WP_Error {
		$client    = new self();
		$post_data = end( $arguments );
		$method    = 'get';
		if ( is_array( $post_data ) || is_object( $post_data ) ) {
			$arguments = array_slice( $arguments, 0, -1 );
			$method    = 'post';
		}

		$path = $client->parse_path( $endpoint, $arguments );

		// Make the API call.
		if ( 'get' === $method ) {
			return $client->api->call( $path );
		}
		return $client->api->call( $path, 'POST', (array) $post_data );
	}

	/**
	 * Set the site ID.
	 *
	 * @param string|int $site_id The site ID.
	 * @return self
	 */
	public function set_site_id( string|int $site_id ): self {
		$this->site_id = (int) $site_id;
		return $this;
	}

	/**
	 * Set the domain name
	 *
	 * @param string $domain_name The domain name.
	 * @return self
	 */
	public function set_domain_name( string $domain_name ): self {
		$this->domain_name = $domain_name;
		return $this;
	}

	/**
	 * Make GET request to WP Cloud API.
	 *
	 * @param string     $endpoint  The endpoint.
	 * @param string|int ...$arguments The arguments.
	 *
	 * @return WPCloud_API_Request_Interface
	 * @throws Exception If the API call fails and throw_exception is set to true.
	 */
	public function get( string $endpoint, string|int ...$arguments ): WPCloud_API_Request_Interface {
		$path = $this->parse_path( $endpoint, $arguments );

		if ( ! $this->use_cache ) {
			$response = $this->api->call( $path );
			return $response;
		}
		$cache_key = md5( $path );
		$response  = get_transient( $cache_key );
		if ( false === $response ) {
			$response = $this->api->call( $path );
			if ( $response->is_ok() ) {
				set_transient( $cache_key, $response, self::CACHE_TTL );
			} elseif ( $this->throw_exception ) {
				throw new Exception( $response->error ); // phpcs:ignore
			}
		}
		return $response;
	}

	/**
	 * Make POST request to WP Cloud API.
	 *
	 * @param string $endpoint The endpoint.
	 * @param mixed  ...$arguments The arguments.
	 *
	 * @return WPCloud_API_Request_Interface
	 * @throws Exception If the API call fails and throw_exception is set to true.
	 */
	public function post( string $endpoint, mixed ...$arguments ): WPCloud_API_Request_Interface {
		$data = array();
		if ( $this->is_post( $arguments ) ) {
			$data      = end( $arguments );
			$arguments = array_slice( $arguments, 0, -1 );
		}
		$path     = $this->parse_path( $endpoint, $arguments );
		$response = $this->api->call( $path, 'POST', $data );
		if ( $response->not_ok() && $this->throw_exception ) {
			throw new Exception( $response->error ); // phpcs:ignore
		}
		return $response;
	}

	/**
	 * Replace placeholders in path.
	 *
	 * @param string $endpoint The path.
	 * @param array  $arguments The arguments.
	 * @return string
	 */
	private function parse_path( string $endpoint, array $arguments ): string {

		$endpoint = trailingslashit( $endpoint );
		if ( ! empty( $arguments ) ) {
			$endpoint .= implode( '/', $arguments );
		}
		$replacements = array(
			':client'  => $this->client_name,
			':site_id' => $this->site_id,
			':domain'  => $this->domain_name,
		);

		$parsed = str_replace( array_keys( $replacements ), array_values( $replacements ), $endpoint );
		return rtrim( $parsed, '/' );
	}

	/**
	 * Check if the request is a POST request.
	 *
	 * @param array $arguments The arguments.
	 * @return bool
	 */
	private function is_post( array $arguments ): bool {
		$data = end( $arguments );
		return $data && ( is_array( $data ) || is_object( $data ) || 'POST' === strtoupper( $data ) );
	}
}
