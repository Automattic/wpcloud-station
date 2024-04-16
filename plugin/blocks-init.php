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

function wpcdb_include_blocks() {
	foreach( glob( __DIR__ . '/blocks/src/*/index.php' ) as $block ) {
		require_once $block;
	}
}

add_action( 'init', 'wpcdb_include_blocks', 9 );

//require_once __DIR__ . '/blocks/src/form/index.php';