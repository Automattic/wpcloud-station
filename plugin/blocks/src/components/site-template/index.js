/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';


/**
 * Internal dependencies
 */

import metadata from './block.json';
import edit from './edit';


registerBlockType( metadata.name, {
	/**
	 * @see ./edit.js
	 */
	edit: edit,

	/**
	 * @see ./save.js
	 */
	save: () => <InnerBlocks.Content />
} );
