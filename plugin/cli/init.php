<?php
/**
 * WP Cloud CLI
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

require_once __DIR__ . '/class-wpcloud-cli-client.php';
require_once __DIR__ . '/class-wpcloud-cli-client-meta.php';

require_once __DIR__ . '/class-wpcloud-cli-job.php';

require_once __DIR__ . '/class-wpcloud-cli-metrics.php';

require_once __DIR__ . '/class-wpcloud-cli-site.php';
require_once __DIR__ . '/class-wpcloud-cli-site-domain.php';
require_once __DIR__ . '/class-wpcloud-cli-site-ssh-user.php';

require_once __DIR__ . '/class-wpcloud-cli-station.php';
require_once __DIR__ . '/class-wpcloud-cli-station-user.php';


add_action(
	'cli_init',
	function () {
		WP_CLI::add_command( 'cloud client', 'WPCloud_CLI_Client' );
		WP_CLI::add_command( 'cloud client meta', 'WPCloud_CLI_Client_Meta' );
		WP_CLI::add_command( 'cloud job', 'WPCloud_CLI_Job' );
		WP_CLI::add_command( 'cloud metrics', 'WPCloud_CLI_Metrics' );
		WP_CLI::add_command( 'cloud site', 'WPCloud_CLI_Site' );
		WP_CLI::add_command( 'cloud site domain', 'WPCloud_CLI_Site_Domain' );
		WP_CLI::add_command( 'cloud site ssh-user', 'WPCloud_CLI_Site_SSH_User' );
		WP_CLI::add_command( 'cloud station setup', 'WPCloud_CLI_Station' );
		WP_CLI::add_command( 'cloud station user', 'WPCloud_CLI_Station_User' );
	}
);
