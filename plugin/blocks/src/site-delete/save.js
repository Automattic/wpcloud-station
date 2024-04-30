/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function save() {
	const blockProps = useBlockProps.save();
	return (
		<div
			{ ...blockProps }
			className={ classNames(
				'wpcloud-block-site-delete',
				blockProps?.className
			) }
		>
			<InnerBlocks.Content />
		</div>
	);
}
