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
		//wpcloud_l( 'metrics results', $result->data );

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
			// Edge Request Logs
			'requests'                   => __( 'Requests', 'wpcloud' ),
			'requests_persec'            => __( 'Requests per second', 'wpcloud' ),
			'response_bytes'             => __( 'Response bytes', 'wpcloud' ),
			'response_bytes_persec'      => __( 'Response bytes per second', 'wpcloud' ),
			'response_bytes_average'     => __( 'Response bytes average', 'wpcloud' ),
			'response_time_average'      => __( 'Response time average', 'wpcloud' ),
			'php_response_time_sum'      => __( 'PHP response time (sum)', 'wpcloud' ),

			/* Available but better as a dimension */
			'edge_cache_hit_percentage'  => __( 'Edge cache hit percentage', 'wpcloud' ),
			'edge_cache_miss_percentage' => __( 'Edge cache miss percentage', 'wpcloud' ),

			// PHP-FPM Origin Logs
			'php_cpu_time'               => __( 'PHP CPU time', 'wpcloud' ),
			'php_cpu_time_persec'        => __( 'PHP CPU time per second', 'wpcloud' ),
			'php_response_time'          => __( 'PHP response time', 'wpcloud' ),
			'php_requests'               => __( 'PHP requests', 'wpcloud' ),
			'php_requests_persec'        => __( 'PHP requests per second', 'wpcloud' ),
			'php_workers_average'        => __( 'PHP workers_average', 'wpcloud' ),
			'php_workers_min'            => __( 'PHP workers min', 'wpcloud' ),
			'php_workers_max'			 => __( 'PHP workers max', 'wpcloud' ),

			/* Available but better as a dimension
			'php_requests_burst_percentage' => __( 'PHP requests burst percentage', 'wpcloud' ),
			'php_requests_limited_percentage' => __( 'PHP requests burst percentage', 'wpcloud' ),
			'php_requests_normal_percentage' => __( 'PHP requests burst percentage', 'wpcloud' ),
			*/

			// Edge based Unique Views
			'uniques'					=> __( 'Unique Visits', 'wpcloud' ),
			'views'						=> __( 'Page Views', 'wpcloud' ),

			// MySQL Pool Stats
			/* Available but does it make sense to share pool info with clients?
			'mysql_pool_replication_lag_average' => __( 'MySQL Replication Lag', 'wpcloud' ),
			*/

			// cgroup User Stats
			'cgroup_cpu_usage' => __( 'CPU Usage', 'wpcloud' ),
			'cgroup_cpu_usage_persec' => __( 'CPU Usage per second', 'wpcloud' ),

			// MySQL / MariaDB User Stats
			// @todo We might want to simplify the list to most used :)
			'mysql_total_connections' => __( 'MYSQL total connections', 'wpcloud' ),
			'mysql_total_connections_persec' => __( 'MYSQL total connections per second', 'wpcloud' ),
			'mysql_concurrent_connections' => __( 'MYSQL concurrent connections', 'wpcloud' ),
			'mysql_concurrent_connections_persec' => __( 'MYSQL concurrent connections per second', 'wpcloud' ),
			'mysql_connected_time' => __( 'MYSQL connected time', 'wpcloud' ),
			'mysql_connected_time_persec' => __( 'MYSQL connected time per second', 'wpcloud' ),
			'mysql_busy_time' => __( 'MYSQL busy time', 'wpcloud' ),
			'mysql_busy_time_persec' => __( 'MYSQL busy time per second', 'wpcloud' ),
			'mysql_cpu_time' => __( 'MYSQL cpu time', 'wpcloud' ),
			'mysql_cpu_time_persec' => __( 'MYSQL cpu time per second', 'wpcloud' ),
			'mysql_bytes_received' => __( 'MYSQL bytes received', 'wpcloud' ),
			'mysql_bytes_received_persec' => __( 'MYSQL bytes received per second', 'wpcloud' ),
			'mysql_bytes_sent' => __( 'MYSQL bytes sent', 'wpcloud' ),
			'mysql_bytes_sent_persec' => __( 'MYSQL bytes sent', 'wpcloud' ),
			'mysql_binlog_bytes_written' => __( 'MYSQL binlog bytes written', 'wpcloud' ),
			'mysql_binlog_bytes_written_persec' => __( 'MYSQL binlog bytes written per second', 'wpcloud' ),
			'mysql_rows_read' => __( 'MYSQL rows read', 'wpcloud' ),
			'mysql_rows_read_persec' => __( 'MYSQL rows read per second', 'wpcloud' ),
			'mysql_rows_sent' => __( 'MYSQL rows sent', 'wpcloud' ),
			'mysql_rows_sent_persec' => __( 'MYSQL rows sent per second', 'wpcloud' ),
			'mysql_rows_deleted' => __( 'MYSQL rows deleted', 'wpcloud' ),
			'mysql_rows_deleted_persec' => __( 'MYSQL rows deleted per second', 'wpcloud' ),
			'mysql_rows_inserted' => __( 'MYSQL rows inserted', 'wpcloud' ),
			'mysql_rows_inserted_persec' => __( 'MYSQL rows inserted per second', 'wpcloud' ),
			'mysql_rows_updated' => __( 'MYSQL rows updated', 'wpcloud' ),
			'mysql_rows_updated_persec' => __( 'MYSQL rows updated per second', 'wpcloud' ),
			'mysql_select_commands' => __( 'MYSQL select commands', 'wpcloud' ),
			'mysql_select_commands_persec' => __( 'MYSQL select commands per second', 'wpcloud' ),
			'mysql_update_commands' => __( 'MYSQL update commands', 'wpcloud' ),
			'mysql_update_commands_persec' => __( 'MYSQL update commands per second', 'wpcloud' ),
			'mysql_other_commands' => __( 'MYSQL other commands', 'wpcloud' ),
			'mysql_other_commands_persec' => __( 'MYSQL other commands per second', 'wpcloud' ),
			'mysql_commit_transactions' => __( 'MYSQL commit transactions', 'wpcloud' ),
			'mysql_commit_transactions_persec' => __( 'MYSQL commit transactions per second', 'wpcloud' ),
			'mysql_rollback_transactions' => __( 'MYSQL rollback transactions', 'wpcloud' ),
			'mysql_rollback_transactions_persec' => __( 'MYSQL rollback transactions per second', 'wpcloud' ),
			'mysql_denied_connections' => __( 'MYSQL denied connections', 'wpcloud' ),
			'mysql_denied_connections_persec' => __( 'MYSQL denied connections per second', 'wpcloud' ),
			'mysql_lost_connections' => __( 'MYSQL lost connections', 'wpcloud' ),
			'mysql_lost_connections_persec' => __( 'MYSQL lost connections per second', 'wpcloud' ),
			'mysql_access_denied' => __( 'MYSQL access denied', 'wpcloud' ),
			'mysql_access_denied_persec' => __( 'MYSQL access denied per second', 'wpcloud' ),
			'mysql_empty_queries' => __( 'MYSQL empty queries', 'wpcloud' ),
			'mysql_empty_queries_persec' => __( 'MYSQL empty queries per second', 'wpcloud' ),
			'mysql_total_ssl_connections' => __( 'MYSQL total ssl connections', 'wpcloud' ),
			'mysql_total_ssl_connections_persec' => __( 'MYSQL total ssl connections per second', 'wpcloud' ),
			'mysql_max_statement_time_exceeded' => __( 'MYSQL max statement time exceeded', 'wpcloud' ),
			'mysql_max_statement_time_exceeded_persec' => __( 'MYSQL max statement time exceeded per second', 'wpcloud' ),

		);
	}

	/**
	 * Get the available dimensions.
	 *
	 * @return array The dimensions.
	 */
	public static function get_available_dimensions( $metric = null ): array {
		if( ! empty( $metric ) && str_starts_with( $metric, 'mysql_pool_' ) ) {
			// MySQL Pool Stats Dimensions
			return array(
				'pool'	 => __( 'Pool Server ID', 'wpcloud' ),
				'server' => __( 'Pool Server Name', 'wpcloud' ),
			);
		} else if( ! empty( $metric ) && str_starts_with( $metric, 'mysql_' ) ) {
			// MySQL User Stats Dimensions
			return array(
				'pool'				=> __( 'Pool Server ID', 'wpcloud' ),
				'server' 			=> __( 'Pool Server Name', 'wpcloud' ),
				'atomic_site_id'	=> __( 'Atomic site ID', 'wpcloud' ),
			);
		} else if( ! empty( $metric ) && str_starts_with( $metric, 'cgroup_' ) ) {
			// cgroup User Stats Dimensions
			return array(
				'pool'				=> __( 'Pool Server ID', 'wpcloud' ),
				'server' 			=> __( 'Pool Server Name', 'wpcloud' ),
				'atomic_site_id'	=> __( 'Atomic site ID', 'wpcloud' ),
			);
		} else if( in_array( $metric, Array( 'uniques', 'views' ), true ) ) {
			// Uniques and Views Dimensions
			return array(
				'hostname' => __('hostname', 'wpcloud'),
			);
		} else if(
			! empty( $metric )
			&& str_starts_with( $metric, 'php_' )
			&& ! in_array( $metric, Array( 'php_response_time_sum' ), true ) ) {
			// PHP-FPM Origin Dimensions
			return array(
				'http_verb'				=> __( 'HTTP Verb', 'wpcloud' ),
				'http_host' 			=> __( 'HTTP Host', 'wpcloud' ),
				'datacenter'			=> __( 'Datacenter', 'wpcloud' ),
				'atomic_site_id'		=> __( 'Site ID', 'wpcloud' ),
				'burst_status'			=> __( 'Burst Status', 'wpcloud' ),
			);
		} else {
			// Edge Dimensions // Use for Default
			return array(
				'server_protocol'      => __( 'Server protocol', 'wpcloud' ),
				'request_method'       => __( 'Request method', 'wpcloud' ),
				'http_host'            => __( 'HTTP host', 'wpcloud' ),
				'http_status'          => __( 'HTTP status', 'wpcloud' ),
				'http_user_agent'      => __( 'HTTP User Agent', 'wpcloud' ),
				'http_referer'         => __( 'HTTP referer', 'wpcloud' ),
				'request_renderer'     => __( 'Request renderer', 'wpcloud' ),
				'is_upstream_cached'   => __( 'Is upstream cached', 'wpcloud' ),
				'wp_admin_ajax_action' => __( 'WP Admin AJAX action', 'wpcloud' ),
				'asn'                  => __( 'Visitor ASN', 'wpcloud' ),
				'country_code'         => __( 'Visitor country code', 'wpcloud' ),
				'is_crawler'           => __( 'Visitor is crawler', 'wpcloud' ),
				'visitor_device_type'  => __( 'Visitor device type', 'wpcloud' ),
				'visitor_is_logged_in' => __( 'Visitor is logged In', 'wpcloud' ),
				'visitor_os'           => __( 'Visitor OS', 'wpcloud' ),
				'visitor_browser'      => __( 'Visitor Browser', 'wpcloud' ),
				'edge_cache_status'    => __( 'Edge cache status', 'wpcloud' ),
				'is_rate_limited'      => __( 'Is rate limited', 'wpcloud' ),
				'rate_limit_reason'    => __( 'Rate limit reason', 'wpcloud' ),
				'datacenter'           => __( 'Datacenter', 'wpcloud' ),
				'path'                 => __( 'Path', 'wpcloud' ),
				'atomic_site_id'       => __( 'Atomic site ID', 'wpcloud' ),
				'proxy_type'           => __( 'Proxy type', 'wpcloud' ),
			);
		}
	}
}
