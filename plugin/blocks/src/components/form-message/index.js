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
		const { success, action, message }	= attributes;
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
							help={ __( 'The WP Cloud form action to respond to. Use JavaScript templates to render response information. For example `${response.message}` will display the response message' ) }
						/>
					</PanelBody>
				</InspectorControls>
				<article {...blockProps}
					className={classnames(
						blockProps.className,
						'wpcloud-form-message',
						{ 'wpcloud-form-message--success': success },
						{ 'wpcloud-form-message--error': !success })
					}
					data-wpcloud-action={action}
				>
					<RichText tagName="p" value={message} onChange={update('message')} />
			</article>
			</>
		)

	},
	save: ({ attributes, innerBlocks }) => {
		const { success, action, message } = attributes;
		const blockProps = useBlockProps.save();

		const role = success ? 'status' : 'alert';
		const ariaLive = success ? 'polite' : 'assertive';


		return (
			<article  {...blockProps}
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
				data-message-template={message}
			>
				<p />
			</article>
		);
	},
} );