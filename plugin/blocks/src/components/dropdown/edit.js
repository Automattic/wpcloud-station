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
	InspectorControls,
	HeadingLevelDropdown,
	BlockControls,
	RichText,
	InnerBlocks
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
/**
 * Internal dependencies
 */
import { updateAttribute } from '@wpcloud/controls/utils';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {

	const blockProps = useBlockProps();
	const style = blockProps.style;
	delete blockProps.style;

	const { hideChevron, summary, level, levelOptions } = attributes;
	const tagName = 'h' + level;

	const update = updateAttribute(setAttributes);

	const template = [
		['wpcloud/list-item'],
	];
	const innerBlocksProps  = useInnerBlocksProps(blockProps, {
		template,
		allowedBlocks: ['wpcloud/dropdown-list'],
	});

	// remove the *-color class names
	const className = blockProps.className
		.split(' ')
		.filter((c) => !c.endsWith('-color'))
		.join(' ');

	return (
		<>
			<BlockControls group="block">
				<HeadingLevelDropdown
					value={ level }
					options={ levelOptions }
					onChange={ update( 'level' ) }
				/>
			</BlockControls>

			<InspectorControls>
				<PanelBody title={__('Dropdown Settings')}>
					<ToggleControl
						label={__('Hide Chevron')}
						checked={hideChevron}
						onChange={update('hideChevron')}
					/>
				</PanelBody>
			</InspectorControls>

			<details {...blockProps}
				className={classnames(className, 'dropdown')}
			>
				<summary style={style}>
					<RichText
						tagName={tagName}
						value={summary}
						onChange={update('summary')}
						placeholder={__('Title')}
					/>
				</summary>
				<ul>
					<InnerBlocks {...innerBlocksProps} className="dropdown-list" />
				</ul >
			</details>
		</>
	);
}
