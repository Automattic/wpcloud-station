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
import { PanelBody, ToggleControl, RadioControl } from '@wordpress/components';
import * as icons from '@wordpress/icons';
const Icon = icons.Icon;

/**
 * Internal dependencies
 */
import { useSyncMetaName } from '@wpcloud/hooks';
import { ButtonControls, IconControls } from '@wpcloud/controls';
import { updateAttribute } from '@wpcloud/controls/utils';
import './editor.scss';

export default function Edit({ clientId, attributes, setAttributes }) {

	const blockProps = useBlockProps();
	const style = blockProps.style;
	delete blockProps.style;


	const {
		accordion,
		hideChevron,
		summary,

		level,
		levelOptions,

		useIcon,
		icon,
		iconSize,

		button,
		outline,
		contrast,
		secondary,
	} = attributes;

	useSyncMetaName(clientId, summary);
	const update = updateAttribute(setAttributes);

	const dropdownProps = {
		template: [['wpcloud/list-item']],
		allowedBlocks: ['wpcloud/list-item']
	};

	const accordionProps = {
		template: [['core/paragraph']]
	};


	let innerBlocksProps = useInnerBlocksProps(blockProps);

	// remove the *-color class names
	const className = blockProps.className
		.split(' ')
		.filter((c) => !c.endsWith('-color'))
		.join(' ');

	const summaryContent = useIcon
		? <Icon icon={icons[icon]} size={iconSize} />
		: <RichText
			tagName={`h${level}`}
			value={summary}
			onChange={update('summary')}
			placeholder={__('Title')}
		/>;

	return (
		<>
			<BlockControls group="block">
				<HeadingLevelDropdown
					value={level}
					options={levelOptions}
					onChange={update('level')}
				/>
			</BlockControls>

			<InspectorControls>
				<PanelBody title={__('Dropdown Settings')}>
					<RadioControl
						label={ __( 'Dropdown Type' ) }
						help="Dropdown or Accordion. Dropdown will open over the content, Accordion will push the content down."
						selected={ accordion }
						options={ [
							{ label: __( 'Dropdown' ), value: false },
							{ label: __( 'Accordion' ), value: true },
					] }
						onChange={(option) => {
							setAttributes({ accordion: option === 'true' });
						} }
					/>
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
					{useIcon &&
						(<IconControls {...{ attributes, setAttributes }} />)
					}

				</PanelBody>
			</InspectorControls>

			<details {...blockProps}
				className={classnames(className, {
					'dropdown': !accordion,
					'hide-chevron': hideChevron,
				})}
			>
				<summary
					style={style}
					role={button ? 'button' : ''}
					className={classnames({
						'secondary': secondary,
						'outline': outline,
						'contrast': contrast
					})}
				>
					{summaryContent}
				</summary>
				{ accordion
					? (<InnerBlocks {...innerBlocksProps} {...accordionProps} />)
					: (<ul>
						<InnerBlocks {...innerBlocksProps} {...dropdownProps} />
						</ul>)}
			</details>
		</>
	);
}
