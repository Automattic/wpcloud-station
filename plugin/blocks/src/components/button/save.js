

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { label, iconOnly } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<div
			{ ...blockProps }
			className={'wpcloud-block-button__content'}
		>

			{!iconOnly && (
				<span className={'wpcloud-block-button__label'}>
					<RichText.Content value={label} />
				</span>
			)}
			<InnerBlocks.Content />
		</div>
	);
}
