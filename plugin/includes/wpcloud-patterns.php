<?php
/**
 * WP Cloud Patterns
 *
 * @package wpcloud-client
 */

declare( strict_types = 1 );

/**
 * Register WP Cloud patterns.
 * This function can be called from the theme's functions.php file.
 *
 * @param bool $override Whether to override existing patterns.
 *                      Default is false.
 *                      If true, existing patterns will be re-registered.
 */
function wpcloud_register_patterns(): void {

	$patterns_dir = plugin_dir_path( __DIR__ ) . 'patterns';

	// Check if patterns directory exists.
	if ( ! file_exists( $patterns_dir ) || ! is_dir( $patterns_dir ) ) {
		return;
	}

	// Get all JSON files in the patterns directory.
	$pattern_files = glob( $patterns_dir . '/*.json' );
	if ( empty( $pattern_files ) ) {
		return;
	}

	// Make sure the pattern category exists.
	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category(
			'wpcloud',
			array(
				'label' => __( 'WP Cloud', 'wpcloud' ),
			)
		);
	}

	foreach ( $pattern_files as $pattern_file ) {
		$pattern_content = file_get_contents( $pattern_file );
		if ( ! $pattern_content ) {
			continue;
		}

		$pattern_data = json_decode( $pattern_content, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			continue;
		}

		// Extract pattern name from filename (without extension).
		$pattern_name  = pathinfo( $pattern_file, PATHINFO_FILENAME );
		$pattern_title = $pattern_data['title'] ?? $pattern_name;
		$pattern_slug  = $pattern_data['slug'] ?? 'wpcloud-station/' . $pattern_name;

		// Only register if the function exists.
		if ( function_exists( 'register_block_pattern' ) ) {
			register_block_pattern(
				$pattern_slug,
				array(
					'title'      => $pattern_title,
					'content'    => $pattern_data['content'],
					'categories' => array( 'wpcloud' ),
				)
			);
		}
	}
}
