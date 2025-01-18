

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import './editor.scss';

export default function Edit() {
	const blockProps = useBlockProps();
	const { children, ...innerBlocksProps } = useInnerBlocksProps(blockProps,
		{
			template: [['core/paragraph', { metadata: { name: "Item" }}]],
		});

	return (
			<li { ...innerBlocksProps} >
				{children}
			</li>
	);
}
