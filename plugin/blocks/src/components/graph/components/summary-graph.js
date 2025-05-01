/**
 * External dependencies
 */
import React, { useMemo } from 'react';

/**
 * Internal dependencies
 */
import Overlay from './overlay';

/**
 * Format a number with appropriate units (K, M, B)
 *
 * @param {number} num - The number to format
 * @returns {string} - Formatted number with units
 */
const formatNumber = (num) => {
		if (num >= 1000000000) {
				return (num / 1000000000).toFixed(1) + 'B';
		}
		if (num >= 1000000) {
				return (num / 1000000).toFixed(1) + 'M';
		}
		if (num >= 1000) {
				return (num / 1000).toFixed(1) + 'K';
		}
		return num.toString();
};

/**
 * SummaryGraph component for horizontal bar graphs
 *
 * @param {Object} props - Component props
 * @returns {JSX.Element} - Rendered component
 */
export default function SummaryGraph({
	title,
	data,
	series,
	dimension,
	refreshing,
	colors = {base: 'rgba(34, 193, 170, 0.35)', warning: 'rgba(242, 73, 92, 0.35)'},
	warningThreshold = Infinity,
}) {
		// Process data for horizontal bar chart
		const barContent = useMemo(() => {
				if (!data || !data.length || !series || series.length <= 1) {
						return null;
				}

				// Get the values from the data array (skip the first element which is timestamps)
				const values = data.slice(1).map(d => d[0]);

				// Find the maximum value for scaling the bars
				const maxValue = Math.max(...values);

				// Render the bars
				return series.slice(1).map((s, i) => {
						const value = values[i];
					const percentage = (value / maxValue) * 100;
					let color =  value > warningThreshold ? colors.warning : colors.base;

						return (
								<div key={i} className="wpcloud-horizontal-bar-graph-row">
										<div
												className="wpcloud-horizontal-bar-graph-label"
												onClick={() => {
														if (['atomic_site_id', 'http_host'].includes(dimension)) {
																var site_url = s.label;
																if (isNaN(site_url)) {
																		site_url = site_url.replace(/\./g, "-");
																}
																window.location.href = `/sites/${site_url}/metrics/`;
														}
												}}
												style={{
														cursor: ['atomic_site_id', 'http_host'].includes(dimension) ? 'pointer' : 'default'
												}}
										>
												{s.label || `Series ${i+1}`}
										</div>
										<div className="wpcloud-horizontal-bar-graph-bar-container">
												<div
														className="wpcloud-horizontal-bar-graph-bar"
														style={{
																width: `${percentage}%`,
																backgroundColor: color,
																transition: 'width 0.3s ease-in-out'
														}}
												></div>
										</div>
										<div className="wpcloud-horizontal-bar-graph-value">
												{formatNumber(value)}
										</div>
								</div>
						);
				});
		}, [data, series, dimension]);

		return (
				<div className="wpcloud-horizontal-bar-graph-container">
						{title && <h3 className="wpcloud-horizontal-bar-graph-title">{title}</h3>}
						{barContent}
						{refreshing && <Overlay refreshing={true} />}
				</div>
		);
}
