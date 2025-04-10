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
			y: {
				range: [0, null],
				ori: 1,
			}
		},
		...opts
	};
}

function withDefaultBarOptions({ ori = 0, ...opts }) {
	// if horizontal ( ori === 1 ) place the y axis marks on the top (0)
	// if vertical ( ori === 0 ) place the y axis marks on the right (3)
	const side = ori === 0 ? 3 : 0;
	// if horizontal ( ori === 1 ) set the y axis to read from top to bottom (1)
	// if vertical ( ori === 0 ) set the y axis to read from left to right (0)
	const yOri = ori === 0 ? 1 : 0;
	const newOpts =  withDefaultOptions({
		...opts,
		ori,
		axes: [{}, {
			side
		}],
		scales: {
			y: {
				range: [0, null],
				ori: yOri,
			}
		},
	});
	console.log('newOpts', newOpts);
	return newOpts;
	/*
	return {
		padding: [null, 0, null, 0],
		ori,
		axes: [{}, {
			side
		}],
		legend: { live: false, markers: { width: 2 } },
		scales: {
			y: {
				range: [0, null],
				ori: yOri,
			}
		},
		...opts
	};
	*/
}

function scaleFunc(options) {
 	const { series, useStatus = false, seriesOptions = {} } = options;
	const domainEnd = series.length - 1;
	return useStatus ? statusScale({seriesOptions}) : indexScale({ domainEnd, ...seriesOptions } );
}

export function stackedOptions({ series:s, data:d, useStatus, seriesOptions, dir, ori, ...options}) {
	const { bands, data } = stack(d);
	const plugins = [seriesBarsPlugin({ stacked: true, dir, ori })];
	const fillScale = scaleFunc({ series: s, useStatus, seriesOptions });
	const series = buildSeries(s, { fillScale, ...seriesOptions });
	return withDefaultBarOptions({ bands, data, plugins, series, ...options });
}

export function barOptions({ series: s, useStatus, seriesOptions, dir, ori, ...options }) {
	const plugins = [seriesBarsPlugin({ dir, ori })];
	const fillScale = scaleFunc({ series: s, useStatus, seriesOptions });
	const series = buildSeries(s, { fillScale, ...seriesOptions });
	return withDefaultBarOptions({ series, plugins, ...options });
}

export function lineOptions({ series, useStatus, seriesOptions, ...options }) {
	// @TODO: Before allowing vertical line graphs, we need to figure out how to rotate the data.
	const ori = 0;
	const strokeScale = scaleFunc({ series, useStatus, seriesOptions });
	return withDefaultOptions({ series: buildSeries(series, { strokeScale, ...seriesOptions }), ori, ...options });
}

export function areaOptions({ series, useStatus, seriesOptions, ...options }) {
	// @TODO: Before allowing vertical area graphs, we need to figure out how to rotate the data.
	const ori = 0;
	const scaleOptions = {series, useStatus, seriesOptions};
	const fillScale = scaleFunc(scaleOptions);
	const strokeScale = scaleFunc(scaleOptions);
	return withDefaultOptions({ series: buildSeries(series, { fillScale, strokeScale, ...seriesOptions }), ori, ...options });
}

export function defaultOptions({ series, useStatus, seriesOptions, ...rest }) {
	const scaleOptions = {series, useStatus, seriesOptions};
	const fillScale = scaleFunc(scaleOptions);
	const strokeScale = scaleFunc(scaleOptions);
	return withDefaultOptions({ ...(buildSeries(series, { strokeScale, fillScale, ...seriesOptions })), ...rest });
}
