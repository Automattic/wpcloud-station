
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


export default function Graph({ apiPath, dimension, type, title, interval, refresh, showLegend }) {
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
				const { data, series, meta } = await stationApi.get( apiPath, {
					query: {
						start,
						end,
						dimension
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
	}, [ apiPath,start, end, refresh ] );


	const { data: d, ...options } = useGraphOptions({ title, data, series, meta, containerRef, showLegend, type });
	const hasData = data.length > 0 && data[0].length > 0;

	// Force loading to false after data is loaded
	useEffect(() => {
		if (hasData && loading) {
			setLoading(false);
		}
	}, [hasData, loading]);

	const showOverlay = loading || !hasData;
	return (
		<div ref={containerRef} className="wpcloud-graph" style={{ width: "100%", height: "500px", backgroundColor: "white", position: "relative" }}>
			{showOverlay && <Overlay loading={loading} title={title} /> }
			{ hasData && <UplotReact options={options} data={d} /> }
		</div>
	);
}
