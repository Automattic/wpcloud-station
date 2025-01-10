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

	const { title } = attributes;
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
          tagName="span" // The HTML tag for the editable content
          value={title} // The current value of the editable text
          onChange={(newTitle) => setAttributes({ title: newTitle })} // Update the attribute on change
          placeholder={__('Dropdown', 'wpcloud')} // Placeholder when empty
        />
			</summary>
			<ul { ...innerBlocksProps} >
				{children}
			</ul>
		</details>
	);
}
