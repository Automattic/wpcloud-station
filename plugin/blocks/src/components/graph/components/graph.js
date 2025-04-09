
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
import Loader from './loader';
// import { stackedOptions, defaultOptions, barOptions, lineOptions, areaOptions } from './lib/options';
import useGraphOptions from './lib/useGraphOptions';
import stationApi from '@wpcloud/utils/api';


export default function Graph({ site, request_type, metric, dimension, type, title, interval, refresh, showLegend, top_x, resolution, summarize, filters, displayClass }) {
	const { start, end } = interval || {};
	const [ data, setData ] = useState([]);
	const [ series, setSeries ] = useState([]);
	const [ meta, setMeta ] = useState({});
	const [ loading, setLoading ] = useState( true );

	const containerRef = useRef(null);

	useEffect(() => {
		const controller = new AbortController();
		const signal = controller.signal;
		setLoading(true);
		async function fetchData() {
			try {
				const { data, series, meta } = await stationApi.get(`metrics/${metric}`, {
					query: {
						request_type,
						site,
						start,
						end,
						dimension,
						top_x,
						resolution,
						summarize,
						filters,
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
	}, [ site, metric, start, end, refresh ] );

	if ( typeof displayClass === 'string' || displayClass instanceof String ) {
		//
	} else {
		displayClass = 'graph-100';
	}

	const { data: d, ...options } = useGraphOptions({ title, data, series, meta, containerRef, showLegend, type });
	const hasData = data.length > 0;
	return (
		<div ref={containerRef} className={`wpcloud-graph ${displayClass}`} style={{ backgroundColor: "white", position: "relative" }}>
			{ loading && <Loader />}
			{ hasData && <UplotReact
				options={options}
				data={d} />}
		</div>
	);
}
