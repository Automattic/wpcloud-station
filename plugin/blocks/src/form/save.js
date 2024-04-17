/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const Save = ( { attributes } ) => {
	const blockProps = useBlockProps.save();
	const { submissionMethod } = attributes;

	return (
		<form
			{ ...blockProps }
			className='wpcloud-block-form'
			encType='text/plain'
		>
			<InnerBlocks.Content />
		</form>
	);
};
export default Save;