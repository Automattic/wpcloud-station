/**
 * Side Legend component for displaying a legend on the right side of the graph
 *
 * @param {Object} props - Component props
 * @returns {JSX.Element} - Rendered component
 */
import React, { useState } from 'react';
import classnames from 'classnames';

// Inline styles for the legend
const styles = {
	legend: {
		padding: '10px 0',
		fontSize: '14px',
	},
	items: {
		display: 'flex',
		flexDirection: 'column',
		gap: '8px',
	},
	item: {
		display: 'flex',
		alignItems: 'center',
		padding: '4px 0px',
		borderRadius: '4px',
		cursor: 'pointer',
		transition: 'all 0.2s ease',
	},
	itemActive: {
		backgroundColor: 'rgba(0, 0, 0, 0.05)',
		fontWeight: 'bold',
	},
	itemInactive: {
		opacity: 0.5,
	},
	marker: {
		width: '12px',
		height: '12px',
		borderRadius: '2px',
		marginRight: '8px',
	},
	label: {
		fontSize: '13px',
	}
};

const SideLegend = ({ series, colors: propColors, onLegendItemClick }) => {
	const colors = propColors || series.map(s => s.stroke).filter(Boolean);
	// Filter out the first series (which is usually the x-axis)
	const displaySeries = series.slice(1);
	const [activeSeriesIdx, setActiveSeriesIdx] = useState(null);

	const handleItemClick = (index) => {
		// If the same item is clicked again, reset to show all series
		const newActiveIdx = activeSeriesIdx === index ? null : index;
		setActiveSeriesIdx(newActiveIdx);

		// Call the parent handler with the adjusted index (accounting for x-axis series)
		if (onLegendItemClick) {
			// Add 1 to account for the x-axis series that was filtered out
			onLegendItemClick(newActiveIdx !== null ? newActiveIdx + 1 : null);
		}
	};

	return (
		<div className="wpcloud-side-legend" style={styles.legend}>
			<div className="wpcloud-side-legend__items" style={styles.items}>
				{displaySeries.map((s, i) => {
					// Determine item style based on active state
					const itemStyle = {
						...styles.item,
						...(activeSeriesIdx === i ? styles.itemActive : {}),
						...(activeSeriesIdx !== null && activeSeriesIdx !== i ? styles.itemInactive : {})
					};

					return (
						<div
							key={i}
							className={classnames('wpcloud-side-legend__item', {
								'wpcloud-side-legend__item--active': activeSeriesIdx === i,
								'wpcloud-side-legend__item--inactive': activeSeriesIdx !== null && activeSeriesIdx !== i
							})}
							onClick={() => handleItemClick(i)}
							style={itemStyle}
						>
							<div
								className="wpcloud-side-legend__marker"
								style={{
									...styles.marker,
									backgroundColor: s.stroke || colors?.[i] || '#000'
								}}
							></div>
							<div className="wpcloud-side-legend__label" style={styles.label}>
								{s.label || `Series ${i + 1}`}
							</div>
						</div>
					);
				})}
			</div>
		</div>
	);
};

export default SideLegend;
