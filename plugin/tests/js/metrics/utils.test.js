/**
 * External dependencies
 */
import '@testing-library/jest-dom';

/**
 * Internal dependencies
 */
import {
    getFromNow,
    isValidDate,
    parseTime,
    styleToObject,
    encodeFilter,
    decodeFilter
} from '../../../blocks/src/metrics/utils';

describe('Metrics Utils', () => {
    describe('getFromNow', () => {
        it('returns empty array for invalid input', () => {
            expect(getFromNow('')).toEqual([]);
            expect(getFromNow(null)).toEqual([]);
            expect(getFromNow(undefined)).toEqual([]);
            expect(getFromNow('invalid')).toEqual([]);
        });

        it('returns ["now"] for "now"', () => {
            expect(getFromNow('now')).toEqual(['now']);
        });

        it('returns empty array for invalid dash', () => {
            expect(getFromNow('now+1h')).toEqual([]);
        });

        it('returns empty array for "now-" without amount', () => {
            expect(getFromNow('now-')).toEqual([]);
        });

        it('returns empty array for "now-1" without unit', () => {
            expect(getFromNow('now-1')).toEqual([]);
        });

        it('returns empty array for invalid unit length', () => {
            expect(getFromNow('now-1hh')).toEqual([]);
        });

        it('returns ["now", "1", "h"] for "now-1h"', () => {
            expect(getFromNow('now-1h')).toEqual(['now', '1', 'h']);
        });

        it('returns ["now", "24", "h"] for "now-24h"', () => {
            expect(getFromNow('now-24h')).toEqual(['now', '24', 'h']);
        });

        it('returns ["now", "7", "d"] for "now-7d"', () => {
            expect(getFromNow('now-7d')).toEqual(['now', '7', 'd']);
        });

        it('returns ["now", "1", "M"] for "now-1M"', () => {
            expect(getFromNow('now-1M')).toEqual(['now', '1', 'M']);
        });
    });

    describe('isValidDate', () => {
        it('returns true for valid ISO date', () => {
            expect(isValidDate('2023-01-01')).toBe(true);
        });

        it('returns true for valid date string', () => {
            expect(isValidDate('January 1, 2023')).toBe(true);
        });

        it('returns true for "now"', () => {
            expect(isValidDate('now')).toBe(true);
        });

        it('returns true for "now-1h"', () => {
            expect(isValidDate('now-1h')).toBe(true);
        });

        it('returns false for invalid date', () => {
            expect(isValidDate('invalid-date')).toBe(false);
        });

        it('returns false for empty string', () => {
            expect(isValidDate('')).toBe(false);
        });
    });

    describe('parseTime', () => {
        beforeEach(() => {
            // Mock Date to return a fixed date for testing
            const mockDate = new Date('2023-01-01T12:00:00Z');
            jest.spyOn(global, 'Date').mockImplementation(() => mockDate);
        });

        afterEach(() => {
            jest.restoreAllMocks();
        });

        it('returns null for invalid input', () => {
            expect(parseTime('invalid')).toBeNull();
        });

        it('returns ISO string without T for valid ISO8601 format', () => {
            expect(parseTime('2023-01-01 12:00:00')).toBe('2023-01-01 12:00:00');
        });

        it('returns current time for "now"', () => {
            expect(parseTime('now')).toBe('2023-01-01 12:00:00');
        });

        it('subtracts seconds correctly', () => {
            const mockDate = new Date('2023-01-01T12:00:00Z');
            jest.spyOn(global, 'Date').mockImplementation(() => mockDate);

            // Get the actual result from the function
            const result = parseTime('now-30s');
            expect(result).toBe('2023-01-01 11:59:30');
        });

        it('subtracts minutes correctly', () => {
            const mockDate = new Date('2023-01-01T12:00:00Z');
            jest.spyOn(global, 'Date').mockImplementation(() => mockDate);

            // Get the actual result from the function
            const result = parseTime('now-30m');
            expect(result).toBe('2023-01-01 11:30:00');
        });

        it('subtracts hours correctly', () => {
            const mockDate = new Date('2023-01-01T12:00:00Z');
            jest.spyOn(global, 'Date').mockImplementation(() => mockDate);

            // Get the actual result from the function
            const result = parseTime('now-2h');
            expect(result).toBe('2023-01-01 10:00:00');
        });

        it('subtracts days correctly', () => {
            const mockDate = new Date('2023-01-01T12:00:00Z');
            jest.spyOn(global, 'Date').mockImplementation(() => mockDate);

            // Get the actual result from the function
            const result = parseTime('now-7d');
            expect(result).toBe('2022-12-25 12:00:00');
        });

        it('subtracts months correctly', () => {
            const mockDate = new Date('2023-01-01T12:00:00Z');
            jest.spyOn(global, 'Date').mockImplementation(() => mockDate);

            // Get the actual result from the function
            const result = parseTime('now-1M');
            expect(result).toBe('2022-12-01 12:00:00');
        });

        it('subtracts years correctly', () => {
            const mockDate = new Date('2023-01-01T12:00:00Z');
            jest.spyOn(global, 'Date').mockImplementation(() => mockDate);

            // Get the actual result from the function
            const result = parseTime('now-1y');
            expect(result).toBe('2022-01-01 12:00:00');
        });
    });

    describe('styleToObject', () => {
        it('returns empty object for null style', () => {
            const element = { getAttribute: () => null };
            expect(styleToObject(element)).toEqual({});
        });

        it('returns empty object for empty style', () => {
            const element = { getAttribute: () => '' };
            expect(styleToObject(element)).toEqual({});
        });

        it('converts style string to object', () => {
            const element = { getAttribute: () => 'color: red; font-size: 16px;' };
            expect(styleToObject(element)).toEqual({
                color: 'red',
                fontSize: '16px'
            });
        });

        it('handles kebab-case properties', () => {
            const element = { getAttribute: () => 'background-color: blue; border-radius: 5px;' };
            expect(styleToObject(element)).toEqual({
                backgroundColor: 'blue',
                borderRadius: '5px'
            });
        });

        it('handles multiple kebab-case properties', () => {
            const element = { getAttribute: () => 'margin-top: 10px; padding-bottom: 20px;' };
            expect(styleToObject(element)).toEqual({
                marginTop: '10px',
                paddingBottom: '20px'
            });
        });
    });

    describe('encodeFilter and decodeFilter', () => {
        it('encodes and decodes simple objects', () => {
            const obj = { name: 'test', value: 123 };
            const encoded = encodeFilter(obj);

            // Verify encoded is a string
            expect(typeof encoded).toBe('string');

            const decoded = decodeFilter(encoded);
            expect(decoded).toEqual(obj);
        });

        it('encodes and decodes arrays', () => {
            const arr = [1, 2, 3, 4, 5];
            const encoded = encodeFilter(arr);

            // Verify encoded is a string
            expect(typeof encoded).toBe('string');

            const decoded = decodeFilter(encoded);
            expect(decoded).toEqual(arr);
        });

        it('encodes and decodes complex objects', () => {
            const obj = {
                name: 'test',
                values: [1, 2, 3],
                nested: {
                    a: 'a',
                    b: 'b'
                }
            };
            const encoded = encodeFilter(obj);

            // Verify encoded is a string
            expect(typeof encoded).toBe('string');

            const decoded = decodeFilter(encoded);
            expect(decoded).toEqual(obj);
        });

        it('encodes and decodes empty objects', () => {
            const obj = {};
            const encoded = encodeFilter(obj);

            // Verify encoded is a string
            expect(typeof encoded).toBe('string');

            const decoded = decodeFilter(encoded);
            expect(decoded).toEqual(obj);
        });

        it('encodes and decodes empty arrays', () => {
            const arr = [];
            const encoded = encodeFilter(arr);

            // Verify encoded is a string
            expect(typeof encoded).toBe('string');

            const decoded = decodeFilter(encoded);
            expect(decoded).toEqual(arr);
        });

        it('encodes and decodes null values', () => {
            const obj = { value: null };
            const encoded = encodeFilter(obj);

            // Verify encoded is a string
            expect(typeof encoded).toBe('string');

            const decoded = decodeFilter(encoded);
            expect(decoded).toEqual(obj);
        });

        it('encodes and decodes boolean values', () => {
            const obj = { isTrue: true, isFalse: false };
            const encoded = encodeFilter(obj);

            // Verify encoded is a string
            expect(typeof encoded).toBe('string');

            const decoded = decodeFilter(encoded);
            expect(decoded).toEqual(obj);
        });

        it('handles the specific error case from the bug', () => {
            // This test simulates the bug scenario where a base62 string was being parsed directly as JSON
            const testObj = { test: 'value' };
            const encoded = encodeFilter(testObj);

            // Verify that trying to JSON.parse the encoded string directly would fail
            expect(() => {
                JSON.parse(encoded);
            }).toThrow();

            // But using decodeFilter works correctly
            const decoded = decodeFilter(encoded);
            expect(decoded).toEqual(testObj);
        });
    });
});
