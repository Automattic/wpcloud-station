<?php
/**
 * WP Cloud Metrics
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/wpcloud-client.php';

/**
 * WP Cloud Metrics
 */
class WPCloud_Metrics {

	/**
	 * The site.
	 *
	 * @var int
	 */
	private int $site;

	/**
	 * The start.
	 *
	 * @var int|null
	 */
	private ?int $start;

	/**
	 * The end.
	 *
	 * @var int|null
	 */
	private ?int $end;

	/**
	 * The result.
	 *
	 * @var mixed
	 */
	public $result;


	/**
	 * The summary.
	 *
	 * @var array
	 */
	public $summary;


	/**
	 * The meta.
	 *
	 * @var array
	 */
	public $meta;


	/**
	 * The data.
	 *
	 * @var array
	 */
	public $periods;

	/**
	 * Constructor.
	 *
	 * @param int      $site     The site.
	 * @param int|null $start    The start.
	 * @param int|null $end      The end.
	 *
	 * @throws Exception If the type is invalid.
	 */
	public function __construct( int $site, ?int $start = null, ?int $end = null ) {
		$this->site  = $site;
		$this->start = $start ?? strtotime( '-24 hours' );
		$this->end   = $end ?? time();
	}

	/**
	 * Get the requests.
	 *
	 * @param string $dimension The dimension.
	 * @param bool   $summarize Summarize the data.
	 *
	 * @return WP_Error|array The data
	 */
	public function requests( ?string $dimension = null, bool $summarize = false ): WP_Error|array {
		$options = array(
			'metric'    => 'requests_persec',
			'dimension' => $dimension,
			'summarize' => $summarize,
		);

		return $this->get_data( $options );
	}

	/**
	 * Get the response bytes.
	 *
	 * @param string $dimension The dimension.
	 * @param bool   $average   Average the data.
	 * @param bool   $summarize Summarize the data.
	 *
	 * @return WP_Error|array The data
	 */
	public function response_bytes( ?string $dimension = null, bool $average = false, bool $summarize = false ): array {
		$metric = $average ? 'response_bytes_average' : 'response_bytes_persec';

		$options = array(
			'metric'    => $metric,
			'dimension' => $dimension,
			'summarize' => $summarize,
		);

		return $this->get_data( $options );
	}

	/**
	 * Get the response time.
	 *
	 * @param string $dimension The dimension.
	 * @param bool   $summarize Summarize the data.
	 *
	 * @return WP_Error|array The data
	 */
	public function response_time( ?string $dimension = null, bool $summarize = false ): array {
		$options = array(
			'metric'    => 'response_time_average',
			'dimension' => $dimension,
			'summarize' => $summarize,
		);

		return $this->get_data( $options );
	}


	/**
	 * Get the data.
	 *
	 * @param array $options The options.
	 *
	 * @return WP_Error|array The data
	 */
	private function get_data( array $options ): WP_Error|array {

		$options['dimension'] = $this->get_dimension( $options['dimension'] );
		if ( is_wp_error( $options['dimension'] ) ) {
			return $options['dimension'];
		}

		$this->result = wpcloud_client_site_metrics( $this->site, $this->start, $this->end, $options );
		if ( is_wp_error( $this->result ) ) {
			return $this->result;
		}
		$this->meta    = (array) $this->result->_meta;
		$this->periods = json_decode( wp_json_encode( $this->result->periods ), true );
		return $this->periods;
	}

	/**
	 * Get the server metrics.
	 *
	 * @param string $dimension The dimension.
	 *
	 * @return WP_Error|array
	 */
	private function get_dimension( ?string $dimension = null ): WP_Error|string {

		if ( is_null( $dimension ) ) {
			return 'http_host';
		}

		$dimensions = array(
			'http_version',
			'http_verb',
			'http_host', // Default.
			'http_status',
			'page_renderer',
			'page_is_cached',
			'wp_admin_ajax_action',
			'visitor_asn',
			'visitor_country_code',
			'visitor_is_crawler',
		);

		if ( ! in_array( $dimension, $dimensions, true ) ) {
			echo "not in array ?\n";
			return new WP_Error( 'invalid_dimension', 'Invalid dimension' );
		}
		return $dimension;
	}

	/**
	 * Get the available metrics.
	 *
	 * @return array The metrics.
	 */
	public static function get_available_metrics(): array {
		return array(
			'requests'                   => __( 'Requests', 'wpcloud' ),
			'requests_persec'            => __( 'Requests per second', 'wpcloud' ),
			'response_bytes'             => __( 'Response bytes', 'wpcloud' ),
			'response_bytes_persec'      => __( 'Response bytes per second', 'wpcloud' ),
			'response_bytes_average'     => __( 'Response bytes average', 'wpcloud' ),
			'response_time_average'      => __( 'Response time average', 'wpcloud' ),
			'php_response_time_sum'      => __( 'PHP response time sum', 'wpcloud' ),
			'edge_cache_hit_percentage'  => __( 'Edge cache hit percentage', 'wpcloud' ),
			'edge_cache_miss_percentage' => __( 'Edge cache miss percentage', 'wpcloud' ),
			'php_cpu_time'               => __( 'PHP CPU time', 'wpcloud' ),
			'php_cpu_time_persec'        => __( 'PHP CPU time per second', 'wpcloud' ),
			'php_response_time'          => __( 'PHP response time', 'wpcloud' ),
			'php_requests'               => __( 'PHP requests', 'wpcloud' ),
			'php_requests_persec'        => __( 'PHP requests per second', 'wpcloud' ),
		);
	}

	/**
	 * Get the available dimensions.
	 *
	 * @return array The dimensions.
	 */
	public static function get_available_dimensions(): array {
		return array(
			'http_version'         => __( 'HTTP version', 'wpcloud' ),
			'server_protocol'      => __( 'Server protocol', 'wpcloud' ),
			'http_verb'            => __( 'HTTP verb', 'wpcloud' ),
			'request_method'       => __( 'Request method', 'wpcloud' ),
			'http_host'            => __( 'HTTP host', 'wpcloud' ),
			'http_status'          => __( 'HTTP status', 'wpcloud' ),
			'page_renderer'        => __( 'Page renderer', 'wpcloud' ),
			'request_renderer'     => __( 'Request renderer', 'wpcloud' ),
			'page_is_cached'       => __( 'Page is cached', 'wpcloud' ),
			'is_upstream_cached'   => __( 'Is upstream cached', 'wpcloud' ),
			'wp_admin_ajax_action' => __( 'WP admin AJAX action', 'wpcloud' ),
			'visitor_asn'          => __( 'Visitor ASN', 'wpcloud' ),
			'asn'                  => __( 'ASN', 'wpcloud' ),
			'visitor_country_code' => __( 'Visitor country code', 'wpcloud' ),
			'country_code'         => __( 'Country code', 'wpcloud' ),
			'visitor_is_crawler'   => __( 'Visitor is crawler', 'wpcloud' ),
			'is_crawler'           => __( 'Is crawler', 'wpcloud' ),
			'visitor_device_type'  => __( 'Visitor device type', 'wpcloud' ),
			'edge_cache_status'    => __( 'Edge cache status', 'wpcloud' ),
			'is_rate_limited'      => __( 'Is rate limited', 'wpcloud' ),
			'rate_limit_reason'    => __( 'Rate limit reason', 'wpcloud' ),
			'datacenter'           => __( 'Datacenter', 'wpcloud' ),
			'path'                 => __( 'Path', 'wpcloud' ),
			'atomic_site_id'       => __( 'Atomic site ID', 'wpcloud' ),
			'proxy_type'           => __( 'Proxy type', 'wpcloud' ),
			/**
			 * Planned
			 * 'remote_address'      => __( 'Remote address', 'wpcloud' ),
			 * 'http_user_agent'     => __( 'HTTP user agent', 'wpcloud' ),
			 * 'http_referer'        => __( 'HTTP referer', 'wpcloud' ),
			*/
		);
	}
}
