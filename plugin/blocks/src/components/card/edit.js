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
import { useSyncInnerBlockMetaName, useInnerBlocksInserter }  from '@wpcloud/hooks';
import './editor.scss';

/**
 *
 * @param {Object} props               Component props.
 * @param {Object} props.attributes
 * @param {Object} props.setAttributes
 * @return {Element} Element to render.
 */
export default function Edit( {clientId, ...props }) {
	const blockProps = useBlockProps();

	useSyncInnerBlockMetaName(clientId, (children) => {
		const target = children.find((child) => child.attributes.section === 'header');
		if (target && target.attributes.text) {
			return `Card: ${target.attributes.text}`;
		}
		return '';
	} );

	const template = [
		['wpcloud/card-section', { section: "header", metadata: { name: "Header" } }],
		['core/paragraph', {metadata: { name: "Body" }}],
		['wpcloud/card-section', { section: "footer", tag: "span", metadata: { name: "Footer" } }],
	];

	const innerBlocksProps =  useInnerBlocksProps( blockProps, { template } );

	return (
		<>
			<article
				{ ...blockProps }
				{ ...innerBlocksProps }
			/>
		</>
	);
}
