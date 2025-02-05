/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps  } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import metadata from './block.json';

const template = [
	['core/group',
		{
			className: 'wpcloud-metrics',
			metadata: {
				name: 'Graphs',
			},
			layout: {
				type: "grid",
				columnCount: 2,
				minimumColumnWidth: null
			},
		},
		[],
	],
];

registerBlockType( metadata.name, {
	edit: () => <InnerBlocks template={template} />,
	save: ({ attributes }) => <div {...useBlockProps.save()} id={attributes.id || 'metrics' }><InnerBlocks.Content /></div>
} );