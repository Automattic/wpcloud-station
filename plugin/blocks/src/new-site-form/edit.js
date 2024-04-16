/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InnerBlocks,
	useBlockProps,
	useInnerBlocksProps,
	//InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import './editor.scss';


const TEMPLATE = [
	[
		'wpcdb/form',
		{},
		[
			[
				'core/heading',
				{
					level: 3,
					content: __('New Site'),
				},
			],
			[
				'wpcdb/form-input',
				{
					type: 'text',
					label: __('Name'),
					placeholder: __('Enter site name'),
					required: true,
				},
			],
			[
				'wpcdb/form-input',
				{
					type: 'select',
					label: __('PHP Version'),
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
				'wpcdb/form-input',
				{
					type: 'select',
					label: __('Data Center'),
						options: [
						{ value: ' ', label: __( 'No Preference' )},
						{ value: 'bur', label: __( 'Los Angeles, CA' ) },
					],
					required: true,
				},
			],
		],
	],
];

/*
 *
 * @return {Element} Element to render.
 */
export default function Edit({ clientId }) {
	const blockProps = useBlockProps();
	const { hasInnerBlocks } = useSelect(
		( select ) => {
			const { getBlock } = select( blockEditorStore );
			const block = getBlock( clientId );
			return {
				hasInnerBlocks: !! ( block && block.innerBlocks.length ),
			};
		},
		[ clientId ]
	);

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: TEMPLATE,
		renderAppender: hasInnerBlocks
			? undefined
			: InnerBlocks.ButtonBlockAppender,
	} );

	return (
		<div { ...innerBlocksProps } className="wpcdb-new-site-form"/>
	);
}
