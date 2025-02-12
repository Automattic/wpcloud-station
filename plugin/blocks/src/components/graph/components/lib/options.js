import chroma from 'chroma-js';

import { seriesBarsPlugin } from './uplot-plugins';
import { stack } from './utils';

const statusScale = (options) => (s) => {
	const {
		opacity = 1,
		colors = ['green', 'yellow', 'orange', 'red']
	} = options || {};
	let scale = chroma.scale(colors).domain([200, 300, 400, 500]);
	return scale(s.label).alpha(opacity).css();
};

const indexScale = (options) => (s, idx) => {
	const {
		opacity = 1,
		colors = ['blue', 'green', 'orange', 'red'],
		domainEnd = 10
	} = options || {};
	let scale = chroma.scale(colors).mode('lch').domain([0, domainEnd]);
	return scale(idx).alpha(opacity).css();
};

function buildSeries(series, options = {}) {
	const {
		fillScale = () => null,
		strokeScale = () => null,
		...rest
	} = options;

	const ySeries = series.slice(1).map((s, idx) => {
		const fill = fillScale(s, idx);
		const stroke = strokeScale(s, idx);
		return {...s, stroke, fill, ...rest };
	});
	return [series[0], ...ySeries];
}

function withDefaultOptions(opts) {
	return {
		padding: [null, 0, null, 0],
		ori: 0,
		axes: [{}, {}],
		legend: { live: false, markers: { width: 2 } },
		scales: {
			y: { range: [0, null], ori: 1 }
		},
		...opts
	};
}


function scaleFunc(options) {
 	const { series, useStatus = false, seriesOptions = {} } = options;
	const domainEnd = series.length - 1;
	return useStatus ? statusScale({seriesOptions}) : indexScale({ domainEnd, ...seriesOptions } );
}

export function stackedOptions({ series:s, data:d, useStatus, seriesOptions, ...options}) {
	const { bands, data  } = stack(d);
	const plugins = [seriesBarsPlugin({ stacked: true })];
	const fillScale = scaleFunc({ series: s, useStatus, seriesOptions });
	const series = buildSeries(s, { fillScale, ...seriesOptions });
	return withDefaultOptions({ bands, data, plugins, series, ...options });
}

export function barOptions({ series:s, useStatus, seriesOptions, ...options }) {
	const plugins = [seriesBarsPlugin()];
	const fillScale = scaleFunc({ series: s, useStatus, seriesOptions });
	const series = buildSeries(s, { fillScale, ...seriesOptions });
	return withDefaultOptions({ series, plugins, ...options });
}

export function lineOptions({ series, useStatus, seriesOptions, ...options }) {
	const strokeScale = scaleFunc({ series, useStatus, seriesOptions });
	return withDefaultOptions({ series: buildSeries(series, { strokeScale, ...seriesOptions }), ...options });
}

export function areaOptions({ series, useStatus, seriesOptions, ...options }) {
	const scaleOptions = {series, useStatus, seriesOptions};
	const fillScale = scaleFunc(scaleOptions);
	const strokeScale = scaleFunc(scaleOptions);
	return withDefaultOptions({ series: buildSeries(series, { fillScale, strokeScale, ...seriesOptions }), ...options });
}

export function defaultOptions({ series, useStatus, seriesOptions, ...rest }) {
	const scaleOptions = {series, useStatus, seriesOptions};
	const fillScale = scaleFunc(scaleOptions);
	const strokeScale = scaleFunc(scaleOptions);
	return withDefaultOptions({ ...(buildSeries(series, { strokeScale, fillScale, ...seriesOptions })), ...rest });
}
