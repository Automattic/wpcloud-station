const uPlot = jest.fn(() => ({
  setSize: jest.fn(),
  setData: jest.fn(),
  redraw: jest.fn(),
  destroy: jest.fn(),
}));

export default uPlot;
