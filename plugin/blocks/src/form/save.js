/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const Save = ( { attributes } ) => {
	const blockProps = useBlockProps.save();
	const { inline } = attributes;

	return (
		<form
			{ ...blockProps }
			encType="text/plain"
			className={ classNames(
				blockProps.className,
				'wpcloud-block-form',
				{ 'is-inline': inline }
			) }
		>
			<InnerBlocks.Content />
		</form>
	);
};
export default Save;
