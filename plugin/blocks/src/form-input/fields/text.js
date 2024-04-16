/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	RichText,
	useBlockProps,
	__experimentalUseBorderProps as useBorderProps,
	__experimentalUseColorProps as useColorProps,
} from '@wordpress/block-editor';

const controls = (
	<>
	</>
);

function Text( props ) {
	const {
		label,
		placeholder,
		onPlaceholderChange,
		onLabelChange,

		blockProps
	} = props;
	return (
		<div { ...blockProps } >
			{ controls }
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

			</span>
		</div>
	);
}

export default Text;