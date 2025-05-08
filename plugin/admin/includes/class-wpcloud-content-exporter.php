<?php
/**
 * WP Cloud Content Exporter
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

/**
 * Class for exporting patterns and templates from the WordPress editor to files.
 * This only runs in development environments.
 */
class WPCloud_Content_Exporter {
	/**
	 * Constructor.
	 */
	public function __construct() {
		// Only run in development environments.
		if ( ! $this->is_development_environment() ) {
			return;
		}

		// Hook into pattern save actions.
		add_action( 'rest_after_insert_wp_block', array( $this, 'export_pattern' ), 10, 3 );
		add_action( 'save_post_wp_block', array( $this, 'export_pattern_on_save' ), 10, 3 );
		add_action( 'wp_insert_post', array( $this, 'export_content_on_insert' ), 10, 3 );
		add_action( 'wp_after_insert_post', array( $this, 'export_content_on_insert' ), 10, 3 );
		add_action( 'post_updated', array( $this, 'export_content_on_update' ), 10, 3 );

		// Add a direct action for debugging.
		add_action( 'admin_init', array( $this, 'debug_content' ) );
	}

	/**
	 * Debug function to check for patterns and templates and export them.
	 */
	public function debug_content() {
		// Export patterns.
		$this->debug_patterns();

		// Export templates.
		$this->debug_templates();
	}

	/**
	 * Debug function to check for patterns and export them.
	 */
	private function debug_patterns() {
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
	 * Debug function to check for templates and export them.
	 */
	private function debug_templates() {
		// Get all template posts.
		$templates = get_posts(
			array(
				'post_type'      => 'wp_template',
				'posts_per_page' => -1,
			)
		);

		// Export each template.
		$exported_count = 0;
		foreach ( $templates as $template ) {
			if ( $this->do_export_template( $template ) ) {
				++$exported_count;
			}
		}

		// Add a notice about the export.
		if ( $exported_count > 0 ) {
			add_action(
				'admin_notices',
				function () use ( $exported_count ) {
					?>
					<div class="notice notice-info is-dismissible">
						<p>
						<?php
						// translators: %d: Number of templates exported.
						printf(
							esc_html__( 'Exported %d templates to the theme directory.', 'wpcloud' ),
							$exported_count
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
	 * Check if the content exporter should be enabled.
	 *
	 * @return bool Whether the content exporter should be enabled.
	 */
	private function is_development_environment() {
		// Check if the content exporter is enabled in settings.
		$options             = get_option( 'wpcloud_settings', array() );
		$enabled_in_settings = isset( $options['enable_content_exporter'] ) && $options['enable_content_exporter'];

		// Logic to determine if this is a development environment.
		$is_debug   = defined( 'WP_DEBUG' ) && WP_DEBUG;
		$is_dev_env = defined( 'WP_ENVIRONMENT_TYPE' ) && 'development' === WP_ENVIRONMENT_TYPE;
		$is_dev     = ( $is_debug || $is_dev_env );

		// The content exporter should be enabled if it's enabled in settings AND we're in a development environment.
		return $enabled_in_settings && $is_dev;
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
	 * Export content to a file when a post is inserted.
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The post object.
	 * @param bool    $update  Whether this is an update.
	 */
	public function export_content_on_insert( $post_id, $post, $update ) {
		// Handle patterns.
		if ( 'wp_block' === $post->post_type ) {
			$this->do_export_pattern( $post );
			return;
		}

		// Handle templates.
		if ( 'wp_template' === $post->post_type ) {
			$this->do_export_template( $post );
			return;
		}
	}

	/**
	 * Export content to a file when a post is updated.
	 *
	 * @param int     $post_id      The post ID.
	 * @param WP_Post $post_after   The post object after the update.
	 * @param WP_Post $post_before  The post object before the update.
	 */
	public function export_content_on_update( $post_id, $post_after, $post_before ) {
		// Handle patterns.
		if ( 'wp_block' === $post_after->post_type ) {
			$this->do_export_pattern( $post_after );
			return;
		}

		// Handle templates.
		if ( 'wp_template' === $post_after->post_type ) {
			$this->do_export_template( $post_after );
			return;
		}
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

		return false !== $write_result;
	}

	/**
	 * Common function to export a template to an HTML file.
	 *
	 * @param WP_Post $post The post object.
	 * @return bool Whether the template was exported successfully.
	 */
	private function do_export_template( $post ) {
		// Get the template slug and theme.
		$template_slug  = get_post_meta( $post->ID, '_wp_template_slug', true );
		$template_theme = get_post_meta( $post->ID, 'theme', true );

		// If no slug or theme, we can't export.
		if ( empty( $template_slug ) || empty( $template_theme ) ) {
			return false;
		}

		// Check if the theme directory exists locally.
		$theme_dir = WP_CONTENT_DIR . '/themes/' . $template_theme;
		if ( ! file_exists( $theme_dir ) ) {
			// Also check in our project directory.
			$theme_dir = dirname( plugin_dir_path( __DIR__ ), 2 ) . '/' . $template_theme;
			if ( ! file_exists( $theme_dir ) ) {
				// Theme doesn't exist locally, so we can't export.
				return false;
			}
		}

		// Create templates directory if it doesn't exist.
		$templates_dir = $theme_dir . '/templates';
		if ( ! file_exists( $templates_dir ) ) {
			mkdir( $templates_dir, 0755, true );
		}

		// Generate filename.
		$file_path = $templates_dir . '/' . $template_slug . '.html';

		// Write template content to file.
		$write_result = file_put_contents( $file_path, $post->post_content );

		// Add admin notice to inform the user.
		if ( false !== $write_result ) {
			add_action(
				'admin_notices',
				function () use ( $post, $template_slug, $template_theme, $file_path ) {
					?>
					<div class="notice notice-success is-dismissible">
						<p>
						<?php
						// translators: %1$s: Template slug, %2$s: Theme name, %3$s: File path.
						printf(
							esc_html__( 'Template "%1$s" exported to %2$s/templates/%1$s.html', 'wpcloud' ),
							esc_html( $template_slug ),
							esc_html( $template_theme )
						);
						?>
						</p>
					</div>
					<?php
				}
			);
		} else {
			add_action(
				'admin_notices',
				function () use ( $post, $template_slug, $template_theme ) {
					?>
					<div class="notice notice-error is-dismissible">
						<p>
						<?php
						// translators: %1$s: Template slug, %2$s: Theme name.
						printf(
							esc_html__( 'Failed to export template "%1$s" for theme "%2$s". Check PHP error log for details.', 'wpcloud' ),
							esc_html( $template_slug ),
							esc_html( $template_theme )
						);
						?>
						</p>
					</div>
					<?php
				}
			);
		}

		return false !== $write_result;
	}
}
