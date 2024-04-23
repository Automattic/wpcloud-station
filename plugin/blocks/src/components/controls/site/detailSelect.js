/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';

export const formatDisplayName = (name = '') => {
	const words = name.replace(/–|-|_/g, ' ');
	if ( ! words.includes( ' ' ) ) {
		return name;
	}

	return words
		.replace( /\b\w/g, ( s ) => s.toUpperCase() )
		.replace( /\b(.{2})\b/i, ( twoChar ) => twoChar.toUpperCase() )
		.replace( /\bapi\b/i, 'API' )
		.replace( /\bphp\b/i, 'PHP' );
};

export default function DetailSelect({ attributes, setAttributes, onChange }) {
	const siteDetailKeys = window.wpcloud?.siteDetailKeys || [];
	const options = [ '-' ].concat(siteDetailKeys);
	const { name } = attributes;
	return (
		<SelectControl
			label={__('Select a site detail')}
			value={name}
			options={options.map((detailKey) => ({
				value: detailKey,
				label: formatDisplayName(detailKey),
			}))}
			onChange={(newName) => {
				setAttributes({
					name: newName,
					label: formatDisplayName(newName),
				});
				onChange && onChange(newName);
			}}
		/>
	);
};
