/**
 * External dependencies
 */
import React, { useState, useEffect, useCallback } from 'react';

/**
 * Internal dependencies
 */
import Graph from '@wpcloud/components/graph/components/graph.js';
import { ApiContext, MetricsOptionsContext } from './contexts';
import Toolbar from './toolbar';
import { useQueryBoundary } from '../hooks';

function renderNodeWithProps(node, key, props) {

	if ('graph' === node.type) {
		return (<Graph key={key} styles={node.style} {...node.attributes} className={node.classNames.join(' ')} {...props} />);
	}
	if ('group' === node.type) {
		return (<div key={key} style={node.style} className={node.classNames.join(' ')}>{node.children.map((child, index) => renderNodeWithProps(child, index, props))}</div>);
	}
	return null;
}

const defaultGraphOptions = {
	seriesPalette: [
		"#bcf60c", // Lime
		"#4363d8", // Royal Blue
		"#3cb44b", // Lime Green
		"#e6194b", // Crimson
		"#ffe119", // Lemon
		"#f58231", // Orange
		"#911eb4", // Purple
		"#46f0f0", // Cyan
		"#f032e6", // Magenta
		"#fabebe", // Rose
	],
	axes: [
		{
			dark: {
				stroke: '#FFF',
				grid: { stroke: '#111', width: 1 },
				ticks: { stroke: '#888', size: 5, width: 1 },
				font: '12px sans-serif',
				//label: "Axis 1",
			},
			light: {
				stroke: '#000',
				grid: { stroke: '#eee', width: 1, dash: [4, 4] },
				ticks: { stroke: '#888', size: 5, width: 1 },
				font: '12px sans-serif',
			}
		},
		{
			dark: {
				stroke: '#FFF',
				grid: { stroke: '#111', width: 1 },
				ticks: { stroke: '#888', size: 5, width: 1 },
				font: '12px sans-serif',
				//label: "Axis 2",
			},
			light: {
				stroke: '#000',
				grid: { stroke: '#eee', width: 1, dash: [4, 4] },
				ticks: { stroke: '#888', size: 5, width: 1 },
				font: '12px sans-serif',
			}
		}
	],

	padding: [10, 10, 0, 0],
	fit: {
		width: 10,
		height: 75,
	},
	scales: {
		y: {
			range: [0, null],
			ori: 1,
		},
		x: {},
	},
	legend: { live: false, isolate: false },
	series: {
		points: { show: false },
		width: 2,
	}
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
				<MetricsOptionsContext.Provider value={ defaultGraphOptions }>
					<div className="wpcloud-metrics__graphs" style={{ marginBottom: 0 }}>
						{tree.children.map(renderNode)}
					</div>
				</MetricsOptionsContext.Provider>
			</div>
		</ApiContext.Provider>
	);
}

export default Metrics;
