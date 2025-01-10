/**
 * External dependencies
 */
import classnames from 'classnames';

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

export default function Edit({ attributes, setAttributes }) {

	const { title, tag } = attributes;
	const blockProps = useBlockProps();

	const template = [
		[ 'wpcloud/list-item' ],
	];
	const { children, ...innerBlocksProps } = useInnerBlocksProps(blockProps, {
		template, templateLock: false
	});

	return (
		<details {...blockProps}
			className={classnames(blockProps.className, 'dropdown')}
		>
			<summary>
				<RichText
          tagName={tag}
          value={title}
          onChange={(newTitle) => setAttributes({ title: newTitle })}
          placeholder={__('Dropdown', 'wpcloud')}
        />
			</summary>
			<ul { ...innerBlocksProps} >
				{children}
			</ul>
		</details>
	);
}
