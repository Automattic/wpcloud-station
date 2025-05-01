/**
 * External dependencies
 */
import React, { useRef, useEffect, useState, useLayoutEffect } from 'react';
import UplotReact from 'uplot-react';
import 'uplot/dist/uPlot.min.css';

/**
 * Internal dependencies
 */
import Overlay from './overlay';
import useGraphOptions from './lib/useGraphOptions';

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

		// Create direct test labels instead of using the hook
		const [directLabels, setDirectLabels] = useState([]);

		// Reference for the side labels container
		const labelContainerRef = useRef(null);

		// Create a ref to track container dimensions
		const containerDimensionsRef = useRef({ width: 0, height: 0 });

		// Check if orientation is horizontal
		const isHorizontal = orientation === 'horizontal';

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

		// Update container dimensions when the component mounts and on window resize
		useEffect(() => {
				const updateDimensions = () => {
						if (containerRef.current) {
								containerDimensionsRef.current = {
										width: containerRef.current.clientWidth,
										height: containerRef.current.clientHeight
								};
						}
				};

				// Initial update
				updateDimensions();

				// Add resize listener
				window.addEventListener('resize', updateDimensions);

				// Cleanup
				return () => {
						window.removeEventListener('resize', updateDimensions);
				};
		}, [containerRef]);

		// Memoize the side labels to avoid unnecessary re-renders
		const sideLabels = React.useMemo(() => {
				if (!isHorizontal || !series || series.length <= 1) {
						return [];
				}

				// Use the stored dimensions
				const graphHeight = containerDimensionsRef.current.height || 500;
				// Get padding values from options, but don't depend on the entire options object
				const paddingTop = options?.padding?.top || 30;
				const paddingBottom = options?.padding?.bottom || 30;
				const contentHeight = graphHeight - paddingTop - paddingBottom;

				// Calculate the height available for each bar
				const numBars = series.length - 1; // Subtract 1 for the time series
				const barHeight = contentHeight / numBars;

				// Create labels based on series data with calculated positions
				return series.slice(1).map((s, i) => {
						// Calculate position to align with the center of each bar
						const topPosition = paddingTop + (i * barHeight) + (barHeight / 2);

						return (
								<div
										key={s.label || `series-${i}`}
										className="wpcloud-graph-side-label"
										style={{
												position: "absolute",
												top: `${topPosition}px`,
												left: '10px',
												transform: "translateY(-50%)", // Center vertically
												fontSize: "12px",
												whiteSpace: "nowrap",
												color: s.stroke || '#000',
												padding: '5px',
												backgroundColor: 'rgba(255,255,255,0.7)',
												borderRadius: '3px',
												boxShadow: '0 1px 3px rgba(0,0,0,0.1)',
												zIndex: 1000
										}}
								>
										{s.label || `Series ${i+1}`}
								</div>
						);
				});
		}, [isHorizontal, series, options?.padding?.top, options?.padding?.bottom]);

		// Update directLabels when sideLabels changes
		useEffect(() => {
				setDirectLabels(sideLabels);
		}, [sideLabels]);

		// Function to adjust container height based on legend size and apply styling for many series
		const adjustContainerHeight = () => {
				if (!uplotInstanceRef.current || !containerRef.current || !showLegend) {
						return;
				}

				const chart = uplotInstanceRef.current;
				const legendEl = chart.root.querySelector('.u-legend');

				if (legendEl) {
						// Count the number of series (excluding the first one which is usually the x-axis)
						const seriesCount = series ? series.length - 1 : 0;

						// If there are many series, add a class to enable multi-column layout
						if (seriesCount > 5) {
								legendEl.classList.add('u-legend-many');
						} else {
								legendEl.classList.remove('u-legend-many');
						}
				}
		};

		// Adjust container height when the component mounts and when data changes
		useEffect(() => {
				if (data && data.length > 0 && showLegend) {
						// Small delay to ensure the chart is fully rendered
						const timer = setTimeout(adjustContainerHeight, 200);
						return () => clearTimeout(timer);
				}
		}, [data, showLegend]);

		// Setup click events for labels using a useEffect hook
		useEffect(() => {
				if (!uplotInstanceRef.current || !['atomic_site_id', 'http_host'].includes(dimension)) {
						return;
				}

				const u = uplotInstanceRef.current;
				const labels = u.root.querySelectorAll('.u-label');

				// Add click handlers
				const clickHandlers = [];
				labels.forEach(label => {
						label.style.cursor = 'pointer';
						const handler = (e) => {
								e.stopPropagation();
								console.log(`Clicked label: ${label.textContent}`);
								// sample: sites/spatial-raccoon-jurassic-ninja/metrics/
								var site_url = label.textContent;
								if (isNaN(site_url)) {
										site_url = site_url.replace(/\./g, "-");
								}
								// Redirect to the single site metrics page.
								window.location.href = `/sites/${site_url}/metrics/`;
						};

						label.addEventListener('click', handler);
						clickHandlers.push({ element: label, handler });
				});

				// Cleanup function to remove event listeners
				return () => {
						clickHandlers.forEach(({ element, handler }) => {
								element.removeEventListener('click', handler);
						});
				};
		}, [dimension]);

		return (
				<>
						<UplotReact
								options={options}
								data={d}
								onCreate={(chart) => {
										// Only set the instance if it's not already set or if it's a different instance
										if (!uplotInstanceRef.current || uplotInstanceRef.current !== chart) {
												uplotInstanceRef.current = chart;

		// Update dimensions after chart is created
		if (containerRef.current) {
				containerDimensionsRef.current = {
						width: containerRef.current.clientWidth,
						height: containerRef.current.clientHeight
				};
				if (isHorizontal && series && series.length > 1) {
						// Just update the dimensions ref
						containerDimensionsRef.current = {
								width: containerRef.current.clientWidth,
								height: containerRef.current.clientHeight
						};
				}

				// Adjust container height and apply styling for many series
				setTimeout(() => {
						const legendEl = chart.root.querySelector('.u-legend');
						if (legendEl && showLegend) {
								// Count the number of series (excluding the first one which is usually the x-axis)
								const seriesCount = series ? series.length - 1 : 0;

								// If there are many series, add a class to enable multi-column layout
								if (seriesCount > 5) {
										legendEl.classList.add('u-legend-many');
								}

								// Remove any extra padding that might be added
								legendEl.style.paddingBottom = '0';
								legendEl.style.marginBottom = '0';
						}

						// Call the adjustContainerHeight function to handle any additional adjustments
						adjustContainerHeight();
				}, 100); // Small delay to ensure the chart is fully rendered
		}
										}
								}}
						/>
						{refreshing && <Overlay refreshing={true} />}
				</>
		);
}
