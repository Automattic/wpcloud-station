/**
 * Mock for @wpcloud/utils/api
 */

const stationApi = {
  get: jest.fn().mockImplementation((path, options = {}) => {
    // Default mock response
    return Promise.resolve({
      data: [
        [0, 1, 2, 3, 4, 5],
        [10, 20, 30, 40, 50, 60],
        [15, 25, 35, 45, 55, 65]
      ],
      series: [
        { label: 'Time' },
        { label: 'Series 1' },
        { label: 'Series 2' }
      ],
      meta: {
        title: 'Mock Graph Data',
        dimension: 'mock_dimension',
        metric: 'mock_metric'
      }
    });
  }),
  post: jest.fn().mockImplementation((path, data, options = {}) => {
    return Promise.resolve({ success: true });
  }),
  put: jest.fn().mockImplementation((path, data, options = {}) => {
    return Promise.resolve({ success: true });
  }),
  delete: jest.fn().mockImplementation((path, options = {}) => {
    return Promise.resolve({ success: true });
  })
};

export default stationApi;
