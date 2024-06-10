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
	const { context, name } = attributes;

	let options = [{ value: '', label: '-'}];
	let optionData = {};
	if ('input' === context) {
		optionData = window.wpcloud?.siteMetaFields || {};
	} else {
		optionData = window.wpcloud?.siteDetails || {};
	}

	for (const [key, value] of Object.entries(optionData)) {
		options.push( { value: key, label: value } );
	}

	return (
		<SelectControl
			label={ __( 'Select a site detail' ) }
			value={ name }
			options={ options }
			onChange={ ( newName ) => {
				const label = optionData[ newName ] || newName;
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
