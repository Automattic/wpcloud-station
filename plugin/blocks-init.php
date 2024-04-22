<?php
/**
 * Plugin Name:       Example Static
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       example-static
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function wpcloud_include_blocks() {

	foreach( glob( __DIR__ . '/blocks/src/*' ) as $block_directory ) {
		if ( 'components' === basename( $block_directory ) ) {
			continue;
		}

		try {
			register_block_type(
				dirname( __FILE__ ) . '/blocks/build/' . basename( $block_directory )
			);
		} catch ( Exception $e ) {
			error_log( 'Error registering block: ' . $e->getMessage() );
		}

		if ( is_file( $block_directory . '/index.php' ) ) {
			// If we need any server code, include a blocks/src/{block}/index.php file.
			require_once $block_directory . '/index.php';
		}
	}
}
add_action( 'init', 'wpcloud_include_blocks' );

add_filter( 'block_categories_all' , function( $categories ) {

  // Adding a new category.
	$categories[] = array(
		'slug'  => 'wpcloud',
		'title' => 'WP Cloud'
	);

	return $categories;
} );
