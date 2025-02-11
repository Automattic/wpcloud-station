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

			register_rest_route(
				$this->namespace,
				$this->rest_base . '/(?<metric>[\w]+)',
				array(
					'args'                => array(
						'site'      => array(
							'description' => esc_html__( 'Unique identifier for the site.', 'wpcloud' ),
							'type'        => 'integer',
						),
						'metric'    => array(
							'description' => esc_html__( 'The metric to retrieve.', 'wpcloud' ),
							'type'        => 'string',
						),
						'dimension' => array(
							'description' => esc_html__( 'The dimension to retrieve.', 'wpcloud' ),
							'type'        => 'string',
						),
						'start'     => array(
							'description' => esc_html__( 'The start time.', 'wpcloud' ),
							'type'        => 'string',
							'options'     => array(
								'default' => null,
							),
						),
						'end'       => array(
							'description' => esc_html__( 'The end time.', 'wpcloud' ),
							'type'        => 'string',
							'options'     => array(
								'default' => null,
							),
						),
					),
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_site_metric' ),
					'permission_callback' => array( $this, 'user_access_check' ),
				)
			);
		}

		/**
		 * Get available metrics
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
		 * Get site metric
		 *
		 * @param WP_REST_Request $request The request.
		 *
		 * @return WP_REST_Response
		 */
		public function get_site_metric( WP_REST_Request $request ): WP_REST_Response {
			$params    = $request->get_params();
			$site_id   = $params['site'];
			$metric    = $params['metric'];
			$dimension = $params['dimension'] ?? null;
			$start     = $this->parseTime( $params['start'] ?? null, 'start' );
			$end       = $this->parseTime( $params['end'] ?? null, 'end' );

			if ( is_wp_error( $start ) || is_wp_error( $end ) ) {
				return new WP_REST_Response( $start->get_error_message(), 400 );
			}

			$site = WPCLOUD_Site::get_by_id( $site_id );

			if ( ! $site ) {
				return new WP_REST_Response( esc_html__( 'Site not found', 'wpcloud' ), 404 );
			}

			$view = WPCLOUD_Metric_Data_View::load(
				site: $site_id,
				metric: $metric,
				dimension: $dimension,
				start: $start,
				end: $end,
			);

			if ( is_wp_error( $view ) ) {
				return new WP_REST_Response( $view->get_error_message(), 500 );
			}

			$result = $view->default();

			$response = array(
				'meta'   => $result->meta,
				'series' => $result->series,
				'data'   => $result->data,
			);
			return new WP_REST_Response( $response, 200 );
		}

		/**
		 * Parse time
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

			// check if it's now-{some value} format.
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
