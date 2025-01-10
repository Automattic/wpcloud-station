/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps, } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
//import save from './save';
import metadata from './block.json';

registerBlockType( metadata.name, {
	/**
	 * @see ./edit.js
	 */
	edit,
	save: () => (
		<li {...useBlockProps.save()}>
			<InnerBlocks.Content />
		</li>
		)

} );
