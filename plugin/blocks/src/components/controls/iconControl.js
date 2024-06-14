/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { SelectControl, TextControl } from '@wordpress/components';

import * as icons from '@wordpress/icons';

const iconOptions = Object.keys(icons).map((key) =>
	key === 'Icon' ? { label: '', key: '' } : { label: key, value: key }
);

export default function( { attributes, setAttributes } ) {
	const { icon, iconSize } = attributes;

	return (
		<>
		<SelectControl
				label={__('Icon')}
			value={ icon }
			options={ iconOptions }
			onChange={ ( newVal ) => {
				setAttributes( { icon: newVal } );
			} }
			/>
			<TextControl
				label={ __( 'size' ) }
				value={ iconSize }
				onChange={ ( newVal ) => {
					setAttributes( { iconSize: newVal } );
				} }
			/>
		</>
	);
}