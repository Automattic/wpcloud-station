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
import { useQueryBoundary, useQueryParams } from '../hooks';
import { encodeFilter, decodeFilter } from '../utils';

function renderNodeWithProps(node, key, props) {

	if ('graph' === node.type) {
		return (<Graph key={key} styles={node.style} {...node.attributes} className={node.classNames.join(' ')} {...props} />);
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
	const [filters, setFilters] = useState([]);

	const qSTart = useQueryBoundary('start', 'now-1h');
	const qEnd = useQueryBoundary('end', 'now');
	const qFilters = useQueryParams().get('filters');

	useEffect(() => {
		setStart(qSTart);
	}, [qSTart]);

	useEffect(() => {
		setEnd(qEnd);
	}, [qEnd]);

	useEffect(() => {
		if (qFilters) {
			try {
				const parsedFilters = decodeFilter(qFilters);
				console.log('Parsed filters from query params:', parsedFilters);
				setFilters(parsedFilters);
			} catch (error) {
				console.error('Error parsing filters from query params:', error);
			}
		}
	}, []);

	const updateQueryParams = async ({ start, end, filters }) => {
		const searchParams = new URLSearchParams(window.location.search);
		start && searchParams.set('start', start);
		end && searchParams.set('end', end);


		if (filters !== undefined) {
			if (filters.length > 0) {
				searchParams.set('filters', encodeFilter( filters ) );
			} else {
				// If filters is an empty array, remove the filters parameter from the URL
				searchParams.delete('filters');
			}
			// Always update the filters state, even if it's an empty array
			setFilters(filters);
		}

		window.history.pushState({}, '', `${window.location.pathname}?${searchParams}`);
		window.dispatchEvent(new PopStateEvent('popstate'));
	};

	const interval = { start, end };
	const onRefresh = () => {
		setToggleRefresh(!toggleRefresh);
	};

	const renderNode = useCallback((node, index) => {
		console.log('Rendering node with filters:', filters);
		return renderNodeWithProps(node, index, { interval, refresh: toggleRefresh, filters });
	}, [interval, toggleRefresh, filters]);

	console.log('Rendering Metrics component with filters:', filters);
	return (
		<ApiContext.Provider value={{ apiPath }}>
			<div className="wpcloud-metrics">
				<h3>Metrics</h3>
				<Toolbar onIntervalUpdate={updateQueryParams} interval={interval} onRefresh={onRefresh} filters={filters} onFiltersUpdate={updateQueryParams} />
				<div className="wpcloud-metrics__graphs">
					{tree.children.map(renderNode)}
				</div>
			</div>
		</ApiContext.Provider>
	);
}

export default Metrics;
