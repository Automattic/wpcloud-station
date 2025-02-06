/**
 * External dependencies
 */
import { createRoot } from 'react-dom/client';

/**
 * Internal dependencies
 */
import Metrics from './components/metrics.js';

document.addEventListener('DOMContentLoaded', () => {
	const siteId = wpcloudSite?.id;
	const container = document.getElementById('metrics');
	const graphs = Array.from(container.querySelectorAll('.wp-block-wpcloud-graph')).map((graph) => {
		const data = JSON.parse(graph.dataset.graphAttributes);
		graph.parentNode?.removeChild(graph);
		return data;
	});

	const root = createRoot(container);
	root.render(<Metrics graphs={graphs} site={siteId} interval={{ start: "now-1h", end: "now" }} />);
});