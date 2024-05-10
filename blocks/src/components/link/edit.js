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
import {
	PanelBody,
	ToggleControl,
	TextControl,
	SelectControl,
} from '@wordpress/components';
import { Dashicon } from '@wordpress/components';

/**
 *
 * Internal dependencies
 */
import LinkableDetailSelectControl from '../controls/site/linkableDetailSelect';

function SiteDetailBlock( { attributes, setAttributes, className } ) {
	const { style, adminOnly, target, externalLink, url, label, download } =
		attributes;
	const blockProps = useBlockProps();

	const controls = (
		<>
			<InspectorControls>
				<PanelBody label={ __( 'Settings' ) }>
					<LinkableDetailSelectControl
						attributes={ attributes }
						setAttributes={ setAttributes }
						help={ __(
							'Select a site detail to link to. Leave blank if using a custom URL.'
						) }
					/>
					<TextControl
						label={ __( 'Custom URL' ) }
						value={ url }
						onChange={ ( newUrl ) =>
							setAttributes( { url: newUrl } )
						}
						help={ __(
							'Add a custom URL for this link. Just use the path for internal links i.e. `/sites` '
						) }
					/>
					<SelectControl
						label={ __( 'Link Style' ) }
						value={ style }
						options={ [
							{ label: __( 'Text' ), value: 'text' },
							{ label: __( 'Button' ), value: 'button' },
						] }
						onChange={ ( newStyle ) =>
							setAttributes( { style: newStyle } )
						}
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
					<ToggleControl
						label={ __( 'External Link' ) }
						checked={ externalLink }
						onChange={ ( newValue ) =>
							setAttributes( { externalLink: newValue } )
						}
						help={ __(
							'Marking a link as external will add an external link icon to the link'
						) }
					/>
					<ToggleControl
						label={ __( 'Download' ) }
						checked={ download }
						onChange={ ( newValue ) =>
							setAttributes( { download: newValue } )
						}
						help={ __( 'Marking a link as a download' ) }
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
			</InspectorControls>
		</>
	);

	return (
		<div
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				className,
				'wpcloud-block-link',
				{
					'is-button': style === 'button',
					'is-admin-only': adminOnly,
				}
			) }
			data-name={ attributes.name }
		>
			{ controls }

			<RichText
				tagName="span"
				className={ 'wpcloud-block-site-detail__title-content' }
				value={ label }
				onChange={ ( newTitle ) => {
					setAttributes( { label: newTitle } );
				} }
				placeholder={ __( 'label' ) }
			/>
			{ externalLink && <Dashicon icon="external" /> }
		</div>
	);
}

export default SiteDetailBlock;
