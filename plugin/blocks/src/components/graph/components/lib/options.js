import chroma from 'chroma-js';

import { seriesBarsPlugin } from './uplot-plugins';
import { stack } from './utils';

const statusScale = chroma.scale( [ 'green', 'yellow', 'orange', 'red' ] ).domain( [ 200, 300, 400, 500 ] );
const indexScale = chroma.scale( [ 'yellow', '008ae5'] ).domain( [ 0, 100 ] );


export function addSeriesFill(series, idx) {
	if ( isNaN( parseInt( series.label || '' ) ) ) {
		series.fill = indexScale( idx ).hex();
	} else {
		series.fill = statusScale( series.label ).hex();
	}
	series.fillTo = () => 0;
	return series;
}

export function stackedOptions({ graphWidth, graphHeight, series, data:d, title, ...options }) {
	console.log("stackedOptions", d);
	const { bands, data  } = stack(d);

	const plugins = [ seriesBarsPlugin({ stacked: true}) ];

	let opts = {
		bands,
		plugins,
		title,
		data,
		width: graphWidth,
		height: graphHeight,
		series: series.map(addSeriesFill),
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

		ori: 0,

	};

	return { ...opts, ...options };
}