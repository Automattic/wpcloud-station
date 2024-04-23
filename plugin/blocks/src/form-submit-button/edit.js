/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, Dashicon } from '@wordpress/components';
const Edit = ( { attributes, setAttributes } ) => {
	const { text, icon } = attributes;
	const template = [
		[
			'core/button',
			{
				text: text || __( 'Submit' ),
				tagName: 'button',
				type: 'submit',
			},
		],
	];

	const controls = (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings' ) }>
					<TextControl
						label={ __( 'icon' ) }
						value={ icon }
						onChange={ ( newValue ) =>
							setAttributes( { icon: newValue } )
						}
						help={ __(
							'Replace the button text with a Dashicon. See https://developer.wordpress.org/resource/dashicons/ for available icons.'
						) }
					/>
				</PanelBody>
			</InspectorControls>
		</>
	);

	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template,
	} );

	if ( icon ) {
		return (
			<>
				{ controls }
				<button
					type="submit"
					onClick={ ( event ) => event.preventDefault() }
					className={ classNames(
						'button',
						'wpcloud-block-form-submit-icon-button',
						blockProps.className
					) }
					{ ...blockProps }
					aria-label={ text }
				>
					<Dashicon icon={ icon } />
				</button>
			</>
		);
	}

	return (
		<>
			<div
				className={ classNames(
					'wpcloud-block-form-submit-wrapper',
					innerBlocksProps.className
				) }
				{ ...innerBlocksProps }
			/>
		</>
	);
};
export default Edit;
