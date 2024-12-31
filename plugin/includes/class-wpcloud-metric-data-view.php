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
	 * Get status codes
	 *
	 * @param bool $plot_view Whether to return a plot view.
	 *
	 * @return WP_Error|WPCLOUD_Metric_Data
	 */
	public function status_codes( $plot_view = true ): WP_Error|WPCLOUD_Metric_Data_View {
		$this->requests( 'http_status' );

		if ( ! $plot_view ) {
			return $this;
		}
		if ( is_wp_error( $this->result ) ) {
			return $this->result;
		}

		// First set up the x axis, noting how many status codes we encounter.
		$status_codes = array();
		$x            = array();
		foreach ( $this->periods as $row ) {
			$x[]          = $row['timestamp'];
			$status_codes = array_unique( array_merge( $status_codes, array_keys( $row['dimension'] ) ) );
		}
		$this->data = array( $x );

		$this->map = array_merge( array( 'timestamp' ), array_map( 'strval', $status_codes ) );
		$series    = array();
		foreach ( $status_codes as $status ) {
			$series[ $status ] = array(
				'label' => $status,
				'width' => 0,
				'fill'  => true, // color is assigned by the client.
			);
		}
		ksort( $series );

		array_unshift( $series, array() );
		$this->series = $series;

		// Set up y axis data.
		$status_code_data = array();
		foreach ( $status_codes as $status ) {
			$status_code_data[ $status ] = array();
		}

		$resolution = $this->meta['resolution'];
		foreach ( $this->periods as $row ) {
			foreach ( $status_codes as $status ) {
				$data_point = null;
				if ( isset( $row['dimension'][ $status ] ) ) {
					// Normalize the data to the resolution to get a total data point.
					$data_point = $resolution * $row['dimension'][ $status ];
				}
				$status_code_data[ $status ][] = $data_point;
			}
		}
		ksort( $status_code_data );
		foreach ( $status_code_data as $status => $data ) {
			$this->data[] = $data;
		}

		return $this;
	}
}
