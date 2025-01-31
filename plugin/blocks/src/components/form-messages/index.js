/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	InnerBlocks
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import './editor.scss'

registerBlockType( metadata.name, {
	edit: () => {
		return (
			<>
				<details {...useBlockProps()}>
					<summary>{__('Click for form messages')}</summary>
					<InnerBlocks />
				</details>
			</>
		)

	},
	save: () => (
		<div {...useBlockProps.save()}>
			<InnerBlocks.Content />
		</div>
	)
} );