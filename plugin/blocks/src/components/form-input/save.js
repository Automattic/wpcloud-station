/**
 * External dependencies
 */
import classNames from 'classnames';
import removeAccents from 'remove-accents';

/**
 * WordPress dependencies
 */
import {
	RichText,
	useBlockProps,
	InnerBlocks,
	__experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles, // eslint-disable-line @wordpress/no-unsafe-wp-apis
	__experimentalGetColorClassesAndStyles as getColorClassesAndStyles, // eslint-disable-line @wordpress/no-unsafe-wp-apis
} from '@wordpress/block-editor';
import { __unstableStripHTML as stripHTML } from '@wordpress/dom'; // eslint-disable-line @wordpress/no-unsafe-wp-apis

/**
 * Get the name attribute from a content string.
 *
 * @param {string} content The block content.
 *
 * @return {string} Returns the slug.
 */
const getNameFromLabel = ( content ) => {
	return (
		removeAccents( stripHTML( content ) )
			// Convert anything that's not a letter or number to a hyphen.
			.replace( /[^\p{L}\p{N}]+/gu, '_' )
			// Convert to lowercase
			.toLowerCase()
			// Remove any remaining leading or trailing hyphens.
			.replace( /(^-+)|(-+$)/g, '' )
	);
};

function renderSelect(
	{ options, label, name, required },
	inputClasses,
	inputStyle
) {
	if ( ! options ) {
		options = [ { label: `{ ${ name } }`, value: '' } ];
	}
	return (
		<select
			className={ classNames(
				'wpcloud-station-form-input__select',
				inputClasses
			) }
			style={ inputStyle }
			name={ name || getNameFromLabel( label ) }
			required={ required }
			aria-required={ required }
		>
			{ options.map( ( option ) => (
				<option key={ option.value } value={ option.value }>
					{ option.label }
				</option>
			) ) }
		</select>
	);
}

function renderText(
	{ type, name, label, required, placeholder, uniqueId },
	inputClasses,
	inputStyle
) {
	const TagName = 'textarea' === type ? 'textarea' : 'input';
	return (
		<TagName
			className={ inputClasses }
			type={ 'textarea' === type ? undefined : type }
			name={ name || getNameFromLabel( label ) }
			required={ required }
			aria-required={ required }
			placeholder={ placeholder || undefined }
			style={inputStyle}
			id={ uniqueId }
		/>
	);
}

function renderField( attributes ) {
	const { type, displayAsToggle, submitOnChange } = attributes;

	const borderProps = getBorderClassesAndStyles( attributes );
	const colorProps = getColorClassesAndStyles( attributes );

	const inputStyle = {
		...borderProps.style,
		...colorProps.style,
	};

	const inputClasses = classNames(
		'wpcloud-block-form-input__input',
		colorProps.className,
		borderProps.className,
		{
			'is-toggle': displayAsToggle,
			'submit-on-change': submitOnChange
		}
	);

	return 'select' === type
		? renderSelect( attributes, inputClasses, inputStyle )
		: renderText( attributes, inputClasses, inputStyle );
}


export default function save( { attributes } ) {
	const { type, label, name, value, inlineLabel, hideLabel, displayAsToggle, uniqueId } = attributes;
	const blockProps = useBlockProps.save();

	if ( 'hidden' === type ) {
		return <input type={ type } name={ name } value={ value } />;
	}

	const inputField = renderField( attributes );

	return (
		<div { ...blockProps }>
			{ /* eslint-disable jsx-a11y/label-has-associated-control */}
			{ displayAsToggle && inputField }
			<label
				className={ classNames( 'wpcloud-block-form-input__label', {
					'is-label-inline': inlineLabel,
					'is-toggle': displayAsToggle,
				})}
				for={ uniqueId }
			>
				{ displayAsToggle && (<span className="toggle-container"></span>)}
				{ ! hideLabel && (
					<span className="wpcloud-block-form-input__label-content">
						<span className="wpcloud-block-form-input__label-text">
							<RichText.Content value={label} />
						</span>
						<InnerBlocks.Content />
					</span>
				) }
				{ ! displayAsToggle && inputField }
			</label>
			{ /* eslint-enable jsx-a11y/label-has-associated-control */ }
		</div>
	);
}
