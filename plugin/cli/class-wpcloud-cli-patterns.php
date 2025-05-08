<?php
/**
 * WP Cloud CLI Patterns
 *
 * @package wpcloud
 */

declare( strict_types = 1 );

/**
 * WP Cloud CLI Patterns
 */
class WPCloud_CLI_Patterns extends WP_CLI_Command {

	/**
	 * Update patterns from JSON files in the plugin/patterns directory.
	 *
	 * ## OPTIONS
	 *
	 * [--force]
	 * : Force update all patterns, even if they haven't changed.
	 *
	 * ## EXAMPLES
	 *
	 * wp wpcloud patterns update
	 * wp wpcloud patterns update --force
	 *
	 * @param array $args       The arguments.
	 * @param array $assoc_args The associative arguments.
	 */
	public function update( $args, $assoc_args = array() ) {
		$force = isset( $assoc_args['force'] );

		WP_CLI::log( 'Updating patterns from JSON files...' );

		// Load the pattern updater.
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-wpcloud-pattern-updater.php';
		$updater = new WPCloud_Pattern_Updater();

		// Update patterns.
		$results = $updater->update_patterns( $force );

		// Display results.
		WP_CLI::log( sprintf( 'Found %d pattern files.', $results['total'] ) );

		if ( ! empty( $results['updated'] ) ) {
			WP_CLI::success( sprintf( 'Updated %d patterns:', count( $results['updated'] ) ) );
			foreach ( $results['updated'] as $pattern_name ) {
				WP_CLI::log( ' - ' . $pattern_name );
			}
		}

		if ( ! empty( $results['created'] ) ) {
			WP_CLI::success( sprintf( 'Created %d patterns:', count( $results['created'] ) ) );
			foreach ( $results['created'] as $pattern_name ) {
				WP_CLI::log( ' - ' . $pattern_name );
			}
		}

		if ( ! empty( $results['skipped'] ) ) {
			WP_CLI::log( sprintf( 'Skipped %d patterns (no changes):', count( $results['skipped'] ) ) );
			foreach ( $results['skipped'] as $pattern_name ) {
				WP_CLI::log( ' - ' . $pattern_name );
			}
		}

		if ( ! empty( $results['errors'] ) ) {
			WP_CLI::warning( sprintf( 'Encountered %d errors:', count( $results['errors'] ) ) );
			foreach ( $results['errors'] as $error ) {
				WP_CLI::log( ' - ' . $error );
			}
		}

		WP_CLI::success( 'Pattern update complete.' );
	}
}

WP_CLI::add_command( 'wpcloud patterns', 'WPCloud_CLI_Patterns' );
