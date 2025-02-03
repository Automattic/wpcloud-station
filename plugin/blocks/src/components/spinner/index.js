/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * Internal dependencies
 */

import metadata from './block.json';
import { updateAttribute } from '@wpcloud/controls';
import SpinnerControls from './controls';
import * as spinners from './library';
import { Spinner } from './library';

registerBlockType(metadata.name, {
	icon: (<Spinner spinner={spinners.barsRotateFade} animation={false} speed={0}  />),
	edit: ({ attributes, setAttributes }) => {
		const { size, spinner, background, speed } = attributes;
		const blockProps = useBlockProps();
		const update = updateAttribute(setAttributes);
		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Spinner Settings')}>
						<SpinnerControls {...{ attributes, updateAttribute: update }} />
					</PanelBody>

				</InspectorControls>
				<div {...blockProps}>
					<Spinner spinner={spinners[spinner]} background={background} speed={speed} size={size} />
				</div>
			</>
		)
	},
	save: ({ attributes }) => {
		const { size, spinner, background, speed, hide } = attributes;
		const blockProps = useBlockProps.save();
		const className = classNames(
			blockProps.className,
			{ 'visibility-none': hide }
		);


		return (
			<div {...blockProps} className={className}>
				<Spinner spinner={spinners[spinner]} background={background} speed={speed} size={size} />
			</div>
		)
	}
} );
