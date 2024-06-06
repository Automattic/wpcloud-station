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
	const { openOnLoad, hideHeader } = attributes;

	return (
		<div
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				'wpcloud-block-expanding-section__content-wrapper', {
					'is-open': openOnLoad,
					'hide-header': hideHeader,
				}
			) }
		>
			<div className="wpcloud-block-expanding-section__content">
				<div className="wpcloud-block-expanding-section__content-inner">
					<InnerBlocks.Content />
				</div>
				</div>
		</div>
	);
};
export default Save;