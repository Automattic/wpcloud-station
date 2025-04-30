/**
 * Mock for @wpcloud/metrics/components/contexts
 */

// Create mock contexts with default values
const ApiContext = {
  Provider: ({ children }) => children,
  Consumer: ({ children }) => children({}),
};

const MetricsOptionsContext = {
  Provider: ({ children }) => children,
  Consumer: ({ children }) => children({}),
};

// Mock the useApiContext hook to return default values
const useApiContext = jest.fn().mockReturnValue({
  apiPath: 'metrics/site/test-site',
});

// Mock the useMetricsOptionsContext hook to return default values
const useMetricsOptionsContext = jest.fn().mockReturnValue({});

export {
  ApiContext,
  useApiContext,
  MetricsOptionsContext,
  useMetricsOptionsContext,
};
