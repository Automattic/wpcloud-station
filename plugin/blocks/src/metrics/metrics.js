import React from 'react';

import Graph from '../components/graph/components/graph.js';

function Metrics({ graphs, site, interval }) {

	const graphComponents = graphs.map((graph, index) => {
		return <Graph key={index} {...graph} site={site} interval={interval} />;
	});
	return (
		<div className="wpcloud-metrics">
			<h3>Metrics</h3>
			{graphComponents}
		</div>
	);

}

export default Metrics;
