/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';



/**
 * Internal dependencies
 */
import Edit from './edit';
import metadata from './block.json';
import save from './save';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType( metadata.name, {
	edit: Edit,
	save,
} );
