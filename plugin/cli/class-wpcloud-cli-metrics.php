<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli.php';
require_once __DIR__ . '/../includes/class-wpcloud-metric-data-view.php';

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
	 * [--start=<start>]
	 * : The start time in <units>. Defaults to 1 hour ago.
	 *
	 * [--end=<end>]
	 * : The end time in hours. Defaults to now.
	 *
	 * [--unit=<unit>]
	 * : The unit of time. Defaults to 'hour'. Options are 'minute', 'hour', 'day'.
	 *
	 * [--average]
	 * : Get the average of the metric ( only for bytes, time is average by default )

	 *
	 * [--format=<format>]
	 * : The output format. Defaults to 'raw'. Options are 'json'.
	 *
	 * @param array $args The arguments.
	 * @param array $options The options.
	 */
	public function status_codes( array $args, array $options ) {
		$site_id = $args[0];

		$view   = $this->init_view( $site_id, $options );
		$format = $options['format'] ?? null;

		$view->status_codes( plot_view: 'plot' === $format );
		if ( $options['meta'] ?? false ) {
			$format = 'meta';
		}

		$this->print_data( $view, $format );
	}

	/**
	 * Get response metrics
	 *
	 * ## OPTIONS
	 *
	 *
	 * <type>
	 * : The type of response metric. `bytes` or `time` (time is in average)
	 *
	 * <site-id>
	 * : The site id.
	 *
	 * [--start=<start>]
	 * : The start time in <units>. Defaults to 1 hour ago.
	 *
	 * [--end=<end>]
	 * : The end time in hours. Defaults to now.
	 *
	 * [--unit=<unit>]
	 * : The unit of time. Defaults to 'hour'. Options are 'minute', 'hour', 'day'.
	 *
	 * [--average]
	 * : Get the average of the metric ( only for bytes, time is average by default )
	 *
	 * [--dimension=<dimension>]
	 * : The dimension of the metric. Defaults to http_post, Options are http_version, http_verb, http_host, http_status, page_renderer, page_is_cached, wp_admin_ajax_action, visitor_asn, visitor_country_code, visitor_is_crawler
	 *
	 * [--summarize]
	 * : Summarize the data.
	 *
	 * [--meta]
	 * : Just show the meta value of the metric.
	 *
	 * [--format=<format>]
	 * : The output format. Defaults to 'raw'. Options are 'json'.
	 *
	 * @param array $args The arguments.
	 * @param array $options The options.
	 */
	public function response( array $args, array $options ) {
		$type    = $args[0];
		$site_id = $args[1];

		$view = $this->init_view( $site_id, $options );

		$dimension = $options['dimension'] ?? null;
		$summarize = $options['summarize'] ?? false;
		$meta      = $options['meta'] ?? false;

		$data = array();
		switch ( $type ) {
			case 'bytes':
				$average = $options['average'] ?? false;
				$view->response_bytes( $dimension, $summarize, $average );
				break;
			case 'time':
				$view->response_time( $dimension, $summarize );
				break;
			default:
				WP_CLI::error( 'Invalid metric type' );
				die( 1 );
		}

		$format = $options['format'] ?? 'raw';
		if ( $meta ) {
			$format = 'meta';
		}

		$this->print_data( $view, $format );
	}

	/**
	 * Get requests
	 *
	 * ## OPTIONS
	 *
	 * <site-id>
	 * : The site id.
	 *
	 * [--start=<start>]
	 * : The start time in <units>. Defaults to 1 hour ago.
	 *
	 * [--end=<end>]
	 * : The end time in hours. Defaults to now.
	 *
	 * [--unit=<unit>]
	 * : The unit of time. Defaults to 'hour'. Options are 'minute', 'hour', 'day'.
	 *
	 * [--dimension=<dimension>]
	 * : The dimension of the metric. Defaults to http_post, Options are http_version, http_verb, http_host, http_status, page_renderer, page_is_cached, wp_admin_ajax_action, visitor_asn, visitor_country_code, visitor_is_crawler
	 *
	 * [--summarize]
	 * : Summarize the data.
	 *
	 * [--meta]
	 * : Just show the meta value of the metric.
	 *
	 * [--format=<format>]
	 * : The output format. Defaults to 'raw'. Options are 'json'.
	 *
	 * @param array $args The arguments.
	 * @param array $options The options.
	 *
	 * @return void
	 */
	public function requests( array $args, array $options ) {
		$site_id = $args[0];

		$view = $this->init_view( $site_id, $options );

		$dimension = $options['dimension'] ?? null;
		$summarize = $options['summarize'] ?? false;
		$meta      = $options['meta'] ?? false;

		$view->requests( $dimension, $summarize );

		$format = $options['format'] ?? 'raw';
		if ( $meta ) {
			$format = 'meta';
		}

		$this->print_data( $view, $format );
	}

	/**
	 * Print data
	 *
	 * @param WPCLOUD_Metric_Data_View $view   The metric data view.
	 * @param string                   $format The format.
	 */
	private function print_data( WPCLOUD_Metric_Data_View $view, ?string $format ) {
		if ( is_wp_error( $view->result ) ) {
			WP_CLI::error( $view->result->get_error_message() );
			die( 1 );
		}
		switch ( $format ) {
			case 'meta':
				WP_CLI::line( wp_json_encode( $view->meta ) );
				break;
			case 'json':
				WP_CLI::line( wp_json_encode( $view->periods ) );
				break;
			case 'plot':
				WP_CLI::line( wp_json_encode( $view->data ) );
				break;
			default:
				print_r( $view->result ); // phpcs:ignore
		}
	}

	/**
	 * Get metrics
	 *
	 * @param string|int $site_id The site identifier, accepts name or id.
	 * @param array      $options The options.
	 *
	 * @return WPCloud_Metric_Data_View The metrics.
	 */
	private function init_view( string|int $site_id, array $options ): WPCLOUD_Metric_Data_View {
		$site_id = WPCloud_CLI_Site::get_site_id( $this->api(), $site_id );

		$unit  = $options['unit'] ?? 'hour';
		$start = $options['start'] ?? null;
		$end   = $options['end'] ?? null;

		if ( $start ) {
			$start_date = sprintf( '-%s %ss', $start, $unit );
			$start      = strtotime( $start_date );
		}

		if ( $end ) {
			$end_date = sprintf( '-%s %ss', $end, $unit );
			$end      = strtotime( $end_date );
		}

		try {
			$view = new WPCloud_Metric_Data_View( site: $site_id, start: $start, end: $end );
		} catch ( Exception $e ) {
				WP_CLI::error( $e );
				die( 1 );
		}
		return $view;
	}
}
