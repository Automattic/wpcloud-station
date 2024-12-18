/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import metadata from './block.json';

const template = [
	['core/group',
		{
			className: 'wpcloud-metrics',
			metadata: {
				name: 'Plots',
			}
		 },
		[

			['core/heading',
				{ level: 3, content: __( 'Metrics' , 'wpcloud') }
			],
		],
	]
];

registerBlockType( metadata.name, {
	edit: () => <InnerBlocks template={template} />,
	save: () => <InnerBlocks.Content />
} );