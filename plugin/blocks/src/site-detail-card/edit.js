/**
 * External dependencies
 */
import classNames from 'classnames';

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
import { CheckboxControl, PanelBody } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import './editor.scss';

/**
 *
 * @param {Object}  props               Component props.
 * @param {Object}  props.attributes
 * @param {Object}  props.setAttributes
 * @param {number}  props.clientId
 * @param {boolean} props.isSelected
 * @return {Element} Element to render.
 */
export default function Edit( {
	attributes,
	setAttributes,
	clientId,
	isSelected,
} ) {
	const { adminOnly } = attributes;
	const blockProps = useBlockProps();

	const isChildSelected = useSelect( ( select ) =>
		select( 'core/block-editor' ).hasSelectedInnerBlock( clientId )
	);

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
		renderAppender:
			isSelected || isChildSelected
				? InnerBlocks.ButtonBlockAppender
				: undefined,
		template: hasInnerBlocks ? undefined : [ [ 'wpcloud/site-detail' ] ],
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Form Settings' ) }>
					<CheckboxControl
						label={ __( 'Limit to Admins' ) }
						checked={ adminOnly }
						onChange={ ( newVal ) => {
							setAttributes( {
								adminOnly: newVal,
							} );
						} }
						help={ __(
							'Only admins will see this field. Inputs marked as admin only will appear with a dashed border in the editor'
						) }
					/>
				</PanelBody>
			</InspectorControls>
			<div
				{ ...innerBlocksProps }
				className={ classNames( 'wpcloud-block-site-detail-card', {
					'is-admin-only': adminOnly,
				} ) }
			/>
		</>
	);
}
