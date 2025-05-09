<?php
/**
 * WP Cloud Pattern Updater
 *
 * @package wpcloud-station
 */

declare( strict_types = 1 );

/**
 * Class for updating patterns from JSON files.
 */
class WPCloud_Pattern_Updater {

	/**
	 * Update patterns from JSON files in the plugin/patterns directory.
	 *
	 * @param bool $force Force update all patterns, even if they haven't changed.
	 * @return array Results of the update operation.
	 */
	public function update_patterns( bool $force = false ): array {
		$results = array(
			'updated' => array(),
			'created' => array(),
			'skipped' => array(),
			'errors'  => array(),
			'total'   => 0,
		);

		$patterns_dir = plugin_dir_path( __DIR__ ) . 'patterns';

		// Check if patterns directory exists.
		if ( ! file_exists( $patterns_dir ) || ! is_dir( $patterns_dir ) ) {
			$results['errors'][] = 'Patterns directory not found: ' . $patterns_dir;
			return $results;
		}

		// Get all JSON files in the patterns directory.
		$pattern_files = glob( $patterns_dir . '/*.json' );
		if ( empty( $pattern_files ) ) {
			$results['errors'][] = 'No pattern files found in: ' . $patterns_dir;
			return $results;
		}

		$results['total'] = count( $pattern_files );

		foreach ( $pattern_files as $pattern_file ) {
			$pattern_filename = basename( $pattern_file );
			$pattern_name     = pathinfo( $pattern_file, PATHINFO_FILENAME );

			// Read and parse the pattern file.
			$pattern_content = file_get_contents( $pattern_file );
			if ( ! $pattern_content ) {
				$results['errors'][] = 'Failed to read pattern file: ' . $pattern_filename;
				continue;
			}

			$pattern_data = json_decode( $pattern_content, true );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				$results['errors'][] = 'Failed to parse pattern JSON: ' . $pattern_filename;
				continue;
			}

			// Check if the pattern exists.
			$existing_pattern = $this->get_pattern_by_name( $pattern_name );

			if ( $existing_pattern ) {
				// Pattern exists, check if it needs to be updated.
				if ( $force || $this->pattern_needs_update( $existing_pattern, $pattern_data ) ) {
					$update_result = $this->update_existing_pattern( $existing_pattern, $pattern_data );
					if ( is_wp_error( $update_result ) ) {
						$results['errors'][] = $update_result->get_error_message();
					} else {
						$results['updated'][] = $pattern_name;
					}
				} else {
					$results['skipped'][] = $pattern_name;
				}
			} else {
				// Pattern doesn't exist, create it.
				$create_result = $this->create_new_pattern( $pattern_name, $pattern_data );
				if ( is_wp_error( $create_result ) ) {
					$results['errors'][] = $create_result->get_error_message();
				} else {
					$results['created'][] = $pattern_name;
				}
			}
		}

		return $results;
	}

	/**
	 * Get a pattern by name.
	 *
	 * @param string $pattern_name The pattern name.
	 * @return WP_Post|null The pattern post or null if not found.
	 */
	private function get_pattern_by_name( string $pattern_name ) {
		$args = array(
			'post_type'      => 'wp_block',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'name'           => $pattern_name,
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			return $query->posts[0];
		}

		// Also try by title in case the slug doesn't match.
		$args = array(
			'post_type'      => 'wp_block',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'title'          => $pattern_name,
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			return $query->posts[0];
		}

		return null;
	}

	/**
	 * Check if a pattern needs to be updated.
	 *
	 * @param WP_Post $existing_pattern The existing pattern post.
	 * @param array   $pattern_data     The pattern data from the JSON file.
	 * @return bool Whether the pattern needs to be updated.
	 */
	private function pattern_needs_update( $existing_pattern, array $pattern_data ): bool {
		// Check if the content has changed.
		if ( $existing_pattern->post_content !== $pattern_data['content'] ) {
			return true;
		}

		// Check if the title has changed.
		if ( $existing_pattern->post_title !== $pattern_data['title'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Update an existing pattern.
	 *
	 * @param WP_Post $existing_pattern The existing pattern post.
	 * @param array   $pattern_data     The pattern data from the JSON file.
	 * @return int|WP_Error The post ID on success, WP_Error on failure.
	 */
	private function update_existing_pattern( $existing_pattern, array $pattern_data ) {
		$post_data = array(
			'ID'           => $existing_pattern->ID,
			'post_title'   => $pattern_data['title'],
			'post_content' => $pattern_data['content'],
		);

		return wp_update_post( $post_data, true );
	}

	/**
	 * Create a new pattern.
	 *
	 * @param string $pattern_name The pattern name.
	 * @param array  $pattern_data The pattern data from the JSON file.
	 * @return int|WP_Error The post ID on success, WP_Error on failure.
	 */
	private function create_new_pattern( string $pattern_name, array $pattern_data ) {
		$post_data = array(
			'post_title'   => $pattern_data['title'] ?? $pattern_name,
			'post_content' => $pattern_data['content'] ?? '',
			'post_status'  => 'publish',
			'post_type'    => 'wp_block',
			'post_name'    => $pattern_name,
		);

		return wp_insert_post( $post_data, true );
	}
}
