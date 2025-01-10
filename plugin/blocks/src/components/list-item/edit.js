

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import './editor.scss';

export default function Edit() {
	const blockProps = useBlockProps();
	const template = [
		['core/paragraph', { metadata: { name: "Item" }}],
	];
	const { children, ...innerBlocksProps } = useInnerBlocksProps( blockProps, { template, templateLock: false } );

	return (
			<li { ...innerBlocksProps} >
				{children}
			</li>
	);
}
