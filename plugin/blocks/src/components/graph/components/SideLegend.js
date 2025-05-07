/**
 * Side Legend component for displaying a legend on the right side of the graph
 *
 * @param {Object} props - Component props
 * @returns {JSX.Element} - Rendered component
 */
import React from 'react';

const SideLegend = ({ series, colors: propColors }) => {
	const colors = propColors || series.map(s => s.stroke).filter(Boolean);
	// Filter out the first series (which is usually the x-axis)
	const displaySeries = series.slice(1);

	return (
		<div className="wpcloud-side-legend">
			<div className="wpcloud-side-legend__items">
				{displaySeries.map((s, i) => (
					<div
						key={i}
						className="wpcloud-side-legend__item"
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
