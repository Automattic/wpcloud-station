/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';

export default function DetailSelect( {
	attributes,
	setAttributes,
	onChange,
}) {
	const { type } = attributes;
	const selectableDetailTypes = window.wpcloud?.selectableDetails || ['wp_version', 'php_version', 'data_center' ];

	const siteDetailKeys = window.wpcloud?.siteDetails || {};
	let options = [];
	for (const [key, value] of Object.entries(siteDetailKeys)) {
		if ('select' === type && ! selectableDetailTypes.includes( key ) ) {
			continue;
		}
		options.push( { value: key, label: value } );
	}

	const { name } = attributes;
	return (
		<SelectControl
			label={ __( 'Select a site detail' ) }
			value={ name }
			options={ options }
			onChange={ ( newName ) => {
				const label = siteDetailKeys[ newName ] || newName;
				setAttributes( {
					name: newName,
					label,
					metadata: { name: label },
				} );
				onChange && onChange( newName );
			} }
		/>
	);
}
