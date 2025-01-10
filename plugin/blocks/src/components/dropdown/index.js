/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import edit from './edit';
//import save from './save';
import metadata from './block.json';

registerBlockType( metadata.name, {
	/**
	 * @see ./edit.js
	 */
	edit,
	save: ({ attributes }) => (
		<details {...useBlockProps.save()}>
			<summary><RichText.Content tagName={attributes.tag} value={attributes.title} /></summary>
			<InnerBlocks.Content />
		</details>
	)
} );
