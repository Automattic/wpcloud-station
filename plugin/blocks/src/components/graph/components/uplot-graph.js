/**
 * External dependencies
 */
import React, { useRef, useEffect } from 'react';
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
 * @returns {JSX.Element} - Rendered component
 */

export default function UplotGraph({
		title,
		data,
		series,
		meta,
		containerRef,
		showLegend,
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
		type,
		orientation
	}), [title, data, series, meta, containerRef, showLegend, type, orientation]);

	// Get graph options from the hook
	const { data: d, ...options } = useGraphOptions(graphOptionsParams);

	// Create a custom plugin for handling legend label click events only
	const legendLabelClickPlugin = React.useMemo(() => {
		return {
			hooks: {
				ready: [
					(u) => {
						// Function to handle legend label events
						const handleLegendEvents = () => {
							// Find all legend labels
							const labels = u.root.querySelectorAll('.u-label');

							// Add click event for specific dimensions
							if (['atomic_site_id', 'http_host'].includes(dimension)) {
								labels.forEach(label => {
									// Skip labels that already have event handlers attached
									if (label.dataset.hasClickEvent === 'true') {
										return;
									}

									label.style.cursor = 'pointer';
									const clickHandler = (e) => {
										e.stopPropagation();
										console.log(`Clicked label: ${label.textContent}`);

										var site_url = label.textContent;
										if (isNaN(site_url)) {
											site_url = site_url.replace(/\./g, "-");
										}
										window.location.href = `/sites/${site_url}/metrics/`;
									};

									label.addEventListener('click', clickHandler);

									// Mark this label as having click event handler attached
									label.dataset.hasClickEvent = 'true';
								});
							}
						};

						// Initial setup with a delay to ensure DOM is ready
						setTimeout(handleLegendEvents, 100);

						// Set up a MutationObserver to watch for changes to the legend
						const observer = new MutationObserver((mutations) => {
							// Re-apply event handlers when the DOM changes
							handleLegendEvents();
						});

						// Start observing the chart root for changes
						observer.observe(u.root, {
							childList: true,
							subtree: true
						});

						// Store the observer for cleanup
						u.legendObserver = observer;
					}
				],
				destroy: [
					(u) => {
						// Clean up the observer when the chart is destroyed
						if (u.legendObserver) {
							u.legendObserver.disconnect();
						}

						// Remove data attributes from labels
						const labels = u.root.querySelectorAll('.u-label');
						labels.forEach(label => {
							if (label.dataset.hasClickEvent === 'true') {
								delete label.dataset.hasClickEvent;
							}
						});
					}
				]
			}
		};
	}, [dimension]);

	// Add the plugin to the options
	React.useEffect(() => {
		if (uplotInstanceRef.current) {
			// Apply the plugin to the existing chart
			const u = uplotInstanceRef.current;

			// Clean up any existing observer
			if (u.legendObserver) {
				u.legendObserver.disconnect();
			}

			// Set up the plugin hooks
			legendLabelClickPlugin.hooks.ready.forEach(hook => hook(u));

			// Clean up when the component unmounts or when dependencies change
			return () => {
				if (u.legendObserver) {
					u.legendObserver.disconnect();
				}
			};
		}
	}, [legendLabelClickPlugin, uplotInstanceRef.current]);

	return (
		<>
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
		</>
	);
}
