/**
 * Set up test environment for Jest
 */

// Set up global mocks
global.wpcloud = {
    copyToClipboard: jest.fn(),
    revealValue: jest.fn(),
};

// Mock the navigator.clipboard API
Object.defineProperty(navigator, 'clipboard', {
    value: {
        writeText: jest.fn().mockResolvedValue(undefined),
    },
    configurable: true,
});

// Mock console methods to prevent noise during tests
global.console = {
    ...console,
    error: jest.fn(),
    warn: jest.fn(),
    log: jest.fn(),
};
