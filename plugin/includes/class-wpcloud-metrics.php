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
	 * The options.
	 *
	 * @var array
	 */
	private $options;

	/**
	 * The raw data.
	 *
	 * @var array
	 */
	private $log_data;

	/**
	 * The result.
	 *
	 * @var mixed
	 */
	private $result;

	/**
	 * HTTP status codes.
	 *
	 * @var array
	 */
	const HTTP_STATUS_CODES = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		103 => 'Checkpoint',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Switch Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a teapot',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		425 => 'Unordered Collection',
		426 => 'Upgrade Required',
		449 => 'Retry With',
		450 => 'Blocked by Windows Parental Controls',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended',
	);

	/**
	 * Constructor.
	 *
	 * @param int      $site    The site.
	 * @param string   $type    The type.
	 * @param int|null $start   The start.
	 * @param int|null $end     The end.
	 * @param array    $options The options.
	 *
	 * @throws Exception If the type is invalid.
	 */
	public function __construct( int $site, string $type, ?int $start = null, ?int $end = null, $options = array() ) {
		$this->site    = $site;
		$this->start   = $start ?? strtotime( '-24 hours' );
		$this->end     = $end ?? time();
		$this->options = $options;

		switch ( $type ) {
			case 'server':
				$this->result = wpcloud_client_site_logs( $this->site, $this->start, $this->end, $options );
				break;
			case 'error':
				$this->result = wpcloud_client_site_error_logs( $this->site, $this->start, $this->end, $options );
				break;
			default:
				throw new Exception( 'Invalid type' );
		}

		if ( is_wp_error( $this->result ) ) {
			throw new Exception( $this->result->get_error_message() ); // phpcs:ignore
		} else {
			$this->log_data = $this->result->logs;
		}
	}

	/**
	 * Get raw log data.
	 *
	 * @return WP_Error|array The raw log data.
	 */
	public function logs(): WP_Error|array {
		return $this->log_data;
	}

	/**
	 * Get the response.
	 *
	 * @return WP_Error|array The response.
	 */
	public function response(): WP_Error|array {
		return (array) $this->result;
	}

	/**
	 * Get the status codes.
	 *
	 * @param int   $rollup_interval_sec The rollup interval in seconds.
	 * @param array $codes               The status codes.
	 *
	 * @return mixed The status codes.
	 */
	public function status_codes( int $rollup_interval_sec = 60, $codes = array() ): mixed {
		if ( is_wp_error( $this->log_data ) ) {
			return $this->log_data;
		}
		if ( empty( $codes ) ) {
			$codes = array_keys( self::HTTP_STATUS_CODES );
		}
		$options           = $this->options;
		$options['filter'] = array( 'status' => $codes );

		return $this->rollup( 'status', $rollup_interval_sec );
	}

	/**
	 * Get the status codes rollup.
	 *
	 * @param string $key                 The key.
	 * @param int    $rollup_interval_sec The rollup interval in seconds.
	 *
	 * @return mixed The status codes rollup.
	 */
	protected function rollup( $key, $rollup_interval_sec ): WP_Error|array {
		// Verify the rollup key.
		$first_row = $this->log_data[0];
		if ( ! property_exists( $first_row, $key ) ) {
			return new WP_Error( 'invalid_rollup_key', "Invalid rollup key:	$key" );
		}

		$rollup = $this->build_rollup_container( $rollup_interval_sec );
		foreach ( $this->log_data as $row ) {
			$timestamp = $row->timestamp;
			$timestamp = $timestamp - ( $timestamp % $rollup_interval_sec );

			if ( ! isset( $rollup[ $timestamp ] ) ) {
				error_log( "Invalid timestamp: $timestamp" ); // phpcs:ignore
				continue;
			}

			$term = $row->$key;

			$rollup[ $timestamp ][ $term ] = $rollup[ $timestamp ][ $term ] ?? 0;
			++$rollup[ $timestamp ][ $term ];
		}
		return $rollup;
	}

	/**
	 * Rollup container
	 *
	 * @param int $interval The interval.
	 *
	 * @return array The container.
	 */
	protected function build_rollup_container( $interval ): array {
		$container = array();
		$end       = $this->end;
		$start     = $this->start - ( $this->start % $interval );
		for ( $i = $start; $i < $end; $i += $interval ) {
			$container[ $i ] = array();
		}
		return $container;
	}
}
