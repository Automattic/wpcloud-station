
"use client";
import { useEffect, useState } from "react";
import { ErrorBoundary } from "react-error-boundary";
import Graph from './components/graph.js';
import GraphError from './components/error.js';


const logError = (error, info) => {
	console.error(error, info);
};

export default function ({ interval, refresh, ...props }) {
	const [resetKey, setResetKey] = useState(0);

  useEffect(() => {
    setResetKey((prev) => prev + 1); // Change key to force reset
  }, [refresh]);

	const error = () => (
		<div className="wpcloud-graph" style={{ width: "100%", height: "500px", position: "relative", backgroundColor: "white" }}>
			<GraphError />
		</div>
	);

	return (
		<ErrorBoundary key={resetKey}  fallbackRender={error} onError={logError}>
			<Graph  {...props} {...{interval, refresh}} />
		</ErrorBoundary>
	);
}