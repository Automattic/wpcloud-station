<?php
/**
 * WP Cloud Metrics
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-metrics.php';

/**
 * WP Cloud Metric Data
 */
class WPCLOUD_Metric_Data_View extends WPCLOUD_Metrics {

	/**
	 * The map.
	 *
	 * @var array
	 */
	public $map;

	/**
	 * The series.
	 *
	 * @var array
	 */
	public $series;

	/**
	 * The data.
	 *
	 * @var array
	 */
	public $data;

	/**
	 * The dimensions.
	 *
	 * @var array
	 */
	public $dimensions;

	/**
	 * Generate the view.
	 *
	 * @param int    $site      The site.
	 * @param string $metric    The metric.
	 * @param string $dimension The dimension.
	 * @param int    $start     The start.
	 * @param int    $end       The end.
	 *
	 * @return WP_Error|WPCLOUD_Metric_Data
	 */
	public static function load( int $site, string $metric, string $dimension, int $start, int $end, string $request_type = 'site', int $resolution = 10, int $top_x = 20, bool $summarize = false, array $filters = null ): WP_Error|WPCLOUD_Metric_Data_View {
		// validate the metric and dimension.
		$view = new self( $site, $metric, $dimension, $start, $end );
		if ( ! array_key_exists( $view->metric, $view->get_available_metrics() ) ) {
			return new WP_Error( 'invalid_metric', 'Invalid metric', array( 'status' => 400 ) );
		}

		if ( ! array_key_exists( $view->dimension, $view->get_available_dimensions() ) ) {
			return new WP_Error( 'invalid_dimension', 'Invalid dimension', array( 'status' => 400 ) );
		}
		$view->fetch( [
			'request_type' => $request_type,
			'resolution' => $resolution,
			'top_x' => $top_x,
			'summarize' => $summarize,
			'filters' => $filters,
		] );
		if ( is_wp_error( $view->result ) ) {
			return $view->result;
		}
		return $view;
	}

	/**
	 * Get the default view.
	 *
	 * @return WP_Error|WPCLOUD_Metric_Data_View
	 */
	public function default(): WP_Error|WPCLOUD_Metric_Data_View {
		$this->set_dimensions();
		if ( empty( $this->dimensions ) ) {
			return new WP_Error( 'no_dimensions', 'No dimensions found', array( 'status' => 400 ) );
		}

		$this->set_series();
		$this->set_data();
		return $this;
	}

	/**
	 * Get the dimensions
	 *
	 * @return void
	 */
	private function set_dimensions(): void {
		$dimensions = array();
		foreach ( $this->periods as $row ) {
			if ( empty( $row['timestamp'] ) ) {
				continue;
			}
			$new_dimensions = array_map( 'strval', array_keys( $row['dimension'] ) );
			$dimensions     = array_merge( $dimensions, $new_dimensions );
		}
		$this->dimensions = array_unique( $dimensions );
	}

	/**
	 * Set the series.
	 *
	 * @return void
	 */
	private function set_series(): void {
		$series = array();

		// Just stub out the series. Let the JS handle the configuration.
		foreach ( $this->dimensions as $dim ) {
			$series[] = array(
				'label' => $dim,
			);
		}
		ksort( $series );
		// Add an empty series (as an object )for the x-axis.
		// See the note about using flat arrays here https://github.com/leeoniya/uPlot/blob/master/docs/README.md#series-scales-axes-grid .
		array_unshift( $series, (object) array() );
		$this->series = $series;
	}

	/**
	 * Set the data.
	 *
	 * @return void
	 */
	private function set_data(): void {

		// Set up all timestamp and dimensions container.
		$data = array(
			'timestamp' => array(),
		);
		foreach ( $this->dimensions as $dim ) {
			$data[ $dim ] = array();
		}

		foreach ( $this->periods as $row ) {
			$x = $row['timestamp'];
			if ( empty( $x ) ) {
				continue;
			}
			array_push( $data['timestamp'], $x );
			foreach ( $this->dimensions as $dim ) {
				$value = $row['dimension'][ $dim ] ?? null;
				array_push( $data[ $dim ], (float) $value );
			}
		}

		$this->data = array_values( $data );
	}
}
