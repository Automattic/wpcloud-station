/**
 * External dependencies
 */
import React, { useRef, useState, useEffect } from 'react';
import UplotReact from 'uplot-react';
import 'uplot/dist/uPlot.min.css';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import Overlay from './overlay';
import SideLegend from './SideLegend';
import useGraphOptions from './lib/useGraphOptions';
import useUplotHeight from './lib/useUplotHeight';

/**
 * UplotGraph component for rendering charts using uPlot
 *
 * @param {Object} props - Component props
 * @returns {JSX.Element} - Rendered component
 */

export default function UplotGraph({
		title,
		data,
		series,
		meta,
		containerRef,
		showLegend,
		legendPosition,
		type,
		orientation,
		dimension,
		refreshing
}) {
	// Use useRef instead of useState to avoid re-renders when setting the uPlot instance
	const uplotInstanceRef = useRef(null);
	// Track which series is active (null means all series are shown)
	const [activeSeriesIdx, setActiveSeriesIdx] = useState(null);
	// Store the original data
	const originalDataRef = useRef(data);
	// Store the current chart data
	const [chartData, setChartData] = useState(data);

	// Update original data reference when data changes
	useEffect(() => {
		originalDataRef.current = data;
		setChartData(data);
		// Reset active series when data changes
		setActiveSeriesIdx(null);
	}, [data]);

	// Use the height adjustment hook
	const { containerDimensionsRef, handleChartCreated } = useUplotHeight({
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
	const { data: d, ...options } = useGraphOptions(graphOptionsParams);

	// Extract colors from series for the SideLegend
	const seriesColors = options.series.map(s => s.stroke).filter(Boolean);

	// Track which series are toggled off
	const [toggledOffSeries, setToggledOffSeries] = useState(new Set());

	// Handle legend item click
	const handleLegendItemClick = (idx) => {
		// If idx is null, show all series
		if (idx === null) {
			setActiveSeriesIdx(null);
			setChartData(originalDataRef.current);
			setToggledOffSeries(new Set());

			// Update the uPlot instance if it exists
			if (uplotInstanceRef.current) {
				uplotInstanceRef.current.setData(originalDataRef.current);
			}
			return;
		}

		// Different behavior based on dimension
		if (dimension === 'atomic_site_id') {
			// For atomic_site_id, show only the clicked series (isolate mode)
			setActiveSeriesIdx(idx);

			// Create a new data array with just the x-axis values and the clicked series
			const newData = originalDataRef.current.map((series, seriesIdx) => {
				if (seriesIdx === 0) {
					// Keep x-axis values
					return series;
				} else if (seriesIdx === idx) {
					// Keep the clicked series data
					return originalDataRef.current[seriesIdx];
				} else {
					// For other series, create an array of the same length as the x-axis but with null values
					return Array(originalDataRef.current[0].length).fill(null);
				}
			});

			setChartData(newData);

			// Update the uPlot instance if it exists
			if (uplotInstanceRef.current) {
				uplotInstanceRef.current.setData(newData);
			}
		} else {
			// For other dimensions, toggle the clicked series (toggle mode)
			const newToggledOffSeries = new Set(toggledOffSeries);

			if (newToggledOffSeries.has(idx)) {
				// If the series is already toggled off, turn it back on
				newToggledOffSeries.delete(idx);
			} else {
				// Otherwise, toggle it off
				newToggledOffSeries.add(idx);
			}

			setToggledOffSeries(newToggledOffSeries);

			// Create a new data array with toggled series set to null
			const newData = originalDataRef.current.map((series, seriesIdx) => {
				if (seriesIdx === 0) {
					// Keep x-axis values
					return series;
				} else if (newToggledOffSeries.has(seriesIdx)) {
					// For toggled off series, create an array of null values
					return Array(originalDataRef.current[0].length).fill(null);
				} else {
					// Keep the data for visible series
					return originalDataRef.current[seriesIdx];
				}
			});

			setChartData(newData);

			// Update the uPlot instance if it exists
			if (uplotInstanceRef.current) {
				uplotInstanceRef.current.setData(newData);
			}

			// Update active series for styling
			setActiveSeriesIdx(newToggledOffSeries.size > 0 ? -1 : null);
		}
	};

	return (
		<div className={classnames('wpcloud-uplot-graph',{
			'wpcloud-uplot-graph--with-side-legend': showLegend && legendPosition === 'right'
		})}>
			<div className="wpcloud-uplot-graph__chart-container">
				<UplotReact
					options={options}
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

			{showLegend && legendPosition === 'right' && (
				<div className="wpcloud-uplot-graph__legend-container">
					<SideLegend
						series={options.series}
						colors={seriesColors}
						onLegendItemClick={handleLegendItemClick}
						dimension={dimension}
					/>
				</div>
			)}
		</div>
	);
}
