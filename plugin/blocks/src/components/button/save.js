/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes, className } ) {
	const { label, isPrimary, iconOnly } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<div
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				className,
				'wpcloud-block-button',
				{
					'is-primary': isPrimary
				}
			) }
		>

			{!iconOnly && (
				<span className={'wpcloud-block-button--label'}>
					<RichText.Content value={label} />
				</span>
			)}
			<InnerBlocks.Content />
		</div>
	);
}
