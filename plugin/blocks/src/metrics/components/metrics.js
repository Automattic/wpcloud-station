/**
 * External dependencies
 */
import React, { useState, useEffect } from 'react';

/**
 * Internal dependencies
 */
import Graph from '@wpcloud/components/graph/components/graph.js';
import Toolbar from './toolbar';
import { useQueryBoundary } from '../hooks';

function Metrics({ graphs, site }) {
	const [start, setStart] = useState('now-1h');
	const [end, setEnd] = useState('now');
	const [toggleRefresh, setToggleRefresh] = useState(false);

	const qSTart = useQueryBoundary('start', 'now-1h');
	const qEnd = useQueryBoundary('end', 'now');

	useEffect(() => {
		setStart(qSTart);
	}, [qSTart]);

	useEffect(() => {
		setEnd(qEnd);
	}, [qEnd]);

	const updateQueryParams = ({ start, end }) => {
		const searchParams = new URLSearchParams(window.location.search);
		searchParams.set('start', start);
		searchParams.set('end', end);
		window.history.pushState({}, '', `${window.location.pathname}?${searchParams}`);
		window.dispatchEvent(new PopStateEvent('popstate'));
	};

	const interval = { start, end };

	const graphComponents = graphs.map((graph, index) => {
		return <Graph key={index} {...graph} site={site} interval={interval} refresh={ toggleRefresh } />;
	});

	const onRefresh = () => {
		setToggleRefresh(!toggleRefresh);
	};

	return (
		<div className="wpcloud-metrics">
			<h3>Metrics</h3>
			<Toolbar onIntervalUpdate={updateQueryParams} interval={interval} onRefresh={onRefresh} />
			{graphComponents}
		</div>
	);
}

export default Metrics;
