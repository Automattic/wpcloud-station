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
	 * Default view.
	 *
	 * @param string $metric The metric.
	 * @param string $dimension The dimension.
	 * @param array  $options The options.
	 *
	 * @return WP_Error|WPCLOUD_Metric_Data_View
	 */
	public function default( string $metric, string $dimension, $options = array() ): WP_Error|WPCLOUD_Metric_Data_View {
		$this->fetch_data( $metric, $dimension, $options );

		if ( is_wp_error( $this->result ) ) {
			return $this->result;
		}

		// Pull out dimensions.
		$this->set_dimensions();
		if ( empty( $this->dimensions ) ) {
			return new WP_Error( 'no_dimensions', esc_html__( 'No dimensions found', 'wpcloud' ) );
		}
		error_log( print_r( $this->dimensions, true ) );

		// Set up the series info.
		$this->set_series();

		$this->set_data();

		return $this;
	}

	/**
	 * Transform the data for stacked views.
	 *
	 * @param string $metric The metric.
	 * @param string $dimension The dimension.
	 * @param array  $options The options.
	 *
	 * @return WP_Error|WPCLOUD_Metric_Data_View
	 */
	public function stacked( string $metric, string $dimension, $options = array() ): WP_Error|WPCLOUD_Metric_Data_View {
		$this->fetch_data( $metric, $dimension, $options );

		if ( is_wp_error( $this->result ) ) {
			return $this->result;
		}

		$aggregate_keys = array();
		$x              = array();
		foreach ( $this->periods as $row ) {
			$x[]            = $row['timestamp'];
			$aggregate_keys = array_unique( array_merge( $aggregate_keys, array_keys( $row['dimension'] ) ) );
		}
		$this->data = array( $x );

		$this->map = array_merge( array( 'timestamp' ), array_map( 'strval', $aggregate_keys ) );
		$series    = array();
		foreach ( $aggregate_keys as $key ) {
			$series[ $key ] = array(
				'label' => $key,
				'width' => 0,
				'fill'  => true,
			);
		}
		ksort( $series );

		array_unshift( $series, array() );
		$this->series = $series;

		$aggregate_key_data = array();
		foreach ( $aggregate_keys as $key ) {
			$aggregate_key_data[ $key ] = array();
		}

		$resolution = $this->meta['resolution'];
		foreach ( $this->periods as $row ) {
			foreach ( $aggregate_keys as $key ) {
				$data_point = null;
				if ( isset( $row['dimension'][ $key ] ) ) {
					$data_point = $resolution * $row['dimension'][ $key ];
				}
				$aggregate_key_data[ $key ][] = $data_point;
			}
		}
		ksort( $aggregate_key_data );
		foreach ( $aggregate_key_data as $key => $data ) {
			$this->data[] = $data;
		}

		return $this;
	}

	/**
	 * Get the dimensions
	 *
	 * @return void
	 */
	public function set_dimensions(): void {
		$dimensions = array();
		foreach ( $this->periods as $row ) {
			if ( empty( $row['timestamp'] ) ) {
				continue;
			}
			$dimensions = array_merge( $dimensions, array_keys( $row['dimension'] ) );
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

		foreach ( $this->dimensions as $dim ) {
			$series[] = array(
				'label' => $dim,
				'width' => 0,
				'fill'  => true,
			);
		}
		ksort( $series );
		// Add an empty series for the x-axis.
		array_unshift( $series, array() );
		$this->series = $series;
	}

	/**
	 * Set the data.
	 *
	 * @return void
	 */
	public function set_data(): void {

		$dimension_data = array();
		foreach ( $this->dimensions as $dim ) {
			$dimension_data[ $dim ] = array();
		}
		$data = array_merge(
			array( 'timestamp' => array() ),
			$dimension_data
		);

		foreach ( $this->periods as $row ) {
			$x = $row['timestamp'];
			if ( empty( $x ) ) {
				continue;
			}

			array_push( $data['timestamp'], $x );
			foreach ( $this->dimensions as $dim ) {
				$datum = $data[ $dim ];
				$value = $row['dimension'][ $dim ] ?? null;
				array_push( $datum, $value );
			}
		}
		$this->data = $data;
	}
}
