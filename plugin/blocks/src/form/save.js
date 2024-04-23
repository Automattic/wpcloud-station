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
		<form
			{ ...blockProps }
			encType="text/plain"
			className={ classNames(
				blockProps.className,
				'wpcloud-block-form'
			) }
		>
			<InnerBlocks.Content />
		</form>
	);
};
export default Save;
