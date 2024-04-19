/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InnerBlocks,
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { CheckboxControl, TextControl, PanelBody } from '@wordpress/components';
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
	const { action, ajax, wpcloudAction } = attributes;
	const blockProps = useBlockProps();

	const isChildSelected = useSelect( ( select ) =>
		select( 'core/block-editor' ).hasSelectedInnerBlock( clientId )
	);

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		renderAppender:
			isSelected || isChildSelected
				? InnerBlocks.ButtonBlockAppender
				: undefined,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Form Settings' ) }>
					<TextControl
						label={ __( 'WP Cloud Action' ) }
						value={ wpcloudAction }
						onChange={ ( newValue ) =>
							setAttributes( { wpcloudAction: newValue } )
						}
						help={ __( 'The WP Cloud form action.' ) }
					/>
					<CheckboxControl
						label={ __( 'Enable AJAX' ) }
						checked={ attributes.ajax }
						onChange={ ( newValue ) =>
							setAttributes( { ajax: newValue } )
						}
						help={ __(
							'Enable AJAX form submission for a smoother experience.'
						) }
					/>
					{ ! ajax && (
						<TextControl
							label={ __( 'Action' ) }
							value={ action }
							onChange={ ( newValue ) =>
								setAttributes( { action: newValue } )
							}
							help={ __( 'The URL to send the form data to.' ) }
						/>
					) }
				</PanelBody>
			</InspectorControls>
			<form
				{ ...innerBlocksProps }
				className="wpcloud-block-form"
				encType="text/plain"
			/>
		</>
	);
}
