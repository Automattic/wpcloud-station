/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const controls = <></>;

function Hidden( { value, onChange, className, styleProps } ) {
	const { colorProps, borderProps } = styleProps;
	return (
		<>
			{ controls }
			<input
				type="hidden"
				className={ classNames(
					className,
					'wpcloud-dashboard-form-input__hidden',
					colorProps.className,
					borderProps.className
				) }
				aria-label={ __( 'Value' ) }
				value={ value }
				onChange={ ( event ) => onChange( event.target.value ) }
			/>
		</>
	);
}

export default Hidden;
