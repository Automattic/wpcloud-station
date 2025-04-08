const api = {
  get: jest.fn(() => Promise.resolve({
    data: [],
    series: [],
    meta: {},
  })),
};

export default api;
