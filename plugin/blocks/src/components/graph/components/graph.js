
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


export default function Graph({ metric, dimension, type, title, interval, refresh, showLegend, styles, className, minWidth='500px' } ) {

	const { apiPath } = useApiContext();
	const { start, end } = interval || {};
	const [ data, setData ] = useState([]);
	const [ series, setSeries ] = useState([]);
	const [ meta, setMeta ] = useState({});
	const [ loading, setLoading ] = useState( true );

	const containerRef = useRef(null);
	const style = { position: "relative", minWidth, minHeight: '500px', ...styles };
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
				const { data, series, meta } = await stationApi.get( `${apiPath}/${metric}`, {
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
	//
	return (
		<div ref={containerRef} className={className} style={style}>
			{showOverlay && <Overlay loading={loading} title={title} /> }
			{ hasData && <UplotReact options={options} data={d} /> }
		</div>
	);
}
