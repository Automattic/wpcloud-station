<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli.php';

/**
 * WP Cloud CLI Site Domain
 */
class WPCloud_CLI_Site_Domain extends WPCloud_CLI_Site {

	/**
	 * The domain name.
	 *
	 * @var string
	 */
	protected $domain_name;


	/**
	 * Get a domain.
	 *
	 * @param array $args The arguments.
	 */
	public function get( array $args ): void {
		$this->set_site_id( $args );

		$details = $this->api()->site_details( $this->site_id )->result;
		if ( is_wp_error( $details ) ) {
			WP_CLI::error( $details->get_error_message() );
		}

		self::log( $details->domain_name );
	}

	/**
	 * Add a domain.
	 *
	 * @param array $args The arguments.
	 */
	public function add( $args ) {
		$this->set_site_id( $args );
		$this->set_domain_name( $args );

		$this->api()
			->site_domain_alias_add( $this->site_id, $this->domain_name )
			->log( success: 'Domain added' );
	}

	/**
	 * Remove a domain.
	 *
	 * @param array $args The arguments.
	 */
	public function remove( $args ) {
		$this->set_site_id( $args );
		$this->set_domain_name( $args );

		$this->api()
			->site_domain_alias_remove( $this->site_id, $this->domain_name )
			->log( success: 'Domain deleted' );
	}

	/**
	 * Make a domain primary.
	 *
	 * @param array $args The arguments.
	 * @param array $switches The switches.
	 */
	public function make_primary( $args, $switches = array() ) {
		$this->set_site_id( $args );
		$this->set_domain_name( $args );
		$keep = $switches['keep'] ?? false;

		$this->api()
			->site_domain_primary_set( $this->site_id, $this->domain_name, $keep )
			->log( success: 'Primary domain set' );
	}

	/**
	 * Get the site ips.
	 *
	 * @param array $args The arguments.
	 */
	public function ip( $args ) {
		$this->api()->site_ip_addresses( $args )->log();
	}

	/**
	 * Validate a domain.
	 *
	 * @param array $args The arguments.
	 */
	public function validate( $args ) {
		$this->set_site_id( $args );
		$this->set_domain_name( $args );
		$this->api()->site_domain_validate( $this->site_id, $this->domain_name )->log();
	}

	/**
	 * Retry a domain.
	 *
	 * @param array $args The arguments.
	 */
	public function retry_ssl( $args ) {
		$this->set_site_id( $args );
		$this->set_domain_name( $args );
		$this->api()
			->site_ssl_retry( $this->site_id, $this->domain_name )
			->log( success: 'SSL retry initiated' );
	}

	/**
	 * Set domain name.
	 *
	 * @param array $args The arguments.
	 */
	protected function set_domain_name( $args ) {
		$this->domain_name = $args[1] ?? '';

		if ( ! $this->domain_name ) {
			WP_CLI::error( 'Please provide a domain' );
		}
	}
}
