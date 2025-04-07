/**
 * External dependencies
 */
import { renderHook } from '@testing-library/react';

/**
 * Internal dependencies
 */
import useGraphOptions from '../../../../blocks/src/components/graph/components/lib/useGraphOptions';
import * as optionsModule from '../../../../blocks/src/components/graph/components/lib/options';

// Mock the options module
jest.mock('../../../../blocks/src/components/graph/components/lib/options', () => ({
    stackedOptions: jest.fn(() => ({ type: 'stacked', data: [] })),
    barOptions: jest.fn(() => ({ type: 'bar', data: [] })),
    lineOptions: jest.fn(() => ({ type: 'line', data: [] })),
    areaOptions: jest.fn(() => ({ type: 'area', data: [] })),
    defaultOptions: jest.fn(() => ({ type: 'default', data: [] })),
}));

describe('useGraphOptions Hook', () => {
    const mockContainerRef = {
        current: {
            offsetWidth: 800,
            offsetHeight: 500,
        },
    };

    const defaultProps = {
        title: 'Test Graph',
        series: [{ label: 'Time' }, { label: 'Value' }],
        meta: { dimension: 'time' },
        data: [[1, 2, 3], [4, 5, 6]],
        containerRef: mockContainerRef,
        showLegend: true,
    };

    beforeEach(() => {
        // Reset all mocks before each test
        jest.clearAllMocks();
    });

    it('returns default options when no type is specified', () => {
        const { result } = renderHook(() => useGraphOptions(defaultProps));

        expect(optionsModule.defaultOptions).toHaveBeenCalled();
        expect(result.current).toEqual({ type: 'default', data: [] });
    });

    it('returns stacked-bar options when type is stacked-bar', () => {
        const props = { ...defaultProps, type: 'stacked-bar' };
        const { result } = renderHook(() => useGraphOptions(props));

        expect(optionsModule.stackedOptions).toHaveBeenCalled();
        expect(result.current).toEqual({ type: 'stacked', data: [] });
    });

    it('returns bar options when type is bar', () => {
        const props = { ...defaultProps, type: 'bar' };
        const { result } = renderHook(() => useGraphOptions(props));

        expect(optionsModule.barOptions).toHaveBeenCalled();
        expect(result.current).toEqual({ type: 'bar', data: [] });
    });

    it('returns line options when type is line', () => {
        const props = { ...defaultProps, type: 'line' };
        const { result } = renderHook(() => useGraphOptions(props));

        expect(optionsModule.lineOptions).toHaveBeenCalled();
        expect(result.current).toEqual({ type: 'line', data: [] });
    });

    it('returns area options when type is area', () => {
        const props = { ...defaultProps, type: 'area' };
        const { result } = renderHook(() => useGraphOptions(props));

        expect(optionsModule.areaOptions).toHaveBeenCalled();
        expect(result.current).toEqual({ type: 'area', data: [] });
    });

    it('passes the correct parameters to the options functions', () => {
        const props = { ...defaultProps, type: 'bar' };
        renderHook(() => useGraphOptions(props));

        expect(optionsModule.barOptions).toHaveBeenCalledWith(expect.objectContaining({
            title: 'Test Graph',
            series: [{ label: 'Time' }, { label: 'Value' }],
            meta: { dimension: 'time' },
            useStatus: false,
            width: expect.any(Number),
            height: expect.any(Number),
        }));
    });

    it('sets useStatus to true when dimension includes status', () => {
        const props = {
            ...defaultProps,
            type: 'bar',
            meta: { dimension: 'status' },
        };
        renderHook(() => useGraphOptions(props));

        expect(optionsModule.barOptions).toHaveBeenCalledWith(expect.objectContaining({
            useStatus: true,
        }));
    });

    it('disables legend when showLegend is false', () => {
        const props = {
            ...defaultProps,
            type: 'bar',
            showLegend: false,
        };
        renderHook(() => useGraphOptions(props));

        expect(optionsModule.barOptions).toHaveBeenCalledWith(expect.objectContaining({
            legend: { live: false },
        }));
    });

    it('calculates width and height based on container dimensions', () => {
        // Mock a different container size
        const customContainerRef = {
            current: {
                offsetWidth: 1000,
                offsetHeight: 600,
            },
        };

        const props = {
            ...defaultProps,
            containerRef: customContainerRef,
        };

        renderHook(() => useGraphOptions(props));

        // The hook should subtract fitGraphWidth (20) from width and fitGraphHeight (75) from height
        expect(optionsModule.defaultOptions).toHaveBeenCalledWith(expect.objectContaining({
            width: 980, // 1000 - 20
            height: 525, // 600 - 75
        }));
    });
});
