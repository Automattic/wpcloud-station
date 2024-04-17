/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	RichText,
	useBlockProps,
	__experimentalUseBorderProps as useBorderProps,
	__experimentalUseColorProps as useColorProps,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, CheckboxControl } from '@wordpress/components';
import { useRef, useCallback } from '@wordpress/element';

/**
 *
 * Internal dependencies
 */
import { Text, Select } from './fields';

function InputFieldBlock( { attributes, setAttributes, className } ) {

	const { type, inlineLabel, label, adminOnly, required } =
		attributes;
	const blockProps = useBlockProps();
	const ref = useRef();

	const borderProps = useBorderProps( attributes );
	const colorProps = useColorProps( attributes );
	if ( ref.current ) {
		ref.current.focus();
	}

	const updatePlaceholder = useCallback((placeholder) => setAttributes({ placeholder }), [setAttributes]);
	const updateValue = useCallback((value) => setAttributes({ value }), [setAttributes]);
	const isHidden = 'hidden' === type;

	const inputTags = {
		text: Text,
		email: Text,
		password: Text,
		hidden: Text,
		textarea: Text,
		select: Select,
	};

	const InputTag = inputTags[type] ? inputTags[type] : Text;

	const controls = (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings' ) }>
					{ 'checkbox' !== type && (
						<CheckboxControl
							label={ __( 'Inline label' ) }
							checked={ inlineLabel }
							onChange={ ( newVal ) => {
								setAttributes( {
									inlineLabel: newVal,
								} );
							} }
						/>
					)}
				 <CheckboxControl
						label={ __( 'Limit to Admins (appears with dashed border in editor) ' ) }
						checked={ adminOnly }
						onChange={ ( newVal ) => {
							setAttributes( {
								adminOnly: newVal,
							} );
						} }
					/>
					<CheckboxControl
						label={ __( 'Required' ) }
						checked={ required }
						onChange={ ( newVal ) => {
							setAttributes( {
								required: newVal,
							} );
						} }
					/>
				</PanelBody>
			</InspectorControls>
		</>
	)

	return (
		<div {...blockProps}>
			{ controls }
			<span
				className={ classNames( 'wpcloud-block-form-input__label', {
					'is-label-inline': inlineLabel || 'checkbox' === type,
					'is-admin-only': adminOnly,
				} ) }
			>
				{ ! isHidden && (
					<RichText
						tagName="span"
						className="wpcloud-block-form-input__label-content"
						value={ label }
						onChange={ ( newLabel ) =>
							setAttributes( { label: newLabel } )
						}
						aria-label={ label ? __( 'Label' ) : __( 'Empty label' ) }
						data-empty={ label ? false : true }
						placeholder={ __( 'Type the label for this input' ) }
				/>
				)}
				<InputTag
					attributes={attributes}
					onPlaceholderChange={updatePlaceholder}
					onValueChange={updateValue}
					className={className}
					styleProps={{ colorProps, borderProps }}
				/>
			</span>
		</div>
	);
}

export default InputFieldBlock;
