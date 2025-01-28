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
	ToggleControl,
	TextControl,
	SelectControl,
} from '@wordpress/components';

/**
 *
 * Internal dependencies
 */
import { updateAttribute } from '@wpcloud/controls/utils';

import { LinkableDetailSelectControl } from '@wpcloud/controls/site';
import { SpinnerControls } from '@wpcloud/controls';
import * as spinners from '@wpcloud/components/spinner/library';
import { Spinner } from '@wpcloud/components/spinner/library';
import './editor.scss';

function ButtonBlock( { attributes, setAttributes } ) {
	const {
		type,
		style,
		adminOnly,
		target,
		addIcon,
		iconOnly,
		url,
		label,
		action,
		isPrimary,
		addSpinner,
		spinnerSpinner,
		spinnerSpeed,
		spinnerBackground,
		spinnerSize,
		previewSpinner,
	} = attributes;

	const spinnerAttributes = {
		spinner: spinnerSpinner,
		speed: spinnerSpeed,
		background: spinnerBackground,
		size: spinnerSize,
	}
	const updateSpinnerAttribute = (key) => (val) => {
		const spinnerKey = 'spinner' + key.charAt(0).toUpperCase() + key.slice(1);
		setAttributes({ [spinnerKey]: val });
	};

	const blockProps = useBlockProps();
	const update = updateAttribute(setAttributes);

	const LinkControls = (
		<>
			<TextControl
				label={ __( 'Custom URL' ) }
				value={ url }
				onChange={ update( 'url' ) }
				help={ __(
					'Add a custom URL for this link. Just use the path for internal links i.e. `/sites` '
				) }
			/>
			<ToggleControl
				label={ __( 'Open in new tab' ) }
				checked={ target === '_blank' }
				onChange={ ( newVal ) =>
					setAttributes( {
						target: newVal ? '_blank' : '_self',
					} )
				}
			/>
		</>
	);

	const DetailControls = (
		<>
			<LinkableDetailSelectControl
				attributes={ attributes }
				setAttributes={ setAttributes }
				help={ __(
					'Select a site detail to link to. Leave blank if using a custom URL.'
				) }
			/>
		</>
	);

	const ActionControls = (
		<>
			{ /* @TODO: add list of available actions */ }
			<TextControl
				label={ __( 'Action' ) }
				value={ action }
				onChange={ update( 'action' ) }
				help={ __(
					'Add an action for this link. This will be used to trigger JS actions'
				) }
			/>
		</>
	);

	const controls = (
		<>
			<InspectorControls>
				<PanelBody label={ __( 'Settings' ) }>
					<SelectControl
						label={ __( 'Button Type' ) }
						value={ type }
						options={ [
							{ label: __( 'Link' ), value: 'link' },
							{ label: __( 'Detail' ), value: 'detail' },
							{ label: __('Action'), value: 'action' },
							{ label: __( 'Submit' ), value: 'submit' },
							{ label: __( 'Reset' ), value: 'reset' },
						] }
						onChange={ update( 'type' ) }
					/>
					<SelectControl
						label={ __( 'Button Style' ) }
						value={ style }
						options={ [
							{ label: __( 'Text' ), value: 'text' },
							{ label: __( 'Button' ), value: 'button' },
						] }
						onChange={update('style')}
					/>
					<ToggleControl
						label={ __( 'Primary Button' ) }
						checked={ isPrimary }
						onChange={ update( 'isPrimary' ) }
						help={ __( 'Use the primary button style if enabled. Otherwise use secondary button style' ) }
					/>
					<ToggleControl
						label={ __( 'Add Spinner' ) }
						checked={ addSpinner }
						onChange={ update( 'addSpinner' ) }
						help={ __( 'Replaces the button text with a spinner while the button is disabled. Mouse-over to see the replacement in the editor.' ) }
					/>
					{addSpinner && (
						<>
							<ToggleControl
								label={__('Preview Spinner')}
								checked={previewSpinner}
								onChange={update('previewSpinner')}
								help={__('Preview the spinner in the editor')}
							/>
							<SpinnerControls attributes={spinnerAttributes} updateAttribute={updateSpinnerAttribute} />
						</>
					)}
					<ToggleControl
						label={ __( 'Add Icon' ) }
						checked={ addIcon }
						onChange={ update( 'addIcon' ) }
						help={ __( 'Add Icon to the button' ) }
					/>

					 <ToggleControl
						label={__('Icon Only')}
						checked={ iconOnly }
						onChange={update('iconOnly')}
						help={__('Only show the icon, no text label')}
					/>

					<ToggleControl
						label={ __( 'Limit to Admins' ) }
						checked={ adminOnly }
						onChange={ update( 'adminOnly' ) }
						help={ __(
							'Only admins will see this field. Inputs marked as admin only will appear with a dashed border in the editor'
						) }
					/>
				</PanelBody>
				<PanelBody label={ __( 'Button Config' ) }>
					{ 'link'   === type && LinkControls }
					{ 'detail' === type && DetailControls }
					{ 'action' === type && ActionControls }
					{ 'submit' === type && ActionControls }
				</PanelBody>
			</InspectorControls>
		</>
	);

	return (
		<>
			{ controls }
			<div
				{ ...blockProps }
				className={ classNames(
					blockProps.className,
					'wpcloud-block-button',
					{
						'is-admin-only': adminOnly,
						'is-secondary': !isPrimary,
						'is-text': style === 'text',
					}
				) }
				data-name={ attributes.name }
			>
				<span
					className={ classNames(
						'wpcloud-block-button__content',
						{ 'wp-block-button__link': style === 'button' }
					)}
				>

					{ !iconOnly && (<RichText
						className={'wpcloud-block-button__label'}
						value={label}
						onChange={updateAttribute('label')}
						placeholder={__('Button')}
					/>
					)}
					{ addIcon && (
						<div className="wpcloud-block-button__icon">
							<InnerBlocks allowedBlocks={ [ 'wpcloud/icon' ] } />
						</div>
					)}
					{addSpinner && (
						<Spinner
							className={classNames('wpcloud-block-button__spinner', { 'preview-spinner': previewSpinner })}
							{...{ ...spinnerAttributes, spinner: spinners[spinnerSpinner] }} />
					)}
				</span>
			</div>
		</>
	);
}

export default ButtonBlock;
