/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InnerBlocks,
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { TextControl, PanelBody } from '@wordpress/components';
import { useSelect } from '@wordpress/data';


/**
 * Internal dependencies
 */
import './editor.scss';


const TEMPLATE = [
	[
		'wpcloud/form',
		{
			ajax: true,
			wpcloudAction: 'create_site'
		},
		[
			[
				'core/heading',
				{
					level: 3,
					content: __('New Site'),
				},
			],
			[
				'wpcloud/form-input',
				{
					type: 'text',
					label: __('Name'),
					name: 'site_name',
					placeholder: __('Enter site name'),
					required: true,
				},
			],
			[
				'wpcloud/form-input',
				{
					type: 'select',
					label: __('PHP Version'),
					name: 'php_version',
					options: [
						{ value: '7.4', label: '7.4' },
						{ value: '8.1', label: '8.1' },
						{ value: '8.2', label: '8.2' },
						{ value: '8.3', label: '8.3' },
						{ value: '7.0', label: '7.0' },
					],
					required: true,
				},
			],
			[
				'wpcloud/form-input',
				{
					type: 'select',
					name: 'data_center',
					label: __('Data Center'),
						options: [
						{ value: ' ', label: __( 'No Preference' )},
						{ value: 'bur', label: __( 'Los Angeles, CA' ) },
					],
					required: true,
				},
			],
			[
				'wpcloud/form-submit-button',
				{
					text: __('Create Site'),
				},
			],
		],
	],
];

/*
 *
 * @return {Element} Element to render.
 */
export default function Edit() {

	const blockProps = useBlockProps();

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: TEMPLATE,
	} );

	return (
		<div { ...innerBlocksProps } className="wpcloud-new-site-form" />
	);
}
