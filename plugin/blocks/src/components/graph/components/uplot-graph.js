/**
 * External dependencies
 */
import React, { useRef, useState, useEffect } from 'react';
import UplotReact from 'uplot-react';
import 'uplot/dist/uPlot.min.css';

/**
 * Internal dependencies
 */
import Overlay from './overlay';
import useGraphOptions from './lib/useGraphOptions';
import useUplotHeight from './lib/useUplotHeight';

/**
 * UplotGraph component for rendering charts using uPlot
 *
 * @param {Object} props - Component props
 * @param {Array} props.data - Chart data (controlled by state in graph.js)
 * @param {boolean} props.refreshing - Whether the chart is refreshing (controlled by state in graph.js)
 * @param {Object} props.options - All other options for the chart
 * @returns {JSX.Element} - Rendered component
 */
export default function UplotGraph({
		data,
		refreshing,
		options
}) {
	// Extract options
	const {
		title,
		series,
		meta,
		containerRef,
		showLegend,
		legendPosition,
		type,
		orientation,
		dimension,
	} = options;
	// Use useRef instead of useState to avoid re-renders when setting the uPlot instance
	const uplotInstanceRef = useRef(null);
	// Store the original data
	const originalDataRef = useRef(data);
	// Store the current chart data
	const [chartData, setChartData] = useState(data);

	// Update original data reference when data changes
	useEffect(() => {
		originalDataRef.current = data;
		setChartData(data);
	}, [data]);

	// Use the height adjustment hook
	const { handleChartCreated } = useUplotHeight({
		containerRef,
		uplotInstanceRef,
		data: chartData,
		series,
		showLegend
	});

	// Memoize the options params to avoid unnecessary re-renders
	const graphOptionsParams = React.useMemo(() => ({
		title,
		data: chartData,
		series,
		meta,
		containerRef,
		showLegend,
		legendPosition,
		type,
		orientation,
		dimension
	}), [title, chartData, series, meta, containerRef, showLegend, legendPosition, type, orientation, dimension]);

	// Get graph options from the hook
	// Use chartData instead of the original data
	const { data: d, ...uplotOptions } = useGraphOptions(graphOptionsParams);

	// No need to extract colors here as they're handled in the SideLegend component

	// Return the chart
	return (
		<div className="wpcloud-uplot-graph">
			<div className="wpcloud-uplot-graph__chart-container" style={{ height: '100%' }}>
				<UplotReact
					options={uplotOptions}
					data={d}
					onCreate={(chart) => {
						// Only set the instance if it's not already set or if it's a different instance
						if (!uplotInstanceRef.current || uplotInstanceRef.current !== chart) {
							uplotInstanceRef.current = chart;

							// Use the handleChartCreated function from the hook
							handleChartCreated(chart);
						}
					}}
				/>
				{refreshing && <Overlay refreshing={true} />}
			</div>
		</div>
	);
}
