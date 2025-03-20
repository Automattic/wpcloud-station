<?php
/**
 * WP Cloud API client library.
 *
 * @package wpcloud-client
 */

declare( strict_types = 1 );

require_once 'interface-wpcloud-api-request.php';

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
	 * Constructor.
	 *
	 * @param string|int                         $site_id The site ID.
	 * @param bool                               $use_cache Whether to use cache.
	 * @param WPCloud_API_Request_Interface|null $api_request The API request object.
	 */
	public function __construct( string|int $site_id = 0, bool $use_cache = true, mixed $api_request = null ) {
		$this->site_id     = $site_id;
		$settings          = get_option( 'wpcloud_settings', array() );
		$this->client_name = apply_filters( 'wpcloud_client_name', $settings['wpcloud_client'] ?? '' );
		$this->api_key     = apply_filters( 'wpcloud_api_key', $settings['wpcloud_api_key'] ?? '' );

		if ( is_null( $api_request ) ) {
			$api_request = apply_filters( 'wpcloud_api_request', new WPCloud_API_Request( $this->client_name, $this->api_key ) );
		}

		$this->api = $api_request;
		if ( ! $use_cache ) {
			$this->use_cache = false;
		} else {
			$this->use_cache = $settings['wpcloud_cache'] ?? false;
		}
	}

	/**
	 * Get an instance of the WP Cloud API client.
	 *
	 * @param string|int $site_id The site ID.
	 * @param bool       $use_cache Whether to use cache.
	 * @return WPCloud_API_Client
	 */
	public static function init( string|int $site_id = 0, bool $use_cache = true ): WPCloud_API_Client {
		return new self( $site_id, $use_cache );
	}

	/**
	 * Call the WP Cloud API.
	 *
	 * @param string     $endpoint  The endpoint.
	 * @param string|int ...$arguments The arguments.
	 * @return array|stdClass|WP_Error
	 */
	public static function call( string $endpoint, string|int ...$arguments ): array|stdClass|WP_Error {
		$client    = new self();
		$post_data = end( $arguments );
		$method    = 'get';
		if ( is_array( $post_data ) || is_object( $post_data ) ) {
			$arguments = array_slice( $arguments, 0, -1 );
			$method    = 'post';
		}

		if ( 'get' === $method ) {
			return $client->get( $endpoint, ...$arguments );
		}
		$path = $client->parse_path( $endpoint, $arguments );
		return $client->post( $path, (array) $post_data );
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
	 * @return stdClass|WP_Error
	 */
	public function get( string $endpoint, string|int ...$arguments ): array|stdClass|WP_Error {
		$path = $this->parse_path( $endpoint, $arguments );
		if ( ! $this->use_cache ) {
			return $this->api->call( $path );
		}
		$cache_key = md5( $path );
		$cache     = get_transient( $cache_key );
		if ( false === $cache ) {
			$cache = $this->api->call( $path );
			if ( ! is_wp_error( $cache ) ) {
				set_transient( $cache_key, $cache, self::CACHE_TTL );
			}
		}
		return $cache;
	}

	/**
	 * Validate if GET request returns a non-error response.
	 *
	 * @param string     $endpoint The endpoint.
	 * @param string|int ...$arguments The arguments.
	 * @return bool
	 */
	public function validate( string $endpoint, string|int ...$arguments ): bool {
		$result = $this->get( $endpoint, ...$arguments );
		if ( is_wp_error( $result ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Make POST request to WP Cloud API.
	 *
	 * @param string $endpoint The endpoint.
	 * @param mixed  ...$arguments The arguments.
	 * @return stdClass|WP_Error
	 */
	public function post( string $endpoint, mixed ...$arguments ): array|stdClass|WP_Error {
		$data = array();
		if ( $this->is_post( $arguments ) ) {
			$data      = end( $arguments );
			$arguments = array_slice( $arguments, 0, -1 );
		}
		$path = $this->parse_path( $endpoint, $arguments );
		return $this->api->call( $path, 'POST', $data );
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
		$id_or_domain = $this->site_id ?? $this->domain_name;
		$replacements = array(
			':id_or_domain' => $id_or_domain,
			':id'           => $this->site_id,
			':domain'       => $this->domain_name,
			':client'       => $this->client_name,
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
