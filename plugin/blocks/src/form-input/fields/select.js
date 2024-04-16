/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function Select(props) {
	const {
		label,
		options,
		value,
		onChange,
		onLabelChange,
		styleProps: {
			colorProps,
			borderProps,
		},
		blockProps
	} = props;
	return (
		<div { ...blockProps } >
			<span className={ classNames( "wpcloud-dashboard-form-input__label" ) }>
				<RichText
					tagName="span"
					className="wp-block-form-input__label-content"
					value={ label }
					onChange={ ( event ) => onLabelChange( event.target.value ) }
					aria-label={ label ? __( 'Label' ) : __( 'Empty label' ) }
					data-empty={ label ? false : true }
					placeholder={ __( 'Type the label for this input' ) }
				/>
				<select
					className={classNames(
						'wpcloud-dashboard-form-input__select',
						colorProps.className,
						borderProps.className
					)}
					aria-label={__('Select')}
					value={ value }
					onChange={ ( event ) => onChange( event.target.value ) }
					style={{
						...borderProps.style,
						...colorProps.style,
					}}
				>
					{ options.map( ( option ) => (
						<option value={ option.value }>{ option.label }</option>
					) ) }
				</select>
			</span>
		</div>
	);
}

export default Select;
