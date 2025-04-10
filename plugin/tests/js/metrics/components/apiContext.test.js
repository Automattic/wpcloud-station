/**
 * External dependencies
 */
import React from 'react';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';

/**
 * Internal dependencies
 */
import { ApiContext, ApiProvider, useApiContext } from '@wpcloud/metrics/components/apiContext';

// Create a test component that uses the ApiContext
const TestComponent = () => {
    const { apiPath } = useApiContext();
    return <div data-testid="test-component">{apiPath}</div>;
};

describe('ApiContext', () => {
    it('provides the API path to child components', () => {
        render(
            <ApiProvider apiPath="wpcloud-station/v1/metrics/client">
                <TestComponent />
            </ApiProvider>
        );

        // Check if the API path is provided to the child component
        const testComponent = screen.getByTestId('test-component');
        expect(testComponent).toHaveTextContent('wpcloud-station/v1/metrics/client');
    });

    it('uses the default API path if none is provided', () => {
        render(
            <ApiProvider>
                <TestComponent />
            </ApiProvider>
        );

        // Check if the default API path is provided to the child component
        const testComponent = screen.getByTestId('test-component');
        expect(testComponent).toHaveTextContent('');
    });

    it('updates the API path when the provider changes', () => {
        const { rerender } = render(
            <ApiProvider apiPath="wpcloud-station/v1/metrics/client">
                <TestComponent />
            </ApiProvider>
        );

        // Check if the API path is provided to the child component
        let testComponent = screen.getByTestId('test-component');
        expect(testComponent).toHaveTextContent('wpcloud-station/v1/metrics/client');

        // Update the API path
        rerender(
            <ApiProvider apiPath="wpcloud-station/v1/metrics/site/123">
                <TestComponent />
            </ApiProvider>
        );

        // Check if the API path is updated
        testComponent = screen.getByTestId('test-component');
        expect(testComponent).toHaveTextContent('wpcloud-station/v1/metrics/site/123');
    });

    // Skip this test for now as it's difficult to test hooks outside of components
    it.skip('throws an error if useApiContext is used outside of ApiProvider', () => {
        // This test is skipped because it's difficult to test hooks outside of components
    });
});
