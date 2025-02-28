<?php
/**
 * Mock class for Atomic_Persistent_Data
 *
 * Copy this file to 0_atomic.php in the hosting folder if you want to mock the Atomic_Persistent_Data class.
 */
class Atomic_Persistent_Data {
	/**
	 * Get the persistent data.
	 *
	 * @param string $key The key.
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'WP_CLOUD_CLIENT_NAME':
				return '';
			case 'WP_CLOUD_API_KEY':
				return '';
		}
		return '';
	}
}
