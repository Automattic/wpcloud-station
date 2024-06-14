<?php
/**
 * WP Cloud Cache
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

// Global cache for site details.
$_WPCLOUD_client_cache = new stdClass();


/**
 * Read through cache.
 *
 * @param int $wpcloud_site_id The site ID.
 * @param callable $call_client The client function to call if the cache is empty.
 * @param string $key The key to get from the cache.
 * @return WP_Error|array The result of the client function or WP_Error if the client function fails.
 */
function wpcloud_cache_get_through(int $wpcloud_site_id, callable $call_client, $key =''): mixed {

	$is_enabled = get_option( 'wpcloud_settings',[] )[ 'client_cache' ] ?? false;
	if ( ! $is_enabled ) {
		return $call_client();
	}
	global $_WPCLOUD_client_cache;
	$cached = $_WPCLOUD_client_cache->$wpcloud_site_id ?? null;

	$cache_hit = $cached && ( ! $key || ( $key && isset( $cached?->$key ) ) );

	if ( $cache_hit ) {
		return  $key ? $cached->$key : $cached;
	}

	$cache_item = $call_client();
	if ( is_wp_error( $cache_item ) ) {
		return $cache_item;
	}

	// If we are not looking for a key we can cache the whole result and return it.
	if ( ! $key ) {
		$_WPCLOUD_client_cache->$wpcloud_site_id  = $cache_item;
		return $cache_item;
	}

	// If the cache was completely empty we need to create the object to cache the key.
	if ( is_null( $cached ) ) {
		$_WPCLOUD_client_cache->$wpcloud_site_id = new stdClass();
	}

	$_WPCLOUD_client_cache->$wpcloud_site_id->$key = $cache_item;

	return $cache_item;
}


/**
 * Get the site details for a site.
 *
 * @param int $wpcloud_site_id The site ID.
 * @return object

 */
function wpcloud_cached_site_details(int $wpcloud_site_id): stdClass | WP_Error {
	return wpcloud_cache_get_through( $wpcloud_site_id, fn() => wpcloud_client_site_details( $wpcloud_site_id, true ) );
}

function wpcloud_cached_site_ip_addresses(int $wpcloud_site_id, string $domain = ''): stdClass | WP_Error {
	return wpcloud_cache_get_through( $wpcloud_site_id, fn() => wpcloud_client_site_ip_addresses( $domain ), 'ip_addresses' );
}

function wpcloud_cached_get_site_meta($wpcloud_site_id, $meta_key): stdClass | WP_Error  {
	return wpcloud_cache_get_through( $wpcloud_site_id, fn() => wpcloud_client_get_site_meta( $wpcloud_site_id, $meta_key ), $meta_key );
}

function wpcloud_cached_php_versions_available(): stdClass | WP_Error  {
	return wpcloud_cache_get_through( 0, fn() => wpcloud_client_php_versions_available( true ), 'php_versions' );
}

function wpcloud_cached_data_centers_available( $include_no_preference = true ): stdClass| WP_Error {
	return wpcloud_cache_get_through( 0, fn() => wpcloud_client_data_centers_available( $include_no_preference ), 'data_centers' );
}
