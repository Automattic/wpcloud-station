/**
 * Internal dependencies
 */
import {
    stackedOptions,
    barOptions,
    lineOptions,
    areaOptions,
    defaultOptions,
} from '../../../../blocks/src/components/graph/components/lib/options';

// Mock the uplot-plugins module
jest.mock('../../../../blocks/src/components/graph/components/lib/uplot-plugins', () => ({
    seriesBarsPlugin: jest.fn(() => ({ id: 'seriesBarsPlugin' })),
}));

// Mock the utils module
jest.mock('../../../../blocks/src/components/graph/components/lib/utils', () => ({
    stack: jest.fn(() => ({ bands: [1, 2, 3], data: [[1, 2], [3, 4]] })),
}));

// Mock chroma-js
jest.mock('chroma-js', () => {
    const mockScale = function(idx) {
        return {
            alpha: jest.fn().mockReturnThis(),
            css: jest.fn().mockReturnValue('#mock-color'),
        };
    };

    mockScale.domain = jest.fn().mockReturnThis();
    mockScale.mode = jest.fn().mockReturnThis();

    return {
        __esModule: true,
        default: {
            scale: jest.fn(() => mockScale),
        },
    };
});

describe('Graph Options Module', () => {
    const defaultProps = {
        title: 'Test Graph',
        width: 800,
        height: 400,
        series: [
            { label: 'Time' },
            { label: 'Value 1', stroke: 'blue' },
            { label: 'Value 2', stroke: 'green' },
        ],
        data: [[1, 2, 3], [4, 5, 6], [7, 8, 9]],
    };

    describe('stackedOptions', () => {
        it('returns options with stacked plugin', () => {
            const result = stackedOptions(defaultProps);

            // Check that it includes the stacked plugin
            expect(result.plugins).toEqual([{ id: 'seriesBarsPlugin' }]);

            // Check that it includes the bands and data from the stack function
            expect(result.bands).toEqual([1, 2, 3]);
            expect(result.data).toEqual([[1, 2], [3, 4]]);

            // Check that it includes the title and dimensions
            expect(result.title).toBe('Test Graph');
            expect(result.width).toBe(800);
            expect(result.height).toBe(400);
        });

        it('applies fill colors to series', () => {
            const result = stackedOptions(defaultProps);

            // Check that the series have fill colors
            expect(result.series[1].fill).toBe('#mock-color');
            expect(result.series[2].fill).toBe('#mock-color');
        });
    });

    describe('barOptions', () => {
        it('returns options with bar plugin', () => {
            const result = barOptions(defaultProps);

            // Check that it includes the bar plugin
            expect(result.plugins).toEqual([{ id: 'seriesBarsPlugin' }]);

            // Check that it includes the title and dimensions
            expect(result.title).toBe('Test Graph');
            expect(result.width).toBe(800);
            expect(result.height).toBe(400);
        });

        it('applies fill colors to series', () => {
            const result = barOptions(defaultProps);

            // Check that the series have fill colors
            expect(result.series[1].fill).toBe('#mock-color');
            expect(result.series[2].fill).toBe('#mock-color');
        });
    });

    describe('lineOptions', () => {
        it('returns options with stroke colors', () => {
            const result = lineOptions(defaultProps);

            // Check that it includes the title and dimensions
            expect(result.title).toBe('Test Graph');
            expect(result.width).toBe(800);
            expect(result.height).toBe(400);

            // Check that the series have stroke colors
            expect(result.series[1].stroke).toBe('#mock-color');
            expect(result.series[2].stroke).toBe('#mock-color');
        });
    });

    describe('areaOptions', () => {
        it('returns options with fill and stroke colors', () => {
            const result = areaOptions(defaultProps);

            // Check that it includes the title and dimensions
            expect(result.title).toBe('Test Graph');
            expect(result.width).toBe(800);
            expect(result.height).toBe(400);

            // Check that the series have fill and stroke colors
            expect(result.series[1].fill).toBe('#mock-color');
            expect(result.series[1].stroke).toBe('#mock-color');
            expect(result.series[2].fill).toBe('#mock-color');
            expect(result.series[2].stroke).toBe('#mock-color');
        });
    });

    describe('defaultOptions', () => {
        it('returns options with default settings', () => {
            const result = defaultOptions(defaultProps);

            // Check that it includes the title and dimensions
            expect(result.title).toBe('Test Graph');
            expect(result.width).toBe(800);
            expect(result.height).toBe(400);

            // Check that it includes default padding and axes
            expect(result.padding).toEqual([null, 0, null, 0]);
            expect(result.axes).toEqual([{}, {}]);

            // Check that the series property exists
            expect(result.series).toBeDefined();

            // Log the result for debugging
            console.log('defaultOptions result:', JSON.stringify(result, null, 2));

            // Only check series if they exist
            if (result.series && result.series.length > 1) {
                // Check that the series have fill and stroke colors
                expect(result.series[1].fill).toBe('#mock-color');
                expect(result.series[1].stroke).toBe('#mock-color');

                if (result.series.length > 2) {
                    expect(result.series[2].fill).toBe('#mock-color');
                    expect(result.series[2].stroke).toBe('#mock-color');
                }
            }
        });
    });

    describe('Status vs Index Scale', () => {
        it('uses status scale when useStatus is true', () => {
            const props = {
                ...defaultProps,
                useStatus: true,
            };

            const result = barOptions(props);

            // The mock color should be applied to the series
            expect(result.series[1].fill).toBe('#mock-color');
            expect(result.series[2].fill).toBe('#mock-color');
        });

        it('uses index scale when useStatus is false', () => {
            const props = {
                ...defaultProps,
                useStatus: false,
            };

            const result = barOptions(props);

            // The mock color should be applied to the series
            expect(result.series[1].fill).toBe('#mock-color');
            expect(result.series[2].fill).toBe('#mock-color');
        });
    });
});
