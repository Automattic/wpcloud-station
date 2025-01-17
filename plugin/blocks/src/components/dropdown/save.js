/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const { hideChevron, level, summary, useIcon, icon, button, secondary, outline, contrast } = attributes;
	const blockProps = useBlockProps.save();

	const summaryContent = useIcon
		? <span className="dropdown-icon">{icon}</span>
		: <RichText.Content tagName={`h${level}`} value={summary} />;

	const summaryClassname = classnames({
		'secondary': secondary,
		'outline': outline,
		'contrast': contrast
	});

	return (
		<details {...blockProps}
			className={classnames('dropdown',
			blockProps.className,
				{
					'hide-chevron': hideChevron,
				}
		)}>
			<summary
				role={ button ? 'button' : '' }
				className={summaryClassname}
			>
				{summaryContent}
			</summary>
			<ul>
				<InnerBlocks.Content />
			</ul>
		</details>
	)
}