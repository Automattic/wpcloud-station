/**
 * Plugin that prevents legend clicks for non-stacked bar charts
 *
 * @returns {Object} - The plugin object
 */
export function preventLegendClickPlugin() {
	return {
		hooks: {
			ready: (u) => {
				// Find the legend element
				const legendEl = u.root.querySelector(".u-legend");

				if (legendEl) {
					// Add capture phase event listener to intercept clicks before they reach uPlot's handlers
					legendEl.addEventListener("click", (e) => {
						// Stop propagation and prevent default to block uPlot's default behavior
						e.stopPropagation();
						e.preventDefault();
						return false;
					}, true); // true for capture phase
				}
			}
		}
	};
}
