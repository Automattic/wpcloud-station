/** External dependencies
 *
 */
import classnames from 'classnames';


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
	save: ({attributes}) => {
		const blockProps = useBlockProps.save();
		const { hideChevron } = attributes;
		return (
			<details {...blockProps} className={ classnames('dropdown',
				blockProps.className,
				{
					'hide-chevron': hideChevron,
				 }
			)}>
				<InnerBlocks.Content />
			</details>
		)
	}
} );
