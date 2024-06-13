/**
 * WordPress dependencies
 */
import { SelectControl } from '@wordpress/components';

import * as icons from '@wordpress/icons';

const iconOptions = Object.keys(icons).map((key) =>
	key === 'Icon' ? { label: '', key: '' } : { label: key, value: key }
);

export default function( { attributes, setAttributes } ) {
	const { icon } = attributes;

	return (
		<SelectControl
			label="Icon"
			value={ icon }
			options={ iconOptions }
			onChange={ ( newVal ) => {
				setAttributes( { icon: newVal } );
			} }
		/>
	);
}