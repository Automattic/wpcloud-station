const mockScale = function(idx) {
  return {
    alpha: jest.fn().mockReturnThis(),
    css: jest.fn().mockReturnValue('#mock-color'),
  };
};

mockScale.domain = jest.fn().mockReturnThis();
mockScale.mode = jest.fn().mockReturnThis();

const chroma = {
  scale: jest.fn(() => mockScale),
};

export default chroma;
