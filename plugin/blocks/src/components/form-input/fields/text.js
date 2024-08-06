/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';


export default function TextField( {
	attributes,
	className,
	onPlaceholderChange,
} ) {
	const { placeholder, required, type, name, uniqueId, inputStyle } = attributes;

	const TagName = type === 'textarea' ? 'textarea' : 'input';
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
