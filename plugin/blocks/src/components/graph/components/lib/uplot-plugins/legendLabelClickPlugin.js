/**
 * Plugin for handling legend label click events for site navigation
 *
 * @param {string} dimension - The dimension to check for clickable labels
 * @returns {Object} - The plugin object
 */
export function legendLabelClickPlugin(dimension) {
	return {
		hooks: {
			ready: [
				(u) => {
					// Function to handle legend label events
					const handleLegendEvents = () => {
						// Find all legend labels
						const labels = u.root.querySelectorAll('.u-label');

						// Add click event for specific dimensions
						if ('atomic_site_id' === dimension) {
							labels.forEach(label => {
								// Skip labels that already have event handlers attached
								if (label.dataset.hasClickEvent === 'true') {
									return;
								}

								label.style.cursor = 'pointer';
								const clickHandler = (e) => {
									e.stopPropagation();
									console.log(`Clicked label: ${label.textContent}`);

									var site_url = label.textContent;
									if (isNaN(site_url)) {
										site_url = site_url.replace(/\./g, "-");
									}
									window.location.href = `/sites/${site_url}/metrics/`;
								};

								label.addEventListener('click', clickHandler);

								// Mark this label as having click event handler attached
								label.dataset.hasClickEvent = 'true';
							});
						}
					};

					// Initial setup with a delay to ensure DOM is ready
					setTimeout(handleLegendEvents, 100);

					// Set up a MutationObserver to watch for changes to the legend
					const observer = new MutationObserver((mutations) => {
						// Re-apply event handlers when the DOM changes
						handleLegendEvents();
					});

					// Start observing the chart root for changes
					observer.observe(u.root, {
						childList: true,
						subtree: true
					});

					// Store the observer for cleanup
					u.legendObserver = observer;
				}
			],
			destroy: [
				(u) => {
					// Clean up the observer when the chart is destroyed
					if (u.legendObserver) {
						u.legendObserver.disconnect();
					}

					// Remove data attributes from labels
					const labels = u.root.querySelectorAll('.u-label');
					labels.forEach(label => {
						if (label.dataset.hasClickEvent === 'true') {
							delete label.dataset.hasClickEvent;
						}
					});
				}
			]
		}
	};
}
