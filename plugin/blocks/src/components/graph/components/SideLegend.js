/**
 * Side Legend component for displaying a legend on the right side of the graph
 *
 * @param {Object} props - Component props
 * @returns {JSX.Element} - Rendered component
 */
import React, { useState, useEffect } from 'react';

const SideLegend = ({ series, onSeriesHover, onSeriesLeave, colors: propColors }) => {
	const colors = propColors || series.map(s => s.stroke).filter(Boolean);
	// Filter out the first series (which is usually the x-axis)
	const displaySeries = series.slice(1);

	const handleMouseEnter = (index) => {
		// Only call onSeriesHover if it's provided
		if (typeof onSeriesHover === 'function') {
			// Add 1 to account for the x-axis series at index 0
			onSeriesHover(index + 1);
		}
	};

	const handleMouseLeave = () => {
		// Only call onSeriesLeave if it's provided
		if (typeof onSeriesLeave === 'function') {
			onSeriesLeave();
		}
	};

	return (
		<div className="wpcloud-side-legend">
			<div className="wpcloud-side-legend__items">
				{displaySeries.map((s, i) => (
					<div
						key={i}
						className="wpcloud-side-legend__item"
						onMouseEnter={() => handleMouseEnter(i)}
						onMouseLeave={handleMouseLeave}
					>
						<div
							className="wpcloud-side-legend__marker"
							style={{ backgroundColor: s.stroke || colors?.[i] || '#000' }}
						></div>
						<div className="wpcloud-side-legend__label">{s.label || `Series ${i + 1}`}</div>
					</div>
				))}
			</div>
		</div>
	);
};

export default SideLegend;
