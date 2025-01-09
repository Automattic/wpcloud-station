

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';


export default function Edit() {
	const blockProps = useBlockProps();

	const template = [
		 [ 'wpcloud/nav-item' ],
	];

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template,
	});

	console.log("nav list innner blk prop", innerBlocksProps);

	return (

			<ul
				{ ...innerBlocksProps }
			/>

	);
}
