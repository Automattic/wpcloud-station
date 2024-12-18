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
				$this->rest_base . '/(?<metric>[\w]+)/site/(?P<id>[\d]+)',
				array(
					'args'                => array(
						'id'     => array(
							'description' => esc_html__( 'Unique identifier for the site.', 'wpcloud' ),
							'type'        => 'integer',
						),
						'metric' => array(
							'description' => esc_html__( 'The metric to retrieve.', 'wpcloud' ),
							'type'        => 'string',
						),
						'start'  => array(
							'description' => esc_html__( 'The start time.', 'wpcloud' ),
							'type'        => 'string',
							'options'     => array(
								'default' => null,
							),
						),
						'end'    => array(
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
		 * Get site metric
		 *
		 * @param WP_REST_Request $request The request.
		 *
		 * @return WP_REST_Response
		 */
		public function get_site_metric( WP_REST_Request $request ): WP_REST_Response {
			$params  = $request->get_params();
			$site_id = $params['id'];
			$metric  = $params['metric'];
			$start   = $this->parseTime( $params['start'] ?? null, 'start' );
			$end     = $this->parseTime( $params['end'] ?? null, 'end' );

			if ( is_wp_error( $start ) || is_wp_error( $end ) ) {
				return new WP_REST_Response( $start->get_error_message(), 400 );
			}

			$site = WPCLOUD_Site::get_by_id( $site_id );

			if ( ! $site ) {
				return new WP_REST_Response( esc_html__( 'Site not found', 'wpcloud' ), 404 );
			}

			$view = new WPCLOUD_Metric_Data_View( site: $site_id, start: $start, end: $end );

			if ( ! method_exists( $view, $metric ) ) {
				// translators: %s: metric.
				return new WP_REST_Response( wp_sprintf( esc_html__( 'Invalid metric: %s', 'wpcloud' ), $metric ), 400 );
			}

			call_user_func( array( $view, $metric ), plot_view: true );

			if ( is_wp_error( $view->result ) ) {
				return new WP_REST_Response( $view->result->get_error_message(), 500 );
			}
			$response = array(
				'meta' => $view->meta,
				'map'  => $view->map,
				'data' => $view->data,
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
			);

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
