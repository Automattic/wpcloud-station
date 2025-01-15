/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { PanelBody } from '@wordpress/components';


/**
 * Internal dependencies
 */
import ButtonControls from '../controls/buttonControl';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit: ({ attributes, setAttributes }) => {
		const blockProps = useBlockProps();
		const innerBlockProps = useInnerBlocksProps(blockProps, {
			template: [['core/paragraph']],
			allowedBlocks: ['wpcloud/icon', 'core/paragraph', 'core/heading'],
			templateLock: false
		});

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Title Settings')}>
						<ButtonControls {...{ attributes, setAttributes }} />
					</PanelBody>
				</InspectorControls>
				<summary {...innerBlockProps} />
			</>
		);
	},
	save: ({ attributes }) => {
		const { button, secondary, contrast, outline } = attributes;
		const innerBlockProps = useInnerBlocksProps.save();

		innerBlockProps.role = button ? 'button' : '';

		return (
			<summary
				{...innerBlockProps}
				className={classnames(innerBlockProps.className, {
					'secondary': secondary,
					'contrast': contrast,
					'outline': outline,
				})}
			/>);
	}
} );
