/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
export default function save( { attributes, className } ) {
	const { adminOnly, style, name, url, target, externalLink, label } = attributes;
	const blockProps = useBlockProps.save();
	return (
				<div
				{ ...blockProps }
				className={classNames(
					blockProps.className,
					className,
					'wpcloud-block-link',
					{
						'is-button': style === 'button',
						'is-admin-only': adminOnly,
					}
				)}
			data-name={name}
			>
			<a href={url} target={ target } rel="noopener">
				{ label }
				{ externalLink && ( <span className="dashicons dashicons-external" /> )	}
			</a>
			</div>
	);
}
