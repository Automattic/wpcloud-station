
import { useEffect, useState } from 'react';
import chroma from 'chroma-js';

import { useMetricsOptionsContext } from '@wpcloud/metrics/components/contexts';

import { seriesBarsPlugin } from './uplot-plugins';
import { stack } from './utils';

const statusScale = () => ( series, _, opacity ) => {
	const colors = [
		'#38c172', // 2xx Success – Green
		'#fcd34d', // 3xx Redirect – Amber
		'#f97316', // 4xx Client Error – Orange
		'#ef4444'  // 5xx Server Error – Red
	];
	let scale = chroma.scale(colors).domain([200, 300, 400, 500]);
	return scale(series.label).alpha(opacity).css();
};

const indexScale = (colors)  => (_, idx, opacity ) => {
	const domainEnd = colors.length;
	const scale = chroma.scale(colors).mode('lch').domain([0, domainEnd]);
	return scale(idx).alpha(opacity).css();
}

export default ({ containerRef, ...options } ) => {
	const [width, setWidth] = useState(containerRef?.current?.offsetWidth || 808);
	const [height, setHeight] = useState(containerRef?.current?.offsetHeight || 404);

	// Graph level options
	const {
		title,
		meta,
		data,
		showLegend,
		type,
		orientation,
	} = options;

	// Metric level options ( with Graph overrides )
	const {
		padding,
		seriesPalette,
		opacity = 'area' === type ? 0.5 : 1,
		fit = {},
		legend,
		...metricsOptions
	} = useMetricsOptionsContext(options);

		// Update width and height when the container ref changes
	useEffect(() => {
		// Track if we're currently processing a resize
		let resizeTimeout = null;
		let lastWidth = width;
		let lastHeight = height;
		const {
			fitWidth = 10,
			fitHeight = 75,
		} = fit;

		const updateSize = () => {
			if (containerRef.current) {
				const newWidth = containerRef.current.offsetWidth - fitWidth;
				const newHeight = containerRef.current.offsetHeight - fitHeight;

				// Only update if dimensions have changed significantly (by at least 5px)
				// This prevents minor fluctuations from causing infinite loops
				if (Math.abs(newWidth - lastWidth) > 5 || Math.abs(newHeight - lastHeight) > 5) {
					if (newWidth > 0 && newHeight > 0) {
						lastWidth = newWidth;
						lastHeight = newHeight;
						setWidth(newWidth);
						setHeight(newHeight);
					}
				}
			}
		};

		// Initial size update
		updateSize();

		// Set up resize observer with debounce
		const handleResize = () => {
			// Clear any existing timeout
			if (resizeTimeout) {
				clearTimeout(resizeTimeout);
			}

			// Set a new timeout to update size after 100ms of no resize events
			resizeTimeout = setTimeout(() => {
				updateSize();
				resizeTimeout = null;
			}, 100);
		};

		// Create and attach the resize observer
		const resizeObserver = new ResizeObserver(handleResize);

		if (containerRef.current) {
			resizeObserver.observe(containerRef.current);
		}

		// Also listen for window resize events
		window.addEventListener('resize', handleResize);

		// Clean up
		return () => {
			if (resizeTimeout) {
				clearTimeout(resizeTimeout);
			}
			resizeObserver.disconnect();
			window.removeEventListener('resize', handleResize);
		};
	}, [containerRef]);

	// Set up the series and axes options
	let {
		series = [],
		axes = [{}, {}],
		scales = { x: {}, y: {} },
	} = options;

	const useStatus = meta.dimension?.includes('status');
	const colors = useStatus ? statusScale() : indexScale(seriesPalette);

	series = series.map((s, idx) => {
		const stroke = colors(s, idx, opacity);
		const fill = ['area', 'bar', 'stacked-bar'].includes(type) ? colors(s, idx, opacity) : null;
		return { ...metricsOptions.series, ...s, stroke, fill,  };
	});

	axes = axes.map((a, idx) => {
		return { ...a, ...metricsOptions.axes[idx] }
	});
	scales.y = { ...metricsOptions.scales.y, ...scales.y };
	scales.x = { ...metricsOptions.scales.x, ...scales.x };

	// Set up orientation and direction
	const isVertical = 'vertical' === orientation;
	const ori = isVertical ? 0 : 1;
	const dir = isVertical ? 1 : -1;

	let graphOptions = {
		title,
		width,
		height,
		series,
		axes,
		data,
		ori:
		dir,
		padding,
		plugins: [],
		scales,
		legend
	};

	if (!showLegend) {
		graphOptions.legend = { show: false };
	}

	if ('bar' === type || 'stacked-bar' === type) {
		// if horizontal ( ori === 1 ) place the y axis marks on the bottom (0) ????
		// if vertical ( ori === 0 ) place the y axis marks on the left (3)
		graphOptions.axes[1].side = isVertical ? 3 : 0;

		// if horizontal ( ori === 1 ) set the y axis to read from top to bottom (1)
		// if vertical ( ori === 0 ) set the y axis to read from left to right (0)
		graphOptions.scales.y.ori = isVertical ? 1 : 0;

		const pluginOptions = { dir, ori };
		if ( 'stacked-bar' === type ) {
			pluginOptions.stacked = true;
			graphOptions = {...graphOptions, ...stack(data)};
		}
		graphOptions.plugins.push(seriesBarsPlugin(pluginOptions));
	}

	return graphOptions;
}
