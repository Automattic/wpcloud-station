/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { SelectControl, ToggleControl, TextControl } from '@wordpress/components';

import * as spinners from '@wpcloud/components/spinner/library';


const spinnerOptions = Object.keys(spinners)
	.filter((key) => key !== 'Spinner')
	.map((key) => ({ label: key.replace(/([a-z])([A-Z])/g, '$1 $2').toLowerCase(), value: key })
);

export default function( { attributes, updateAttribute } ) {
	const { spinner, background, speed, hide, size} = attributes;


	return (
		<>
			<SelectControl
				label={__('Spinner Style')}
				value={ spinner }
				options={ spinnerOptions }
				onChange={ updateAttribute('spinner') }
			/>
			<TextControl
				label={__('Size')}
				value={size}
				onChange={updateAttribute('size')}
			/>
			<SelectControl
				label={__('Speed')}
				value={speed}
				options={[
					{ label: 'Slow', value: 1.5 },
					{ label: 'Normal', value: 1},
					{ label: 'Fast', value: 0.5 },
				]}
				onChange={updateAttribute('speed')}
			/>
			<ToggleControl
				label={__('Background')}
				checked={background}
				help={ __('Show background if available') }
				onChange={updateAttribute('background')}
			/>
			<ToggleControl
				label={__('Hide by default')}
				checked={hide}
				onChange={updateAttribute('hide')}
			/>
		</>
	);
}