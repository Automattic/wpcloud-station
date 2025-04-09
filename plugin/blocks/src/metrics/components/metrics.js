/**
 * External dependencies
 */
import React, { useState, useEffect, useCallback } from 'react';

/**
 * Internal dependencies
 */
import Graph from '@wpcloud/components/graph/components/graph.js';
import { ApiContext } from './apiContext.js';
import Toolbar from './toolbar';
import { useQueryBoundary } from '../hooks';

function renderNodeWithProps(node, key, props) {

	if ('graph' === node.type) {
		return (<Graph key={key} style={node.style} {...node.attributes} className={node.classNames.join(' ')} {...props} />);
	}
	if ('group' === node.type) {
		return (<div key={key} style={node.style} className={node.classNames.join(' ')}>{node.children.map((child, index) => renderNodeWithProps(child, index, props))}</div>);
	}
	return null;
}

function Metrics({ tree, apiPath }) {
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
	const onRefresh = () => {
		setToggleRefresh(!toggleRefresh);
	};

	const renderNode = useCallback((node, index) => {
		return renderNodeWithProps(node, index, { interval, refresh: toggleRefresh });
	}, [interval, toggleRefresh]);

	return (
		<ApiContext.Provider value={{ apiPath }}>
			<div className="wpcloud-metrics">
				<h3>Metrics</h3>
				<Toolbar onIntervalUpdate={updateQueryParams} interval={interval} onRefresh={onRefresh} />
				<div className="wpcloud-metrics__graphs">
					{tree.children.map(renderNode)}
				</div>
			</div>
		</ApiContext.Provider>
	);
}

export default Metrics;
