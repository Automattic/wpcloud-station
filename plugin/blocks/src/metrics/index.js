/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	SelectControl,
	Spinner
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import {  updateAttribute } from '@wpcloud/controls';
import metadata from './block.json';

function edit( {attributes, setAttributes } ) {
	const update = updateAttribute(setAttributes);
	const blockProps = useBlockProps();

	const template = [
		['core/group',
			{
				className: 'wpcloud-metrics',
				metadata: {
					name: 'Graphs',
				},
				layout: {
					type: "grid",
					columnCount: 2,
					minimumColumnWidth: null
				},
			},
			[],
		],
	];

	const controls = (
		<InspectorControls>
			<PanelBody title={__('Metrics Settings')}>
				<SelectControl
					label={__('Type')}
					value={attributes.type}
					options={[
						{ label: 'Client', value: 'client' },
						{ label: 'Site', value: 'site' },
					]}
					onChange={ update( 'type' ) }
				/>
			</PanelBody>
		</InspectorControls>
	)


	return (
		<div {...blockProps}>
			{controls}
			<InnerBlocks template={template} />
		</div>
	);
}

registerBlockType( metadata.name, {
	edit,
	save: ({ attributes }) => <div {...useBlockProps.save()} id={attributes.id || 'metrics' } data-metrics-attributes={JSON.stringify(attributes)}><InnerBlocks.Content /></div>
} );