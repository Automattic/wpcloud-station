/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

export default function SelectField( {
	attributes,
	className,
	onValueChange,
} ) {
	const { name, value, inputStyle, required } = attributes;
	let { options } = attributes;

	if (!options) {
		options = [{ label: `{ ${name} } `, value: '' }];
	}

	return (
		<div className="wpcloud-form-input--select--wrapper">
			<select
				className={ className }
				aria-label={ __( 'Select' ) }
				value={ value }
				onChange={ ( event ) => onValueChange( event.target.value ) }
				style={ inputStyle }
				name={ name }
				required={ required }
			>
				{ options.map( ( option, index ) => ( <option key={ index } value={ option.value }>{ option.label }</option> ) ) }
			</select>
		</div>
	);
}
