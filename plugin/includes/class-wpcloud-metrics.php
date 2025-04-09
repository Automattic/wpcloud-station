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
	protected ?int $site;

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
	protected ?string $dimension;

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
	 * @param string   $metric   The metric.
	 * @param string   $dimension The dimension.
	 * @param int|null $start    The start.
	 * @param int|null $end      The end.
	 * @param int      $site     The site.
	 *
	 * @throws Exception If the type is invalid.
	 */
	public function __construct( string $metric, ?string $dimension, ?int $start = null, ?int $end = null, ?int $site = null ) {
		$this->site      = $site;
		$this->metric    = $metric;
		$this->dimension = $dimension;
		$this->start     = $start ?? strtotime( '-24 hours' );
		$this->end       = $end ?? time();
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
	public function fetch( array $options = array() ): WP_Error|WPCloud_Metrics {
		$options   = array_merge(
			array(
				'metric'    => $this->metric,
				'dimension' => $this->dimension,
				'start'     => $this->start,
				'end'       => $this->end,
			),
			$options
		);
		$summarize = $options['summarize'] ?? false;
		unset( $options['summarize'] );

		$wpcloud_client = new WPCloud_API_Client( use_cache: false );
		if ( $this->site ) {
			$wpcloud_client->set_site_id( $this->site );
			$path = 'metrics/site/:site_id';
		} else {
			$path = 'metrics/client/:client';
		}
		if ( $summarize ) {
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
