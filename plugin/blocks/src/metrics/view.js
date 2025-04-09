/**
 * External dependencies
 */
import { createRoot } from 'react-dom/client';

/**
 * Internal dependencies
 */
import Metrics from './components/metrics.js';

document.addEventListener('DOMContentLoaded', () => {
	const container = document.getElementById('metrics');
	const metricsAttributes = container.dataset.metricsAttributes;
	if ( !metricsAttributes ) {
		console.error('No metrics attributes found in the container.');
		return;
	}
	const { type } = JSON.parse(metricsAttributes);
	let apiPath = `metrics/${type}`;
	if ( 'site' == type ) {
		const siteId = window.wpcloudSite?.id;
		if ( ! siteId ) {
			console.error( 'No site ID found in the window object.' );
			return;
		}

		apiPath += `/${siteId}`;
	}
	const graphs = Array.from(container.querySelectorAll('.wp-block-wpcloud-graph')).map((graph) => {
		const data = JSON.parse(graph.dataset.graphAttributes);
		data.apiPath = apiPath;
		graph.parentNode?.removeChild(graph);
		return data;
	});

	const root = createRoot(container);
	root.render(<Metrics graphs={graphs} />);
});