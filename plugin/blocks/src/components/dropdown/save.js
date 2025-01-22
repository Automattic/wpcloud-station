/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps, RichText } from '@wordpress/block-editor';
import * as icons from '@wordpress/icons';
const Icon = icons.Icon;

export default function save({ attributes }) {
	const {
		accordion,
		hideChevron,
		level,
		summary,
		useIcon,
		icon,
		iconSize,
		button,
		secondary,
		outline,
		contrast,
		onRight
	} = attributes;

	const blockProps = useBlockProps.save();

	const summaryContent = useIcon
		? <Icon icon={icons[icon]} size={iconSize} />
		: <RichText.Content tagName={`h${level}`} value={summary} />;

	const summaryClassname = classnames({
		'secondary': secondary,
		'outline': outline,
		'contrast': contrast
	});

	return (
		<details {...blockProps}
			className={classnames(
			blockProps.className,
				{
					'dropdown' : !accordion,
					'hide-chevron': hideChevron,
					'open-right': onRight
				}
		)}>
			<summary
				role={ button ? 'button' : '' }
				className={summaryClassname}
			>
				{summaryContent}
			</summary>
			{ accordion
				? ( <InnerBlocks.Content /> )
				: (
					<ul>
						<InnerBlocks.Content />
					</ul>
				)
			}
		</details>
	)
}