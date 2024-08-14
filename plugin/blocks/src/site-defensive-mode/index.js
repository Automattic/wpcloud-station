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
	[ 'core/group',
		{
			className: 'wpcloud-group-defensive-mode',

			metaData: {
				name: 'Defensive Mode',
			},
		},
		[
			[ 'core/heading',
				{
					content: __('Defensive Mode'),
					level: 3,
				},
			],
			[ 'wpcloud/site-detail',
				{
					label: 'Current Defensive Mode',
					name: 'defensive_mode',
					hideLabel: true,
					metadata: {
						name: 'Current Defensive Mode'
					}
				}
			],
			['wpcloud/form',
				{
					wpcloudAction: 'defensive_mode_disable',
					metadata: { name: 'Disable Defensive Mode Form'  },
				},
				[
					[ 'wpcloud/button',
						{
							label: __('Disable'),
							type: 'submit',
							metadata: { name: 'Disable' },
						},
					],
				]
			],
			['wpcloud/form',
				{
					wpcloudAction: 'defensive_mode_update',
					metadata: { name: 'Update Defensive Mode Form' }
				},
				[
					[ 'wpcloud/form-input',
						{
							name: 'timestamp',
							label: 'Enable Defensive mode ( enter minutes; 0 to disable) ',
							metadata: {
								name: 'Defensive Mode'
							}
						}
					],
					[ 'wpcloud/button',
						{
							label: __('Update'),
							type: 'submit',
						}
					]
				]
			]
		]
	]
];

registerBlockType( metadata.name, {
	edit: () => <InnerBlocks template={ template } />,
	save: () => <InnerBlocks.Content />,
} );
