/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const Save = ({ attributes }) => {
	const { openOnLoad } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<div
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				'wpcloud-block-expanding-section', {
					'is-open': openOnLoad,
					'is-closed': ! openOnLoad,
				}
			) }
		>
			<InnerBlocks.Content />
		</div>
	);
};
export default Save;