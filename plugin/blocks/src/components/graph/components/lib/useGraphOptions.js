import { useEffect, useState } from 'react';
import { stackedOptions, defaultOptions, barOptions, lineOptions, areaOptions } from './options';

// Remove this amount from the container height to fit the graph legend.
const fitGraphHeight = 75;
// Reduced the width adjustment to ensure graphs aren't too skinny
const fitGraphWidth = 10;

export default (options) => {
	const {
		title,
		series,
		meta,
		data,
		containerRef,
		showLegend,
		type,
		orientation,
	} = options;

	// Set more appropriate initial values for flex containers
	const [width, setWidth] = useState(containerRef?.current?.offsetWidth || 808);
	const [height, setHeight] = useState(containerRef?.current?.offsetHeight || 404);

	// Use a more direct approach with fixed dimensions
	useEffect(() => {
		// Only run once on mount to set initial dimensions
		if (containerRef.current) {
			// Set fixed dimensions based on container size
			const initialWidth = containerRef.current.offsetWidth - fitGraphWidth;
			const initialHeight = containerRef.current.offsetHeight - fitGraphHeight;

			if (initialWidth > 0 && initialHeight > 0) {
				setWidth(initialWidth);
				setHeight(initialHeight);
			}
		}

		// No ResizeObserver - use fixed dimensions instead
	}, [containerRef]); // Only run when containerRef changes (essentially on mount)

	let graphOptions = {
		title,
		width,
		height,
		series,
		data,
		useStatus: meta.dimension?.includes('status'),
		ori: 'vertical' === orientation ? 0 : 1,
		dir: 'vertical' === orientation ? 1 : -1,
	};

	console.log('graphOptions', orientation, graphOptions);

	if (!showLegend) {
		graphOptions.legend = { live: false };
	}

	// @TODO: populate seriesOptions with block attributes
	let seriesOptions = {};

	switch (type) {
		case 'stacked-bar':
			return stackedOptions({...graphOptions, seriesOptions });

		case 'bar':
			return barOptions({ ...graphOptions, seriesOptions });

		case 'line':
			seriesOptions = { points: { show: false } };
			return lineOptions({...graphOptions, useStatus: false, seriesOptions });

		case 'area':
			seriesOptions = { points: { show: false }, opacity: 0.5 };
			return areaOptions({...graphOptions, useStatus: false, seriesOptions });

		default:
			return defaultOptions( graphOptions );
	}

}
