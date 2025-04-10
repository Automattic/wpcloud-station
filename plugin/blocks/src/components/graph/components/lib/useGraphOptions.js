import { useEffect, useState } from 'react';
import { stackedOptions, defaultOptions, barOptions, lineOptions, areaOptions } from './options';

// Remove this amount from the container height to fit the graph legend.
const fitGraphHeight = 75;
const fitGraphWidth = 20;

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

	const [width, setWidth] = useState(808);
	const [height, setHeight] = useState(404);

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
	}, [ containerRef ]);

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