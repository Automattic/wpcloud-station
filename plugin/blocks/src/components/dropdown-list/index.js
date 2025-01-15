/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks
} from '@wordpress/block-editor';


function edit() {
	const blockProps = useBlockProps();
	const { children , ...innerBlocksProps } = useInnerBlocksProps( blockProps, {
		template: [ [ 'wpcloud/list-item' ] ],
		allowedBlocks: ['wpcloud/list-item'],
		templateLock: false
	});

	return(
		<ul {...innerBlocksProps} >
			{children}
		</ul>
	)

}

function save() {
	return (
		<ul  {...useBlockProps.save()}>
			<InnerBlocks.Content />
		</ul>
	);
}

/**
 * Internal dependencies
 */
import metadata from './block.json';

registerBlockType(metadata.name, { edit, save } );
