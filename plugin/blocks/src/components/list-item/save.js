/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';
import * as icons from '@wordpress/icons';

const Icon = icons.Icon;

const Wrap = ({ condition, wrapper, children }) => condition ? wrapper(children) : children;

const Save = ( {attributes} ) => {
	const blockProps = useBlockProps.save();
	const { url, tag, text, secondary, contrast, outline, icon, iconOnly, asButton } = attributes;

	const classes = classNames({
		'is-icon-only': iconOnly,
		'secondary': secondary,
		'contrast': contrast,
		'outline': outline,
	});

	let content = (
		<>
			{!iconOnly && (<RichText.Content
				tagName={tag}
				value={text}
			/>)}
			{icon && (
				<Icon icon={icons[icon]} />
			)}
		</>
	);

	if (url) {
		content = (
			<a href={url} className={ asButton ? '' : classes}>
				{content}
			</a>
		)

		if (asButton) {
			content = (
				<button className={classes}>
					{content}
				</button>
			)
		}
	}


	return (
		<li {...blockProps}>
			{content}
		</li>
	);
};
export default Save;