/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl } from '@wordpress/components';

/**
 * Internal dependencies
 */

import metadata from './block.json';
import { updateAttribute } from '@wpcloud/controls';
import SpinnerControl from './controls';
import * as spinners from './library';
import { Spinner } from './library';

registerBlockType(metadata.name, {
	icon: (<Spinner spinner={spinners.barsRotateFade} animation={false} speed={0}  />),
	edit: ({ attributes, setAttributes }) => {
		const { size, spinner, background, speed } = attributes;
		const style = {
			width:size, height:size
		}
		const blockProps = useBlockProps({style});
		const update = updateAttribute(setAttributes);
		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Spinner Settings')}>
						<TextControl
							label={__('size')}
							value={size}
							onChange={update('size')}
						/>
						<SelectControl
							label={__('Speed')}
							value={speed}
							onChange={update('speed')}
						/>
						<SpinnerControl {...{ attributes, setAttributes }} />
					</PanelBody>

				</InspectorControls>
				<div {...blockProps}>
					<Spinner spinner={spinners[spinner]} background={background} speed={speed} />
				</div>
			</>
		)
	},
	save: ({ attributes }) => {
		const { size, spinner, background, speed, hide} = attributes;
		const style = {
			width: size, height: size,
			//stroke: 'currentColor'
		}
		if (hide) {
			style.display = 'none';
		}
		const blockProps = useBlockProps.save({ style });

		return (
			<div {...blockProps}>
				<Spinner spinner={spinners[spinner]} background={background} speed={speed} foo="bar" />
			</div>
		)
	}
} );
