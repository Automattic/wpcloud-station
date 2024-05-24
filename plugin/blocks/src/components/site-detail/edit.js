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
import { Icon, copySmall } from '@wordpress/icons';

/**
 *
 * Internal dependencies
 */
import DetailSelectControl from '../controls/site/detailSelect';
import './editor.scss';

function SiteDetailBlock( { attributes, setAttributes, className } ) {
	const { label, adminOnly, inline, hideLabel, refreshLink, showCopyButton } =
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
						label={ __( 'Refresh linked details' ) }
						checked={ refreshLink }
						onChange={ ( newVal ) => {
							setAttributes( {
								refreshLink: newVal,
							} );
						} }
						help={ __(
							'Refresh urls on site detail links. A new link will be generated when clicked.'
						) }
					/>
					{
						// @TODO add refresh rate when refresh is enabled
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
						{ `{ ${ label } }` }
					</div>
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
