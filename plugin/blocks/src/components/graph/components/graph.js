
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
import { stackedOptions } from './lib/options';
import stationApi from '@wpcloud/utils/api';

// Remove this amount from the container height to fit the graph legend.
const fitGraphHeight = 75;
const fitGraphWidth = 20;

export default function Graph( { site, metric, dimension, type, title, interval, refresh } ) {
	const { start, end } = interval || {};
	const [ data, setData ] = useState([]);
	const [ series, setSeries ] = useState([]);

	const containerRef = useRef(null);
	const [ width, setWidth ] = useState( 808 );
	const [ height, setHeight ] = useState( 404 );

	useEffect(() => {
		const updateSize = () => {
			if ( containerRef.current ) {
				setWidth( containerRef.current.offsetWidth - fitGraphWidth );
				setHeight( containerRef.current.offsetHeight - fitGraphHeight );
			}
		};

		updateSize(); // Initial update

		const resizeObserver = new ResizeObserver( () => {
			if (containerRef.current) {
				updateSize();
			}
		});
		if (containerRef.current) {
			resizeObserver.observe( containerRef.current );
		}
		return () => resizeObserver.disconnect();
	}, [containerRef, data]);

	useEffect(() => {
		const controller = new AbortController();
		const signal = controller.signal;
		setData([]);
		async function fetchData() {
			try {
				const { data, series } = await stationApi.get(`metrics/${metric}`, { query: { site, start, end, dimension }, parse: true, signal });
				setData(data);
				setSeries(series);
			} catch (error) {
				if (error.name !== 'AbortError') {
					console.error(error);
				}
			}
		}

		fetchData();
		return () => controller.abort();
	}, [ site, metric, start, end, refresh ] );

	if ( data.length === 0 ) {
		return (
			<div className="wpcloud-graph" style={{ width: "100%", height: "500px", position:"relative" }}>
				<Loader />
			</div>
		);
	}
	let graphOptions = {};
	if ( type.startsWith('stacked') ) {
		graphOptions = stackedOptions({ width, height, series, data, title });
	}

	const { data: d, ...options } = graphOptions;
	return (
		<div ref={containerRef} className="wpcloud-graph" style={{ width: "100%", height: "500px", backgroundColor: "white", position: "relative" }}>
			<UplotReact
				options={options}
				data={d} />
		</div>
	);
}
