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
	['wpcloud/site-details',
		{},
		[
			['core/heading',
				{
					level: 3,
					className: "wpcloud-site-detail-card__title",
					content: __('SSH Users')
				}
			],
			['wpcloud/form',
				{
					wpcloudAction: 'site_ssh_disconnect_all_users',
				},
				[
					['wpcloud/button',
						{
							label: __('Disconnect All Users'),
							className: 'wpcloud-block-button--danger',
							type: 'submit',
						}
					]
				]
			],
			['wpcloud/form',
				{
					wpcloudAction: 'site_access_type',
				},
				[
					['wpcloud/form-input',
						{
							type: "checkbox",
							name: "site_access_with_ssh",
							label: __('Access via SSH (if disabled, SFTP will be used)'),
							displayAsToggle: true,
							submitOnChange: true,
						}
					]
				]
			]
		]
	]
];

registerBlockType(metadata.name, {
	edit: () => <InnerBlocks template={template} />,
	save: () => <InnerBlocks.Content />,
});