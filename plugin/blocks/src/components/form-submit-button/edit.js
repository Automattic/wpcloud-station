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
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

import './editor.scss';

const Edit = ( { attributes, setAttributes, className } ) => {
	const { text, asButton, outline } = attributes;

	const controls = (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings' ) }>
					<ToggleControl
						label={ __( 'Display Outline' ) }
						checked={ asButton }
						onChange={ ( newVal ) => {
							setAttributes( {
								outline: newVal,
							} );
						} }
					/>
					<ToggleControl
						label={ __( 'Display as Button' ) }
						checked={ asButton }
						onChange={ ( newVal ) => {
							setAttributes( {
								asButton: newVal,
							} );
						} }
					/>
				</PanelBody>
			</InspectorControls>
		</>
	);

	const blockProps = useBlockProps();
	return (
		<>
			{ controls }
			<div
				{ ...blockProps }
				className={ classNames(
					className,
					blockProps.className,
					'wpcloud-block-form-submit',
					{
						'wp-block-button': asButton,
					}
				) }
			>
				<RichText
					value={ text }
					onChange={ ( newVal ) => {
						setAttributes( { text: newVal } );
					} }
					placeholder={ __( 'submit' ) }
					className={ classNames(
						'wpcloud-block-form-submit-button',
						{
							'wp-block-button__link': asButton,
							'wp-element-button': asButton,
							'as-text': ! asButton,
							'as-outline': outline,
						}
					) }
				/>
			</div>
		</>
	);
};
export default Edit;
