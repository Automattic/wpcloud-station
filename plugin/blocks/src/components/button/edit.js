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

function ButtonBlock( { attributes, setAttributes, className } ) {
	const { kind, style, adminOnly, target, icon, url, label, action } =
		attributes;
	const blockProps = useBlockProps();
	/**
	 *
	 * 1. General link
	 * 2. Site Detail ? ( would need the refresh option )
	 * 3. Fire action for JS to pick up
	 *
	 */

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
						value={ kind }
						options={ [
							{ label: __( 'Link' ), value: 'link' },
							{ label: __( 'Detail' ), value: 'detail' },
							{ label: __( 'Action' ), value: 'action' },
						] }
						onChange={ updateAttribute( 'kind' ) }
					/>
					<SelectControl
						label={ __( 'Button Style' ) }
						value={ style }
						options={ [
							{ label: __( 'Text' ), value: 'text' },
							{ label: __( 'Button' ), value: 'button' },
						] }
						onChange={ updateAttribute( 'style' ) }
					/>

					<ToggleControl
						label={ __( 'Icon' ) }
						checked={ icon }
						onChange={ updateAttribute( 'icon' ) }
						help={ __( 'Add Icon to the button' ) }
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
					{ 'link'   === kind && LinkControls }
					{ 'detail' === kind && DetailControls }
					{ 'action' === kind && ActionControls }
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
					<RichText
						className={ 'wpcloud-block-button__label' }
						value={ label }
						onChange={ updateAttribute( 'label' ) }
						placeholder={ __( 'Button' ) }
					/>
					{ icon && (
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
