
import { useEffect, useState } from 'react';
import chroma from 'chroma-js';

import { useMetricsOptionsContext } from '@wpcloud/metrics/components/contexts';

import { seriesBarsPlugin, isolateStackedPlugin, preventLegendClickPlugin, tooltipPlugin, legendLabelClickPlugin } from './uplot-plugins';
import { legendPositionPlugin } from './legendPositionPlugin';
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
	return colors[idx].alpha(opacity).css();
}

const buildPalette = ( baseColors, total ) => {
	const scale = chroma.scale(baseColors).mode("lab");
	const colors = Array.from({ length: total }, (_, i) => scale(i / total));
	return colors;
}

const containerContentSize = (container) => {
	if( !container ) {
		return { width: 808, height: 404 };
	}
	const style = getComputedStyle(container);
	const paddingX = parseFloat(style.paddingLeft) + parseFloat(style.paddingRight);
	const paddingY = parseFloat(style.paddingTop) + parseFloat(style.paddingBottom);

	const width = container.clientWidth - paddingX;
	const height = container.clientHeight - paddingY;
	return { width, height } ;
}

export default ({ containerRef, dimension, ...options }) => {
	const contentSize = containerContentSize(containerRef?.current);

	const [width, setWidth] = useState(contentSize.width);
	const [height, setHeight] = useState(contentSize.height);
	const [isDark, setIsDark] = useState(() => window.matchMedia('(prefers-color-scheme: dark)').matches);

	// Graph level options
	const {
		title,
		meta,
		data,
		showLegend,
		legendPosition = 'bottom',
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

	const colorPalette = buildPalette(seriesPalette, data.length);

	// Update width when the container ref changes
	useEffect(() => {
		const resizeObserver = new ResizeObserver(() => {
			const { width } = containerContentSize(containerRef?.current);
			setWidth(width);
		});

		resizeObserver.observe(containerRef?.current);

		return () => {
			if (typeof u !== 'undefined' && u) {
				u.destroy();
			}
			resizeObserver.disconnect();
		};
	}, [containerRef]);

	// Check for dark mode preference
	useEffect(() => {
		const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

		const handleChange = (event) => {
			setIsDark(event.matches);
		};

		mediaQuery.addEventListener('change', handleChange);

		return () => {
			mediaQuery.removeEventListener('change', handleChange);
		};
	}, []);

	// Set up the series and axes options
	let {
		series = [],
		axes = [{}, {}],
		scales = { x: {}, y: {} },
	} = options;

	const useStatus = meta.dimension?.includes('status');
	const colors = useStatus ? statusScale() : indexScale(colorPalette);

	series = series.map((s, idx) => {
		const stroke = colors(s, idx, opacity);
		const fill = ['area', 'bar', 'stacked-bar'].includes(type) ? colors(s, idx, opacity) : null;
		return { ...metricsOptions.series, ...s, stroke, fill,  };
	});

	axes = axes.map((a, idx) => {
		const theme = isDark ? 'dark' : 'light';
		const metricAxes = metricsOptions.axes[idx][theme] || metricsOptions.axes[idx];
		const graphAxes = a[theme] || a;
		return { ...metricAxes, ...graphAxes };
	});
	scales.y = { ...metricsOptions.scales.y, ...scales.y };
	scales.x = { ...metricsOptions.scales.x, ...scales.x };

	// Set up orientation and direction
	const isVertical = 'vertical' === orientation;
	const ori = isVertical ? 0 : 1;
	const dir = isVertical ? 1 : -1;

	// Adjust padding based on orientation and legend position
	let adjustedPadding = padding;

	// Add extra left padding for horizontal graphs to accommodate side labels
	if (!isVertical) {
		adjustedPadding = {
			...adjustedPadding,
			left: (adjustedPadding.left || 0) + 130 // Add 130px for side labels (120px width + 10px margin)
		};
	}

	// Add extra right padding when legend is positioned on the right
	if (showLegend && legendPosition === 'right') {
		const legendWidth = Math.min(200, width * 0.3); // Same width calculation as used for the legend
		adjustedPadding = {
			...adjustedPadding,
			right: (adjustedPadding.right || 0) + legendWidth + 10 // Add legend width plus some margin
		};
	}

	// Configure legend based on position
	const legendConfig = {
		...legend,
		show: showLegend !== false, // Show legend by default unless explicitly disabled
		live: false, // Don't update the legend on hover
		isolate: false, // Don't isolate series on legend hover
		width: width, // Set legend width to match the chart width
		stroke: null, // No stroke for legend markers
	};

	// Add position-specific legend configuration
	if (legendPosition === 'right') {
		legendConfig.position = 'right';
		legendConfig.width = Math.min(200, width * 0.3); // Set a reasonable width for the right legend
		legendConfig.dataAttr = { position: 'right' }; // Add data attribute for CSS targeting
	} else {
		legendConfig.dataAttr = { position: 'bottom' }; // Add data attribute for CSS targeting
	}

	let graphOptions = {
		title,
		width,
		height,
		series,
		axes,
		data,
		ori,
		dir,
		padding: adjustedPadding,
		plugins: [], // The hover effect is now integrated into seriesBarsPlugin
		scales,
		legend: legendConfig
	};

	// Add the legend position plugin
	graphOptions.plugins.push(legendPositionPlugin(legendPosition));

	// Add the legend label click plugin if dimension is provided
	if (dimension) {
		graphOptions.plugins.push(legendLabelClickPlugin(dimension));
	}

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
			graphOptions = { ...graphOptions, ...stack(data) };
			graphOptions.plugins.push(isolateStackedPlugin(data));
		} else {
			// For non-stacked bar charts, prevent legend clicks
			graphOptions.plugins.push(preventLegendClickPlugin());
		}

		graphOptions.plugins.push(seriesBarsPlugin(pluginOptions));
	}

	return graphOptions;
}
