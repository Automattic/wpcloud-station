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

	if ( ! options ) {
		const optionData = window.wpcloud?.siteMutableFields || {};

		options = optionData[name]?.options || [ { value: '', label: '-' } ];
	}

	// Loading options from the backend can have the form { value: 'label' }
	if ( ! Array.isArray( options ) ) {
		const optionData = [];
		for ( const [ key, value ] of Object.entries( options ) ) {
			optionData.push( { value: key, label: value } );
		}
		options = optionData;
	}

	const selected = options.find( ( option ) => option.value === value || option.default );
	return (
		<div className="wpcloud-form-input--select--wrapper">
			<select
				className={ className }
				aria-label={ __( 'Select' ) }
				onChange={ ( event ) => onValueChange( event.target.value ) }
				style={ inputStyle }
				name={ name }
				required={ required }
				value={ selected?.value }
			>
				{ options.map( ( option, index ) => ( <option key={ index } value={ option.value }>{ option.label }</option> ) ) }
			</select>
		</div>
	);
}
