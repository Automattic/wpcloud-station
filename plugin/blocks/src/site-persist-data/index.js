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
			className: 'wpcloud-site-persist-data',
			metadata: {
				name: 'Site Persist Data',
			}
		 },
		[

			['core/heading',
				{ level: 3, content: __( 'Persist Site Data' , 'wpcloud') }
			],


			['wpcloud/form', {
				wpcloudAction: 'site_persist_data_set',
			},
					[
						[ 'core/heading', { level: 4, content: __('Add Key Value Pair', 'wpcloud') }],

						[ 'core/group',
							{
								layout: {
									type: 'flex',
									flexWrap: 'wrap',
								},
								className: 'wpcloud-site-persist-data__key-value-pair',
							},
							[
								[ 'wpcloud/form-input', { name: 'key', label: __('Key' , 'wpcloud'), hideLabel: true, placeholder: __('Key', 'wpcloud') } ],
								[ 'wpcloud/form-input', { name: 'value', label: __('Value', 'wpcloud'), hideLabel: true, placeholder: __('Value', 'wpcloud') } ],
							]
						],

						['wpcloud/button', { label: __('Add'), type: 'submit' } ],
					]
			],
			['wpcloud/form', {
				wpcloudAction: 'site_persist_data_delete',
			},
				[
					[ 'core/heading',	{ level: 4, content: __('Delete Key', 'wpcloud') } ],
					[ 'wpcloud/form-input', { name: 'key', label: __('Key', 'wpcloud'), hideLabel: true, placeholder: __('Key', 'wpcloud') } ],
					[ 'wpcloud/button', { label: __('Delete'), type: 'submit' } ],
				]
			],
		],
	]
];

registerBlockType( metadata.name, {
	edit: () => <InnerBlocks template={template} />,
	save: () => <InnerBlocks.Content />
} );
