
/**
 * External dependencies
 */
import UplotReact from 'uplot-react';
import chroma from 'chroma-js';
import 'uplot/dist/uPlot.min.css';

/**
 * WordPress dependencies
 */
import { useRef, useEffect, useState } from "@wordpress/element";

/**
 * Internal dependencies
 */
import Loader from './loader';
import { seriesBarsPlugin } from './lib/uplot-plugins';
import { stack } from './lib/utils';

import stationApi from '@wpcloud/utils/api';


// Remove this amount from the container height to fit the graph legend.
const fitGraphHeight = 75;
const fitGraphWidth = 20;

const statusScale = chroma.scale( [ 'green', 'yellow', 'orange', 'red' ] ).domain( [ 200, 300, 400, 500 ] );
const indexScale = chroma.scale( [ 'yellow', '008ae5'] ).domain( [ 0, 100 ] );

function addSeriesFill( series, idx ) {
	if ( isNaN( parseInt( series.label || '' ) ) ) {
		series.fill = indexScale( idx ).hex();
	} else {
		series.fill = statusScale( series.label ).hex();
	}
	series.fillTo = () => 0;
	return series;
}

export default function Graph( { site, metric, type, title, interval } ) {
	const { start, end } = interval || {};
	const [ data, setData ] = useState([]);
	const [ series, setSeries ] = useState([]);

	const containerRef = useRef(null);
	const [ graphWidth, setGraphWidth ] = useState( 808 );
	const [ graphHeight, setGraphHeight ] = useState( 404 );

	useEffect(() => {
		const updateSize = () => {
			if ( containerRef.current ) {
				setGraphWidth( containerRef.current.offsetWidth - fitGraphWidth );
				setGraphHeight( containerRef.current.offsetHeight - fitGraphHeight );
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
				const { data, series } = await stationApi.get(`metrics/${metric}`, { query: { site, start, end }, parse: true, signal });
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
	}, [ site, metric, start, end ] );

	if ( data.length === 0 ) {
		return (
			<div className="wpcloud-graph" style={{ width: "100%", height: "500px", position:"relative" }}>
				<Loader />
			</div>
		);
	}

	let options = {
		title,
		width: graphWidth,
		height: graphHeight,
		scales: {
			y: {
				range: [ 0, null ],
				ori: 1
			  }
		},

		axes: [
			{
			 // See the stacked series plugin for changing the x axis labels.
			},
			{
				side: 3,
			}
		],
		legend: {
			live: false,
			markers: {
				width: 0,
			}
		},
		padding: [ null, 0, null, 0 ],
		series: series.map( addSeriesFill ),
		ori: 0,
	};

	let d, bands;
	if ( 'bar' === type ) {
		( { bands, data:d } = stack( data ) );
		options.bands = bands;
		options.plugins = [ seriesBarsPlugin( { stacked: true } ) ];
	} else {
		d = data;
	}

	return (
		<div ref={containerRef} className="wpcloud-graph" style={{ width: "100%", height: "500px", backgroundColor: "white", position: "relative" }}>
			<UplotReact
				options={options}
				data={d} />
		</div>
	);
}
