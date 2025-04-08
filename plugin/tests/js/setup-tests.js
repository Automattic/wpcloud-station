/**
 * External dependencies
 */
import '@testing-library/jest-dom';
import React from 'react';

// Mock ResizeObserver
global.ResizeObserver = class ResizeObserver {
    constructor(callback) {
        this.callback = callback;
    }
    observe() {}
    unobserve() {}
    disconnect() {}
};

// Mock the uplot-react module
jest.mock('uplot-react', () => {
    return {
        __esModule: true,
        default: jest.fn(() => <div data-testid="uplot-graph">Graph</div>),
    };
});
