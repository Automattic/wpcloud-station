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
import LinkableDetailSelectControl from '../controls/site/linkableDetailSelect';
import './editor.scss';

function ButtonBlock( { attributes, setAttributes, className } ) {
	const { type, style, adminOnly, target, addIcon, iconOnly, url, label, action, isPrimary } =
		attributes;
	const blockProps = useBlockProps();

	const updateAttribute = ( key ) => ( val ) => {
		setAttributes( { [ key ]: val } );
	};

	const LinkControls = (
		<>
			<TextControl
				label={ __( 'Custom URL' ) }
				value={ url }
				onChange={ updateAttribute( 'url' ) }
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
				onChange={ updateAttribute( 'action' ) }
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
							{ label: __( 'Action' ), value: 'action' },
						] }
						onChange={ updateAttribute( 'type' ) }
					/>
					<SelectControl
						label={ __( 'Button Style' ) }
						value={ style }
						options={ [
							{ label: __( 'Text' ), value: 'text' },
							{ label: __( 'Button' ), value: 'button' },
						] }
						onChange={updateAttribute('style')}
					/>
					<ToggleControl
						label={ __( 'Primary Button' ) }
						checked={ isPrimary }
						onChange={ updateAttribute( 'isPrimary' ) }
						help={ __( 'Use the primary button style if enabled. Otherwise use secondary button style' ) }
					/>
					<ToggleControl
						label={ __( 'Add Icon' ) }
						checked={ addIcon }
						onChange={ updateAttribute( 'addIcon' ) }
						help={ __( 'Add Icon to the button' ) }
					/>

					 <ToggleControl
						label={__('Icon Only')}
						checked={ iconOnly }
						onChange={updateAttribute('iconOnly')}
						help={__('Only show the icon, no text label')}
					/>

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
				</PanelBody>
				<PanelBody label={ __( 'Button Config' ) }>
					{ 'link'   === type && LinkControls }
					{ 'detail' === type && DetailControls }
					{ 'action' === type && ActionControls }
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
					className,
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
					) }
				</span>
			</div>
		</>
	);
}

export default ButtonBlock;
