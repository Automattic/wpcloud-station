import chroma from 'chroma-js';

import { seriesBarsPlugin } from './uplot-plugins';
import { stack } from './utils';

const statusScale = chroma.scale( [ 'green', 'yellow', 'orange', 'red' ] ).domain( [ 200, 300, 400, 500 ] );
const indexScale = chroma.scale( [ 'yellow', '008ae5'] ).domain( [ 0, 100 ] );

function buildSeries(series, options) {
	const { fillScale = (s,idx) => indexScale(idx).hex(), fillTo = 0 } = options;
	const ySeries = series.slice(1).map((s, idx) => {
		s.fill = fillScale( s, idx );
		s.fillTo = fillTo;
		return s;
	});
	return [ series[0], ...ySeries ];
}


export function stackedOptions({ series, data:d, ...rest}) {
	const { bands, data  } = stack(d);
	const plugins = [seriesBarsPlugin({ stacked: true })];
	const stackedSeries = buildSeries(series, { fillScale: (s) => statusScale(s.label).hex() });
	return withDefaultOptions({ bands, data, plugins, series: stackedSeries, ...rest });
}

function withDefaultOptions( opts ) {
	return {
		padding: [null, 0, null, 0],
		ori: 0,
		axes: [{}, {}],
		legend: { live: false, markers: { width: 0 } },
		scales: {
			y: { range: [0, null], ori: 1 }
		},
		...opts
	};
}