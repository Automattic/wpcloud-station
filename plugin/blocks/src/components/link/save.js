/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import { Dashicon } from '@wordpress/components';

export default function save( { attributes } ) {
	const { url, target, externalLink, label } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<a
			{ ...blockProps }
			href={ url }
			target={ target }
			rel="noopener"
			data-name="name"
		>
			{ label }
			{ externalLink && <Dashicon icon="external" /> }
		</a>
	);
}
