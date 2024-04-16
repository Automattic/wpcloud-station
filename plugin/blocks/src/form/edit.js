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

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes, clientId }) {
	//const { action, method } = attributes;
	const blockProps = useBlockProps();
	const hasInnerBlocks = useSelect(
		(select) => {
			const { getBlockOrder } = select(blockEditorStore);
			const block = getBlockOrder(clientId);
			return {
				hasInnerBlocks: !! block?.innerBlocks?.length,
			};
		},
		[clientId]
	);

	const innerBlocksProps = useInnerBlocksProps(blockProps, {
		allowedBlocks: [
			'core/paragraph',
			'core/heading',
			'wpcloud-dashboard/form-input',
			/*
			'wpcloud-dashboard/form-submit',
			'wpcloud-dashboard/form-notice',
			*/
		],
		templateLock: false,
		/*
		renderAppender: hasInnerBlocks
			? undefined
			: InnerBlocks.ButtonBlockAppender,
		*/

	});

	return (
		<form
			{...innerBlocksProps}
			className="wpcloud-dashboard-form"
			encType="text/plain"
		/>
	);
}
