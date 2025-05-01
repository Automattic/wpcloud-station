/**
 * External dependencies
 */
import UplotReact from 'uplot-react';
import 'uplot/dist/uPlot.min.css';
import { useSideLabels } from './lib/useSideLabels';


/**
 * WordPress dependencies
 */
import { useRef, useEffect, useState, useMemo } from "@wordpress/element";

/**
 * Internal dependencies
 */
import Overlay from './overlay';
// import { stackedOptions, defaultOptions, barOptions, lineOptions, areaOptions } from './lib/options';
import useGraphOptions from './lib/useGraphOptions';
import stationApi from '@wpcloud/utils/api';
import { useApiContext } from '@wpcloud/metrics/components/contexts';
import Filters from './lib/filters';

/**
 * Parse URL parameters to get filters for a specific graph
 *
 * @param {string} graphId - The unique ID of the graph
 * @returns {Array} - Array of filter objects
 */
const getFiltersFromUrl = (graphId) => {
	try {
		// Get the URL search parameters
		const urlParams = new URLSearchParams(window.location.search);

		// Look specifically for the parameter with this graph's ID
		const paramName = `filters_${graphId}`;
		const filterParam = urlParams.get(paramName);

		// If there's no parameter specifically for this graph, return empty array
		if (!filterParam) {
			return [];
		}

		// Parse the JSON string from the URL
		const decodedFilters = JSON.parse(decodeURIComponent(filterParam));

		// Convert the array format to the filter object format
		return decodedFilters.map(filter => ({
			enabled: true,
			value: {
				field: filter[0],
				operator: filter[1],
				value: filter[2]
			},
			compact: `${filter[0]} ${filter[1]} ${filter[2]}`,
			label: `${filter[0]} ${filter[1]} ${filter[2]}`
		}));
	} catch (error) {
		console.error('Error parsing filters from URL:', error);
		return [];
	}
};

/**
 * Update URL parameters with filters for a specific graph
 *
 * @param {string} graphId - The unique ID of the graph
 * @param {Array} filters - Array of filter objects
 */
const updateUrlWithFilters = (graphId, filters) => {
	try {
		// Get the active filters in array format
		const activeFilters = filters
			.filter(f => f.enabled)
			.map(f => [f.value.field, f.value.operator, String(f.value.value)]);

		// Get the current URL search parameters
		const urlParams = new URLSearchParams(window.location.search);

		// If there are active filters, add them to the URL
		if (activeFilters.length > 0) {
			urlParams.set(`filters_${graphId}`, encodeURIComponent(JSON.stringify(activeFilters)));
		} else {
			// If there are no active filters, remove the parameter
			urlParams.delete(`filters_${graphId}`);
		}

		// Update the URL without reloading the page
		const newUrl = `${window.location.pathname}?${urlParams.toString()}${window.location.hash}`;
		window.history.replaceState({}, '', newUrl);
	} catch (error) {
		console.error('Error updating URL with filters:', error);
	}
};

export default function Graph( props ) {

	const {
		// Core props.
		title,
		type,
		showLegend,

		// Styling props.
		styles = {},
		className = '',
		minWidth = '500px',

		// Static metric props.
		metric,
		dimension,
		resolution,
		summarize,
		topX,
		orientation,

		// Dynamic metric props.
		interval, refresh,

		// Unique identifier for this graph
		id = `graph-${metric}-${dimension}`,

		// Predefined filters from block attributes
		predefinedFilters = [],

		// Allow frontend filter building
		allowFrontendFilters = true

	} = props;

	// Format a number with appropriate units (K, M, B)
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

	// Get a color from a predefined palette based on index
	const getColorForIndex = (index) => {
		const colors = [
			"#bcf60c", // Lime
			"#4363d8", // Royal Blue
			"#3cb44b", // Lime Green
			"#e6194b", // Crimson
			"#ffe119", // Lemon
			"#f58231", // Orange
			"#911eb4", // Purple
			"#46f0f0", // Cyan
			"#f032e6", // Magenta
			"#fabebe", // Rose
		];
		return colors[index % colors.length];
	};

	// Create a deterministic ID based on the graph's properties
	// This will be the same across page loads for the same graph
	const generateStableId = (metric, dimension, title) => {
		// Create a string that uniquely identifies this graph
		const baseString = `${metric}-${dimension}-${title}`;
		// Simple hash function to generate a numeric hash
		let hash = 0;
		for (let i = 0; i < baseString.length; i++) {
			const char = baseString.charCodeAt(i);
			hash = ((hash << 5) - hash) + char;
			hash = hash & hash; // Convert to 32bit integer
		}
		// Convert to a positive number and return
		return `${id.replace(/[^a-zA-Z0-9-_]/g, '-')}-${Math.abs(hash)}`;
	};

	// Generate a stable ID that will be the same across page loads
	const graphId = generateStableId(metric, dimension, title);

	const { apiPath } = useApiContext();
	const { start, end } = interval || {};
	const [ data, setData ] = useState([]);
	const [ series, setSeries ] = useState([]);
	const [ meta, setMeta ] = useState({});
	const [ loading, setLoading ] = useState( true );
	const [refreshing, setRefreshing] = useState(false);
	// Use useRef instead of useState to avoid re-renders when setting the uPlot instance
	const uplotInstanceRef = useRef(null);

	// Create direct test labels instead of using the hook
	const [directLabels, setDirectLabels] = useState([]);

	// Initialize filters from URL parameters if available
	const [ filters, setFilters ] = useState([]);

	// Convert predefined filters to filter objects
	const convertPredefinedFilters = (filters) => {
		if (!filters || !Array.isArray(filters) || filters.length === 0) {
			return [];
		}

		return filters.map(filter => {
			// Handle array format [field, operator, value]
			if (Array.isArray(filter)) {
				const [field, operator, value] = filter;
				return {
					enabled: true,
					value: {
						field,
						operator,
						value
					},
					compact: `${field} ${operator} ${value}`,
					label: `${field} ${operator} ${value}`
				};
			}
			return null;
		}).filter(f => f !== null);
	};

	// Load filters from predefined filters or URL on mount
	useEffect(() => {
		// First check if we have predefined filters from block attributes
		if (predefinedFilters && Array.isArray(predefinedFilters) && predefinedFilters.length > 0) {
			const blockFilters = convertPredefinedFilters(predefinedFilters);
			if (blockFilters.length > 0) {
				setFilters(blockFilters);
				return; // Use predefined filters, don't check URL
			}
		}

		// If no predefined filters, try to get from URL
		if (typeof window !== 'undefined') {
			try {
				const urlFilters = getFiltersFromUrl(graphId);
				if (urlFilters && Array.isArray(urlFilters) && urlFilters.length > 0) {
					setFilters(urlFilters);
				}
			} catch (error) {
				console.error('Error initializing filters:', error);
			}
		}
	}, [graphId, predefinedFilters]);

	const containerRef = useRef(null);
	// Reference for the side labels container
	const labelContainerRef = useRef(null);
	// Ensure the container takes full width of parent and has appropriate minimum dimensions
	const style = {
		position: "relative",
		width: '100%',
		minWidth,
		minHeight: '500px',
		...styles
	};

	useEffect(() => {
		const controller = new AbortController();
		const signal = controller.signal;

		// Set loading state without depending on hasData
		const isRefreshing = data.length > 0 && data[0].length > 0;
		if (isRefreshing) {
			setRefreshing(true);
		} else {
			setLoading(true);
		}

		async function fetchData() {
			try {
				// Extract active filters for the API query and ensure values are strings
				const activeFilters = filters.filter(f => f.enabled).map(f => {
					// Ensure the value is a string to avoid PHP strpos() errors
					return [
						f.value.field,
						f.value.operator,
						// Convert all values to strings to avoid PHP type errors
						String(f.value.value)
					];
				});

				// Create the query parameters
				const queryParams = {
					start,
					end,
					dimension,
					resolution,
					summarize,
					top_x: topX,
				};

				// Only add filters if there are any active ones
				// Stringify the filters array to ensure it's sent as a JSON array
				if (activeFilters.length > 0) {
					queryParams.filters = JSON.stringify(activeFilters);
				}

				const { data, series, meta } = await stationApi.get( `${apiPath}/${metric}`, {
					query: queryParams,
					parse: true,
					signal
				});
				setData(data);
				setSeries(series);
				setMeta(meta);
				setLoading(false);
				setRefreshing(false);
			} catch (error) {
				if (error.name !== 'AbortError') {
					console.error(error);
					setLoading(false);
					setRefreshing(false);
				}
			}
		}

		fetchData();
		return () => controller.abort();
	}, [ apiPath, start, end, refresh, filters ] );


	// Check if we have data before using it
	const hasData = data.length > 0 && data[0].length > 0;

	// Memoize the options to avoid unnecessary re-renders
	const graphOptionsParams = useMemo(() => ({
		title,
		data,
		series,
		meta,
		containerRef,
		showLegend,
		type,
		orientation
	}), [title, data, series, meta, containerRef, showLegend, type, orientation]);

	// Only call useGraphOptions if we have data
	const { data: d, ...options } = useGraphOptions(graphOptionsParams);

	// Always call the hook, but only use its results when needed
	const isHorizontal = orientation === 'horizontal';

	// Process data for horizontal bar chart - moved outside of JSX to avoid conditional hook calls
	const horizontalBarContent = useMemo(() => {
		if (!hasData || !series || series.length <= 1 || !isHorizontal || !summarize) {
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
								backgroundColor: s.stroke || getColorForIndex(i)
							}}
						></div>
					</div>
					<div className="wpcloud-horizontal-bar-graph-value">
						{formatNumber(value)}
					</div>
				</div>
			);
		});
	}, [hasData, data, series, dimension, formatNumber, getColorForIndex, isHorizontal, summarize]);

	// Setup click events for labels using a useEffect hook instead of modifying options directly
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
	}, [dimension]); // Only depend on dimension, not uplotInstanceRef.current

	// Create a ref to track container dimensions
	const containerDimensionsRef = useRef({ width: 0, height: 0 });

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
	}, []);

	// Generate side labels when the component mounts or when series changes
	useEffect(() => {
		if (!isHorizontal || !series || series.length <= 1) {
			setDirectLabels([]);
			return;
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
		const labelEls = series.slice(1).map((s, i) => {
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

		setDirectLabels(labelEls);
	}, [isHorizontal, series]); // Remove options from dependencies

	// Force loading to false after data is loaded
	// This is a one-time effect that runs when hasData becomes true
	useEffect(() => {
		if (hasData && loading) {
			// Use a timeout to avoid immediate state updates
			const timer = setTimeout(() => {
				setLoading(false);
			}, 0);
			return () => clearTimeout(timer);
		}
	}, [hasData]); // Only depend on hasData, not loading

	const showOverlay = loading || !hasData;
	//
	// Handle filter updates
	const handleFiltersUpdate = ({ filters: newFilters }) => {
		const processedFilters = newFilters.map(filter => {
			// Handle both array format [field, operator, value] and object format {field, operator, value}
			let field, operator, value;

			if (Array.isArray(filter)) {
				// Array format: [field, operator, value]
				field = filter[0];
				operator = filter[1];
				value = filter[2];
			} else {
				// Object format: {field, operator, value}
				field = filter.field;
				operator = filter.operator;
				value = filter.value;
			}

			// Convert value to string for display
			const valueStr = String(value);

			return {
				enabled: true,
				value: {
					field,
					operator,
					value
				},
				compact: `${field} ${operator} ${valueStr}`,
				label: `${field} ${operator} ${valueStr}`
			};
		});

		// Update the state with the new filters
		setFilters(processedFilters);

		// Update the URL with the new filters
		updateUrlWithFilters(graphId, processedFilters);
	};

	return (
		<div style={{ width: '100%' }} data-graph-id={graphId}>
			<div ref={containerRef} className={className} style={style}>
				{showOverlay && <Overlay loading={loading} title={title} /> }
				{hasData && (
					<>
						{isHorizontal && summarize ? (
							// Horizontal Bar Graph for summarized data
							<div className="wpcloud-horizontal-bar-graph-container">
								{title && <h3 className="wpcloud-horizontal-bar-graph-title">{title}</h3>}
								{horizontalBarContent}
								{refreshing && <Overlay refreshing={true} />}
							</div>
						) : (
							// Standard uPlot graph
							<>
								{isHorizontal && (
									<div
										ref={labelContainerRef}
										className="wpcloud-graph-side-labels-container"
										style={{
											position: 'absolute',
											top: options && options.padding?.top || 0,
											left: 0,
											bottom: options && options.padding?.bottom || 0,
											width: '120px', // Adjust width as needed
											zIndex: 100, // Higher z-index for debugging
											pointerEvents: 'none', // Allow clicks to pass through to the graph
											overflow: 'visible',
											backgroundColor: 'rgba(200, 200, 200, 0.2)', // Light background for debugging
											border: '1px dashed #ccc', // Border for debugging
										}}
									>
										{directLabels.length > 0 ? (
											directLabels
										) : (
											<div style={{padding: '10px', color: 'red'}}>No direct labels rendered</div>
										)}
									</div>
								)}
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

												// Force a re-render of labels with the updated dimensions
												if (isHorizontal && series && series.length > 1) {
													// Use setTimeout to ensure the chart is fully rendered
													setTimeout(() => {
														const event = new Event('resize');
														window.dispatchEvent(event);
													}, 100);
												}
											}
										}
									}} />
								{refreshing && <Overlay refreshing={true} />}
							</>
						)}
					</>
				)}
			</div>
			{allowFrontendFilters && (
				<div style={{ position: 'relative', marginTop: '10px', zIndex: 1 }}>
					<Filters
						filters={filters}
						onFiltersUpdate={handleFiltersUpdate}
						loading={loading || refreshing}
						allowFrontendFilters={allowFrontendFilters}
					/>
				</div>
			)}
		</div>
	);
}
