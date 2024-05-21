/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';

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
	const { name } = attributes;
	return (
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
	);
}
