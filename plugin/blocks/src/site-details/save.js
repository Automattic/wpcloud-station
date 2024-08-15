/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const { adminOnly } = attributes;
	const blockProps = useBlockProps.save();
	return (
		<div
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				'wpcloud-block-site-detail-card',
				{
				 'wpcloud-admin-only': adminOnly,
				}
			) }
		>
			<InnerBlocks.Content />
		</div>
	);
}
