/**
 * External dependencies
 */
import { useState, useEffect } from 'react';


/**
 * Internal dependencies
 */
import { parseTime } from './utils';

export function useQueryParams()  {
	const [queryParams, setQueryParams] = useState(new URLSearchParams(window.location.search));

	useEffect(() => {
		const handlePopState = () => {
			setQueryParams(new URLSearchParams(window.location.search));
		};

		window.addEventListener("popstate", handlePopState);
		return () => {
			window.removeEventListener("popstate", handlePopState);
		};
	}, []);

	return queryParams;
};

export function useQueryBoundary(boundary, defaultValue) {
	const queryParams = useQueryParams();
	const boundaryValue = queryParams.get(boundary);

	if (!parseTime(boundaryValue)) {
		return defaultValue;
	}

	return boundaryValue;
}