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
	 * The metric.
	 *
	 * @var string
	 */
	protected string $metric;

	/**
	 * The dimension.
	 *
	 * @var string
	 */
	protected string $dimension;

	/**
	 * The metric options.
	 *
	 * @var array
	 */
	protected array $options;

	/**
	 * The start.
	 *
	 * @var int|null
	 */
	protected ?int $start;

	/**
	 * The end.
	 *
	 * @var int|null
	 */
	protected ?int $end;

	/**
	 * Summarize the data.
	 *
	 * @var bool
	 */
	protected bool $summarize;

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
	 * @param string $metric   The metric.
	 * @param string $dimension The dimension.
	 * @param array  $interval The interval.
	 * @param bool   $summarize Whether to summarize the data.
	 * @param array  $options  The metric options.
	 *
	 * @throws Exception If the type is invalid.
	 */
	public function __construct( string $metric, string $dimension, array $interval, bool $summarize = false, array $options = array() ) {

		$this->metric    = $metric;
		$this->dimension = $dimension;
		$this->start     = $interval['start'];
		$this->end       = $interval['end'];
		$this->options   = $options;
		$this->summarize = $summarize;
	}

	/**
	 * Get the data.
	 *
	 * @param array $options The options.
	 *
	 * @return WP_Error|array The data
	 */
	public function fetch( array $options = array() ): WP_Error|WPCloud_Metrics {
		$options = array_merge(
			array(
				'metric'    => $this->metric,
				'dimension' => $this->dimension,
				'start'     => $this->start,
				'end'       => $this->end,
			),
			$this->options,
			$options
		);

		$site_id = $options['site_id'] ?? 0;
		unset( $options['site_id'] );

		$wpcloud_client = new WPCloud_API_Client( site_id: $site_id, use_cache: false );
		if ( $site_id ) {
			$path = 'metrics/site/:site_id';
		} else {
			$path = 'metrics/client/:client';
		}

		if ( $this->summarize ) {
			$path .= '/summarize';
		}

		$result = $wpcloud_client->post( $path, $options );
		wpcloud_l( 'metrics results', $result->data );

		if ( ! $result->is_ok() ) {
			$this->result = $result->error;
			return $result->error;
		}

		$this->result  = $result;
		$this->meta    = (array) $result->_meta;
		$this->periods = json_decode( wp_json_encode( $result->periods ), true );

		return $this;
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
