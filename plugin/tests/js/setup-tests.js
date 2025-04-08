/**
 * External dependencies
 */
import '@testing-library/jest-dom';
import React from 'react';

/**
 * WordPress dependencies
 */
import { setFetchHandler } from '@wordpress/api-fetch';

// Mock the WordPress API fetch
setFetchHandler( ( options ) => {
    return Promise.reject( {
        code: 'fetch_error',
        message: 'API Fetch not implemented in tests',
    } );
} );

// Mock ResizeObserver
global.ResizeObserver = class ResizeObserver {
    constructor(callback) {
        this.callback = callback;
    }
    observe() {}
    unobserve() {}
    disconnect() {}
};

// Mock the WordPress element module
jest.mock('@wordpress/element', () => {
    return {
        ...jest.requireActual('@wordpress/element'),
        useEffect: jest.fn((callback, deps) => {
            callback();
            return undefined;
        }),
    };
});

// Mock the WordPress components module
jest.mock('@wordpress/components', () => {
    return {
        Spinner: () => <div data-testid="spinner">Loading...</div>,
    };
});

// Mock the uplot-react module
jest.mock('uplot-react', () => {
    return {
        __esModule: true,
        default: jest.fn(() => <div data-testid="uplot-graph">Graph</div>),
    };
});

// Mock the stationApi
jest.mock('@wpcloud/utils/api', () => {
    return {
        __esModule: true,
        default: {
            get: jest.fn(() => Promise.resolve({
                data: [],
                series: [],
                meta: {},
            })),
        },
    };
});
