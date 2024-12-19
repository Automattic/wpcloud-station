import React from 'react';

import Graph from '../components/graph/graph.js';



function Metrics({ graphs, site }) {

	const graphComponents = graphs.map((graph, index) => {
		return <Graph key={index} {...graph} site={site} />;
	});
	return (
		<div className="wpcloud-metrics">
			<h3>Metrics</h3>
			{graphComponents}
		</div>
	);

}

export default Metrics;
