
/**
 * External dependencies
 */
import UplotReact from 'uplot-react';
import 'uplot/dist/uPlot.min.css';


/**
 * WordPress dependencies
 */
import { useRef, useEffect, useState } from "@wordpress/element";

/**
 * Internal dependencies
 */
import Overlay from './overlay';
// import { stackedOptions, defaultOptions, barOptions, lineOptions, areaOptions } from './lib/options';
import useGraphOptions from './lib/useGraphOptions';
import stationApi from '@wpcloud/utils/api';
import { useApiContext } from '@wpcloud/metrics/components/apiContext';
import Filters from './lib/filters';

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
		interval, refresh

	} = props;

	const { apiPath } = useApiContext();
	const { start, end } = interval || {};
	const [ data, setData ] = useState([]);
	const [ series, setSeries ] = useState([]);
	const [ meta, setMeta ] = useState({});
	const [ loading, setLoading ] = useState( true );
	const [ filters, setFilters ] = useState([]);

	const containerRef = useRef(null);
	// Ensure the container takes full width of parent and has appropriate minimum dimensions
	const style = {
		position: "relative",
		width: '100%',
		minWidth,
		minHeight: '500px',
		...styles
	};
	// if the className does not contain has-background add a background color
	if ( ! className.includes( 'has-background' ) ) {
		styles.backgroundColor = 'white';
	}

	useEffect(() => {
		const controller = new AbortController();
		const signal = controller.signal;
		setLoading(true);
		async function fetchData() {
			try {
				// Extract active filters for the API query
				const activeFilters = filters.filter(f => f.enabled).map(f => [f.value.field, f.value.operator, f.value.value]);

				const { data, series, meta } = await stationApi.get( `${apiPath}/${metric}`, {
					query: {
						start,
						end,
						dimension,
						resolution,
						summarize,
						top_x: topX,
						filters: activeFilters.length > 0 ? activeFilters : undefined,
					}, parse: true, signal
				});
				setData(data);
				setSeries(series);
				setMeta(meta);
				setLoading(false);
			} catch (error) {
				if (error.name !== 'AbortError') {
					console.error(error);
				}
			}
		}

		fetchData();
		return () => controller.abort();
	}, [ apiPath, start, end, refresh, filters ] );


	const { data: d, ...options } = useGraphOptions({ title, data, series, meta, containerRef, showLegend, type, orientation });
	const hasData = data.length > 0 && data[0].length > 0;

	// Force loading to false after data is loaded
	useEffect(() => {
		if (hasData && loading) {
			setLoading(false);
		}
	}, [hasData, loading]);

	const showOverlay = loading || !hasData;
	//
	// Handle filter updates
	const handleFiltersUpdate = ({ filters: newFilters }) => {
		setFilters(newFilters.map(filter => ({
			enabled: true,
			value: {
				field: filter[0],
				operator: filter[1],
				value: filter[2]
			},
			compact: `${filter[0]} ${filter[1]} ${filter[2]}`,
			label: `${filter[0]} ${filter[1]} ${filter[2]}`
		})));
	};

	return (
		<div style={{ width: '100%' }}>
			<div ref={containerRef} className={className} style={style}>
			{showOverlay && <Overlay loading={loading} title={title} /> }
			{ hasData && <UplotReact options={options} data={d} /> }
			</div>
			<div style={{ position: 'relative', marginTop: '10px', zIndex: 1 }}>
				<Filters
					filters={filters}
					onFiltersUpdate={handleFiltersUpdate}
					loading={loading}
				/>
			</div>
		</div>
	);
}
