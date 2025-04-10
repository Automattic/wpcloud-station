
import { useEffect, useState } from 'react';
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
	Spinner
} from '@wordpress/components';

import apiFetch from '@wordpress/api-fetch';

import {  updateAttribute } from '@wpcloud/controls';
import './editor.scss';


export default function Edit( { attributes, setAttributes } ) {

	const { metric, dimension, type, title, showLegend, minWidth } = attributes;
	const update = updateAttribute(setAttributes);

	const [metrics, setMetrics] = useState({});
	const [dimensions, setDimensions] = useState({});
	const [loading, setLoading] = useState(true);

	useEffect(() => {
		async function fetchAvailable() {
			const available = await apiFetch({ path: 'wpcloud-station/v1/metrics/available' });
			setMetrics(available.metrics);
			setDimensions(available.dimensions);
			setLoading(false);
		}
		fetchAvailable();
	}, []);

	const controls = (
		<InspectorControls>
			<PanelBody title={__('graph Settings')}>

				{loading && <Spinner />}
				{!loading && (
					<>
						<SelectControl
							label={__('Metric')}
							value={metric}
							options={Object.keys(metrics).map((key) => ({ label: metrics[key], value: key })) }
							onChange={update('metric')}
						/>
						<SelectControl
							label={__('Dimension')}
							value={dimension}
							options={Object.keys(dimensions).map((key) => ({ label: dimensions[key], value: key })) }
							onChange={update('dimension')}
						/>
					</>)}
				<SelectControl
					label={ __( 'Type' ) }
					value={ type }
					options={ [
						{ label: __( 'Area' ), value: 'area' },
						{ label: __( 'Line' ), value: 'line' },
						{ label: __('Bar'), value: 'bar' },
						{ label: __('Stacked Bar'), value: 'stacked-bar' },
					] }
					onChange={ update('type') }
				/>
				<TextControl
					label={ __( 'Title' ) }
					value={ title }
					onChange={ update('title') }
				/>
				<ToggleControl
					label={ __( 'Show Legend' ) }
					checked={ showLegend }
					onChange={ update('showLegend') }
				/>
			</PanelBody>
			<PanelBody title={__('Graph Size')}>
				<TextControl
					label={ __( 'Minimum Width' ) }
					value={ minWidth }
					onChange={ update('minWidth') }
				/>
				</PanelBody>
		</InspectorControls>
	)
	return (
		<div {...useBlockProps()}>
			{ controls }
			<figure>
				<div className="graph-container">graph placeholder.. </div>
				<RichText
					tagName="figcaption"
					className={
						'wpcloud-block-graph__title'
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
