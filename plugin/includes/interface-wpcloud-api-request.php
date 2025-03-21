<?php
/**
 * WP Cloud API request interface file.
 *
 * This file contains the interface definition for WP Cloud API requests.
 *
 * @package wpcloud-client
 */

/**
 * WP Cloud API request interface.
 */
interface WPCloud_API_Request_Interface {

	/**
	 * Call the API.
	 *
	 * @param string $path   The path to the API endpoint.
	 * @param string $method The HTTP method to use.
	 * @param array  $body   The data to send to the API.
	 * @return mixed|WP_Error The response from the API or a WP_Error object.
	 */
	public function call( string $path, string $method = 'GET', array $body = array() ): array|stdClass|WP_Error;

	/**
	 * Check if the request was successful.
	 *
	 * @return bool
	 */
	public function is_ok(): bool;


	/**
	 * Get a property from the result.
	 *
	 * @param string $name The property name.
	 * @return mixed The property value.
	 */
	public function __get( string $name ): mixed;
}
