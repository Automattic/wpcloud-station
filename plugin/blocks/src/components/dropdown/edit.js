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
import { ButtonControls, IconControls } from '@wpcloud/controls';
import { updateAttribute } from '@wpcloud/controls/utils';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {

	const blockProps = useBlockProps();
	const style = blockProps.style;
	delete blockProps.style;

	const { hideChevron, summary, level, levelOptions, useIcon, outline, contrast, secondary, button } = attributes;
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
					<ButtonControls {...{ attributes, setAttributes }} />
					<ToggleControl
						label={__('Use Icon')}
						checked={useIcon}
						onChange={update('useIcon')}
					/>
					{ useIcon &&
						(<IconControls {...{ attributes, setAttributes }} />)
					}

				</PanelBody>
			</InspectorControls>

			<details {...blockProps}
				role={ button ? 'button' : '' }
				className={classnames(className, 'dropdown', {
					'hide-chevron': hideChevron,
					'outline': outline,
					'contrast': contrast,
					'secondary': secondary,
				})}
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
