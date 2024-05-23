/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';

export default function DetailSelect( {
	attributes,
	setAttributes,
	onChange,
} ) {
	const siteDetailKeys = window.wpcloud?.siteDetails || {};
	let options = [];
	for (const [key, value] of Object.entries(siteDetailKeys)) {
		options.push({ value: key, label: value });
	}

	const { name } = attributes;
	return (
		<SelectControl
			label={ __( 'Select a site detail' ) }
			value={ name }
			options={ options }
			onChange={ ( newName ) => {
				setAttributes( {
					name: newName,
					label: siteDetailKeys[newName] || newName,
				} );
				onChange && onChange( newName );
			} }
		/>
	);
}
