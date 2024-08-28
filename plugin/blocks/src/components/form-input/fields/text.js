/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';


export default function TextField( {
	attributes,
	className,
	onPlaceholderChange,
} ) {
	const { placeholder, required, name, uniqueId, inputStyle } = attributes;
	let { type } = attributes;
	const TagName = type === 'textarea' ? 'textarea' : 'input';
	if ('datetime' === type) {
		type = 'datetime-local';
	}
	return (

			<TagName
				type={ 'textarea' === type ? undefined : type }
				className={ className }
				aria-label={ __( 'Optional placeholder text' ) }
				placeholder={ placeholder || undefined }
				onChange={ ( event ) =>
					onPlaceholderChange( event.target.value )
				}
				name={ name }
				id={ uniqueId }
				style={ inputStyle }
				required={ required }
				aria-required={ required }
			/>
	);
}
