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

	return (
		<li {...blockProps}>
			<Wrap
				condition={asButton}
				wrapper={(content) => (<button classNames={classes}> {content}</button>)}>
				<Wrap
					condition={url}
					wrapper={(content) => (<a className={!asButton && classes}> {content}</a>)}>
					<>
						{!iconOnly && (<RichText.Content
							tagName={tag}
							value={text}
						/>)}
						{icon && (
							<Icon icon={icons[icon]} />
						)}
					</>
				</Wrap>
			</Wrap>
		</li>
	);


};
export default Save;