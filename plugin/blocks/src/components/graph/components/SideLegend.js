/**
 * Side Legend component for displaying a legend on the right side of the graph
 *
 * @param {Object} props - Component props
 * @returns {JSX.Element} - Rendered component
 */
import React, { useState, useRef, useEffect } from 'react';
import classnames from 'classnames';

// Inline styles for the legend
const styles = {
	legend: {
		padding: '10px 0',
		fontSize: '14px',
	},
	tooltip: {
		position: 'fixed',
		pointerEvents: 'none',
		zIndex: 1000,
		backgroundColor: 'rgba(0, 0, 0, 0.8)',
		color: 'white',
		padding: '4px 8px',
		borderRadius: '4px',
		fontSize: '12px',
		maxWidth: '300px',
		whiteSpace: 'nowrap',
	},
	items: {
		display: 'flex',
		flexDirection: 'column',
		gap: '8px',
	},
	item: {
		display: 'flex',
		alignItems: 'center',
		borderRadius: '4px',
		transition: 'all 0.2s ease',
	},
	itemActive: {
		backgroundColor: 'rgba(0, 0, 0, 0.05)',
		fontWeight: 'bold',
	},
	itemInactive: {
		opacity: 0.5,
	},
	itemHover: {
		backgroundColor: 'rgba(0, 0, 0, 0.05)',
	},
	marker: {
		width: '12px',
		borderRadius: '2px',
		marginRight: '8px',
		cursor: 'pointer',
		transition: 'all 0.2s ease',
	},
	markerTall: {
		height: '12px',
	},
	markerActive: {
		filter: 'brightness(1.3)',
		boxShadow: '0 0 3px rgba(0, 0, 0, 0.2)',
	},
	markerHover: {
		filter: 'brightness(1.2)',
	},
	label: {
		fontSize: '13px',
		overflow: 'hidden',
		textOverflow: 'ellipsis',
		whiteSpace: 'nowrap',
		maxWidth: '150px', // Limit width to allow truncation
	},
	labelClickable: {
		cursor: 'pointer',
	}
};

const SideLegend = ({ series, colors: propColors, onLegendItemClick, dimension }) => {
	const colors = propColors || series.map(s => s.stroke).filter(Boolean);
	// Filter out the first series (which is usually the x-axis)
	const displaySeries = series.slice(1);
	const [activeSeriesIdx, setActiveSeriesIdx] = useState(null);
	const [hoveredIdx, setHoveredIdx] = useState(null);

	// Tooltip state
	const [tooltip, setTooltip] = useState({ visible: false, text: '', x: 0, y: 0 });
	const labelRefs = useRef([]);
	const tooltipTimeoutRef = useRef(null);

	// Initialize refs array
	useEffect(() => {
		labelRefs.current = labelRefs.current.slice(0, displaySeries.length);
	}, [displaySeries]);

	// Check if a label is truncated
	const isLabelTruncated = (index) => {
		const labelEl = labelRefs.current[index];
		if (!labelEl) return false;

		return labelEl.scrollWidth > labelEl.clientWidth;
	};

	// Show tooltip for truncated label
	const showTooltip = (text, event) => {
		clearTimeout(tooltipTimeoutRef.current);
		const { clientX, clientY } = event;

		// Check if cursor is close to the right edge of the window
		const windowWidth = window.innerWidth;
		const tooltipWidth = Math.min(300, text.length * 8); // Estimate tooltip width
		const rightEdgeDistance = windowWidth - clientX;

		// If cursor is close to the right edge, position tooltip to the left
		const xPosition = rightEdgeDistance < (tooltipWidth + 20)
			? clientX - tooltipWidth - 12 // Position to the left of cursor
			: clientX + 12; // Default position to the right of cursor

		setTooltip({ visible: true, text, x: xPosition, y: clientY + 12 });
	};

	// Hide tooltip
	const hideTooltip = (delay = 100) => {
		tooltipTimeoutRef.current = setTimeout(() => {
			setTooltip(t => ({ ...t, visible: false }));
		}, delay);
	};

	// Cancel hiding tooltip
	const cancelHideTooltip = () => {
		clearTimeout(tooltipTimeoutRef.current);
	};

	// Handle mouse enter on label
	const handleLabelMouseEnter = (index, label, event) => {
		if (isLabelTruncated(index)) {
			showTooltip(label, event);
		}
	};

	// Handle mouse leave on label
	const handleLabelMouseLeave = () => {
		hideTooltip();
	};

	const handleMarkerClick = (index, event) => {
		// Stop event propagation to prevent it from triggering the item click
		event.stopPropagation();

		// If the same item is clicked again, reset to show all series
		const newActiveIdx = activeSeriesIdx === index ? null : index;
		setActiveSeriesIdx(newActiveIdx);

		// Call the parent handler with the adjusted index (accounting for x-axis series)
		if (onLegendItemClick) {
			// Add 1 to account for the x-axis series that was filtered out
			onLegendItemClick(newActiveIdx !== null ? newActiveIdx + 1 : null);
		}
	};

	const handleLabelClick = (index, label, event) => {
		// Different behavior based on dimension
		if (dimension === 'atomic_site_id') {
			event.stopPropagation();

			// Format the site URL
			let siteUrl = label;
			if (isNaN(siteUrl)) {
				siteUrl = siteUrl.replace(/\./g, "-");
			}

			// Navigate to the site metrics page
			window.location.href = `/sites/${siteUrl}/metrics/`;
		} else {
			// For other dimensions, clicking the label should also toggle the series
			handleMarkerClick(index, event);
		}
	};

	// All labels are clickable, but they have different behaviors based on dimension
	const isLabelClickable = true;
	const isAtomicSiteId = dimension === 'atomic_site_id';

	return (
		<div className="wpcloud-side-legend" style={styles.legend}>
			{/* Tooltip for truncated labels */}
			{tooltip.visible && (
				<div
					className="wpcloud-side-legend__tooltip"
					style={{
						...styles.tooltip,
						top: tooltip.y,
						left: tooltip.x
					}}
					onMouseEnter={cancelHideTooltip}
					onMouseLeave={() => hideTooltip()}
				>
					{tooltip.text}
				</div>
			)}
			<div className="wpcloud-side-legend__items" style={styles.items}>
				{displaySeries.map((s, i) => {
					// Determine item style based on active state and hover state
					const itemStyle = {
						...styles.item,
						...(activeSeriesIdx === i ? styles.itemActive : {}),
						...(activeSeriesIdx !== null && activeSeriesIdx !== i ? styles.itemInactive : {}),
						...(hoveredIdx === i ? styles.itemHover : {})
					};

					// Determine label style based on clickability
					const labelStyle = {
						...styles.label,
						...(isLabelClickable ? styles.labelClickable : {})
					};

					const label = s.label || `Series ${i + 1}`;

					return (
						<div
							key={i}
							className={classnames('wpcloud-side-legend__item', {
								'wpcloud-side-legend__item--active': activeSeriesIdx === i,
								'wpcloud-side-legend__item--inactive': activeSeriesIdx !== null && activeSeriesIdx !== i
							})}
							style={itemStyle}
							onMouseEnter={() => setHoveredIdx(i)}
							onMouseLeave={() => setHoveredIdx(null)}
						>
							<div
								className="wpcloud-side-legend__marker"
								style={{
									...styles.marker,
									...(isAtomicSiteId ? styles.markerTall : {}),
									...(activeSeriesIdx === i ? styles.markerActive : {}),
									...(hoveredIdx === i ? styles.markerHover : {}),
									backgroundColor: s.stroke || colors?.[i] || '#000'
								}}
								onClick={(e) => handleMarkerClick(i, e)}
								title="Click to show/hide this series"
							></div>
							<div
								ref={el => labelRefs.current[i] = el}
								className="wpcloud-side-legend__label"
								style={labelStyle}
								onClick={(e) => handleLabelClick(i, label, e)}
								onMouseEnter={(e) => handleLabelMouseEnter(i, label, e)}
								onMouseLeave={handleLabelMouseLeave}
								onMouseMove={(e) => isLabelTruncated(i) && showTooltip(label, e)}
							>
								{label}
							</div>
						</div>
					);
				})}
			</div>
		</div>
	);
};

export default SideLegend;
