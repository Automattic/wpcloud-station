/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const Save = ({ attributes }) => {
	const { hideOnOpen } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<div
			{ ...blockProps }
			className={ classNames(
				blockProps.className,
				'wpcloud-block-expanding-section__header-wrapper', {
					'hide-on-open': hideOnOpen,
				}
			) }
		>
			<div className="wpcloud-block-expanding-section__header">
				<InnerBlocks.Content />
			</div>
		</div>
	);
};
export default Save;