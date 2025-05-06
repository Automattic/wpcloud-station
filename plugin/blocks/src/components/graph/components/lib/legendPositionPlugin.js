/**
 * Plugin to position the legend on the right side of the chart
 *
 * @returns {Object} The plugin object
 */
export function legendPositionPlugin(position = 'bottom') {
    return {
        hooks: {
            ready: [
                (u) => {
                    // Find the legend element
                    const legendEl = u.root.querySelector('.u-legend');

                    if (!legendEl) {
                        console.warn('Legend element not found');
                        return;
                    }

                    // Set the data attribute for CSS targeting
                    legendEl.setAttribute('data-position', position);

                    if (position === 'right') {
                        // Get the legend width (use 200px as default if not specified)
                        const legendWidth = u.legend.width || 200;

                        // Apply styles for right-positioned legend
                        legendEl.style.position = 'absolute';
                        legendEl.style.top = '30px';
                        legendEl.style.right = '10px'; // Position in the padding area
                        legendEl.style.bottom = '0';
                        legendEl.style.width = `${legendWidth}px`;
                        legendEl.style.maxWidth = `${legendWidth}px`;
                        legendEl.style.overflowY = 'auto';
                        legendEl.style.paddingLeft = '10px';
                        legendEl.style.boxSizing = 'border-box';

                        // Add a background and border to make it stand out
                        legendEl.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
                        legendEl.style.borderLeft = '1px solid #ddd';
                        legendEl.style.paddingTop = '10px';

                        // Style the table for vertical layout
                        const table = legendEl.querySelector('table');
                        if (table) {
                            table.style.width = '100%';
                        }

                        // Style the rows for vertical layout
                        const rows = legendEl.querySelectorAll('tr');
                        rows.forEach(row => {
                            row.style.display = 'block';
                            row.style.marginBottom = '5px';
                        });

                        // Style the cells for vertical layout
                        const cells = legendEl.querySelectorAll('th, td');
                        cells.forEach(cell => {
                            cell.style.display = 'block';
                            cell.style.textAlign = 'left';
                            cell.style.padding = '2px 0';
                        });
                    }
                }
            ]
        }
    };
}
