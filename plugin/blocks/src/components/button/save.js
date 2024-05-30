/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes, className } ) {
	const { label, iconOnly } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<div
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				'wpcloud-block-button',
			) }
		>

			{!iconOnly && (
				<span className={'wpcloud-block-button__label'}>
					<RichText.Content value={label} />
				</span>
			)}
			<InnerBlocks.Content />
		</div>
	);
}
