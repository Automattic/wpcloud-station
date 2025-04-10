/**
 * External dependencies
 */
import React from 'react';
import { render, screen, waitFor, fireEvent } from '@testing-library/react';
import '@testing-library/jest-dom';

/**
 * Internal dependencies
 */
import Metrics from '@wpcloud/metrics/components/metrics';
import stationApi from '@wpcloud/utils/api';

// Mock the stationApi.get method
jest.mock('@wpcloud/utils/api', () => ({
    get: jest.fn(),
}));

// Mock the window.history.pushState
const mockPushState = jest.fn();
Object.defineProperty(window, 'history', {
    writable: true,
    value: { pushState: mockPushState }
});

// Mock the PopStateEvent
class MockPopStateEvent extends Event {
    constructor(type, eventInitDict) {
        super(type, eventInitDict);
    }
}
global.PopStateEvent = MockPopStateEvent;

describe('Metrics Component', () => {
    beforeEach(() => {
        // Reset the mocks before each test
        stationApi.get.mockReset();
        mockPushState.mockReset();

        // Mock the window.location
        Object.defineProperty(window, 'location', {
            writable: true,
            value: {
                pathname: '/metrics',
                search: '',
                href: 'http://localhost/metrics'
            }
        });

        // Mock the window.dispatchEvent
        window.dispatchEvent = jest.fn();
    });

    it('renders the metrics component', () => {
        const tree = {
            type: 'group',
            style: {},
            classNames: ['metrics-container'],
            children: []
        };

        render(<Metrics tree={tree} apiPath="wpcloud-station/v1/metrics/client" />);

        // Check if the component renders
        expect(screen.getByText('Metrics')).toBeInTheDocument();
    });

    it('renders graphs from the tree', () => {
        const tree = {
            type: 'group',
            style: {},
            classNames: ['metrics-container'],
            children: [
                {
                    type: 'graph',
                    style: {},
                    classNames: ['graph-1'],
                    attributes: {
                        metric: 'requests',
                        dimension: 'time',
                        title: 'Requests'
                    }
                },
                {
                    type: 'graph',
                    style: {},
                    classNames: ['graph-2'],
                    attributes: {
                        metric: 'response_time',
                        dimension: 'time',
                        title: 'Response Time'
                    }
                }
            ]
        };

        // Mock the API response
        const mockResponse = {
            meta: { dimension: 'time' },
            series: [{ label: 'Time' }, { label: 'Value' }],
            data: [[1, 2, 3], [4, 5, 6]]
        };
        stationApi.get.mockResolvedValue(mockResponse);

        render(<Metrics tree={tree} apiPath="wpcloud-station/v1/metrics/client" />);

        // Check if the component renders
        expect(screen.getByText('Metrics')).toBeInTheDocument();
    });

    it('renders the toolbar', () => {
        const tree = {
            type: 'group',
            style: {},
            classNames: ['metrics-container'],
            children: [
                {
                    type: 'graph',
                    style: {},
                    classNames: ['graph-1'],
                    attributes: {
                        metric: 'requests',
                        dimension: 'time',
                        title: 'Requests'
                    }
                }
            ]
        };

        render(<Metrics tree={tree} apiPath="wpcloud-station/v1/metrics/client" />);

        // Find the toolbar
        const toolbar = screen.getByTestId('toolbar');
        expect(toolbar).toBeInTheDocument();
        expect(toolbar).toHaveTextContent('Toolbar');
    });

    it('handles nested groups in the tree', () => {
        const tree = {
            type: 'group',
            style: {},
            classNames: ['metrics-container'],
            children: [
                {
                    type: 'group',
                    style: {},
                    classNames: ['nested-group'],
                    children: [
                        {
                            type: 'graph',
                            style: {},
                            classNames: ['graph-1'],
                            attributes: {
                                metric: 'requests',
                                dimension: 'time',
                                title: 'Requests'
                            }
                        }
                    ]
                }
            ]
        };

        // Mock the API response
        const mockResponse = {
            meta: { dimension: 'time' },
            series: [{ label: 'Time' }, { label: 'Value' }],
            data: [[1, 2, 3], [4, 5, 6]]
        };
        stationApi.get.mockResolvedValue(mockResponse);

        render(<Metrics tree={tree} apiPath="wpcloud-station/v1/metrics/client" />);

        // Check if the component renders
        expect(screen.getByText('Metrics')).toBeInTheDocument();
    });

    it('renders the graph nodes', () => {
        const tree = {
            type: 'group',
            style: {},
            classNames: ['metrics-container'],
            children: [
                {
                    type: 'graph',
                    style: {},
                    classNames: ['graph-1'],
                    attributes: {
                        metric: 'requests',
                        dimension: 'time',
                        title: 'Requests'
                    }
                }
            ]
        };

        render(<Metrics tree={tree} apiPath="wpcloud-station/v1/metrics/client" />);

        // Check if the component renders
        expect(screen.getByText('Metrics')).toBeInTheDocument();

        // Check if the graph node is rendered
        expect(screen.getByTestId('node-0')).toBeInTheDocument();
        expect(screen.getByTestId('node-0')).toHaveTextContent('graph');
    });
});
