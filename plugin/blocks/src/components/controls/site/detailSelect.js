/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';

const formatDisplayKey = ( key = '' ) => {
	return key
		.replace( /_/g, ' ' )
		.replace( /\b\w/g, ( s ) => s.toUpperCase() )
		.replace( /\b(.{2})\b/i, ( twoChar ) => twoChar.toUpperCase() )
		.replace( /\bapi\b/i, 'API' )
		.replace( /\bphp\b/i, 'PHP' );
};

export default function DetailSelect({ attributes, setAttributes }) {
	const siteDetailKeys = window.wpcloud?.siteDetailKeys || [];
	const options = [ '-' ].concat(siteDetailKeys);
	const { name } = attributes;
	return (
		<SelectControl
			label={ __( 'Select a site detail' ) }
			value={ name }
			options={ options.map( ( detailKey ) => ( {
				value: detailKey,
				label: formatDisplayKey( detailKey ),
			} ) ) }
			onChange={ ( newName ) => {
				setAttributes( {
					name: newName,
					label: formatDisplayKey( newName ),
				} );
			} }
		/>
	);
};
