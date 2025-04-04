<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/../includes/wpcloud-client.php';

/**
 * WP Cloud CLI Api
 */
class WPCLOUD_CLI_Api_Client {

	/**
	 * The logger.
	 *
	 * @var callable|null
	 */
	private $logger;

	/**
	 * The result.
	 *
	 * @var mixed
	 */
	public $result;

	/**
	 * Constructor.
	 *
	 * @param callable|null $logger The logger.
	 */
	public function __construct( ?callable $logger ) {
		$this->logger = $logger;
	}

	/**
	 * Call logger with the result
	 *
	 * @param string $success The success message to log.
	 */
	public function log( string $success = '' ): void {
		if ( $this->logger ) {
			( $this->logger )( $this->result, $success );
		}
	}

	/**
	 * Call wpcloud client function.
	 *
	 * @param string $name The name of the function.
	 * @param array  $arguments The arguments.
	 *
	 * @return mixed The result.
	 */
	public function __call( string $name, array $arguments ): mixed {
		$this->result = call_user_func_array( 'wpcloud_client_' . $name, $arguments );
		return $this;
	}
}
