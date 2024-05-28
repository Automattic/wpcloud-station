/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { SelectControl, ToggleControl } from '@wordpress/components';

export default function LinkableDetailSelect( {
	attributes,
	setAttributes,
	onChange,
	help,
} ) {
	const linkableSiteDetails = window.wpcloud?.linkableSiteDetails || [];
	let options = [];
	for ( const [ key, value ] of Object.entries( linkableSiteDetails ) ) {
		options.push( { value: key, label: value } );
	}
	const { name, refresh } = attributes;
	return (
		<>
			<SelectControl
				label={ __( 'Select a site detail link' ) }
				value={ name }
				options={ options }
				onChange={ ( newName ) => {
					setAttributes( {
						name: newName,
						label: linkableSiteDetails[ newName ] || newName,
					} );
					onChange && onChange( newName );
				} }
				help={ help || __( 'Select a site detail link to display' ) }
			/>
			<ToggleControl
				label={ __( 'Refresh linked details' ) }
				checked={ refresh }
				onChange={ ( newVal ) => {
					setAttributes( {
						refresh: newVal,
					} );
				} }
				help={ __(
					'Refresh urls on site detail links. A new link will be generated when clicked.'
				) }
			/>
		</>
	);
}
