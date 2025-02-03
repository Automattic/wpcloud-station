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
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, RadioControl, ToggleControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import { useDispatch } from '@wordpress/data';

import * as icons from '@wordpress/icons';
const Icon = icons.Icon;

/**
 * Internal dependencies
 */
import { updateAttribute } from '@wpcloud/controls/utils';
import { IconControls } from '@wpcloud/controls';
import metadata from './block.json';
import { renderMessage } from './view';
import './editor.scss';

// Check if any property in the previewSchema is non-empty.
function shouldRenderMessageWith( preview ) {
	if (typeof preview  !== "object" || preview === null) {
		return preview !== "";
	}
	return Object.values(preview).some(shouldRenderMessageWith);
}

registerBlockType( metadata.name, {
	edit: ({ attributes, setAttributes, context }) => {
		const { success, action, dismiss, icon, message, iconSize } = attributes;

		const { createNotice } = useDispatch('core/notices');

		const showPreviewMessageWarning = () => {
			createNotice(
				'warning',
				__('Clear the preview schema before editing form message'),
				{
					type: 'snackbar',
					isDismissible: true,
				}
			);
		};

		const blockProps = useBlockProps();
		const update = updateAttribute(setAttributes);
		const {
			'wpcloud-form-message/previewAction': previewAction,
			'wpcloud-form-message/previewType': previewType,
			'wpcloud-form-message/previewSchema': previewSchema
		 } = context;
		let style = {};
		if (previewAction && previewAction !== action) {
			style = { display: 'none' };
		}
		if (previewType && previewType !== (success ? 'success' : 'error')) {
			style = { display: 'none' };
		}

		let previewMessage = null;
		if ( shouldRenderMessageWith( previewSchema ) ) {
			previewMessage = renderMessage(message, previewSchema);
		}

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
						<ToggleControl
							label={__('Dismissible')}
							checked={dismiss}
							onChange={update('dismiss')}
						/>
					{ dismiss && <IconControls {...{ attributes, setAttributes }} /> }
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
					style={style}
				>
					<RichText tagName="p"
						value={previewMessage || message}
						onChange={(value) => {
							if (previewMessage) {
								showPreviewMessageWarning();
								return;
							}
							update('message')(value);
						}}
						placeholder={__(`${success ? 'Success' : 'Error'} message.`)} />
					{ dismiss && <Icon icon={ icons[icon] } size={iconSize} /> }
			</article>
			</>
		)

	},
	save: ({ attributes }) => {
		const { success, action, message, dismiss, icon, iconSize } = attributes;
		const blockProps = useBlockProps.save();

		const role = success ? 'status' : 'alert';
		const ariaLive = success ? 'polite' : 'assertive';

		return (
			<article  {...blockProps}
				className={classnames(
					blockProps.className,
					'display-none',
					'wpcloud-form-message',
					{
						'wpcloud-form-message--success': success,
						'wpcloud-form-message--error': !success,
						'wpcloud-form-message--dismissable': dismiss
					})}
				data-wpcloud-action={action}
				data-message-type={success ? 'success' : 'error'}
				role={role}
				aria-live={ariaLive}
				data-message-template={message}
			>
				<p />
				{ dismiss && <Icon icon={ icons[icon] } size={iconSize}  class="dismiss" /> }
			</article>
		);
	},
} );