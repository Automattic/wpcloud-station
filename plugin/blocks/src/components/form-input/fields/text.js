/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

export default function TextField( {
	attributes,
	className,
	onPlaceholderChange,
} ) {
	const { placeholder, required, type } = attributes;

	const controls = <></>;

	const TagName = type === 'textarea' ? 'textarea' : 'input';
	return (
		<>
			{ controls }
			<TagName
				type={ 'textarea' === type ? undefined : type }
				className={ classNames(
					className,
					'wpcloud-block-form-input__input',
				) }
				aria-label={ __( 'Optional placeholder text' ) }
				placeholder={
					placeholder ? undefined : __( 'Optional placeholder…' )
				}
				value={ placeholder }
				onChange={ ( event ) =>
					onPlaceholderChange( event.target.value )
				}
				aria-required={ required }
			/>
		</>
	);
}
