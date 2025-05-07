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
					/>
				</div>
			)}
		</div>
	);
}
