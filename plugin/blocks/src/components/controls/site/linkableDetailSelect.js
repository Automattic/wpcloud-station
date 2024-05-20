/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';

/*
export const formatDisplayName = ( name = '' ) => {
	const words = name.replace( /–|-|_/g, ' ' );
	if ( ! words.includes( ' ' ) ) {
		return name;
	}

	return words
		.replace( /\b\w/g, ( s ) => s.toUpperCase() )
		.replace( /\b(.{2})\b/i, ( twoChar ) => twoChar.toUpperCase() )
		.replace( /\bapi\b/i, 'API' )
		.replace( /\bphp\b/i, 'PHP' );
};
*/
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
			options={ options.map( ( detailKey ) => ( {
				value: detailKey,
				label: formatDisplayName( detailKey ),
			} ) ) }
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
