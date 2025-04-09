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
	const { type } = JSON.parse(metricsAttributes);
	let apiPath = `metrics/${type}`;
	if ('site' == type) {
		const siteId = window.wpcloudSite?.id;
		if (!siteId) {
			console.error('No site ID found in the window object.');
			return;
		}

		apiPath += `/${siteId}`;
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