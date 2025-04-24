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
	 * Load data.
	 *
	 * @return WP_Error|WPCLOUD_Metric_Data_View
	 */
	public function load(): WP_Error|WPCLOUD_Metric_Data_View {
		// Validate the metric and dimension.
		if ( ! array_key_exists( $this->metric, $this->get_available_metrics() ) ) {
			return new WP_Error( 'invalid_metric', 'Invalid metric', array( 'status' => 400 ) );
		}

		if ( ! array_key_exists( $this->dimension, $this->get_available_dimensions( $this->metric ) ) ) {
			return new WP_Error( 'invalid_dimension', 'Invalid dimension', array( 'status' => 400 ) );
		}

		$this->fetch();
		if ( is_wp_error( $this->result ) ) {
			return $this->result;
		}
		return $this;
	}

	/**
	 * Get the default view.
	 *
	 * @return WP_Error|WPCLOUD_Metric_Data_View
	 */
	public function default(): WP_Error|WPCLOUD_Metric_Data_View {
		$this->set_dimensions();
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
			$x = $row['timestamp'] ?? '';
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
