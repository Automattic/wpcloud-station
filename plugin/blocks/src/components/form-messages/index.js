/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextareaControl,
	RadioControl,
	Button,
	SelectControl,
	__experimentalSpacer as Spacer,
} from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import './editor.scss'
import { updateAttribute } from '@wpcloud/controls';

function deepMerge(target, source) {
	for (let key in source) {
		if (typeof source[key] === 'object' && source[key] !== '') {
			target[key] = deepMerge(target[key] || {}, source[key]);
		} else {
			target[key] = source[key];
		}
	}
	return target;
}

registerBlockType( metadata.name, {
	edit: ({ attributes, setAttributes, clientId }) => {
		const { previewAction, previewType, previewSchema } = attributes;
				const [schemaString, setSchemaString] = useState("");

		const [schema, actions] = useSelect((select) => {
			const { getBlocks } = select('core/block-editor');
			const kids = getBlocks(clientId);

			const { messages, actions } = kids.reduce((acc, child) => {
				const { message, action } = child.attributes;
				if (message) {
					acc.messages.push(message);
				}
				if (action) {
					acc.actions.push(action);
				}
				return acc;
			}, { messages: [], actions: [] });

			const schema = messages.reduce((s, message) => {
				const template = message.match(/\${(.*?)}/g);

				let found = {};
				if (template) {
					found = template.reduce((t, temp) => {
						let keys = temp.replace('${', '').replace('}', '').split('.');
						const subSchema = keys
							.reduceRight((k, key) => { return { [key]: k } }, '');
						return deepMerge(t,subSchema );
					}, {});
				}
				return deepMerge(s, found);
			}, {});


			return [schema, [...new Set(['', ...actions])]];

		}, [clientId]);

				// clear out any previews when we mount the block.
		useEffect(() => {
			setAttributes({ previewAction: '', previewType: '', previewSchema: schema });
			setSchemaString(JSON.stringify(schema, null, 2));
		}, []);
		const update = updateAttribute(setAttributes);

		return (
			<>
				<InspectorControls>
					<PanelBody title={__(' Preview Message Settings')}>

						<SelectControl
							label={ __( 'Message Action' ) }
							value={previewAction}
							options={ actions.map((action) => ({ label: action || 'All', value: action })) }
							onChange={ update('previewAction')}
							help={ __('Only show messages with this action in the editor')}
						/>
						<RadioControl
							label={ __( 'Message Type' ) }
							selected={ previewType }
							options={ [
								{ label: __( 'Success' ), value: 'success' },
								{ label: __('Error'), value: 'error' },
								{ label: __('All'), value: '' },
							] }
							onChange={update('previewType')}
						/>
						<TextareaControl
							label={__('Message Schema')}
							value={schemaString}
							onChange={setSchemaString}
						/>
						<Spacer >
							<Button
								variant="primary"
								onClick={() => {
									try {
										const schema = JSON.parse(schemaString);
										setAttributes({ previewSchema: schema });
									} catch (error) {
											alert('Invalid schema');
									}
								 }}>
								{__('Update Preview Message Data')}
							</Button>
						</Spacer>
						<Button
							variant="primary"
							onClick={() => {
								setAttributes({ previewAction: '', previewType: '' });
								setSchemaString(JSON.stringify(schema, null, 2));
							}}
						>
						{__('Reset Preview')}
						</Button>

					</PanelBody>
				</InspectorControls>

				<details {...useBlockProps()}>
					<summary>{__('Click for form messages')}</summary>
					<InnerBlocks />
				</details>
			</>
		)

	},
	save: () => (
		<div {...useBlockProps.save()}>
			<InnerBlocks.Content />
		</div>
	)
});