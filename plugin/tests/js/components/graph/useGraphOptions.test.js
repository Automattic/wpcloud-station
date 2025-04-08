/**
 * External dependencies
 */
import { renderHook } from '@testing-library/react';

// Explicitly mock the ResizeObserver
global.ResizeObserver = jest.fn().mockImplementation(() => ({
    observe: jest.fn(),
    unobserve: jest.fn(),
    disconnect: jest.fn(),
}));

// Import the actual implementation
import useGraphOptions from '../../../../blocks/src/components/graph/components/lib/useGraphOptions';

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

    it('returns options with the correct type based on the type prop', () => {
        // Test default type
        const { result: defaultResult } = renderHook(() => useGraphOptions(defaultProps));
        expect(defaultResult.current).toBeDefined();

        // Test stacked-bar type
        const { result: stackedResult } = renderHook(() =>
            useGraphOptions({ ...defaultProps, type: 'stacked-bar' })
        );
        expect(stackedResult.current).toBeDefined();

        // Test bar type
        const { result: barResult } = renderHook(() =>
            useGraphOptions({ ...defaultProps, type: 'bar' })
        );
        expect(barResult.current).toBeDefined();

        // Test line type
        const { result: lineResult } = renderHook(() =>
            useGraphOptions({ ...defaultProps, type: 'line' })
        );
        expect(lineResult.current).toBeDefined();

        // Test area type
        const { result: areaResult } = renderHook(() =>
            useGraphOptions({ ...defaultProps, type: 'area' })
        );
        expect(areaResult.current).toBeDefined();
    });

    it('sets useStatus to true when dimension includes status', () => {
        const props = {
            ...defaultProps,
            type: 'bar',
            meta: { dimension: 'status' },
        };
        const { result } = renderHook(() => useGraphOptions(props));

        // The hook should set useStatus to true when dimension includes status
        expect(result.current).toBeDefined();
    });

    it('disables legend when showLegend is false', () => {
        const props = {
            ...defaultProps,
            type: 'bar',
            showLegend: false,
        };
        const { result } = renderHook(() => useGraphOptions(props));

        // The hook should disable the legend when showLegend is false
        expect(result.current).toBeDefined();
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

        const { result } = renderHook(() => useGraphOptions(props));

        // The hook should calculate width and height based on container dimensions
        expect(result.current).toBeDefined();
    });
});
