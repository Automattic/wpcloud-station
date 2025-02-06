import React, { useState } from 'react';

import Graph from '@wpcloud/components/graph/components/graph.js';
import Toolbar from './toolbar';

function Metrics({ graphs, site, ...props }) {
	const [ interval, setInterval ] = useState( props.interval );
	const graphComponents = graphs.map((graph, index) => {
		return <Graph key={index} {...graph} site={site} interval={interval} />;
	});
	return (
		<div className="wpcloud-metrics">
			<h3>Metrics</h3>
			<Toolbar onIntervalUpdate={setInterval} interval={interval} />
			{graphComponents}
		</div>
	);

}

export default Metrics;
