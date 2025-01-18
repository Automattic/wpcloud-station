

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

	const template = [
		[ 'wpcloud/nav-list' ],
	];

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template,
	});

	return (
		<nav
			{ ...innerBlocksProps }
		/>
	);
}
