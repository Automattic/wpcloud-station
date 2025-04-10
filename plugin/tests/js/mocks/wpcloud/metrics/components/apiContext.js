/**
 * Mock for the API context
 */
import React, { createContext, useContext } from 'react';

// Create the context
export const ApiContext = createContext({
    apiPath: ''
});

// Create a provider component
export const ApiProvider = ({ apiPath = '', children }) => {
    return (
        <ApiContext.Provider value={{ apiPath }}>
            {children}
        </ApiContext.Provider>
    );
};

// Create a hook to use the context
export const useApiContext = () => {
    const context = useContext(ApiContext);
    if (!context) {
        throw new Error('useApiContext must be used within an ApiProvider');
    }
    return context;
};
