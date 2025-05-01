import React, { useEffect, useState, useRef } from 'react';

/**
 * Custom hook to render labels on the side of a horizontal graph
 *
 * @param {Object} uplotInstance - The uPlot instance
 * @param {Object} options - The graph options
 * @param {Object} containerRef - Reference to the container element
 * @param {boolean} isHorizontal - Whether the graph is horizontal
 * @returns {Object} - The rendered labels and container ref
 */
export function useSideLabels(uplotInstance, options, containerRef, isHorizontal = false) {
	const [renderedLabels, setRenderedLabels] = useState([]);
	const labelContainerRef = useRef(null);

	// Store the options in a ref to access the latest value without triggering re-renders
	const optionsRef = useRef(options);

	// Update the ref when options change
	useEffect(() => {
		optionsRef.current = options;
	}, [options]);

	// Create a function to update the labels
	const updateLabels = () => {
		console.log('updateLabels called', {
			isHorizontal,
			hasUplot: !!uplotInstance,
			hasOptions: !!optionsRef.current,
			hasSeries: optionsRef.current?.series?.length
		});

		// Only process labels if the graph is horizontal and we have the necessary data
		if (!isHorizontal || !uplotInstance || !optionsRef.current || !optionsRef.current.series) {
			console.log('Early return from updateLabels - missing required data');
			if (renderedLabels.length > 0) {
				setRenderedLabels([]);
			}
			return;
		}

		try {
			// Create a simple test label to verify rendering is working
			const testLabels = [
				<div key="test1" style={{
					position: "absolute",
					top: '50px',
					left: 0,
					color: 'red',
					padding: '5px',
					border: '1px solid red',
					backgroundColor: 'rgba(255,255,255,0.7)',
					zIndex: 1000
				}}>Test Label 1</div>,
				<div key="test2" style={{
					position: "absolute",
					top: '100px',
					left: 0,
					color: 'blue',
					padding: '5px',
					border: '1px solid blue',
					backgroundColor: 'rgba(255,255,255,0.7)',
					zIndex: 1000
				}}>Test Label 2</div>
			];

			console.log('Setting test labels');
			setRenderedLabels(testLabels);

			// We'll implement the actual dynamic labels once we confirm the test labels work
		} catch (e) {
			console.error('Error in updateLabels:', e);
		}
	};

	// Update labels when uPlot instance or horizontal state changes
	useEffect(() => {
		console.log('useSideLabels effect triggered', {
			isHorizontal,
			hasUplot: !!uplotInstance
		});

		if (isHorizontal && uplotInstance) {
			console.log('Adding hooks to uPlot instance');

			// Initial update
			updateLabels();

			// Add hooks to update labels after uPlot renders and draws, but only if hooks exist
			if (uplotInstance.hooks && uplotInstance.hooks.setData && uplotInstance.hooks.draw) {
				const dataHook = () => {
					console.log('setData hook called');
					updateLabels();
				};

				const drawHook = () => {
					console.log('draw hook called');
					updateLabels();
				};

				uplotInstance.hooks.setData.push(dataHook);
				uplotInstance.hooks.draw.push(drawHook);

				return () => {
					// Clean up the hooks when component unmounts or dependencies change
					console.log('Cleaning up uPlot hooks');
					if (uplotInstance.hooks && uplotInstance.hooks.setData) {
						const dataIdx = uplotInstance.hooks.setData.indexOf(dataHook);
						if (dataIdx !== -1) {
							uplotInstance.hooks.setData.splice(dataIdx, 1);
						}
					}

					if (uplotInstance.hooks && uplotInstance.hooks.draw) {
						const drawIdx = uplotInstance.hooks.draw.indexOf(drawHook);
						if (drawIdx !== -1) {
							uplotInstance.hooks.draw.splice(drawIdx, 1);
						}
					}
				};
			}

			// Return a no-op cleanup function if hooks don't exist
			return () => {};
		}
	}, [uplotInstance, isHorizontal]);

	// Return both the rendered labels and the container ref
	return { renderedLabels, labelContainerRef };
}
