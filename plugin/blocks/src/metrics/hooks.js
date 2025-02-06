/**
 * External dependencies
 */
import { useState,useEffect } from 'react';

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
	return queryParams.get(boundary) || defaultValue;
}