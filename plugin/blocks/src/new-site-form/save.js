/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
export default function save() {
	const blockProps = useBlockProps.save();
	return (
		<div { ...blockProps } className="wpcloud-new-site-form">
			<InnerBlocks.Content />
		</div>
	);
}
