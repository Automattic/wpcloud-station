/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const blockProps = useBlockProps.save();
		const { hideChevron, level, summary, icon } = attributes;
		const summaryContent = icon
			? <span className="dropdown-icon">{icon}</span>
			: <RichText.Content tagName={`h${level}`} value={summary} />;

		return (
			<details {...blockProps} className={ classnames('dropdown',
				blockProps.className,
				{
					'hide-chevron': hideChevron,
				 }
			)}>
				<summary>{summaryContent}</summary>
				<ul className="dropdown-list">
					<InnerBlocks.Content />
				</ul>
			</details>
		)
}