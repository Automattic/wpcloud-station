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
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { Icon, copySmall, seen } from '@wordpress/icons';

/**
 *
 * Internal dependencies
 */
import DetailSelectControl from '../controls/site/detailSelect';
import './editor.scss';

function SiteDetailBlock( { attributes, setAttributes, className } ) {
	const { label, adminOnly, inline, hideLabel, obscureValue, showCopyButton, revealButton } =
		attributes;
	const blockProps = useBlockProps();

	const controls = (
		<>
			<InspectorControls>
				<PanelBody label={ __( 'Settings' ) }>
					<DetailSelectControl
						attributes={ attributes }
						setAttributes={ setAttributes }
					/>
					<ToggleControl
						label={ __( 'Display Inline' ) }
						checked={ inline }
						onChange={ ( newVal ) => {
							setAttributes( { inline: newVal } );
						} }
					/>
					<ToggleControl
						label={ __( 'Show Value only' ) }
						checked={ hideLabel }
						onChange={ ( newVal ) => {
							setAttributes( {
								hideLabel: newVal,
							} );
						} }
						help={ __(
							'Only show the value of the site detail. The label will be hidden.'
						) }
					/>
					<ToggleControl
						label={ __( 'Add Copy to Clipboard button' ) }
						checked={ showCopyButton }
						onChange={ ( newVal ) => {
							setAttributes( {
								showCopyButton: newVal,
							} );
						} }
						help={ __(
							'Add a button to copy the value of the site detail to the clipboard.'
						) }
					/>
					<ToggleControl
						label={ __( 'Obscure value' ) }
						checked={ obscureValue }
						onChange={ ( newVal ) => {
							setAttributes( {
								obscureValue: newVal,
							} );
						} }
						help={ __(
							'Show the detail value as asterisks like "********". If copy to clipboard is enabled the value will be copied as plain text.'
						) }
					/>
					{obscureValue && (
						<ToggleControl
							label={__('Add reveal button')}
							checked={revealButton}
							onChange={(newVal) => {
								setAttributes({
									revealButton: newVal,
								});
							}}
						/>)}
					{
						// @TODO add option to show external link icon
					 }

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
			</InspectorControls>
		</>
	);

	const valueText = obscureValue ? '********' : label;

	return (
		<span { ...blockProps }>
			<div
				className={ classNames(
					className,
					'wpcloud-block-site-detail',
					{
						'is-inline': inline,
						'is-admin-only': adminOnly,
						'copy-to-clipboard': showCopyButton,
					}
				) }
			>
				{ controls }
				{ hideLabel ? null : (
					<div
						className={ classNames(
							className,
							'wpcloud-block-site-detail__title'
						) }
					>
						<RichText
							tagName="h5"
							className={
								'wpcloud-block-site-detail__title-content'
							}
							value={ label }
							onChange={ ( newVal ) => {
								setAttributes( {
									label: newVal,
									metadata: { name: newVal },
								} );
							} }
							placeholder={ __( 'label' ) }
						/>
					</div>
				) }

				<div
					className={ classNames(
						'wpcloud-block-site-detail__value-container',
						{
							'copy-to-clipboard': showCopyButton,
						}
					) }
				>
					<div className={ 'wpcloud-block-site-detail__value' }>
						{ `{ ${ valueText } }` }
					</div>
					{ ( obscureValue && revealButton ) && (
						<Icon
							className="wpcloud-reveal-value"
							icon={seen}
						/>
					) }
					{ showCopyButton && (
						<Icon
							className="wpcloud-copy-to-clipboard"
							icon={ copySmall }
						/>
					) }
				</div>
			</div>
		</span>
	);
}

export default SiteDetailBlock;
