/**
 * Jest configuration for WP Cloud Station
 */

module.exports = {
    // Indicates whether each individual test should be reported during the run
    verbose: true,

    // The root directory that Jest should scan for tests and modules
    rootDir: '.',

    // A list of paths to directories that Jest should use to search for files in
    roots: ['<rootDir>/tests/js'],

    // The test environment that will be used for testing
    testEnvironment: 'jsdom',

    // The glob patterns Jest uses to detect test files
    testMatch: ['**/?(*.)+(spec|test).[jt]s?(x)'],

    // An array of regexp pattern strings that are matched against all test paths
    testPathIgnorePatterns: ['/node_modules/'],

    // An array of regexp pattern strings that are matched against all source file paths
    transformIgnorePatterns: ['/node_modules/'],

    // A map from regular expressions to paths to transformers
    transform: {
        '^.+\\.[jt]sx?$': 'babel-jest',
    },

    // Setup files that will be executed before each test file
    setupFilesAfterEnv: ['<rootDir>/tests/js/setup-tests.js'],

    // Automatically clear mock calls and instances between every test
    clearMocks: true,

    // Indicates whether the coverage information should be collected
    collectCoverage: false,

    // The directory where Jest should output its coverage files
    coverageDirectory: 'coverage',

    // An array of regexp pattern strings used to skip coverage collection
    coveragePathIgnorePatterns: ['/node_modules/', '/tests/'],

    // A list of reporter names that Jest uses when writing coverage reports
    coverageReporters: ['text', 'lcov'],

    // Automatically reset mock state between every test
    resetMocks: false,

    // Reset the module registry before running each individual test
    resetModules: false,

    // This option allows the use of a custom results processor
    // testResultsProcessor: null,

    // This option allows use of a custom test runner
    // testRunner: "jasmine2",

    // This option sets the URL for the jsdom environment
    testEnvironmentOptions: {
        url: 'http://localhost',
    },

    // Setting this value to "fake" allows the use of fake timers for functions such as "setTimeout"
    fakeTimers: {
        enableGlobally: true,
    },

    // Pass with no tests
    passWithNoTests: true,

    // An array of regexp pattern strings that are matched against all modules before they are loaded
    moduleNameMapper: {
        '\\.(css|less|scss|sass)$': '<rootDir>/tests/js/mocks/styleMock.js',
        '\\.(gif|ttf|eot|svg|png)$': '<rootDir>/tests/js/mocks/fileMock.js',
        '^@wordpress/(.*)$': '<rootDir>/tests/js/mocks/wordpress/$1.js',
        '^@wpcloud/(.*)$': '<rootDir>/tests/js/mocks/wpcloud/$1.js',
        '^uplot$': '<rootDir>/tests/js/mocks/uplot.js',
        '.*uplot-plugins$': '<rootDir>/tests/js/mocks/uplot-plugins.js',
        '^chroma-js$': '<rootDir>/tests/js/mocks/chroma.js',
        '^croma$': '<rootDir>/tests/js/mocks/chroma.js',
    },
};
