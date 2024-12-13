<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli.php';

/**
 * WP Cloud CLI Job
 */
class WPCLoud_CLI_Job extends WPCloud_CLI {

	/**
	 * List all jobs.
	 *
	 * @param array $args     The arguments.
	 */
	public function __invoke( $args ) {
		$job_id = $args[0] ?? 0;
		if ( ! $job_id ) {
			WP_CLI::error( 'Please provide a job id.' );
		}

		$this->api()->job_status( $job_id )->log();
	}
}
