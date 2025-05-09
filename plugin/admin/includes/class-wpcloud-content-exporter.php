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
	 * Debug function to check for templates and template parts and export them.
	 */
	private function debug_templates() {
		// Get all template posts.
		$templates = get_posts(
			array(
				'post_type'      => 'wp_template',
				'posts_per_page' => -1,
			)
		);

		// Get all template part posts.
		$template_parts = get_posts(
			array(
				'post_type'      => 'wp_template_part',
				'posts_per_page' => -1,
			)
		);

		// Combine templates and template parts.
		$all_templates = array_merge( $templates, $template_parts );
		$this->debug_log( 'Found templates and parts', count( $all_templates ) );

		// Export each template/part.
		$exported_count = 0;
		foreach ( $all_templates as $template ) {
			$this->debug_log( 'Processing template/part', $template->post_type . ' - ' . $template->post_name );
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
							esc_html__( 'Exported %d templates/parts to the theme directory.', 'wpcloud' ),
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

		// Handle template parts.
		if ( 'wp_template_part' === $post->post_type ) {
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

		// Handle template parts.
		if ( 'wp_template_part' === $post_after->post_type ) {
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
			'slug'       => 'wpcloud-station/' . sanitize_title( $post->post_title ),
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
	 * Log debug information to the error log.
	 *
	 * @param string $message The message to log.
	 * @param mixed  $data    Optional data to include in the log.
	 */
	private function debug_log( $message, $data = null ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$log_message = '[WPCloud Content Exporter] ' . $message;
			if ( null !== $data ) {
				$log_message .= ': ' . print_r( $data, true );
			}
			error_log( $log_message );
		}
	}

	/**
	 * Get the mapped theme directory name.
	 *
	 * This handles the mapping between theme names in the database and actual directory names
	 * on the local file system, which can be different when using Docker/wp-env.
	 *
	 * @param string $theme_name The theme name from the database.
	 * @return string The mapped directory name.
	 */
	private function get_mapped_theme_dir( $theme_name ) {
		// Define mappings between theme names in DB and local directory names.
		$theme_mappings = array(
			'wpcloud-station'        => 'theme',
			'wpcloud-station-plugin' => 'theme-pico',
		);

		$this->debug_log( 'Original theme name', $theme_name );

		// Check if we have a mapping for this theme.
		if ( isset( $theme_mappings[ $theme_name ] ) ) {
			$mapped_name = $theme_mappings[ $theme_name ];
			$this->debug_log( 'Mapped to directory', $mapped_name );
			return $mapped_name;
		}

		// No mapping found, return the original name.
		$this->debug_log( 'No mapping found, using original name' );
		return $theme_name;
	}

	/**
	 * Get all post meta for debugging.
	 *
	 * @param int $post_id The post ID.
	 * @return array The post meta.
	 */
	private function get_all_post_meta( $post_id ) {
		global $wpdb;
		$meta       = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", $post_id ) );
		$meta_array = array();
		foreach ( $meta as $row ) {
			$meta_array[ $row->meta_key ] = $row->meta_value;
		}
		return $meta_array;
	}

	/**
	 * Common function to export a template to an HTML file.
	 *
	 * @param WP_Post $post The post object.
	 * @return bool Whether the template was exported successfully.
	 */
	private function do_export_template( $post ) {
		$this->debug_log( 'Starting template export for post ID', $post->ID );
		$this->debug_log( 'Post type', $post->post_type );
		$this->debug_log( 'Post name', $post->post_name );
		$this->debug_log( 'Post title', $post->post_title );

		// Dump all post meta for debugging.
		$all_meta = $this->get_all_post_meta( $post->ID );
		$this->debug_log( 'All post meta', $all_meta );

		// Try different approaches to get the template slug.
		$template_slug = get_post_meta( $post->ID, '_wp_template_slug', true );
		if ( empty( $template_slug ) ) {
			$template_slug = get_post_meta( $post->ID, 'wp_template_slug', true );
		}
		if ( empty( $template_slug ) ) {
			$template_slug = get_post_meta( $post->ID, 'slug', true );
		}
		if ( empty( $template_slug ) ) {
			// Try to infer from post name.
			$template_slug = $post->post_name;
		}

		// Try different approaches to get the theme.
		$template_theme = get_post_meta( $post->ID, 'theme', true );
		if ( empty( $template_theme ) ) {
			$template_theme = get_post_meta( $post->ID, '_wp_theme', true );
		}
		if ( empty( $template_theme ) ) {
			// Default to the active theme if we can't find it.
			$template_theme = wp_get_theme()->get_stylesheet();
		}

		$this->debug_log( 'Final template slug', $template_slug );
		$this->debug_log( 'Final template theme', $template_theme );

		// If no slug, we can't export.
		if ( empty( $template_slug ) ) {
			$this->debug_log( 'Cannot export template: missing slug' );
			return false;
		}

		// If no theme, use a default.
		if ( empty( $template_theme ) ) {
			$template_theme = 'wpcloud-station'; // Default to main theme
			$this->debug_log( 'Using default theme', $template_theme );
		}

		// Check if the template is a template part.
		$is_template_part = false;
		if ( 'wp_template_part' === $post->post_type ) {
			$is_template_part = true;
			$this->debug_log( 'This is a template part' );
		}

		// Get the mapped theme directory name.
		$mapped_theme_dir = $this->get_mapped_theme_dir( $template_theme );

		// Check if the theme directory exists locally.
		$theme_dir = WP_CONTENT_DIR . '/themes/' . $mapped_theme_dir;
		$this->debug_log( 'Checking theme directory at', $theme_dir );

		if ( ! file_exists( $theme_dir ) ) {
			$this->debug_log( 'Theme directory not found at WP_CONTENT_DIR, checking project directory' );
			// Also check in our project directory.
			$theme_dir = dirname( plugin_dir_path( __DIR__ ), 2 ) . '/' . $mapped_theme_dir;
			$this->debug_log( 'Checking theme directory at', $theme_dir );

			if ( ! file_exists( $theme_dir ) ) {
				// Try with the original theme name as a last resort.
				$theme_dir = dirname( plugin_dir_path( __DIR__ ), 2 ) . '/' . $template_theme;
				$this->debug_log( 'Trying with original theme name at', $theme_dir );

				if ( ! file_exists( $theme_dir ) ) {
					// Theme doesn't exist locally, so we can't export.
					$this->debug_log( 'Theme directory not found in any location. Cannot export.' );
					return false;
				}
			}
		}

		// Determine the correct directory (templates or parts).
		if ( $is_template_part ) {
			$export_dir = $theme_dir . '/parts';
			$this->debug_log( 'Using parts directory for template part' );
		} else {
			$export_dir = $theme_dir . '/templates';
			$this->debug_log( 'Using templates directory for template' );
		}

		$this->debug_log( 'Export directory', $export_dir );

		// Create export directory if it doesn't exist.
		if ( ! file_exists( $export_dir ) ) {
			$this->debug_log( 'Export directory does not exist, creating it' );
			$mkdir_result = mkdir( $export_dir, 0755, true );
			$this->debug_log( 'mkdir result', $mkdir_result ? 'success' : 'failed' );

			if ( ! $mkdir_result ) {
				$this->debug_log( 'Failed to create export directory. Error', error_get_last() );
				return false;
			}
		}

		// Generate filename.
		$file_path = $export_dir . '/' . $template_slug . '.html';
		$this->debug_log( 'File path for export', $file_path );

		// Write template content to file.
		$this->debug_log( 'Writing content to file' );
		$write_result = file_put_contents( $file_path, $post->post_content );
		$this->debug_log( 'file_put_contents result', $write_result ? 'success (' . $write_result . ' bytes)' : 'failed' );

		if ( false === $write_result ) {
			$this->debug_log( 'Failed to write file. Error', error_get_last() );
		}

		// Add admin notice to inform the user.
		if ( false !== $write_result ) {
			$this->debug_log( 'Export successful' );
			add_action(
				'admin_notices',
				function () use ( $post, $template_slug, $template_theme, $file_path, $is_template_part ) {
					?>
					<div class="notice notice-success is-dismissible">
						<p>
						<?php
						if ( $is_template_part ) {
							// translators: %1$s: Template slug, %2$s: Theme name.
							printf(
								esc_html__( 'Template part "%1$s" exported to %2$s/parts/%1$s.html', 'wpcloud' ),
								esc_html( $template_slug ),
								esc_html( $template_theme )
							);
						} else {
							// translators: %1$s: Template slug, %2$s: Theme name.
							printf(
								esc_html__( 'Template "%1$s" exported to %2$s/templates/%1$s.html', 'wpcloud' ),
								esc_html( $template_slug ),
								esc_html( $template_theme )
							);
						}
						?>
						</p>
					</div>
					<?php
				}
			);
		} else {
			$this->debug_log( 'Export failed' );
			add_action(
				'admin_notices',
				function () use ( $post, $template_slug, $template_theme, $is_template_part ) {
					?>
					<div class="notice notice-error is-dismissible">
						<p>
						<?php
						if ( $is_template_part ) {
							// translators: %1$s: Template slug, %2$s: Theme name.
							printf(
								esc_html__( 'Failed to export template part "%1$s" for theme "%2$s". Check PHP error log for details.', 'wpcloud' ),
								esc_html( $template_slug ),
								esc_html( $template_theme )
							);
						} else {
							// translators: %1$s: Template slug, %2$s: Theme name.
							printf(
								esc_html__( 'Failed to export template "%1$s" for theme "%2$s". Check PHP error log for details.', 'wpcloud' ),
								esc_html( $template_slug ),
								esc_html( $template_theme )
							);
						}
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
