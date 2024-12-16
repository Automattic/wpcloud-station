<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli.php';
require_once __DIR__ . '/../includes/class-wpcloud-metrics.php';

/**
 * WP Cloud CLI Site SSH User
 */
class WPCloud_CLI_Metrics extends WPCloud_CLI {

	/**
	 * Get status codes
	 *
	 * ## OPTIONS
	 *
	 * <site-id>
	 * : The site id.
	 *
	 *
	 * [--format=<format>]
	 * : The output format. Defaults to 'raw'. Options are 'json'.
	 *
	 * [--rollup=<rollup>]
	 * : The rollup period in seconds. Defaults to 1 minute.
	 *
	 * [--start=<start>]
	 * : The start time in hours. Defaults to 1 hour ago.
	 *
	 * [--end=<end>]
	 * : The end time in hours. Defaults to now.
	 *
	 * [--unit=<unit>]
	 * : The unit of time. Defaults to 'hour'. Options are 'minute', 'hour', 'day'.
	 *
	 * @param array $args The arguments.
	 * @param array $options The options.
	 */
	public function status_codes( array $args, array $options ): void {
		$metrics = $this->get_metrics( $args, $options );
		$data    = $metrics->status_codes();
		$this->print_logs( $data, $options['format'] ?? 'raw' );
	}

	/**
	 * Get logs
	 *
	 * ## OPTIONS
	 *
	 * <site-id>
	 * : The site id.
	 *
	 * <type>
	 * : The type of logs. Options are 'server' and 'error'.
	 *
	 * [--format=<format>]
	 * : The output format. Defaults to 'raw'. Options are 'json'.
	 *
	 * [--start=<start>]
	 * : The start time in hours. Defaults to 1 hour ago.
	 *
	 * [--end=<end>]
	 * : The end time in hours. Defaults to now.
	 *
	 * [--unit=<unit>]
	 * : The unit of time. Defaults to 'hour'. Options are 'minute', 'hour', 'day'.
	 *
	 * @param array $args The arguments.
	 * @param array $options The options.
	 */
	public function logs( array $args, array $options ): void {
		$metrics = $this->get_metrics( $args, $options );
		$data    = $metrics->response();
		$this->print_logs( $data, $options['format'] ?? 'raw' );
	}

	/**
	 * Get metrics
	 *
	 * @param array  $data   The data.
	 * @param string $format The format.
	 */
	private function print_logs( array $data, string $format ) {
		switch ( $format ) {
			case 'json':
				WP_CLI::line( wp_json_encode( $data ) );
				break;
			default:
				print_r( $data ); // phpcs:ignore
		}
	}


	/**
	 * Get metrics
	 *
	 * @param array $args The arguments.
	 * @param array $options The options.
	 *
	 * @return WP_Error|WPCloud_Metrics The metrics.
	 */
	private function get_metrics( array $args, array $options ): WP_Error|WPCloud_Metrics {
		$site_id = WPCloud_CLI_Site::get_site_id( $this->api(), $args[0] );
		$type    = $args[1] ?? 'server';

		$unit = $options['unit'] ?? 'hour';

		$start = $options['start'] ?? null;
		if ( $start ) {
			$start_date = sprintf( '-%s %ss', $start, $unit );
			$start      = strtotime( $start_date );
		}

		$end = $options['end'] ?? null;
		if ( $end ) {
			$end_date = sprintf( '-%s %ss', $end, $unit );
			$end      = strtotime( $end_date );
		}

		try {
			$metrics = new WPCloud_Metrics( site: $site_id, type: $type, start: $start, end: $end );
		} catch ( Exception $e ) {
				WP_CLI::error( $e );
				die( 1 );
		}
		return $metrics;
	}
}
