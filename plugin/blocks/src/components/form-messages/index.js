/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, TextareaControl, TextControl, RadioControl, Button, SelectControl } from '@wordpress/components';


import { useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import './editor.scss'
import { updateAttribute } from '@wpcloud/controls';

registerBlockType( metadata.name, {
	edit: ({ attributes, setAttributes, clientId }) => {
		const { previewAction, previewType } = attributes;

		// clear out any previews when we mount the block.
		useEffect(() => {
			setAttributes({ previewAction: '', previewType: '' });
		}, []);

		const actions = useSelect((select) => {
			const { getBlocks } = select('core/block-editor');
			const kids = getBlocks(clientId);
			const actions = kids
				.filter(({ attributes }) => attributes.action)
				.map((child) => child.attributes.action);
			return [ ...new Set([ '', ...actions]) ];
		}, [ clientId ] );


		const update = updateAttribute(setAttributes);

		return (
			<>
				<InspectorControls>
					<PanelBody title={__(' Preview Message Settings')}>
						<Button
							onClick={() => setAttributes({ previewAction: '' })}
						>
						{__('Clear Preview')}
						</Button>
						<SelectControl
							label={ __( 'Message Action' ) }
							value={previewAction}
							options={ actions.map((action) => ({ label: action || 'All', value: action })) }
							onChange={update('previewAction')}
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

/*

					<RadioControl
							label={ __( 'Message Type' ) }
							selected={ messagePreview.type }
							options={ [
								{ label: __( 'Success' ), value: 'success' },
								{ label: __('Error'), value: 'error' },
								{ label: __('All'), value: 'all' },
							] }
							onChange={updatePreview('type')}
						/>

						<TextareaControl
							label={__('Test Response')}
							value={messagePreview.response}
							onChange={(responseString) => {
								try {
									const response = JSON.parse(responseString);
									updatePreview('response')(response);
								} catch (error) {
									alert('Invalid JSON');
									console.error(error);
								}
							}
							}
						/>
						*/