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
	InnerBlocks,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	SelectControl,
} from '@wordpress/components';
import { useCallback, useEffect } from '@wordpress/element';

/*
 * Internal dependencies
 */
import './editor.scss';
import DetailSelectControl from '../controls/site/detailSelect';
import { Text, Select, Hidden } from './fields';

function InputFieldBlock( { attributes, setAttributes, className, context, clientId } ) {
	const { 'wpcloud-form/isActive': isFormActive } = context;

	const { type, inlineLabel, label, adminOnly, required, name, hideLabel, displayAsToggle } =
		attributes;

	useEffect(() => {
		setAttributes({ uniqueId: `${clientId}-${name}`})
	}, [clientId, setAttributes, name]);

	const blockProps = useBlockProps();

	const updatePlaceholder = useCallback(
		( placeholder ) => setAttributes( { placeholder } ),
		[ setAttributes ]
	);
	const updateValue = useCallback(
		( value ) => setAttributes( { value } ),
		[ setAttributes ]
	);

	const inputTags = {
		text: Text,
		email: Text,
		password: Text,
		hidden: Hidden,
		textarea: Text,
		select: Select,
		checkbox: Text,
	};

	const typeOptions = [
		{ value: 'text', label: __( 'Text' ) },
		{ value: 'email', label: __( 'Email' ) },
		{ value: 'password', label: __( 'Password' ) },
		{ value: 'hidden', label: __( 'Hidden' ) },
		{ value: 'textarea', label: __( 'Textarea' ) },
		{ value: 'checkbox', label: __( 'Checkbox' )},
		{ value: 'select', label: __( 'Select' ) },
	];

	const InputTag = inputTags[ type ] ? inputTags[ type ] : Text;
	const showLabel = ! ( hideLabel || 'hidden' === type );

	const controls = (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings' ) }>
					<TextControl
						label={ __( 'Field Name' ) }
						value={ name }
						onChange={ ( newValue ) =>
							setAttributes( { name: newValue } )
						}
						help={ __( 'The name attribute of the input field' ) }
					/>
					<SelectControl
						label={ __( 'Input Type' ) }
						value={ type }
						options={ typeOptions }
						onChange={ ( newType ) => {
							setAttributes( { type: newType } );
						} }
					/>

					<DetailSelectControl
						attributes={{ context: 'input' }}
						setAttributes={ setAttributes }
					/>
					{'textarea' !== type && 'text' !== type && (
						<ToggleControl
							label={__('Submit on Change')}
							checked={attributes.submitOnChange}
							onChange={(newVal) => {
								setAttributes({
									submitOnChange: newVal,
								});
							}}
							help={__('Submit the input on change instead of on submit. The parent form must be set to ajax.')}
						/>
					)}
					{ 'checkbox' !== type && (
						<>
							<ToggleControl
								label={ __( 'Inline Label' ) }
								checked={ inlineLabel }
								onChange={ ( newVal ) => {
									setAttributes( {
										inlineLabel: newVal,
									} );
								} }
							/>
							<ToggleControl
								label={ __( 'Hide Label' ) }
								checked={ hideLabel }
								onChange={ ( newVal ) => {
									setAttributes( {
										hideLabel: newVal,
									} );
								} }
							/>
						</>
					)}
					{'checkbox' == type && (
						<ToggleControl
							label={__('Display as Toggle')}
							checked={displayAsToggle}
							onChange={(newVal) => {
								setAttributes({
									displayAsToggle: newVal,
								});
							}}
						/>
					)}
					<ToggleControl
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
					<ToggleControl
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
	);

	if ( ! isFormActive && 'hidden' === type ) {
		return null;
	}

	return (
		<div { ...blockProps }>
			{ controls }
			<span
				className={ classNames( 'wpcloud-block-form-input__label', {
					'is-label-inline': inlineLabel || 'checkbox' === type,
					'is-admin-only': adminOnly,
				} ) }
			>
				{showLabel && (
					<span className="wpcloud-block-form-input__label-content">
						<RichText
							tagName="span"
							className="wpcloud-block-form-input__label-text"
							value={ label }
							onChange={ ( newLabel ) =>
								setAttributes( { label: newLabel } )
							}
							aria-label={
								label ? __( 'Label' ) : __( 'Empty label' )
							}
							data-empty={ label ? false : true }
						/>
						<InnerBlocks allowedBlocks={ [ 'core/paragraph', 'core/heading', 'wpcloud/icon', 'wpcloud/button', 'wpcloud/expanding-section', 'core/group', 'wpcloud/more-menu'] } />
					</span>
				)}
				{ displayAsToggle && ( <span className="toggle-container"></span> ) }
				<InputTag
					attributes={ attributes }
					onPlaceholderChange={ updatePlaceholder }
					onValueChange={ updateValue }
					className={classNames( className, { 'is-toggle': displayAsToggle } ) }
					isSelected={ blockProps.isSelected }
				/>
			</span>
		</div>
	);
}

export default InputFieldBlock;
