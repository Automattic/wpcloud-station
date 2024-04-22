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

const siteDetailKeys = wpcloud?.siteDetails || [];

export default DetailSelect = function ({ attributes, setAttributes }) {
	const { key } = attributes;
	return (
		<SelectControl
			label={ __( 'Select a site detail' ) }
			value={ key }
			options={ siteDetailKeys.map( ( detailKey ) => ( {
				value: detailKey,
				label: formatDisplayKey( detailKey ),
			} ) ) }
			onChange={ ( newKey ) => {
				const display = formatDisplayKey( newKey );
				setAttributes( {
					key: newKey,

					displayKey: sprintf(
						/* translators: %s is the display name of the site detail key */
						__( '{The %s}', 'wpcloud' ),
						display
					),
					title: display,
				} );
			} }
		/>
	);
};
