<?php
/**
 * WP Cloud Metrics Controller
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

if ( ! class_exists( 'WPCLOUD_Metrics_Controller' ) ) {

	/**
	 * Class WPCLOUD_Metrics_Controller
	 */
	class WPCLOUD_Metrics_Controller extends WP_REST_Controller {

		/**
		 * The namespace.
		 *
		 * @var string
		 */
		protected $namespace = 'wpcloud-station/v1';

		/**
		 * Rest base for the current object.
		 *
		 * @var string
		 */
		protected $rest_base = '/metrics';

		/**
		 * Register the routes for the objects of the controller.
		 */
		public function register_routes() {

			register_rest_route(
				$this->namespace,
				$this->rest_base . '/available',
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_available_metrics' ),
					'permission_callback' => array( $this, 'user_access_check' ),
				)
			);

			$common_args = array(
				// required args.
				'metric'     => array(
					'description' => esc_html__( 'The metric to retrieve.', 'wpcloud' ),
					'type'        => 'string',
				),
				'dimension'  => array(
					'description' => esc_html__( 'The dimension to retrieve.', 'wpcloud' ),
					'type'        => 'string',
				),

				// Default args.
				'start'      => array(
					'description' => esc_html__( 'The start time.', 'wpcloud' ),
					'type'        => 'string',
					'options'     => array(
						'default' => '-24 hours',
					),
				),
				'end'        => array(
					'description' => esc_html__( 'The end time.', 'wpcloud' ),
					'type'        => 'string',
					'options'     => array(
						'default' => 'now',
					),
				),
				'resolution' => array(
					'description' => esc_html__( 'The metric resolution.', 'wpcloud' ),
					'type'        => 'int',
					'options'     => array(
						'default' => 10,
					),
				),
				'top_x'      => array(
					'description' => esc_html__( 'The top X data points.', 'wpcloud' ),
					'type'        => 'int',
					'options'     => array(
						'default' => 20,
					),
				),
				'summarize'  => array(
					'description' => esc_html__( 'Retrieve a summary vs time based results', 'wpcloud' ),
					'type'        => 'boolean',
					'options'     => array(
						'default' => false,
					),
				),

				// Optional args.
				'filters'    => array(
					'description' => esc_html__( 'Filters to be applied to the metric query', 'wpcloud' ),
					'type'        => 'string', // This is an associated array but to make it easier to handle accept json_encoded( array ) aka string.
					'options'     => array(
						'default' => null,
					),
				),
			);

			// Client metrics.
			register_rest_route(
				$this->namespace,
				$this->rest_base . '/client/(?<metric>[\w]+)',
				array(
					'args'                => $common_args,
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_metric' ),
					'permission_callback' => array( $this, 'user_access_check' ),
				)
			);

			// Site metrics.
			register_rest_route(
				$this->namespace,
				$this->rest_base . '/site/(?<site_id>[\d]+)/(?<metric>[\w]+)',
				array(
					'args'                =>
					array_merge(
						$common_args,
						// make site_id required.
						array(
							'site_id' => array(
								'description' => esc_html__( 'Unique identifier for the site.', 'wpcloud' ),
								'type'        => 'integer',
							),
						),
					),
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_metric' ),
					'permission_callback' => array( $this, 'user_access_check' ),
				)
			);
		}

		/**
		 * Get available metrics.
		 *
		 * @return WP_REST_Response
		 */
		public function get_available_metrics(): WP_REST_Response {
			return new WP_REST_Response(
				array(
					'dimensions' => WPCLOUD_Metrics::get_available_dimensions(),
					'metrics'    => WPCLOUD_Metrics::get_available_metrics(),
				),
				200
			);
		}

		/**
		 * Get metric.
		 *
		 * @param WP_REST_Request $request The request.
		 *
		 * @return WP_REST_Response
		 */
		public function get_metric( WP_REST_Request $request ): WP_REST_Response {
			try {
				$params    = $request->get_params();
				$metric    = $params['metric'];
				$dimension = $params['dimension'];
				$summarize = $params['summarize'] ?? false;

				$options = array(
					'filters'    => $params['filters'] ?? null,
					'top_x'      => $params['top_x'] ?? 20,
					'resolution' => $params['resolution'] ?? 10,
				);

				$site_id = $params['site_id'] ?? null;
				if ( $site_id ) {
					$site = new WPCLOUD_Site( $site_id );

					if ( ! $site->post ) {
						return new WP_REST_Response( esc_html__( 'Site not found', 'wpcloud' ), 404 );
					}
					$options['site_id'] = $site_id;
				}

				$interval = $this->get_interval( $params );

				$view = new WPCLOUD_Metric_Data_View(
					metric: $metric,
					dimension: $dimension,
					interval: $interval,
					summarize: $summarize,
					options: $options,
				);
				$view = $view->load();

				if ( is_wp_error( $view ) ) {
					return new WP_REST_Response( $view->get_error_message(), 500 );
				}

				$result = $view->default();

				if ( is_wp_error( $result ) ) {
					return new WP_REST_Response( $result->get_error_message(), 500 );
				}

				$response = array(
					'meta'   => $result->meta,
					'series' => $result->series,
					'data'   => $result->data,
				);
				return new WP_REST_Response( $response, 200 );
			} catch ( Exception $e ) {
				return new WP_REST_Response( $e->getMessage(), 500 );
			}
		}

		/**
		 * Get the interval.
		 *
		 * @param array $params The parameters.
		 *
		 * @return array The interval.
		 * @throws Exception If the time is invalid.
		 */
		public function get_interval( array $params ): array {
			$start = $params['start'] ?? 'now';
			$end   = $params['end'] ?? 'now-24h';
			$start = $this->parseTime( $start, 'start' );
			if ( is_wp_error( $start ) ) {
				throw new Exception( $start->get_error_message() ); // phpcs:ignore
			}

			$end = $this->parseTime( $end, 'end' );
			if ( is_wp_error( $end ) ) {
				throw new Exception( $end->get_error_message() ); // phpcs:ignore
			}

			return array(
				'start' => $start,
				'end'   => $end,
			);
		}

		/**
		 * Parse time.
		 *
		 * @param string $time The time.
		 * @param string $position The position.
		 *
		 * @return int|null|WP_Error a timestamp.
		 */
		public function parseTime( ?string $time, string $position ): int|null|WP_Error {
			if ( is_null( $time ) ) {
				return null;
			}

			$units = array(
				's' => 'seconds',
				'm' => 'minutes',
				'h' => 'hours',
				'd' => 'days',
				'M' => 'months',
			);

			// Check if it's now-{some value} format.
			if ( 0 === strpos( $time, 'now-' ) ) {
				$time = substr( $time, 3 );
			}
			$unit = substr( $time, -1 );
			$ts   = null;

			if ( array_key_exists( $unit, $units ) ) {
				$value = ltrim( substr( $time, 0, -1 ), '-' );
				$ts    = strtotime( sprintf( '-%s %s', $value, $units[ $unit ] ) );
			} else {
				$ts = strtotime( $time );
			}

			// If the timestamp is false or in the future, return an error.
			if ( false === $ts || $ts > time() ) {
				// translators: %s: position.
				return new WP_Error( 'rest_invalid_param', wp_sprintf( esc_html__( 'Invalid %s time', 'wpcloud' ), $position ), array( 'status' => 400 ) );
			}
			return $ts;
		}

		/**
		 * Check permissions for the current request.
		 *
		 * @return bool|WP_Error True if the requester has manage site capabilities. False if logged in but with out manage site capabilities, WP_Error if not logged in.
		 */
		public function user_access_check(): bool|WP_Error {
			if ( is_user_logged_in() && current_user_can( WPCLOUD_CAN_MANAGE_SITES ) ) {
				return true;
			}
			return new WP_Error( 'rest_forbidden', esc_html__( 'Unauthorized request', 'wpcloud' ), rest_authorization_required_code() );
		}
	}
}
