import { createRoot } from 'react-dom/client';

import Metrics from './metrics.js';


document.addEventListener('DOMContentLoaded', () => {
 /**
	* @TODO fetch the site id from wpcloud.something....
	*/
	const siteId = 150743966;
	const container = document.getElementById('metrics');
	const graphs = Array.from(container.querySelectorAll('.wp-block-wpcloud-graph')).map((graph) => {
		const data = JSON.parse(graph.dataset.graphAttributes);
		container.removeChild(graph);
		return data;
	});


	const root = createRoot(container);
	root.render(<Metrics graphs={graphs} site={siteId}  />);
});