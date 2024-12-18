
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const { metric, type, showLegend } = attributes;
	const blockProps = useBlockProps.save();
	return (
		<div { ...blockProps } data-metric={metric} data-plot={type} data-show-legend={showLegend} data-site-id={150743966}>
			<InnerBlocks.Content />
		</div>
	);
}
