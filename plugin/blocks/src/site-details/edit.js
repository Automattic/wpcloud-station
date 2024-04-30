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
} ) {
	const { adminOnly } = attributes;
	const blockProps = useBlockProps();

	const template = [
		[
			'core/group',
			{
				className: 'wpcloud-site-detail-card',
			},
			[
				['core/heading', { level: 3, className: 'wpcloud-site-detail-card__title', content: __('Site Details') }],
				[ 'wpcloud/site-detail' ]
			]
		]
	]

	const innerBlocksProps = useInnerBlocksProps(blockProps, {
		template
	});

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
				className={ classNames( innerBlocksProps.className, 'wpcloud-block-site-detail-card', {
					'is-admin-only': adminOnly,
				} ) }
			/>
		</>
	);
}
