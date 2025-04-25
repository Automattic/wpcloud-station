/**
 * External dependencies
 */
import { createRoot } from 'react-dom/client';

/**
 * Internal dependencies
 */
import Metrics from './components/metrics.js';

import { buildTree } from './utils.js';

document.addEventListener('DOMContentLoaded', () => {
	const container = document.getElementById('metrics');
	const metricsAttributes = container.dataset.metricsAttributes;
	if (!metricsAttributes) {
		console.error('No metrics attributes found in the container.');
		return;
	}

	let apiPath = 'metrics/';
	const siteId = window.wpcloudSite?.id;
	if (siteId) {
		apiPath += 'site/' + siteId;
	} else {
		apiPath += 'client'
	}

	const tree = {
		type: 'metric',
		children: Array.from(container.children)
			.map((child) => buildTree(child))
			.filter(Boolean),
	}

	const root = createRoot(container);
	root.render(<Metrics { ...{ tree, apiPath } }/>);
});
