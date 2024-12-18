
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	RichText,
	useBlockProps
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	SelectControl,
} from '@wordpress/components';
import { useCallback, useEffect } from '@wordpress/element';

import './editor.scss';


export default function Edit( { attributes, setAttributes } ) {

	const { metric, type, title, showLegend } = attributes;

	const controls = (
		<InspectorControls>
			<PanelBody title={__('Plot Settings')}>

				<SelectControl
					label={ __( 'Metric' ) }
					value={ metric }
					options={ [
						{ label: __( 'Status Codes' ), value: 'status_codes' },
					] }
					onChange={(newVal) => setAttributes({ metric: newVal })}
				/>
				<SelectControl
					label={ __( 'Type' ) }
					value={ type }
					options={ [
						{ label: __( 'Area' ), value: 'area' },
						{ label: __( 'Line' ), value: 'line' },
						{ label: __( 'Bar' ), value: 'bar' },
					] }
					onChange={ ( newVal ) => setAttributes( { type: newVal } ) }
				/>
				<TextControl
					label={ __( 'Title' ) }
					value={ title }
					onChange={ ( newVal ) => setAttributes( { title: newVal } ) }
				/>
				<ToggleControl
					label={ __( 'Show Legend' ) }
					checked={ showLegend }
					onChange={ ( newVal ) => setAttributes( { showLegend: newVal } ) }
				/>
			</PanelBody>
		</InspectorControls>
	)
	return (
		<div {...useBlockProps()}>
			{ controls }
			<figure>
				<div className="plot-container">Plot placeholder.. </div>
				<RichText
					tagName="figcaption"
					className={
						'wpcloud-block-plot__title'
					}
					value={ title }
					onChange={ ( newVal ) => {
						setAttributes( {
							title: newVal,
							metadata: { name: newVal },
						} );
					} }
					placeholder={ __( 'Title' ) }
				/>
			</figure>
		</div>
	);
}
