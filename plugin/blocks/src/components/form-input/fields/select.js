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
	let { options = { label: `{ ${name} } `, value: '' } } = attributes;

	// Loading options from the backend can have the form { value: 'label' }
	if ( ! Array.isArray( options ) ) {
		const optionData = [];
		for ( const [ key, value ] of Object.entries( options ) ) {
			optionData.push( { value: key, label: value } );
		}
		options = optionData;
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
