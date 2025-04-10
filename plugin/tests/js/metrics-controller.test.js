/**
 * External dependencies
 */
import { render, screen, waitFor } from '@testing-library/react';
import '@testing-library/jest-dom';

/**
 * Internal dependencies
 */
import stationApi from '@wpcloud/utils/api';

// Mock the stationApi.get method
jest.mock('@wpcloud/utils/api', () => ({
    get: jest.fn(),
}));

describe('Metrics Controller', () => {
    beforeEach(() => {
        // Reset the mock before each test
        stationApi.get.mockReset();
    });

    describe('Available Metrics Endpoint', () => {
        it('fetches available metrics', async () => {
            // Mock the API response
            const mockResponse = {
                dimensions: ['time', 'status', 'url'],
                metrics: ['requests', 'response_time', 'errors']
            };
            stationApi.get.mockResolvedValue(mockResponse);

            // Call the API
            const response = await stationApi.get('wpcloud-station/v1/metrics/available');

            // Verify the API was called correctly
            expect(stationApi.get).toHaveBeenCalledWith('wpcloud-station/v1/metrics/available');

            // Verify the response
            expect(response).toEqual(mockResponse);
            expect(response.dimensions).toContain('time');
            expect(response.metrics).toContain('requests');
        });

        it('handles errors when fetching available metrics', async () => {
            // Mock an API error
            const mockError = new Error('API error');
            stationApi.get.mockRejectedValue(mockError);

            // Call the API and expect it to throw
            await expect(stationApi.get('wpcloud-station/v1/metrics/available')).rejects.toThrow('API error');
        });
    });

    describe('Client Metrics Endpoint', () => {
        it('fetches client metrics', async () => {
            // Mock the API response
            const mockResponse = {
                meta: { dimension: 'time' },
                series: [{ label: 'Time' }, { label: 'Requests' }],
                data: [[1, 2, 3], [4, 5, 6]]
            };
            stationApi.get.mockResolvedValue(mockResponse);

            // Call the API
            const response = await stationApi.get('wpcloud-station/v1/metrics/client/requests', {
                query: {
                    dimension: 'time',
                    start: 'now-24h',
                    end: 'now'
                }
            });

            // Verify the API was called correctly
            expect(stationApi.get).toHaveBeenCalledWith('wpcloud-station/v1/metrics/client/requests', {
                query: {
                    dimension: 'time',
                    start: 'now-24h',
                    end: 'now'
                }
            });

            // Verify the response
            expect(response).toEqual(mockResponse);
            expect(response.meta.dimension).toBe('time');
            expect(response.series).toHaveLength(2);
            expect(response.data).toHaveLength(2);
        });

        it('handles errors when fetching client metrics', async () => {
            // Mock an API error
            const mockError = new Error('API error');
            stationApi.get.mockRejectedValue(mockError);

            // Call the API and expect it to throw
            await expect(stationApi.get('wpcloud-station/v1/metrics/client/requests')).rejects.toThrow('API error');
        });
    });

    describe('Site Metrics Endpoint', () => {
        it('fetches site metrics', async () => {
            // Mock the API response
            const mockResponse = {
                meta: { dimension: 'time' },
                series: [{ label: 'Time' }, { label: 'Requests' }],
                data: [[1, 2, 3], [4, 5, 6]]
            };
            stationApi.get.mockResolvedValue(mockResponse);

            // Call the API
            const response = await stationApi.get('wpcloud-station/v1/metrics/site/123/requests', {
                query: {
                    dimension: 'time',
                    start: 'now-24h',
                    end: 'now'
                }
            });

            // Verify the API was called correctly
            expect(stationApi.get).toHaveBeenCalledWith('wpcloud-station/v1/metrics/site/123/requests', {
                query: {
                    dimension: 'time',
                    start: 'now-24h',
                    end: 'now'
                }
            });

            // Verify the response
            expect(response).toEqual(mockResponse);
            expect(response.meta.dimension).toBe('time');
            expect(response.series).toHaveLength(2);
            expect(response.data).toHaveLength(2);
        });

        it('handles errors when fetching site metrics', async () => {
            // Mock an API error
            const mockError = new Error('API error');
            stationApi.get.mockRejectedValue(mockError);

            // Call the API and expect it to throw
            await expect(stationApi.get('wpcloud-station/v1/metrics/site/123/requests')).rejects.toThrow('API error');
        });
    });

    describe('Time Parsing', () => {
        it('handles different time formats', async () => {
            // Mock the API response
            const mockResponse = {
                meta: { dimension: 'time' },
                series: [{ label: 'Time' }, { label: 'Requests' }],
                data: [[1, 2, 3], [4, 5, 6]]
            };
            stationApi.get.mockResolvedValue(mockResponse);

            // Test different time formats
            const timeFormats = [
                'now',
                'now-1h',
                'now-24h',
                'now-7d',
                '2023-01-01',
                '2023-01-01 12:00:00'
            ];

            for (const start of timeFormats) {
                for (const end of timeFormats) {
                    // Reset the mock
                    stationApi.get.mockReset();
                    stationApi.get.mockResolvedValue(mockResponse);

                    // Call the API
                    await stationApi.get('wpcloud-station/v1/metrics/client/requests', {
                        query: {
                            dimension: 'time',
                            start,
                            end
                        }
                    });

                    // Verify the API was called correctly
                    expect(stationApi.get).toHaveBeenCalledWith('wpcloud-station/v1/metrics/client/requests', {
                        query: {
                            dimension: 'time',
                            start,
                            end
                        }
                    });
                }
            }
        });
    });

    describe('Filters', () => {
        it('applies filters to metrics queries', async () => {
            // Mock the API response
            const mockResponse = {
                meta: { dimension: 'time' },
                series: [{ label: 'Time' }, { label: 'Requests' }],
                data: [[1, 2, 3], [4, 5, 6]]
            };
            stationApi.get.mockResolvedValue(mockResponse);

            // Define filters
            const filters = {
                status: [200, 404],
                url: ['/api', '/home']
            };

            // Call the API
            const response = await stationApi.get('wpcloud-station/v1/metrics/client/requests', {
                query: {
                    dimension: 'time',
                    start: 'now-24h',
                    end: 'now',
                    filters: JSON.stringify(filters)
                }
            });

            // Verify the API was called correctly
            expect(stationApi.get).toHaveBeenCalledWith('wpcloud-station/v1/metrics/client/requests', {
                query: {
                    dimension: 'time',
                    start: 'now-24h',
                    end: 'now',
                    filters: JSON.stringify(filters)
                }
            });

            // Verify the response
            expect(response).toEqual(mockResponse);
        });
    });

    describe('Summarize Option', () => {
        it('fetches summarized metrics', async () => {
            // Mock the API response
            const mockResponse = {
                meta: { dimension: 'status' },
                series: [{ label: 'Status' }, { label: 'Count' }],
                data: [[200, 404, 500], [100, 20, 5]]
            };
            stationApi.get.mockResolvedValue(mockResponse);

            // Call the API
            const response = await stationApi.get('wpcloud-station/v1/metrics/client/requests', {
                query: {
                    dimension: 'status',
                    start: 'now-24h',
                    end: 'now',
                    summarize: true
                }
            });

            // Verify the API was called correctly
            expect(stationApi.get).toHaveBeenCalledWith('wpcloud-station/v1/metrics/client/requests', {
                query: {
                    dimension: 'status',
                    start: 'now-24h',
                    end: 'now',
                    summarize: true
                }
            });

            // Verify the response
            expect(response).toEqual(mockResponse);
            expect(response.meta.dimension).toBe('status');
        });
    });
});
