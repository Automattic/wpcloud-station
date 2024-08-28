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
import { inputTemplate } from '../utils/templates';


// Generate a template with all the mutable site options
function buildTemplate() {
	const mutableOptions = window.wpcloud?.siteMutableOptions || {};

	let fields = [];
	for (const [key, value] of Object.entries(mutableOptions)) {
		fields.push(inputTemplate({ name: key, ...value }));
	}

	return [
		[
			'wpcloud/form',
			{
				ajax: true,
				wpcloudAction: 'site_update',
				submitOnChange: false,
			},
			[
				...fields,
				[ 'wpcloud/button', { label: __('Update'), type: 'submit' } ],
			],
		],
	];
}

registerBlockType( metadata.name, {
	edit: () => {
		const template = buildTemplate();
		return ( <InnerBlocks template={template} /> );
	},
	save: () => <InnerBlocks.Content />,
} );
