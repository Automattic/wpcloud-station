/**
 * Plugin that customizes legend clicks
 * When a legend item is clicked, it shows only that series and hides all others
 *
 * @param {Array} originalData - The original data array
 * @returns {Object} - The plugin object
 */
export function isolateStackedPlugin(originalData) {
	let activeSeriesIdx = null; // Track which series is currently active
	let stackedData = null; // Store the stacked data

	return {
		hooks: {
			ready: (u) => {

				// Store the stacked data that's currently in the chart
				if (u.data && Array.isArray(u.data) && u.data.length > 0) {
					stackedData = [...u.data];
				}

				// Find the legend element
				const legendEl = u.root.querySelector(".u-legend");

				if (legendEl) {
					// Add capture phase event listener to intercept clicks before they reach uPlot's handlers
					legendEl.addEventListener("click", (e) => {
						// Stop propagation and prevent default to block uPlot's default behavior
						e.stopPropagation();
						e.preventDefault();

						// Find the closest legend item (tr element)
						const target = e.target;
						const legendItem = target.closest("tr");

						if (!legendItem) {
							return false;
						}

						// Find all legend items (tr elements)
						const legendItems = legendEl.querySelectorAll("tr");

						// Get the index of the clicked legend item
						let clickedIdx = Array.from(legendItems).indexOf(legendItem);

						// If the same item is clicked again, show all series
						if (activeSeriesIdx === clickedIdx) {
							console.log("Showing all series");
							// Remove u-off class from all legend items
							legendItems.forEach(item => {
								item.classList.remove("u-off");
							});
							activeSeriesIdx = null;

							// Restore the stacked data
							if (stackedData) {
								u.setData(stackedData);
							}
						} else {
							console.log("Showing only series", clickedIdx);
							// Apply u-off class to all legend items except the clicked one
							legendItems.forEach((item, idx) => {
								if (idx !== clickedIdx) {
									item.classList.add("u-off");
								} else {
									item.classList.remove("u-off");
								}
							});
							activeSeriesIdx = clickedIdx;

							// Replace the data with original data for the clicked series
							if (originalData && Array.isArray(originalData) && originalData.length > clickedIdx) {
								++clickedIdx; // Adjust for the x-axis series

								// Create a new data array with just the x-axis values and the clicked series
								const newData = stackedData.map((series, idx) => {
									if (idx === 0) {
										// Keep x-axis values
										return series;
									} else if (idx === clickedIdx) {
										// Use the original unstacked data for this series
										return originalData[idx];
									} else {
										// For other series, create an array of the same length as the x-axis but with null values
										return Array(stackedData[0].length).fill(null);
									}
								});

								// Update the graph with the new data
								u.setData(newData);
							}
						}

						return false;
					}, true); // true for capture phase
				} else {
					console.warn("Legend element not found");
				}
			}
		}
	};
}
