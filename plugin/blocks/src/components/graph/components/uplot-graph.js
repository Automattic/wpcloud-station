/**
 * External dependencies
 */
import React, { useRef, useEffect } from 'react';
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

	// Use the height adjustment hook
	const { containerDimensionsRef, handleChartCreated } = useUplotHeight({
		containerRef,
		uplotInstanceRef,
		data,
		series,
		showLegend
	});

	// Add an effect to log when the uPlot instance is created or updated
	React.useEffect(() => {
		console.log('uplotInstanceRef.current in useEffect:', uplotInstanceRef.current);
	}, [uplotInstanceRef.current]);

	// Memoize the options params to avoid unnecessary re-renders
	const graphOptionsParams = React.useMemo(() => ({
		title,
		data,
		series,
		meta,
		containerRef,
		showLegend,
		legendPosition,
		type,
		orientation,
		dimension
	}), [title, data, series, meta, containerRef, showLegend, legendPosition, type, orientation, dimension]);

	// Get graph options from the hook
	const { data: d, ...options } = useGraphOptions(graphOptionsParams);

	// Function to highlight a specific series
	const highlightSeries = (index) => {
		console.log('highlightSeries called with index:', index);
		console.log('uplotInstanceRef.current:', uplotInstanceRef.current);

		if (!uplotInstanceRef.current) {
			console.error('uPlot instance is not available');
			return;
		}

		// Make sure we have the original series properties
		if (!uplotInstanceRef.current._originalSeriesProps) {
			console.log('Creating _originalSeriesProps');
			uplotInstanceRef.current._originalSeriesProps = uplotInstanceRef.current.series.map(s => ({
				stroke: s.stroke,
				fill: s.fill,
				alpha: s.alpha || 1
			}));
		}

		try {
			// Log the current series state
			console.log('Current series state:', uplotInstanceRef.current.series);
			console.log('Original series properties:', uplotInstanceRef.current._originalSeriesProps);

			// Update each series based on whether it's highlighted
			uplotInstanceRef.current.series.forEach((s, i) => {
				if (i === 0) return; // skip x-axis

				const origProps = uplotInstanceRef.current._originalSeriesProps[i];
				const isHighlighted = i === index;

				console.log(`Setting properties for series ${i} (highlighted: ${isHighlighted})`);
				console.log('Original properties:', origProps);

				// Set properties based on highlight state
				uplotInstanceRef.current.setSeries(i, {
					alpha: isHighlighted ? 1 : 0.3,
					stroke: isHighlighted ? origProps.stroke : 'rgba(200, 200, 200, 0.3)',
					fill: isHighlighted ? origProps.fill : 'rgba(200, 200, 200, 0.1)'
				});
			});

			// Force redraw
			console.log('Calling redraw');
			uplotInstanceRef.current.redraw();
		} catch (error) {
			console.error('Error in highlightSeries:', error);
		}
	};

	// Function to reset all series to their original state
	const resetSeries = () => {
		console.log('resetSeries called');

		if (!uplotInstanceRef.current) {
			console.error('uPlot instance is not available in resetSeries');
			return;
		}

		try {
			// Make sure we have the original series properties
			if (!uplotInstanceRef.current._originalSeriesProps) {
				console.error('No _originalSeriesProps found');
				return;
			}

			console.log('Original series properties:', uplotInstanceRef.current._originalSeriesProps);

			// Reset all series to their original properties
			uplotInstanceRef.current.series.forEach((s, i) => {
				if (i === 0) return; // skip x-axis

				const origProps = uplotInstanceRef.current._originalSeriesProps[i];
				console.log(`Resetting series ${i} to:`, origProps);

				uplotInstanceRef.current.setSeries(i, {
					alpha: origProps.alpha,
					stroke: origProps.stroke,
					fill: origProps.fill
				});
			});

			// Force redraw
			console.log('Calling redraw in resetSeries');
			uplotInstanceRef.current.redraw();
		} catch (error) {
			console.error('Error in resetSeries:', error);
		}
	};

	// Extract colors from series for the SideLegend
	const seriesColors = options.series.map(s => s.stroke).filter(Boolean);

	return (
		<div className={classnames('wpcloud-uplot-graph',{
			'wpcloud-uplot-graph--with-side-legend': showLegend && legendPosition === 'right'
		})}>
			<div className="wpcloud-uplot-graph__chart-container">
				<UplotReact
					options={options}
					data={d}
					onCreate={(chart) => {
						console.log('onCreate called with chart:', chart);
						console.log('Chart series:', chart.series);

						// Only set the instance if it's not already set or if it's a different instance
						if (!uplotInstanceRef.current || uplotInstanceRef.current !== chart) {
							uplotInstanceRef.current = chart;
							console.log('Setting uplotInstanceRef.current to chart');

							// Store original series properties for later use
							chart._originalSeriesProps = chart.series.map(s => ({
								stroke: s.stroke,
								fill: s.fill,
								alpha: s.alpha || 1
							}));
							console.log('Stored original series properties:', chart._originalSeriesProps);

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
						onSeriesHover={highlightSeries}
						onSeriesLeave={resetSeries}
					/>
				</div>
			)}
		</div>
	);
}
