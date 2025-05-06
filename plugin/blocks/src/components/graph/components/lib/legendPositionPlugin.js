/**
 * Plugin to set data attributes for legend positioning
 * Note: This plugin only sets data attributes for CSS targeting
 * The actual legend rendering is handled by the SideLegend component for 'right' position
 *
 * @returns {Object} The plugin object
 */
export function legendPositionPlugin(position = 'bottom') {
    return {
        hooks: {
            ready: [
                (u) => {
                    // Find the legend element (only for bottom position)
                    if (position === 'bottom') {
                        const legendEl = u.root.querySelector('.u-legend');
                        if (legendEl) {
                            // Set the data attribute for CSS targeting
                            legendEl.setAttribute('data-position', position);
                        }
                    }
                }
            ]
        }
    };
}
