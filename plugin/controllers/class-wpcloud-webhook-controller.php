<?php
/**
 * WP Cloud Webhook Controller.
 *
 * @package wpcloud-dashboard
 */

 declare( strict_types = 1 );

if ( ! class_exists( 'WPCLOUD_Webhook_Controller' ) ) {

	class WPCLOUD_Webhook_Controller extends WP_REST_Controller {

		/**
		 * The namespace.
		 *
		 * @var string
		 */
		protected $namespace = 'wpcloud/v1';

		/**
		 * Rest base for the current object.
		 *
		 * @var string
		 */
		protected $rest_base = 'webhook';

		/**
		 * Register the routes.
		 */
		public function register_routes() {
			register_rest_route(
				$this->namespace, '/' . $this->rest_base,
				array(
					array(
						'methods'  => WP_REST_Server::CREATABLE,
						'callback' => array( $this, 'post_webhook' ),
						'permission_callback' => '__return_true',
					),
				)
			);
		}
		/**
		 * Post a webhook.
		 *
		 * @param WP_REST_Request $request
		 *
		 * @return WP_Error|WP_REST_Response
		 */
		public function post_webhook( $request ) {
			$params          = $request->get_params();
			$event           = str_replace( '-', '_', $params['event'] );
			$timestamp       = $params['timestamp'];
			$wpcloud_site_id = $params['atomic_site_id'];
			$data            = $params['data'] ?? array();

			/**
			 * General action for events.
			 *
			 * @param string $event           The event name.
			 * @param int    $timestamp       The timestamp of the event in unix milliseconds.
			 * @param int    $wpcloud_site_id The WP Cloud Site Id.
			 * @param array  $data            An array of data sent with the event.
			 */
			do_action( "wpcloud_webhook", $event, $timestamp, $wpcloud_site_id, $data );

			/**
			 * Specific action for events (e.g. `site_provisioned`, `on-demand-backup`).
			 *
			 * @param int    $timestamp       The timestamp of the event in unix milliseconds.
			 * @param int    $wpcloud_site_id The WP Cloud Site Id.
			 * @param array  $data            An array of data sent with the event.
			 */
			do_action( "wpcloud_webhook_$event", $timestamp, $wpcloud_site_id, $data );

			return new WP_REST_Response( null, WP_Http::NO_CONTENT );
		}

	}

}
