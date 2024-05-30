/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const Save = ( {attributes} ) => {
	const blockProps = useBlockProps.save();
	const { clickToToggle } = attributes;

	return (
		<div
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				'wpcloud-block-expanding-section', {
					'click-to-toggle': clickToToggle,
				}
			) }
		>
			<InnerBlocks.Content />
		</div>
	);
};
export default Save;