
/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
/**
 * Internal dependencies
 */
import { updateAttribute } from './utils';


export default function ({ attributes, setAttributes }) {
	const update = updateAttribute(setAttributes);
	const { button, outline, contrast, secondary } = attributes;

	return (
		<>
				<ToggleControl
					label={ __( 'Display as Button' ) }
					checked={ button }
					onChange={ update( 'button' ) }
				/>
				{ button && (
					<>
						<ToggleControl
							label={ __( 'outline' ) }
							checked={ outline }
							onChange={ update( 'outline' ) }
						/>
						<ToggleControl
							label={ __( 'contrast' ) }
							checked={ contrast }
							onChange={ update( 'contrast' ) }
						/>
						<ToggleControl
							label={ __( 'secondary' ) }
							checked={ secondary }
							onChange={ update( 'secondary' ) }
						/>
					</>
					)}
		</>
	);
}