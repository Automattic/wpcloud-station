/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const Save = () => {
	const blockProps = useBlockProps.save();

	return (
		<div
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				'wpcloud-block-ssh-user-list'
			) }
		>
			<InnerBlocks.Content />
		</div>
	);
};
export default Save;
