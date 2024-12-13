<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli-api.php';

/**
 * WP Cloud CLI
 */
class WPCloud_CLI {
	/**
	 * Log a message.
	 *
	 * @param string $message The message to log.
	 * @return void
	 */
	protected static function log( string $message ): void {
		WP_CLI::log( WP_CLI::colorize( $message . '%n' ) );
	}

	/**
	 * Log a response.
	 *
	 * @param mixed    $result The result to log.
	 * @param int|null $padding The padding for the log.
	 * @return mixed
	 */
	protected static function log_result( mixed $result, int|null $padding = null ): mixed {
		if ( empty( $result ) ) {
			return null;
		}

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
			return null;
		}

		$result = json_decode( wp_json_encode( $result ), true );

		if ( is_scalar( $result ) ) {
			if ( is_bool( $result ) ) {
				$result = $result ? '%gtrue' : '%rfalse';
			}
			self::log( $result );
			return null;
		}

		$padding = $padding ? $padding : ( max( array_map( 'strlen', array_keys( $result ) ) ) + 1 );

		foreach ( $result as $key => $value ) {

			if ( is_array( $value ) && ! array_is_list( $value ) ) {
				return self::log_result( $value, $padding );
			}

			if ( str_contains( $key, 'wpcom' ) || is_object( $value ) ) {
				continue;
			}

			if ( is_int( $key ) ) {
				$key = '-';
			} else {
				$key .= ':';
			}
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}
			self::log( sprintf( '%%_%s  %%G%s', str_pad( $key, $padding, ' ', STR_PAD_LEFT ), $value ) );
		}
		return null;
	}

	/**
	 * Log a result.
	 *
	 * @param mixed  $result  The result to log.
	 * @param string $message The message to log.
	 * @return void
	 */
	public static function log_success( mixed $result, string $message ): void {
		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
		}
		if ( $message ) {
			WP_CLI::success( $message );
		}
	}

	/**
	 * WP Cloud CLI Api
	 */
	public static function api(): WPCloud_CLI_Api {
		return new WPCloud_CLI_Api(
			logger: function ( $result, $message = '' ) {
				if ( $message ) {
					self::log_success( $result, $message );
					return;
				}
				self::log_result( $result );
			}
		);
	}
}
