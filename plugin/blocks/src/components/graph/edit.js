
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
	__experimentalNumberControl as NumberControl,
	Spinner
} from '@wordpress/components';

import apiFetch from '@wordpress/api-fetch';

import { updateAttribute } from '@wpcloud/controls';
import './editor.scss';


export default function Edit( { attributes, setAttributes } ) {

	const { title, type, orientation, showLegend, metric, dimension, resolution, topX, summarize, minWidth  } = attributes;
	const update = updateAttribute(setAttributes);

	const [metrics, setMetrics] = useState({});
	const [dimensionOptions, setDimensionOptions] = useState({});
	const [loading, setLoading] = useState(true);

	useEffect(() => {
		async function fetchAvailable() {
			const available = await apiFetch({ path: 'wpcloud-station/v1/metrics/available' });
			setMetrics(available.metrics);
			//setDimensions(available.dimensions);
			setLoading(false);
		}
		fetchAvailable();
	}, []);

	// Define all possible options for Dimensions based on Metrics
	// @TODo - Move this to use the php function so single mapping?
	//  Or alternatively update the json response to contain the mapping groups so not making multiple calls.
	const getDimensionsMap = function( metric ) {
		if( metric.startsWith( 'php_') && metric !== 'php_response_time_sum' ) {
			return [
				{ label: 'HTTP Verb', value: 'http_verb' },
				{ label: 'HTTP Host', value: 'http_host' },
				{ label: 'DataCenter', value: 'datacenter' },
				{ label: 'Atomic Site ID', value: 'atomic_site_id' },
				{ label: 'Burst Status', value: 'burst_status' },
			];
		} else if ( metric.startsWith( 'mysql_pool_' ) ) {
			return [
				{ label: 'Pool Server Number', value: 'pool' },
				{ label: 'Pool Server Name', value: 'server' },
			];
		} else if ( metric.startsWith( 'mysql_' ) ) {
			return [
				{ label: 'Pool Server Number', value: 'pool' },
				{ label: 'Pool Server Name', value: 'server' },
				{ label: 'Atomic Site ID', value: 'atomic_site_id' },
			];
		}else if ( metric.startsWith( 'cgroup_' ) ) {
			return [
				{ label: 'Pool Server Number', value: 'pool' },
				{ label: 'Pool Server Name', value: 'server' },
				{ label: 'Atomic Site ID', value: 'atomic_site_id' },
			];
		} else if ( metric === 'uniques' || metric === 'views' ) {
			return [
				{ label: 'Host Name', value: 'hostname' },
			];
		}
		return [
			{ label: 'Server Protocol (HTTP Version)', value: 'server_protocol' },
			{ label: 'Request Method (HTTP Verb)', value: 'request_method' },
			{ label: 'Host Name', value: 'http_host' },
			{ label: 'Response Code (HTTP Status)', value: 'http_status' },
			{ label: 'User Agent', value: 'http_user_agent' },
			{ label: 'Referer Domain', value: 'referer_domain' },
			{ label: 'Request Renderer', value: 'request_renderer' },
			{ label: 'Is Upstream Cached?', value: 'is_upstream_cached' },
			{ label: 'WP Admin Ajax Action', value: 'admin_ajax_action' },
			{ label: 'Visitor ASN', value: 'asn' },
			{ label: 'Visitor Country Code', value: 'country_code' },
			{ label: 'Visitor Is Crawler?', value: 'is_crawler' },
			{ label: 'Visitor Device Type', value: 'device_type' },
			{ label: 'Visitor Is Logged In?', value: 'visitor_is_logged_in' },
			{ label: 'Visitor Operating System', value: 'visitor_os' },
			{ label: 'Visitor Browser', value: 'visitor_browser' },
			{ label: 'Edge Cache Status', value: 'edge_cache_status' },
			{ label: 'Is Rate Limited?', value: 'is_rate_limited' },
			{ label: 'Rate Limit Reason', value: 'rate_limit_reason' },
			{ label: 'DataCenter', value: 'datacenter' },
			{ label: 'Request URL (excluding Query String)', value: 'request_url_no_qs' },
			{ label: 'Remote Address', value: 'remote_addr' },
			{ label: 'Atomic Site ID', value: 'atomic_site_id' },
			{ label: 'Proxy Type', value: 'proxy_type' },
		]
	};

	// Update Dimension Options when Metric is selected.
	useEffect( () => {
		// Update Dimension options when metrics changes
		if ( metrics ) {
			// @ToDo - don't update if in same map.
			const _dimensionOptions = getDimensionsMap(metric);
			setDimensionOptions(_dimensionOptions);
			// update dimension if map has changed.
			if (! _dimensionOptions.some(dimObj => dimObj.value === dimension)) {
				setAttributes({ 'dimension': _dimensionOptions[0].value });
			}
		}
	}, [metric] );

	const controls = (
		<InspectorControls>
			<PanelBody title={__('Graph Settings')}>
				<TextControl
					label={ __( 'Title' ) }
					value={ title }
					onChange={ update('title') }
				/>
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
				<SelectControl
					label={__('Orientation')}
					value={orientation}
					options={[
						{ label: __('Vertical'), value: 'vertical' },
						{ label: __('Horizontal'), value: 'horizontal' },
					]}
					onChange={ update('orientation') }
				/>
				<ToggleControl
					label={ __( 'Show Legend' ) }
					checked={ showLegend }
					onChange={ update('showLegend') }
				/>
			</PanelBody>
			<PanelBody title={__('Graph Data')}>
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
							options={[ ...dimensionOptions, ]}
							onChange={update('dimension')}
						/>
						<NumberControl
							label={__('Top X')}
							value={topX}
							onChange={update('topX')}
							min={1}
							max={20}
							step={1}
							isShiftStepEnabled={true}
							shiftStep={1}
						/>
						<ToggleControl
							label={__('Summarize')}
							checked={summarize}
							onChange={update('summarize')}
						/>
						{ ! summarize && (
							<SelectControl
								label={__('Resolution')}
								value={resolution}
								options={[
									{ label: __('10 Seconds'), value: '10' },
									{ label: __('20 Seconds'), value: '20' },
									{ label: __('30 Seconds'), value: '30' },
									{ label: __('1 Minute'), value: '60' },
									{ label: __('2 Minutes'), value: '120' },
									{ label: __('3 Minutes'), value: '180' },
									{ label: __('4 Minutes'), value: '240' },
									{ label: __('5 Minutes'), value: '300' },
									{ label: __('10 Minutes'), value: '600' },
									{ label: __('15 Minutes'), value: '900' },
									{ label: __('20 Minutes'), value: '1200' },
									{ label: __('30 Minutes'), value: '1800' },
									{ label: __('1 Hour'), value: '3600' },
									{ label: __('2 Hours'), value: '7200' },
									{ label: __('3 Hours'), value: '10800' },
									{ label: __('4 Hours'), value: '14400' },
									{ label: __('6 Hours'), value: '21600' },
									{ label: __('8 Hours'), value: '28800' },
									{ label: __('12 Hours'), value: '43200' },
									{ label: __('1 Day'), value: '86400' },
								]}
								onChange={update('resolution')}
							/>
						) }
					</>)}

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
