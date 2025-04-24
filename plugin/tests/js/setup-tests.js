/**
 * External dependencies
 */
import '@testing-library/jest-dom';
import React from 'react';
import { TextEncoder, TextDecoder } from 'util';

// Polyfill TextEncoder and TextDecoder for Node.js environment
global.TextEncoder = TextEncoder;
global.TextDecoder = TextDecoder;

// Mock ResizeObserver
global.ResizeObserver = class ResizeObserver {
    constructor(callback) {
        this.callback = callback;
    }
    observe() {}
    unobserve() {}
    disconnect() {}
};

// Mock matchMedia
global.matchMedia = jest.fn().mockImplementation(query => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: jest.fn(),
    removeListener: jest.fn(),
    addEventListener: jest.fn(),
    removeEventListener: jest.fn(),
    dispatchEvent: jest.fn(),
}));

// Mock the uplot module
jest.mock('uplot', () => {
    return {
        __esModule: true,
        default: jest.fn(() => ({
            setSize: jest.fn(),
            setData: jest.fn(),
            redraw: jest.fn(),
            destroy: jest.fn(),
        })),
    };
});

// Mock the uplot-react module
jest.mock('uplot-react', () => {
    return {
        __esModule: true,
        default: jest.fn(() => <div data-testid="uplot-graph">Graph</div>),
    };
});
