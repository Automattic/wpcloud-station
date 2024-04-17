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
import { CheckboxControl, TextControl, SelectControl, PanelBody } from '@wordpress/components';
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
	const { action, ajax, wpcloudAction } = attributes;
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
			'wpcloud/form-input',
			'wpcloud/form-submit-button',
		],
		templateLock: false,
		renderAppender: hasInnerBlocks
			? undefined
			: InnerBlocks.ButtonBlockAppender,
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Form Settings' ) }>
					<TextControl
						label={ __( 'WP Cloud Action' ) }
						value={ wpcloudAction }
						onChange={(wpcloudAction) => setAttributes({ wpcloudAction })}
						help={
								__( 'The WP Cloud form action.' )
						}
					/>
					<CheckboxControl
						label={__('Enable AJAX')}
						checked={attributes.ajax}
						onChange={(ajax) => setAttributes({ ajax })}
						help={
							__( 'Enable AJAX form submission for a smoother experience.' )
						}
					/>
					{ !ajax && (
						<TextControl
							label={ __( 'Action' ) }
							value={ action }
							onChange={ ( action ) => setAttributes( { action } ) }
							help={
								__( 'The URL to send the form data to.' )
							}
						/>
					) }
				</PanelBody>
			</InspectorControls>
			<form
				{...innerBlocksProps}
				className="wpcloud-block-form"
				encType="text/plain"
			/>
	</>
	);
}
