/**
 * WordPress dependencies
 */
import { useRef, useEffect, useState, useMemo } from "@wordpress/element";

/**
 * Internal dependencies
 */
import Overlay from './overlay';
import SummaryGraph from './summary-graph';
import UplotGraph from './uplot-graph';
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

export default function Graph(props) {
	const {
		// Core props.
		title,
		type,
		showLegend,
		legendPosition = 'bottom',

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
	const [data, setData] = useState([]);
	const [series, setSeries] = useState([]);
	const [meta, setMeta] = useState({});
	const [loading, setLoading] = useState(true);
	const [refreshing, setRefreshing] = useState(false);

	// Initialize filters from URL parameters if available
	const [filters, setFilters] = useState([]);

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

	// Ensure the container takes full width of parent and has appropriate minimum dimensions
	// Allow the container to grow in height to accommodate the legend
	const style = {
		position: "relative",
		width: '100%',
		minWidth,
		minHeight: '500px',
		height: 'auto', // Allow the container to grow
		...styles
	};

	// Fetch data from API
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

				const { data, series, meta } = await stationApi.get(`${apiPath}/${metric}`, {
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
	}, [apiPath, start, end, refresh, filters, dimension, metric, resolution, summarize, topX]);

	// Check if we have data before using it
	const hasData = data.length > 0 && data[0].length > 0;

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
	}, [hasData, loading]);

	const showOverlay = loading || !hasData;

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

	// Determine which chart component to render
	const renderChart = () => {
		if (!hasData) return null;

		// Use SummaryGraph for horizontal bar graphs with summarized data
		if (orientation === 'horizontal' && summarize) {
			return (
				<SummaryGraph
					title={title}
					data={data}
					series={series}
					dimension={dimension}
					refreshing={refreshing}
				/>
			);
		}

		// Use UplotGraph for all other graph types
		return (
			<UplotGraph
				title={title}
				data={data}
				series={series}
				meta={meta}
				containerRef={containerRef}
				showLegend={showLegend}
				legendPosition={legendPosition}
				type={type}
				orientation={orientation}
				dimension={dimension}
				refreshing={refreshing}
			/>
		);
	};

	return (
		<div style={{ width: '100%', marginBottom: 0 }} data-graph-id={graphId}>
			<div ref={containerRef} className={className} style={style}>
				{showOverlay && <Overlay loading={loading} title={title} />}
				{hasData && renderChart()}
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
