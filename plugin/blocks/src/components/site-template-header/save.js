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
		<span
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				'wpcloud-block-site-list--header'
			) }
		>
			<InnerBlocks.Content />
		</span>
	);
};
export default Save;
