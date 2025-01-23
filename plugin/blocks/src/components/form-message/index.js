/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	HeadingLevelDropdown,
	BlockControls,
	RichText,
	InnerBlocks
} from '@wordpress/block-editor';
import { PanelBody, TextControl, RadioControl } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { updateAttribute } from '@wpcloud/controls/utils';
import metadata from './block.json';
import './editor.scss';


registerBlockType( metadata.name, {
	edit: ({ attributes, setAttributes }) => {
		const { success, action }	= attributes;
		const template = [ ['core/paragraph'] ];

		const blockProps = useBlockProps();

		const innerBlocksProps = useInnerBlocksProps(blockProps, { template, templateLock: 'insert' });
		const update = updateAttribute(setAttributes);

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Message Settings')}>
						<RadioControl
							label={ __( 'Message Type' ) }
							help="Dropdown or Accordion. Dropdown will open over the content, Accordion will push the content down."
							selected={ success }
							options={ [
								{ label: __( 'Success' ), value: true },
								{ label: __( 'Error' ), value: false },
							] }
							onChange={(option) => {
								setAttributes({ success: option === 'true' });
							}}
						/>
						<TextControl
							label={ __( 'WP Cloud Action' ) }
							value={ action }
							onChange={update('action')}
							help={ __( 'The WP Cloud form action to respond to. Use the template {response.message} in the rich text to display the form response.' ) }
						/>
					</PanelBody>
				</InspectorControls>
				<article {...innerBlocksProps}
					className={classnames(
						blockProps.className,
						'wpcloud-form-message',
						{ 'wpcloud-form-message--success': success },
						{ 'wpcloud-form-message--error': !success })
					}
			data-wpcloud-action={action}/>
			</>
		)

	},
	save: ({ attributes, innerBlocks }) => {
		const { success, action } = attributes;
		const blockProps = useBlockProps.save();
		const innerBlocksProps = useInnerBlocksProps.save(blockProps);
		const role = success ? 'status' : 'alert';
		const ariaLive = success ? 'polite' : 'assertive';

		console.log(innerBlocks);
		return (
			<article  {...innerBlocksProps}
				className={classnames(
					blockProps.className,
					'hidden',
					'wpcloud-form-message',
					{ 'wpcloud-form-message--success': success },
					{ 'wpcloud-form-message--error': !success })}
				data-wpcloud-action={action}
				data-message-type={success ? 'success' : 'error'}
				role={role}
				aria-live={ariaLive}
			/>
		);
	},
} );