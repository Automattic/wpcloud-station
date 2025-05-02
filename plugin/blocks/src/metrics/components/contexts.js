import { createContext, useContext } from 'react';

export const ApiContext = createContext(null);
export const useApiContext = () => useContext(ApiContext);

export const MetricsOptionsContext = createContext({});
export const useMetricsOptionsContext = () => useContext(MetricsOptionsContext);

export { LegendTooltipProvider, useLegendTooltip } from './legendTooltipContext';
