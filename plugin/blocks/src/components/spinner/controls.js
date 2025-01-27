/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { SelectControl, ToggleControl } from '@wordpress/components';

import * as spinners from '@wpcloud/components/spinner/library';

import { updateAttribute } from '@wpcloud/controls/utils';

const spinnerOptions = Object.keys(spinners)
	.filter((key) => key !== 'Spinner')
	.map((key) => ({ label: key.replace(/([a-z])([A-Z])/g, '$1 $2').toLowerCase(), value: key })
);

export default function( { attributes, setAttributes } ) {
	const { spinner, background, speed, hide} = attributes;
	const update = updateAttribute( setAttributes );

	return (
		<>
			<SelectControl
				label={__('Spinner Style')}
				value={ spinner }
				options={ spinnerOptions }
				onChange={ update('spinner') }
			/>
			<SelectControl
				label={__('Speed')}
				value={speed}
				options={[
					{ label: 'Slow', value: 1.5 },
					{ label: 'Normal', value: 1},
					{ label: 'Fast', value: 0.5 },
				]}
				onChange={update('speed')}
			/>
			<ToggleControl
				label={__('Background')}
				checked={background}
				help={ __('Show background if available') }
				onChange={update('background')}
			/>
			<ToggleControl
				label={__('Hide by default')}
				checked={hide}
				onChange={update('hide')}
			/>
		</>
	);
}