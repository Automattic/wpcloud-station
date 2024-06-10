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

const metaOptions = window.wpcloud?.siteMetaOptions || {};

function addInputTemplate(name, label) {
	let options = metaOptions[name]?.options;

	const type = metaOptions[name]?.type || 'text';
	const input = [
		'wpcloud/form-input',
		{
			type,
			name,
			label,
			meta: { name: label },
		},
	];

	if (type === 'select') {
		let optionData = [];

		if (Array.isArray(options)) {
			options.forEach((option) => {
				optionData.push({ value: option, label: option });
			});
		} else {
			for (const [key, value] of Object.entries(options)) {
				optionData.push({ value: key, label: value });
			}
		}
		input[1].options = optionData;
	}

	const hintText = metaOptions[name]?.hint;
	if (hintText) {
		const hint = ['wpcloud/expanding-section',
			{
				metadata: {
					name: `${name} hint`,
				},
				clickToToggle: true,
				hideHeader: false,

			},
			[
				['wpcloud/expanding-header', {}, [
					['wpcloud/icon', { icon: 'info' }],
				]],
				['wpcloud/expanding-content', {}, [
					['core/paragraph', {
						content: hintText,
					}],
				]],
			]
		];
		input.push([ hint ]);
	}

	console.log("input", input);
	return input
}

const template = [
	[ 'wpcloud/form', {
		ajax: true,
		wpcloudAction: 'site_update',
		submitOnChange: true,
	},
		[
			addInputTemplate( 'suspended', __( 'Suspended HTTP Status Code' ) ) ,
			addInputTemplate( 'suspend_after', __( 'Suspend after' ) ) ,
			addInputTemplate( 'php_version', __( 'PHP Version' ) ) ,
			addInputTemplate( 'wp_version', __( 'WP Version' ) ) ,
			addInputTemplate( 'do_not_delete', __( 'Do Not Delete' ) ),
			addInputTemplate( 'space_quota', __( 'Space Quota' ) ),
			addInputTemplate( 'photon_subsizes', __( 'Photon Subsizes' ) ),
			addInputTemplate( 'privacy_model', __( 'Privacy Model' ) ),
			addInputTemplate('geo_affinity', __( 'Geo Affinity' ) ),
			addInputTemplate('static_file_404', __('Static File 404' ) ),
			addInputTemplate('default_php_conns', __('Default PHP Connections')),
			addInputTemplate('burst_php_conns', __( 'Burst PHP Connections' ) ),
			addInputTemplate('php_fs_permissions', __('PHP File Permissions')),
			addInputTemplate( 'canonicalize_aliases', __('Canonize Aliases' ) ),
		]
	],
]

registerBlockType( metadata.name, {
	edit: () => (<InnerBlocks template={template} />),
	save: () => (<InnerBlocks.Content />),
} );
