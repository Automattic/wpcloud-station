/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const Save = ({ attributes }) => {
	const blockProps = useBlockProps.save();

	return (
		<div
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				'wpcloud-block-expanding-section__content-wrapper', {
					'is-open': attributes.openOnLoad,
				}
			) }
		>
			<div className="wpcloud-block-expanding-section__content">
				<InnerBlocks.Content />
				</div>
		</div>
	);
};
export default Save;