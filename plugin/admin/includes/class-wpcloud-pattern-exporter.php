<?php
/**
 * WP Cloud Pattern Exporter
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

/**
 * Class for exporting patterns from the WordPress editor to JSON files.
 * This only runs in development environments.
 */
class WPCloud_Pattern_Exporter {
	/**
	 * Constructor.
	 */
	public function __construct() {
		// Only run in development environments.
		if ( ! $this->is_development_environment() ) {
			return;
		}

		// Hook into various pattern save actions to ensure we catch all cases.
		add_action( 'rest_after_insert_wp_block', array( $this, 'export_pattern' ), 10, 3 );
		add_action( 'save_post_wp_block', array( $this, 'export_pattern_on_save' ), 10, 3 );
		add_action( 'wp_insert_post', array( $this, 'export_pattern_on_insert' ), 10, 3 );
		add_action( 'wp_after_insert_post', array( $this, 'export_pattern_on_insert' ), 10, 3 );
		add_action( 'post_updated', array( $this, 'export_pattern_on_update' ), 10, 3 );

		// Add a direct action for debugging.
		add_action( 'admin_init', array( $this, 'debug_patterns' ) );
	}

	/**
	 * Debug function to check for patterns and export them.
	 */
	public function debug_patterns() {
		// Get all pattern posts.
		$patterns = get_posts(
			array(
				'post_type'      => 'wp_block',
				'posts_per_page' => -1,
			)
		);

		// Export each pattern.
		foreach ( $patterns as $pattern ) {
			$this->do_export_pattern( $pattern );
		}

		// Add a notice about the export.
		if ( ! empty( $patterns ) ) {
			add_action(
				'admin_notices',
				function () use ( $patterns ) {
					?>
					<div class="notice notice-info is-dismissible">
						<p>
						<?php
						// translators: %d: Number of patterns exported.
						printf(
							esc_html__( 'Exported %d patterns to the plugin/patterns directory.', 'wpcloud' ),
							count( $patterns )
						);
						?>
						</p>
					</div>
					<?php
				}
			);
		}
	}

	/**
	 * Check if this is a development environment.
	 *
	 * @return bool Whether this is a development environment.
	 */
	private function is_development_environment() {
		// Logic to determine if this is a development environment.
		$is_debug   = defined( 'WP_DEBUG' ) && WP_DEBUG;
		$is_dev_env = defined( 'WP_ENVIRONMENT_TYPE' ) && 'development' === WP_ENVIRONMENT_TYPE;

		return ( $is_debug || $is_dev_env );
	}

	/**
	 * Export a pattern to a JSON file when saved via REST API.
	 *
	 * @param WP_Post         $post     The post object.
	 * @param WP_REST_Request $request  The request object.
	 * @param bool            $creating Whether the post is being created.
	 */
	public function export_pattern( $post, $request, $creating ) {
		// Only export wp_block post types.
		if ( 'wp_block' !== $post->post_type ) {
			return;
		}

		$this->do_export_pattern( $post );
	}

	/**
	 * Export a pattern to a JSON file when saved via standard WordPress save.
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The post object.
	 * @param bool    $update  Whether this is an update.
	 */
	public function export_pattern_on_save( $post_id, $post, $update ) {
		// Don't run on autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Don't run on post revisions.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Only export wp_block post types.
		if ( 'wp_block' !== $post->post_type ) {
			return;
		}

		$this->do_export_pattern( $post );
	}

	/**
	 * Export a pattern to a JSON file when a post is inserted.
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The post object.
	 * @param bool    $update  Whether this is an update.
	 */
	public function export_pattern_on_insert( $post_id, $post, $update ) {
		// Only export wp_block post types.
		if ( 'wp_block' !== $post->post_type ) {
			return;
		}

		$this->do_export_pattern( $post );
	}

	/**
	 * Export a pattern to a JSON file when a post is updated.
	 *
	 * @param int     $post_id      The post ID.
	 * @param WP_Post $post_after   The post object after the update.
	 * @param WP_Post $post_before  The post object before the update.
	 */
	public function export_pattern_on_update( $post_id, $post_after, $post_before ) {
		// Only export wp_block post types.
		if ( 'wp_block' !== $post_after->post_type ) {
			return;
		}

		$this->do_export_pattern( $post_after );
	}

	/**
	 * Common function to export a pattern to a JSON file.
	 *
	 * @param WP_Post $post The post object.
	 */
	private function do_export_pattern( $post ) {
		// Extract pattern data.
		$pattern_data = array(
			'__file'     => 'wp_block',
			'title'      => $post->post_title,
			'content'    => $post->post_content,
			'syncStatus' => '',
		);

		// Create patterns directory if it doesn't exist.
		$patterns_dir = plugin_dir_path( dirname( __DIR__ ) ) . 'patterns';

		if ( ! file_exists( $patterns_dir ) ) {
			mkdir( $patterns_dir, 0755, true );
		}

		// Generate filename from post title.
		$filename  = sanitize_title( $post->post_title );
		$file_path = $patterns_dir . '/' . $filename . '.json';

		// Write pattern data to file.
		$json_data    = json_encode( $pattern_data, JSON_PRETTY_PRINT );
		$write_result = file_put_contents( $file_path, $json_data );

		// Add admin notice to inform the user.
		add_action(
			'admin_notices',
			function () use ( $post, $filename, $file_path, $write_result ) {
				if ( false !== $write_result ) {
					?>
					<div class="notice notice-success is-dismissible">
						<p>
						<?php
						// translators: %1$s: Pattern title, %2$s: Filename.
						printf( esc_html__( 'Pattern "%1$s" exported to plugin/patterns/%2$s.json', 'wpcloud' ), esc_html( $post->post_title ), esc_html( $filename ) );
						?>
						</p>
					</div>
					<?php
				} else {
					?>
					<div class="notice notice-error is-dismissible">
						<p>
						<?php
						// translators: %1$s: Pattern title.
						printf( esc_html__( 'Failed to export pattern "%1$s". Check PHP error log for details.', 'wpcloud' ), esc_html( $post->post_title ) );
						?>
						</p>
					</div>
					<?php
				}
			}
		);
	}
}
