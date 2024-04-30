/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

export default function SelectField( {
	attributes,
	className,
	styleProps = {},
	onValueChange,
} ) {
	const { value, options } = attributes;
	const { borderProps, colorProps } = styleProps;

	const controls = <></>;
	return (
		<>
			{ controls }
			<select
				className={ classNames(
					className,
					'wpcloud-dashboard-form-input__select',
					colorProps.className,
					borderProps.className
				) }
				aria-label={ __( 'Select' ) }
				value={ value }
				onChange={ ( event ) => onValueChange( event.target.value ) }
				style={ {
					...borderProps.style,
					...colorProps.style,
				} }
			>
				{ options.map( ( option ) => (
					<option key={ option.value } value={ option.value }>
						{ option.label }
					</option>
				) ) }
			</select>
		</>
	);
}
