/**
 * External dependencies
 */
import { useEffect, useRef } from 'react';

/**
 * Custom hook to handle uPlot graph height adjustments
 *
 * @param {Object} params - Hook parameters
 * @param {Object} params.containerRef - Reference to the container element
 * @param {Object} params.uplotInstanceRef - Reference to the uPlot instance
 * @param {Array} params.data - The graph data
 * @param {Array} params.series - The graph series
 * @param {boolean} params.showLegend - Whether to show the legend
 * @returns {Object} - The container dimensions ref
 */
export default function useUplotHeight({
    containerRef,
    uplotInstanceRef,
    data,
    series,
    showLegend
}) {
    // Create a ref to track container dimensions
    const containerDimensionsRef = useRef({ width: 0, height: 0 });

    // Update container dimensions when the component mounts and on window resize
    useEffect(() => {
        const updateDimensions = () => {
            if (containerRef.current) {
                containerDimensionsRef.current = {
                    width: containerRef.current.clientWidth,
                    height: containerRef.current.clientHeight
                };
            }
        };

        // Initial update
        updateDimensions();

        // Add resize listener
        window.addEventListener('resize', updateDimensions);

        // Cleanup
        return () => {
            window.removeEventListener('resize', updateDimensions);
        };
    }, [containerRef]);

    // Function to adjust container height based on legend size and apply styling for many series
    const adjustContainerHeight = () => {
        if (!uplotInstanceRef.current || !containerRef.current || !showLegend) {
            return;
        }

        const chart = uplotInstanceRef.current;
        const legendEl = chart.root.querySelector('.u-legend');

        if (legendEl) {
            // Count the number of series (excluding the first one which is usually the x-axis)
            const seriesCount = series ? series.length - 1 : 0;

            // If there are many series, add a class to enable multi-column layout
            if (seriesCount > 5) {
                legendEl.classList.add('u-legend-many');
            } else {
                legendEl.classList.remove('u-legend-many');
            }
        }
    };

    // Adjust container height when the component mounts and when data changes
    useEffect(() => {
        if (data && data.length > 0 && showLegend) {
            // Small delay to ensure the chart is fully rendered
            const timer = setTimeout(adjustContainerHeight, 200);
            return () => clearTimeout(timer);
        }
    }, [data, showLegend]);

    // Function to handle height adjustments when the chart is created
    const handleChartCreated = (chart) => {
        // Update dimensions after chart is created
        if (containerRef.current) {
            containerDimensionsRef.current = {
                width: containerRef.current.clientWidth,
                height: containerRef.current.clientHeight
            };

            // Adjust container height and apply styling for many series
            setTimeout(() => {
                const legendEl = chart.root.querySelector('.u-legend');
                if (legendEl && showLegend) {
                    // Count the number of series (excluding the first one which is usually the x-axis)
                    const seriesCount = series ? series.length - 1 : 0;
                    // If there are many series, add a class to enable multi-column layout
                    if (seriesCount > 5) {
                        legendEl.classList.add('u-legend-many');
                    }
                    // Remove any extra padding that might be added
                    legendEl.style.paddingBottom = '0';
                    legendEl.style.marginBottom = '0';
                }
                // Call the adjustContainerHeight function to handle any additional adjustments
                adjustContainerHeight();
            }, 100); // Small delay to ensure the chart is fully rendered
        }
    };

    return {
        containerDimensionsRef,
        handleChartCreated
    };
}
