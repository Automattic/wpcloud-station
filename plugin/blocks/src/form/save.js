/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const Save = () => {
	const blockProps = useBlockProps.save();

	return (
		<form
			{ ...blockProps }
			className="wpcloud-block-form"
			encType="text/plain"
		>
			<InnerBlocks.Content />
		</form>
	);
};
export default Save;
