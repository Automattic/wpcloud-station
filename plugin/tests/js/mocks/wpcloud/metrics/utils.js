/**
 * Re-export the actual utils functions for testing
 */
import * as utils from '../../../../../blocks/src/metrics/utils';

// Export all functions from the original utils module
export default utils;
export const {
    getFromNow,
    isValidDate,
    parseTime,
    buildTree,
    styleToObject,
    encodeFilter,
    decodeFilter
} = utils;
