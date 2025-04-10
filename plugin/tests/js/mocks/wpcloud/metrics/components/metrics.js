/**
 * Mock for the metrics component
 */
import React from 'react';

const Metrics = ({ tree, apiPath }) => {
    return (
        <div className="wpcloud-metrics">
            <h3>Metrics</h3>
            <div data-testid="toolbar">Toolbar</div>
            <div className="wpcloud-metrics__graphs">
                {tree && tree.children && tree.children.map((node, index) => (
                    <div key={index} data-testid={`node-${index}`}>{node.type}</div>
                ))}
            </div>
        </div>
    );
};

export default Metrics;
