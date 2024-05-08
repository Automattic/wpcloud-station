/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const { url, target, externalLink, label } = attributes;
	const blockProps = useBlockProps.save();

	return (
			<a { ...blockProps } href={url} target={ target } rel="noopener" data-name="name">
				{ label }
				{ externalLink && ( <span className="dashicons dashicons-external" /> )	}
			</a>
	);
}
