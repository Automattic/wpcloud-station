/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
export default function save( { attributes, className } ) {
	const { isHeader } = attributes;
	const blockProps = useBlockProps.save();

	const Cell = isHeader ? 'th' : 'td';
	return (
		<Cell
			{ ...blockProps }
			className={ classNames( blockProps.className, className ) }
		>
			<InnerBlocks.Content />
		</Cell>
	);
}
